<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = ['quote', 'author', 'category_id'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
