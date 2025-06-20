<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    protected $fillable = ['title','event_date','theme'];

    public function photos()
    {
        return $this->hasMany(Photo::class);
    }
}
