<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    protected $table = 't_e_commande_com';
    protected $primaryKey = 'com_id';
    public $timestamps = false;

    public function auteur()
    {
        return $this->belongsTo('App\Adherent', "adh_id");
    }

    public function relais()
    {
        return $this->belongsTo('App\PointRelais', "rel_id");
    }

    public function adresse()
    {
        return $this->belongsTo('App\Adresse', "adr_id");
    }

    public function magasin()
    {
        return $this->belongsTo('App\Magasin', "mag_id");
    }

    public function lignes()
    {
        return $this->hasMany('App\LigneCommande', 'com_id');
    }

    protected $appends = ["montant", "typeLivraison"];

    public function getMontantAttribute()
    {
        return $this->lignes->sum("montant");
    }

    public function getTypeLivraisonAttribute()
    {
        if ($this->rel_id !== null)
            return 0;
        if ($this->adr_id !== null)
            return 1;
        if ($this->mag_id !== null)
            return 2;
    }
}
