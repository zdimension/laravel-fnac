<?php

namespace App;

class LigneCommande extends ModelMultiplePrimary
{
    protected $table = 't_j_lignecommande_lec';
    protected $primaryKey = ["com_id", "liv_id"];
    public $timestamps = false;

    public function livre()
    {
        return $this->belongsTo('App\Livre', 'liv_id');
    }

    public function commande()
    {
        return $this->belongsTo('App\Commande', 'com_id');
    }

    protected $appends = ["montant"];

    public function getMontantAttribute()
    {
        return $this->livre->liv_prixttc * $this->lec_quantite;
    }
}
