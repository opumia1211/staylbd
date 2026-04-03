<?php

namespace App\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;

class SearchLog extends Model
{
    use Searchable;

    protected $table = 'user_search_logs';

    public const UPDATED_AT = null;

    protected $fillable = [
        'query', 'user_id', 'ip', 'user_agent', 'results_count', 'source', 'image_path',
    ];

    protected $casts = [
        'results_count' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
