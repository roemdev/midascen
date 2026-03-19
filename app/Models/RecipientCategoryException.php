<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecipientCategoryException extends Model
{
    protected $fillable = [
        'recipient_id',
        'category_id',
    ];

    public function recipient()
    {
        return $this->belongsTo(Recipient::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}