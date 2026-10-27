<?php

namespace App\Domains\Audit\Http\Resources;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin AuditLog
 *
 * `auditable_type` est réduit à son nom court (section 7, « sans donnée de
 * santé en clair ») : le chemin de classe complet n'apporte rien à la
 * lecture du journal et ne doit pas fuiter la structure interne du code.
 */
class AuditLogResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'action' => $this->action,
            'actor_id' => $this->actor_id,
            'actor_name' => $this->actor?->name,
            'auditable_type' => $this->auditable_type === null ? null : class_basename($this->auditable_type),
            'auditable_id' => $this->auditable_id,
            'context' => $this->context ?? [],
            'ip_address' => $this->ip_address,
            'created_at' => $this->created_at,
        ];
    }
}
