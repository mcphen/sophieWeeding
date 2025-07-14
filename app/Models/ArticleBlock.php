<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleBlock extends Model
{
    protected $fillable = [
        'article_id',
        'type',
        'content',
        'position',
    ];

    protected $casts = [
        'content' => 'array',
    ];

    /**
     * Get the article that owns the block.
     */
    public function article()
    {
        return $this->belongsTo(Actualite::class, 'article_id');
    }
}
