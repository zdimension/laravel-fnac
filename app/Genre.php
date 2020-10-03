<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    protected $table = 't_r_genre_gen';
    protected $primaryKey = 'gen_id';
    public $timestamps = false;
}
