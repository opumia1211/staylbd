<?php

namespace App\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    use Searchable;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'image',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'status'
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
