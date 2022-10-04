<?php

namespace Z3d0X\FilamentFabricator\Models;

use Spatie\Translatable\HasTranslations;

class TranslatablePage extends Page
{
    use HasTranslations;

    protected $table = 'pages';
    public $translatable = ['title', 'blocks'];

}
