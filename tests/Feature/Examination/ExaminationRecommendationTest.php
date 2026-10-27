<?php

use App\Domains\Assistant\DTOs\LlmReply;
use App\Domains\Assistant\Providers\ArrayLlmProvider;
use App\Domains\Conversation\Enums\ConversationTopic;
use App\Models\User;
use Database\Seeders\RoleSeeder;

beforeEach(function (): void {
    $this->seed(RoleSeeder::class);
});

it('generates a draft examination request from a conversation', function (): void {
    $user = User::factory()->create();
    $user->assignRole('patient');

    $conversation = $this->actingAs($user)
        ->postJson('/api/v1/conversations', ['topic' => ConversationTopic::Consultation->value])
        ->json('data');

    /** @var ArrayLlmProvider $llm */
    $llm = app(ArrayLlmProvider::class);
    $llm->queue(new LlmReply(
        content: json_encode([
            ['name' => 'Numération formule sanguine', 'reason' => 'Fatigue persistante', 'prep_instructions' => ''],
        ]),
        model: 'array-fake',
        inputTokens: 5,
        outputTokens: 5,
    ));

    $response = $this->actingAs($user)
        ->postJson("/api/v1/conversations/{$conversation['id']}/examination-recommendation")
        ->assertCreated();

    $response->assertJson(['data' => ['status' => 'draft']]);

    $this->assertDatabaseHas('examination_requests', [
        'user_id' => $user->id,
        'conversation_id' => $conversation['id'],
        'status' => 'draft',
    ]);
});

it('fails explicitly when the model never returns a valid recommendation', function (): void {
    $user = User::factory()->create();
    $user->assignRole('patient');

    $conversation = $this->actingAs($user)
        ->postJson('/api/v1/conversations', ['topic' => ConversationTopic::Consultation->value])
        ->json('data');

    /** @var ArrayLlmProvider $llm */
    $llm = app(ArrayLlmProvider::class);
    $llm->queue(new LlmReply(content: 'pas du JSON du tout', model: 'array-fake', inputTokens: 1, outputTokens: 1));
    $llm->queue(new LlmReply(content: 'toujours pas du JSON', model: 'array-fake', inputTokens: 1, outputTokens: 1));

    $this->actingAs($user)
        ->postJson("/api/v1/conversations/{$conversation['id']}/examination-recommendation")
        ->assertStatus(502);
});
