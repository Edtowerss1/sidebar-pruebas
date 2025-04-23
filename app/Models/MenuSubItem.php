<?php

// app/Models/MenuSubItem.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuSubItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_item_id',
        'subitem_id',
        'label',
        'href',
        'operation',
        'order',
    ];

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }
}
