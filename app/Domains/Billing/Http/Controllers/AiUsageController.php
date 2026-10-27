<?php

namespace App\Domains\Billing\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AiUsageLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Suivi de la consommation de l'IA (section 5.12) : un utilisateur consulte
 * sa propre consommation ; un administrateur consulte l'agrégat par
 * utilisateur pour piloter les coûts.
 */
class AiUsageController extends Controller
{
    public function mine(Request $request): JsonResponse
    {
        $totals = AiUsageLog::query()
            ->where('user_id', $request->user()->id)
            ->selectRaw('COUNT(*) as calls, SUM(input_tokens) as input_tokens, SUM(output_tokens) as output_tokens')
            ->first();

        return response()->json([
            'data' => [
                'calls' => (int) $totals->calls,
                'input_tokens' => (int) $totals->input_tokens,
                'output_tokens' => (int) $totals->output_tokens,
            ],
        ]);
    }

    public function summary(): JsonResponse
    {
        $rows = AiUsageLog::query()
            ->select('user_id')
            ->selectRaw('COUNT(*) as calls, SUM(input_tokens) as input_tokens, SUM(output_tokens) as output_tokens')
            ->groupBy('user_id')
            ->orderByDesc(DB::raw('SUM(output_tokens)'))
            ->limit(100)
            ->get();

        return response()->json([
            'data' => $rows->map(fn ($row) => [
                'user_id' => $row->user_id,
                'calls' => (int) $row->calls,
                'input_tokens' => (int) $row->input_tokens,
                'output_tokens' => (int) $row->output_tokens,
            ]),
        ]);
    }
}
