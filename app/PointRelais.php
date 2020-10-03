<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PointRelais extends Model
{
    protected $table = 't_e_relais_rel';
    protected $primaryKey = 'rel_id';
    public $timestamps = false;

    public function adherents()
    {
        return $this->belongsToMany('App\Adherent', 't_j_relaisadherent_rea', "rel_id", "adh_id");
    }

    public function pays()
    {
        return $this->belongsTo('App\Pays', 'pay_id');
    }

    public function affichageAdresse($sep="\n")
    {
        return implode($sep, array_filter([
            $this->rel_rue,
            $this->rel_cp . " " . $this->rel_ville
        ]));
    }
}
