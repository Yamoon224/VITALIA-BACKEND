<?php

use App\Domains\Assistant\DTOs\LlmReply;
use App\Domains\Assistant\Providers\ArrayLlmProvider;
use App\Domains\Conversation\Enums\ConversationTopic;
use App\Models\User;
use Database\Seeders\RoleSeeder;

beforeEach(function (): void {
    $this->seed(RoleSeeder::class);
});

it('lets an authenticated patient start a conversation and receive the agent reply', function (): void {
    $user = User::factory()->create();
    $user->assignRole('patient');

    /** @var ArrayLlmProvider $llm */
    $llm = app(ArrayLlmProvider::class);
    $llm->queue(new LlmReply(
        content: 'Merci pour ces précisions. Peux-tu me dire depuis quand tu as mal à la tête ?',
        model: 'array-fake',
        inputTokens: 10,
        outputTokens: 15,
    ));

    $conversation = $this->actingAs($user)
        ->postJson('/api/v1/conversations', ['topic' => ConversationTopic::Consultation->value])
        ->assertCreated()
        ->json('data');

    $response = $this->actingAs($user)
        ->postJson("/api/v1/conversations/{$conversation['id']}/messages", [
            'content' => "J'ai mal à la tête depuis ce matin.",
        ])
        ->assertOk();

    $response->assertJson([
        'content' => 'Merci pour ces précisions. Peux-tu me dire depuis quand tu as mal à la tête ?',
        'is_emergency' => false,
        'urgency_level' => null,
    ]);

    $this->assertDatabaseCount('messages', 2);
});

it('short-circuits the LLM and flags an emergency without calling the model', function (): void {
    $user = User::factory()->create();
    $user->assignRole('patient');

    /** @var ArrayLlmProvider $llm */
    $llm = app(ArrayLlmProvider::class);

    $conversation = $this->actingAs($user)
        ->postJson('/api/v1/conversations', ['topic' => ConversationTopic::Consultation->value])
        ->json('data');

    $response = $this->actingAs($user)
        ->postJson("/api/v1/conversations/{$conversation['id']}/messages", [
            'content' => "J'ai une douleur thoracique insupportable, je n'arrive plus à respirer.",
        ])
        ->assertOk();

    $response->assertJson(['is_emergency' => true, 'urgency_level' => 'emergency']);

    // Le niveau d'urgence prime sur toute autre logique (section 5.4) :
    // le LLM n'est jamais appelé quand une urgence est détectée en amont.
    expect($llm->sentMessages())->toBeEmpty();

    $this->assertDatabaseCount('emergency_detections', 1);
});

it('rejects a message for a conversation that does not belong to the user', function (): void {
    $owner = User::factory()->create();
    $owner->assignRole('patient');
    $intruder = User::factory()->create();
    $intruder->assignRole('patient');

    $conversation = $this->actingAs($owner)
        ->postJson('/api/v1/conversations', ['topic' => ConversationTopic::Consultation->value])
        ->json('data');

    $this->actingAs($intruder)
        ->postJson("/api/v1/conversations/{$conversation['id']}/messages", ['content' => 'Bonjour'])
        ->assertNotFound();
});
