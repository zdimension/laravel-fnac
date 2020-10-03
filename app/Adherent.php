<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Adherent extends Model
{
    protected $table = 't_e_adherent_adh';
    protected $primaryKey ='adh_id';
    public $timestamps =false;

    public function signalements()
    {
        return $this->hasMany('App\AvisAbusif', 'adh_id');
    }

    public function compte()
    {
        return $this->belongsTo('App\User', 'cop_id');
    }

    public function relais()
    {
        return $this->belongsToMany('App\PointRelais', 't_j_relaisadherent_rea', "adh_id", "rel_id");
    }

    public function magasin()
    {
        return $this->belongsTo('App\Magasin', 'mag_id');
    }

    public function adresses()
    {
        return $this->hasMany('App\Adresse', 'adh_id');
    }

    public function commandes()
    {
        return $this->hasMany('App\Commande', 'adh_id');
    }

    public static function boot()
    {
        parent::boot();

        static::deleting(function($adh) {
            $adh->signalements()->delete();
        });
    }

    protected $appends = ["livresCommandes"];

    public function getLivresCommandesAttribute()
    {
        return $this->hasManyThrough(
            'App\LigneCommande',
            'App\Commande', 'adh_id', 'com_id')->get()->map(function(LigneCommande $l) { return $l->livre; });
    }
}
