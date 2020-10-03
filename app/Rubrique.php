<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rubrique extends Model
{
    protected $table = 't_r_rubrique_rub';
    protected $primaryKey = 'rub_id';
    public $timestamps = false;

    public function livres()
    {
        return $this->belongsToMany('App\Livre', 't_j_rubriquelivre_rul', "rub_id", "liv_id");
    }
}
