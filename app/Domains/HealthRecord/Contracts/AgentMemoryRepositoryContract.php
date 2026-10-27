<?php

namespace App\Domains\HealthRecord\Contracts;

use App\Domains\HealthRecord\Enums\MemoryEntryStatus;
use App\Models\AgentMemoryEntry;
use App\Models\User;
use Illuminate\Support\Collection;

interface AgentMemoryRepositoryContract
{
    public function propose(User $user, string $content, ?int $conversationId = null): AgentMemoryEntry;

    /** @return Collection<int, AgentMemoryEntry> */
    public function pendingFor(User $user): Collection;

    /** @return Collection<int, AgentMemoryEntry> */
    public function acceptedFor(User $user): Collection;

    public function find(int $id): ?AgentMemoryEntry;

    public function updateStatus(AgentMemoryEntry $entry, MemoryEntryStatus $status): AgentMemoryEntry;
}
