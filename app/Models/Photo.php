<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Photo extends Model
{
    protected $fillable = ['album_id','image_path','caption','is_banner','banner_order'];

    public function album()
    {
        return $this->belongsTo(Album::class);
    }

    public function getImageUrlAttribute()
    {
        return Storage::url($this->image_path);
    }
}
