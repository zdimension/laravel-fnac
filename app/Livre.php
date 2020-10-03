<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Livre extends Model
{
    protected $table = 't_e_livre_liv';
    protected $primaryKey = 'liv_id';
    public $timestamps = false;

    public function auteurs()
    {
        return $this->belongsToMany('App\Auteur', 't_j_auteurlivre_aul', "liv_id", "aut_id");
    }

    public function rubriques()
    {
        return $this->belongsToMany('App\Rubrique', 't_j_rubriquelivre_rul', "liv_id", "rub_id");
    }

    public function photos()
    {
        return $this->hasMany('App\Photo', 'liv_id');
    }

    public function genre()
    {
        return $this->hasOne('App\Genre', 'gen_id');
    }

    public function format()
    {
        return $this->hasOne('App\Format', 'for_id');
    }

    public function editeur()
    {
        return $this->hasOne('App\Editeur', 'edi_id');
    }

    public function avis()
    {
        return $this->hasMany('App\Avis', 'liv_id');
    }

    public function lignesApparait()
    {
        return $this->hasMany('App\LigneCommande', 'liv_id');
    }
}
