<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Source extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'source_key',
        'video_id',
        'movie_id',
        'channel_id',
        'prioritize',
        'status',
        'user_id',
    ];

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function video()
    {
        return $this->belongsTo(Video::class);
    }
}
