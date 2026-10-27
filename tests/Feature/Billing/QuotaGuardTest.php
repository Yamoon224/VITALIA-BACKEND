<?php

use App\Domains\Assistant\DTOs\LlmReply;
use App\Domains\Assistant\Providers\ArrayLlmProvider;
use App\Domains\Billing\Enums\SubscriptionPlan;
use App\Domains\Conversation\Enums\ConversationTopic;
use App\Models\Subscription;
use App\Models\User;
use Database\Seeders\RoleSeeder;

beforeEach(function (): void {
    $this->seed(RoleSeeder::class);
    // Le message en cours est compté avant l'appel au modèle : avec une
    // limite de 3, les deux premiers messages passent et le troisième est
    // bloqué (le compte atteint 3 au moment de sa propre vérification).
    config(['assistant.quotas.free_messages_per_day' => 3]);
});

it('blocks a free user once the daily quota is reached, without calling the model', function (): void {
    $user = User::factory()->create();
    $user->assignRole('patient');

    /** @var ArrayLlmProvider $llm */
    $llm = app(ArrayLlmProvider::class);
    $llm->queue(new LlmReply('Réponse 1', 'array-fake', 1, 1));
    $llm->queue(new LlmReply('Réponse 2', 'array-fake', 1, 1));

    $conversation = $this->actingAs($user)
        ->postJson('/api/v1/conversations', ['topic' => ConversationTopic::Consultation->value])
        ->json('data');

    foreach (range(1, 2) as $i) {
        $this->actingAs($user)
            ->postJson("/api/v1/conversations/{$conversation['id']}/messages", ['content' => "Message {$i}"])
            ->assertOk()
            ->assertJson(['quota_exceeded' => false]);
    }

    $this->actingAs($user)
        ->postJson("/api/v1/conversations/{$conversation['id']}/messages", ['content' => 'Message 3'])
        ->assertOk()
        ->assertJson(['quota_exceeded' => true]);

    // Les deux réponses mises en file ont été consommées par les deux
    // premiers messages : le troisième n'a pas appelé le modèle.
    expect($llm->sentMessages())->toHaveCount(2);
});

it('never blocks an emergency message, even once the quota is exhausted', function (): void {
    $user = User::factory()->create();
    $user->assignRole('patient');

    $conversation = $this->actingAs($user)
        ->postJson('/api/v1/conversations', ['topic' => ConversationTopic::Consultation->value])
        ->json('data');

    $this->actingAs($user)->postJson("/api/v1/conversations/{$conversation['id']}/messages", ['content' => 'Un']);
    $this->actingAs($user)->postJson("/api/v1/conversations/{$conversation['id']}/messages", ['content' => 'Deux']);

    $this->actingAs($user)
        ->postJson("/api/v1/conversations/{$conversation['id']}/messages", [
            'content' => "J'ai une douleur thoracique insupportable, je n'arrive plus à respirer.",
        ])
        ->assertOk()
        ->assertJson(['is_emergency' => true, 'quota_exceeded' => false]);
});

it('never blocks a premium subscriber regardless of message count', function (): void {
    $user = User::factory()->create();
    $user->assignRole('patient');
    Subscription::factory()->for($user)->premium()->create(['plan' => SubscriptionPlan::Premium]);

    /** @var ArrayLlmProvider $llm */
    $llm = app(ArrayLlmProvider::class);
    foreach (range(1, 3) as $i) {
        $llm->queue(new LlmReply("Réponse {$i}", 'array-fake', 1, 1));
    }

    $conversation = $this->actingAs($user)
        ->postJson('/api/v1/conversations', ['topic' => ConversationTopic::Consultation->value])
        ->json('data');

    foreach (range(1, 3) as $i) {
        $this->actingAs($user)
            ->postJson("/api/v1/conversations/{$conversation['id']}/messages", ['content' => "Message {$i}"])
            ->assertOk()
            ->assertJson(['quota_exceeded' => false]);
    }
});
