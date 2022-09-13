<?php

namespace Z3d0X\FilamentFabricator\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'blocks',
        'layout',
        'parent_id',
    ];

    protected $casts = [
        'blocks' => 'array',
        'parent_id' => 'integer',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
