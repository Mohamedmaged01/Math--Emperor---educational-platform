<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** one row of an imperial-workspace dataset (students, questions, orders, …) */
class AdminRecord extends Model
{
    protected $fillable = ['dataset', 'key', 'data', 'position'];

    protected function casts(): array
    {
        return ['data' => 'array'];
    }

    /** the row as the UI sees it: its data with the public id merged in */
    public function toRow(): array
    {
        return ['id' => $this->key] + ($this->data ?? []);
    }
}
