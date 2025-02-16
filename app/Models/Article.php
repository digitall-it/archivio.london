<?php

namespace App\Models;

use App\Events\LabelPrintRequestedEvent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Tags\HasTags;

class Article extends Model
{
    use HasTags;

    protected $fillable = [
        'name',
        'quantity',
        'article_container_id',
    ];

    public function articleContainer(): BelongsTo
    {
        return $this->belongsTo(ArticleContainer::class);
    }

    public function printLabel(): void
    {
        event(new LabelPrintRequestedEvent([
            'type' => 'article',
            'id' => $this->id,
        ]));
    }
}
