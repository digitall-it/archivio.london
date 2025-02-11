<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Tags\HasTags;

class ArticleContainer extends Model
{
    use HasTags;
    protected $fillable = ['name'];
}
