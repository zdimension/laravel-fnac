<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Auteur extends Model
{
    protected $table = "t_e_auteur_aut";
    protected $primaryKey = "aut_id";
    public $timestamps = false;

    public function livres()
    {
        return $this->belongsToMany('App\Livre', 't_j_auteurlivre_aul', "aut_id", "liv_id");
    }
}
