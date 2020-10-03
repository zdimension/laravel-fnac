<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Adresse extends Model
{
    protected $table = 't_e_adresse_adr';
    protected $primaryKey = 'adr_id';
    public $timestamps = false;

    public function affichage($sep="\n")
    {
        return implode($sep, array_filter([
            $this->adr_rue,
            $this->adr_complementrue,
            $this->adr_cp . " " . $this->adr_ville
        ]));
    }
}
