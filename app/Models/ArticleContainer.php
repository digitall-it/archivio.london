<?php

namespace App\Models;

use App\Events\ArticleContainerReportPrintRequestedEvent;
use App\Events\LabelPrintRequestedEvent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Tags\HasTags;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;

class ArticleContainer extends Model
{
    use HasRecursiveRelationships, HasTags;

    protected $fillable = ['name', 'parent_id'];

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'article_container_id');
    }

    public function printContent(bool $includeChildren = true): void
    {
        event(new ArticleContainerReportPrintRequestedEvent(
            includeChildren: $includeChildren,
            articleContainerId: $this->id
        ));
    }

    public function printLabels(array $modes): void
    {
        foreach ($modes as $mode) {
            event(new LabelPrintRequestedEvent([
                'type' => 'container',
                'mode' => $mode,
                'id' => $this->id,
            ]));
        }
    }
}
