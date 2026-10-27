<?php

namespace App\Domains\HealthRecord\Repositories;

use App\Domains\HealthRecord\Contracts\AgentMemoryRepositoryContract;
use App\Domains\HealthRecord\Enums\MemoryEntryStatus;
use App\Models\AgentMemoryEntry;
use App\Models\User;
use Illuminate\Support\Collection;

class EloquentAgentMemoryRepository implements AgentMemoryRepositoryContract
{
    public function propose(User $user, string $content, ?int $conversationId = null): AgentMemoryEntry
    {
        return AgentMemoryEntry::query()->create([
            'user_id' => $user->id,
            'conversation_id' => $conversationId,
            'content' => $content,
            'status' => MemoryEntryStatus::Proposed,
        ]);
    }

    /** @return Collection<int, AgentMemoryEntry> */
    public function pendingFor(User $user): Collection
    {
        return AgentMemoryEntry::query()
            ->where('user_id', $user->id)
            ->where('status', MemoryEntryStatus::Proposed)
            ->latest()
            ->get();
    }

    /** @return Collection<int, AgentMemoryEntry> */
    public function acceptedFor(User $user): Collection
    {
        return AgentMemoryEntry::query()
            ->where('user_id', $user->id)
            ->where('status', MemoryEntryStatus::Accepted)
            ->latest()
            ->get();
    }

    public function find(int $id): ?AgentMemoryEntry
    {
        return AgentMemoryEntry::query()->find($id);
    }

    public function updateStatus(AgentMemoryEntry $entry, MemoryEntryStatus $status): AgentMemoryEntry
    {
        $entry->status = $status;
        $entry->save();

        return $entry;
    }
}
