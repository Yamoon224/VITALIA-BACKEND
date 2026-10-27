<?php

namespace App\Domains\Billing\Services;

use App\Domains\Assistant\DTOs\LlmReply;
use App\Domains\Billing\Contracts\AiUsageRecorderContract;
use App\Models\AiUsageLog;
use App\Models\User;

class AiUsageRecorder implements AiUsageRecorderContract
{
    public function record(User $user, LlmReply $reply): void
    {
        AiUsageLog::query()->create([
            'user_id' => $user->id,
            'model' => $reply->model,
            'input_tokens' => $reply->inputTokens,
            'output_tokens' => $reply->outputTokens,
            'created_at' => now(),
        ]);
    }
}
