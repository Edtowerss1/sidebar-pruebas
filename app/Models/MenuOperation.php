<?php

// app/Models/MenuOperation.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuOperation extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_item_id',
        'operation',
        'enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }
}
