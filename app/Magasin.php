<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Magasin extends Model
{
    protected $table = 't_r_magasin_mag';
    protected $primaryKey = 'mag_id';
    public $timestamps = false;

    public function adherents()
    {
        return $this->hasMany('App\Adherent', 'mag_id');
    }
}
