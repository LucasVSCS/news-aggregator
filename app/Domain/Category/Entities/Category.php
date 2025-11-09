<?php

namespace App\Domain\Category\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Article\Entities\Article;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(
            Article::class,
            'article_category',
            'category_id',
            'article_id'
        );
    }
}
