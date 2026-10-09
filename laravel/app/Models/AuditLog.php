<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = ['uid', 'actor', 'action', 'module', 'entity', 'detail', 'result', 'at'];

    public function toRow(): array
    {
        return [
            'id' => $this->uid,
            'actor' => $this->actor,
            'action' => $this->action,
            'module' => $this->module,
            'entity' => $this->entity,
            'detail' => $this->detail,
            'result' => $this->result,
            'at' => $this->at,
        ];
    }
}
