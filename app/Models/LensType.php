<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LensType extends Model
{
    protected $fillable = ['name', 'slug', 'additional_price', 'description'];
}
