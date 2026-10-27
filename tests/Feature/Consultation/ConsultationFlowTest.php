<?php

use App\Domains\Assistant\DTOs\LlmReply;
use App\Domains\Assistant\Providers\ArrayLlmProvider;
use App\Domains\Conversation\Enums\ConversationTopic;
use App\Models\User;
use Database\Seeders\RoleSeeder;

beforeEach(function (): void {
    $this->seed(RoleSeeder::class);
});

it('completes a consultation and persists the structured synthesis', function (): void {
    $user = User::factory()->create();
    $user->assignRole('patient');

    $conversation = $this->actingAs($user)
        ->postJson('/api/v1/conversations', ['topic' => ConversationTopic::Consultation->value])
        ->json('data');

    $this->actingAs($user)
        ->postJson("/api/v1/conversations/{$conversation['id']}/consultation/start")
        ->assertCreated()
        ->assertJson(['data' => ['status' => 'in_progress']]);

    /** @var ArrayLlmProvider $llm */
    $llm = app(ArrayLlmProvider::class);
    $llm->queue(new LlmReply(
        content: json_encode([
            'summary' => 'Douleur abdominale modérée depuis 24h, sans signe de gravité rapporté.',
            'hypotheses' => [['condition' => 'Gastro-entérite', 'confidence' => 0.5]],
            'orientation_level' => 'scheduled_consultation',
            'red_flags' => ['fièvre persistante', 'sang dans les selles'],
        ]),
        model: 'array-fake',
        inputTokens: 5,
        outputTokens: 5,
    ));

    $response = $this->actingAs($user)
        ->postJson("/api/v1/conversations/{$conversation['id']}/consultation/finish")
        ->assertOk();

    $response->assertJson([
        'data' => [
            'status' => 'completed',
            'orientation_level' => 'scheduled_consultation',
        ],
    ]);

    $this->assertDatabaseHas('consultation_sessions', [
        'conversation_id' => $conversation['id'],
        'status' => 'completed',
    ]);
});

it('fails explicitly when the model never returns a valid synthesis', function (): void {
    $user = User::factory()->create();
    $user->assignRole('patient');

    $conversation = $this->actingAs($user)
        ->postJson('/api/v1/conversations', ['topic' => ConversationTopic::Consultation->value])
        ->json('data');

    $this->actingAs($user)->postJson("/api/v1/conversations/{$conversation['id']}/consultation/start");

    /** @var ArrayLlmProvider $llm */
    $llm = app(ArrayLlmProvider::class);
    $llm->queue(new LlmReply(content: 'pas du JSON du tout', model: 'array-fake', inputTokens: 1, outputTokens: 1));
    $llm->queue(new LlmReply(content: 'toujours pas du JSON', model: 'array-fake', inputTokens: 1, outputTokens: 1));

    $this->actingAs($user)
        ->postJson("/api/v1/conversations/{$conversation['id']}/consultation/finish")
        ->assertStatus(502)
        ->assertJson(['error_code' => 'synthesis_generation_failed']);
});
