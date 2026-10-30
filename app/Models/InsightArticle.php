<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class InsightArticle extends Model
{
    protected $fillable = ['category', 'title', 'slug', 'author', 'seo_title', 'seo_description', 'description', 'published_on', 'image', 'banner_image', 'url', 'sort_order', 'is_active', 'show_related'];

    protected $casts = ['published_on' => 'date', 'is_active' => 'boolean', 'show_related' => 'boolean'];

    protected static function booted(): void
    {
        // The slug is made once from the title and then kept, so links to the article never break.
        static::creating(function (self $article) {
            if (! $article->slug) {
                $article->slug = static::uniqueSlug($article->title);
            }
        });
    }

    public function sections(): HasMany
    {
        return $this->hasMany(InsightArticleContent::class)->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Renumber every article 1..n so the display order is always a clean sequence.
     * $focusId is the article just saved: on a tie it goes first (inserted at that position),
     * or last when $focusAfter is true (moved further down the list).
     */
    public static function resequence(?int $focusId = null, bool $focusAfter = false): void
    {
        $all = static::orderBy('sort_order')->orderByDesc('published_on')->orderByDesc('id')->get();

        $ranked = $all->sortBy(fn ($a) => [$a->sort_order, $a->id === $focusId ? ($focusAfter ? 1 : 0) : ($focusAfter ? 0 : 1)])->values();

        foreach ($ranked as $i => $article) {
            if ($article->sort_order !== $i + 1) {
                $article->forceFill(['sort_order' => $i + 1])->saveQuietly();
            }
        }
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    private static function uniqueSlug(string $title): string
    {
        $base = Str::slug($title) ?: 'article';
        $slug = $base;
        for ($n = 2; static::where('slug', $slug)->exists(); $n++) {
            $slug = "{$base}-{$n}";
        }

        return $slug;
    }
}
