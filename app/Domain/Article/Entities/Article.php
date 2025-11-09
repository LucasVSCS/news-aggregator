<?php

namespace App\Domain\Article\Entities;

use App\Domain\Source\Entities\Source;
use Illuminate\Database\Eloquent\Model;
use App\Domain\Category\Entities\Category;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'external_id',
        'source_id',
        'title',
        'description',
        'content',
        'url',
        'image_url',
        'author',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            Category::class,
            'article_category',
            'article_id',
            'category_id'
        );
    }
}
