<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Req extends Model
{
    protected $table = 'requests';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
