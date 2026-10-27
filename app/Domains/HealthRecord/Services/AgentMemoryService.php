<?php

namespace App\Domains\HealthRecord\Services;

use App\Domains\HealthRecord\Contracts\AgentMemoryRepositoryContract;
use App\Domains\HealthRecord\Enums\MemoryEntryStatus;
use App\Domains\HealthRecord\Exceptions\MemoryEntryAlreadyReviewedException;
use App\Models\AgentMemoryEntry;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Validation de l'utilisateur avant enregistrement (section 5.8) : seules
 * `accept()` et `reject()` font sortir une entrée de l'état `Proposed`, et
 * uniquement vers un état terminal — pas d'écriture silencieuse possible.
 */
class AgentMemoryService
{
    public function __construct(private readonly AgentMemoryRepositoryContract $memory) {}

    public function propose(User $user, string $content, ?int $conversationId = null): AgentMemoryEntry
    {
        return $this->memory->propose($user, $content, $conversationId);
    }

    /** @return Collection<int, AgentMemoryEntry> */
    public function pendingFor(User $user): Collection
    {
        return $this->memory->pendingFor($user);
    }

    public function accept(AgentMemoryEntry $entry): AgentMemoryEntry
    {
        $this->assertPending($entry);

        return $this->memory->updateStatus($entry, MemoryEntryStatus::Accepted);
    }

    public function reject(AgentMemoryEntry $entry): AgentMemoryEntry
    {
        $this->assertPending($entry);

        return $this->memory->updateStatus($entry, MemoryEntryStatus::Rejected);
    }

    private function assertPending(AgentMemoryEntry $entry): void
    {
        if ($entry->status !== MemoryEntryStatus::Proposed) {
            throw MemoryEntryAlreadyReviewedException::make();
        }
    }
}
