<?php

namespace App\Domains\Assistant\Providers;

use App\Domains\Assistant\Contracts\LlmGatewayContract;
use App\Domains\Assistant\DTOs\LlmMessage;
use App\Domains\Assistant\DTOs\LlmReply;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Adaptateur Claude (section 6, « Fournisseur et accès »).
 *
 * La clé d'API n'est jamais exposée aux applications clientes : cette classe
 * n'est instanciée que côté backend, par `DomainServiceProvider`, à partir
 * de `config('assistant.claude.key')` (une variable d'environnement).
 */
class ClaudeLlmProvider implements LlmGatewayContract
{
    private const string API_VERSION = '2023-06-01';

    public function __construct(
        private readonly string $apiKey,
        private readonly string $model,
        private readonly string $baseUrl = 'https://api.anthropic.com/v1',
        private readonly int $maxTokens = 1024,
    ) {}

    public function complete(string $systemPrompt, array $messages): LlmReply
    {
        /** @var array{content?: list<array{type: string, text?: string}>, model?: string, usage?: array{input_tokens?: int, output_tokens?: int}} $response */
        $response = $this->client()
            ->post('/messages', $this->payload($systemPrompt, $messages, stream: false))
            ->throw()
            ->json();

        $text = '';

        foreach ($response['content'] ?? [] as $block) {
            if ($block['type'] === 'text') {
                $text .= $block['text'] ?? '';
            }
        }

        return new LlmReply(
            content: $text,
            model: $response['model'] ?? $this->model,
            inputTokens: $response['usage']['input_tokens'] ?? 0,
            outputTokens: $response['usage']['output_tokens'] ?? 0,
            raw: $response,
        );
    }

    public function stream(string $systemPrompt, array $messages, callable $onDelta): LlmReply
    {
        $response = $this->client()
            ->withOptions(['stream' => true])
            ->post('/messages', $this->payload($systemPrompt, $messages, stream: true))
            ->throw();

        $body = $response->toPsrResponse()->getBody();
        $buffer = '';
        $content = '';
        $model = $this->model;
        $inputTokens = 0;
        $outputTokens = 0;

        while (! $body->eof()) {
            $buffer .= $body->read(1024);

            while (($position = strpos($buffer, "\n\n")) !== false) {
                $event = substr($buffer, 0, $position);
                $buffer = substr($buffer, $position + 2);

                foreach (explode("\n", $event) as $line) {
                    if (! str_starts_with($line, 'data:')) {
                        continue;
                    }

                    $payload = json_decode(trim(substr($line, 5)), true);

                    if (! is_array($payload)) {
                        continue;
                    }

                    [$content, $model, $inputTokens, $outputTokens] = $this->applyStreamEvent(
                        $payload,
                        $onDelta,
                        $content,
                        $model,
                        $inputTokens,
                        $outputTokens,
                    );
                }
            }
        }

        return new LlmReply($content, $model, $inputTokens, $outputTokens);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array{0: string, 1: string, 2: int, 3: int}
     */
    private function applyStreamEvent(
        array $payload,
        callable $onDelta,
        string $content,
        string $model,
        int $inputTokens,
        int $outputTokens,
    ): array {
        $type = $payload['type'] ?? null;

        if ($type === 'content_block_delta' && ($payload['delta']['type'] ?? null) === 'text_delta') {
            $delta = (string) $payload['delta']['text'];
            $content .= $delta;
            $onDelta($delta);
        }

        if ($type === 'message_start') {
            $model = $payload['message']['model'] ?? $model;
            $inputTokens = $payload['message']['usage']['input_tokens'] ?? $inputTokens;
        }

        if ($type === 'message_delta') {
            $outputTokens = $payload['usage']['output_tokens'] ?? $outputTokens;
        }

        return [$content, $model, $inputTokens, $outputTokens];
    }

    private function client(): PendingRequest
    {
        if ($this->apiKey === '') {
            throw new RuntimeException('ANTHROPIC_API_KEY manquante : configurez assistant.claude.key.');
        }

        return Http::baseUrl($this->baseUrl)->withHeaders([
            'x-api-key' => $this->apiKey,
            'anthropic-version' => self::API_VERSION,
            'content-type' => 'application/json',
        ])->timeout(60);
    }

    /**
     * @param  list<LlmMessage>  $messages
     * @return array<string, mixed>
     */
    private function payload(string $systemPrompt, array $messages, bool $stream): array
    {
        return [
            'model' => $this->model,
            'max_tokens' => $this->maxTokens,
            'system' => $systemPrompt,
            'messages' => array_map(
                fn (LlmMessage $message): array => ['role' => $message->role, 'content' => $message->content],
                $messages,
            ),
            'stream' => $stream,
        ];
    }
}
