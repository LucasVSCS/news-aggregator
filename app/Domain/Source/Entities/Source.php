<?php

namespace App\Domain\Source\Entities;

use Database\Factories\SourceFactory;
use Illuminate\Database\Eloquent\Model;
use App\Domain\Article\Entities\Article;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Source extends Model
{
    use HasFactory;
    use Notifiable;

    protected static function newFactory(): SourceFactory
    {
        return SourceFactory::new();
    }

    protected $fillable = [
        'name',
        'slug',
        'url',
        'is_active'
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'is_active' => 'boolean'
        ];
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }
}
