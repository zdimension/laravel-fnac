<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class LivreAuteur extends Pivot
{
    protected $table = "t_j_auteurlivre_aul";
    protected $primaryKey = "aut_id";
    public $timestamps = false;

    /*public function livre()
    {
        return $this->belongsTo('App\Livre', "liv_id");
    }

    public function auteur()
    {
        return $this->belongsTo('App\Auteur', "aut_id");
    }*/
}
