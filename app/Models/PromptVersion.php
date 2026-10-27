<?php

namespace App\Models;

use App\Domains\Assistant\Enums\PromptVersionStatus;
use Database\Factories\PromptVersionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $block_key
 * @property int $version
 * @property string $content
 * @property PromptVersionStatus $status
 * @property int|null $created_by
 * @property Carbon|null $published_at
 */
#[Fillable(['block_key', 'version', 'content', 'status', 'created_by', 'published_at'])]
class PromptVersion extends Model
{
    /** @use HasFactory<PromptVersionFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'status' => PromptVersionStatus::class,
            'published_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
