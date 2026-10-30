<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InsightArticleContent extends Model
{
    protected $table = 'insight_article_contents';

    protected $fillable = ['insight_article_id', 'title', 'body', 'read_minutes', 'sort_order'];

    /** Reading time for this entry: the value set in the admin, or an estimate from its words (about 200 a minute, at least 1). */
    public function minutes(): int
    {
        return $this->read_minutes ?: max(1, (int) ceil(str_word_count($this->title . ' ' . $this->body) / 200));
    }
}
