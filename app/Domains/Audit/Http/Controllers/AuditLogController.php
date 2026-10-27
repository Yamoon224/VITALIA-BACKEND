<?php

namespace App\Domains\Audit\Http\Controllers;

use App\Domains\Audit\Http\Resources\AuditLogResource;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Journal d'audit exhaustif et inviolable (section 5.13, 8) : lecture seule,
 * aucune route n'expose de mise à jour ni de suppression — le journal reste
 * append-only (voir `App\Models\AuditLog`). Réservé aux administrateurs.
 */
class AuditLogController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $entries = AuditLog::query()
            ->with('actor')
            ->when($request->filled('action'), fn ($query) => $query->where('action', $request->string('action')->toString()))
            ->when($request->filled('actor_id'), fn ($query) => $query->where('actor_id', $request->integer('actor_id')))
            ->when($request->filled('from'), fn ($query) => $query->where('created_at', '>=', $request->date('from')))
            ->when($request->filled('to'), fn ($query) => $query->where('created_at', '<=', $request->date('to')))
            ->latest('created_at')
            ->limit(100)
            ->get();

        return response()->json([
            'data' => AuditLogResource::collection($entries),
        ]);
    }
}
