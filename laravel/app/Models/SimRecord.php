<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SimRecord extends Model
{
    protected $fillable = ['label', 'score', 'correct', 'total', 'taken_on'];

    protected function casts(): array
    {
        return ['taken_on' => 'date'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
