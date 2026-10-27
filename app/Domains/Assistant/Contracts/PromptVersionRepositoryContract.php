<?php

namespace App\Domains\Assistant\Contracts;

use App\Models\PromptVersion;
use App\Models\User;
use Illuminate\Support\Collection;

interface PromptVersionRepositoryContract
{
    /**
     * Lecture étroite exposée au `PromptBuilder` (section 10.1 — I) : le
     * contenu publié d'un bloc, ou `null` si aucune version n'a encore été
     * publiée (le bloc codé en dur fait alors foi).
     */
    public function publishedContentFor(string $blockKey): ?string;

    public function draft(string $blockKey, string $content, ?User $author): PromptVersion;

    public function publish(PromptVersion $version): PromptVersion;

    public function find(int $id): ?PromptVersion;

    /** @return Collection<int, PromptVersion> */
    public function historyFor(string $blockKey): Collection;
}
