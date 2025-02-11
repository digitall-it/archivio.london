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
        // Qui puoi aggiungere la logica per stampare l'etichetta
        //\Log::info("Stampa etichetta per l'articolo: " . $this->id);
        // raise(new LabelPrintRequestedEvent with data ['type' => 'article', 'id' => $this->id, 'mode' => 'normal']);

        event(new LabelPrintRequestedEvent([
            'type' => 'article',
            'id' => $this->id,
        ]));
    }
}
