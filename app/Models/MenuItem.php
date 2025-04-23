<?php

// app/Models/MenuItem.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'label',
        'icon',
        'href',
        'enabled',
        'order',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    public function operations(): HasMany
    {
        return $this->hasMany(MenuOperation::class);
    }

    public function subItems(): HasMany
    {
        return $this->hasMany(MenuSubItem::class);
    }
}
