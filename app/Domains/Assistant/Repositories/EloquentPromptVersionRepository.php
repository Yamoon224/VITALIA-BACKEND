<?php

namespace App\Domains\Assistant\Repositories;

use App\Domains\Assistant\Contracts\PromptVersionRepositoryContract;
use App\Domains\Assistant\Enums\PromptVersionStatus;
use App\Models\PromptVersion;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class EloquentPromptVersionRepository implements PromptVersionRepositoryContract
{
    private const string CACHE_PREFIX = 'prompt_version:published:';

    public function publishedContentFor(string $blockKey): ?string
    {
        // Mis en cache (section 6, « mise en cache des blocs stables ») :
        // ce contenu est lu à chaque message, jamais pour chaque écriture.
        return Cache::remember(self::CACHE_PREFIX.$blockKey, now()->addMinutes(10), function () use ($blockKey) {
            return PromptVersion::query()
                ->where('block_key', $blockKey)
                ->where('status', PromptVersionStatus::Published)
                ->value('content');
        });
    }

    public function draft(string $blockKey, string $content, ?User $author): PromptVersion
    {
        $nextVersion = (int) PromptVersion::query()->where('block_key', $blockKey)->max('version') + 1;

        return PromptVersion::query()->create([
            'block_key' => $blockKey,
            'version' => $nextVersion,
            'content' => $content,
            'status' => PromptVersionStatus::Draft,
            'created_by' => $author?->id,
        ]);
    }

    public function publish(PromptVersion $version): PromptVersion
    {
        PromptVersion::query()
            ->where('block_key', $version->block_key)
            ->where('status', PromptVersionStatus::Published)
            ->update(['status' => PromptVersionStatus::Archived]);

        $version->status = PromptVersionStatus::Published;
        $version->published_at = now();
        $version->save();

        Cache::forget(self::CACHE_PREFIX.$version->block_key);

        return $version;
    }

    public function find(int $id): ?PromptVersion
    {
        return PromptVersion::query()->find($id);
    }

    /** @return Collection<int, PromptVersion> */
    public function historyFor(string $blockKey): Collection
    {
        return PromptVersion::query()->where('block_key', $blockKey)->orderByDesc('version')->get();
    }
}
