<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
//use Illuminate\Support\Facades\Auth;
use App\Adherent;

class User extends Authenticatable
{
    const ADMIN = 0;
    const RESPO_COMM = 1;
    const RESPO_VENTE = 2;
    const RESPO_ADH = 3;
    const ADHERENT = 4;

    use Notifiable;

    protected $table = "t_e_compte_cop";
    public $timestamps = false;
    protected $primaryKey = "cop_id";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cop_typecompte', 'cop_mel', 'cop_motpasse', 'adh_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'cop_motpasse', 'remember_token',
    ];

    //si le type de compte est de type 4, on fait un tableau qui contient tous les elements de l'adherent correspondant
    public function adherent()
    {
        return $this->hasOne('App\Adherent', 'cop_id');
    }

    public function est($type)
    {
        return $this->cop_typecompte == $type || ($type != self::ADHERENT && $this->cop_typecompte == self::ADMIN);
    }

    public function nomAffichage()
    {
        if ($this->cop_typecompte == self::ADHERENT)
            return $this->adherent->adh_prenom . " " . $this->adherent->adh_nom;
        else
            return $this->cop_mel;
    }

    public function roleAffichage()
    {
        return [
            "Administrateur",
            "Resp. communication",
            "Resp. ventes",
            "Resp. adhérents",
            "Adhérent(e)"
        ][$this->cop_typecompte];
    }

    //ICI-----------------------------------------------------------------------------------
    //jeter un coup d'oeil dans adherent.php aussi ça pourrait être cool

    public function getAuthPassword()
    {
        return $this->cop_motpasse;
    }

    public function save(array $options = array())
    {
        if (isset($this->remember_token))
            unset($this->remember_token);
        return parent::save($options);
    }

    public static function create(array $data)
    {
        $user = new User;
        $adh = new Adherent;

        $adh->adh_numadherent = random_int(1, 9999999999);

        $user->cop_typecompte = self::ADHERENT;

        $user->update($data, [], $adh);

        return $user;
    }

    public function update(array $attributes = [], array $options = [], $adh = null)
    {
        if ($adh === null)
            $adh = $this->adherent;

        foreach ($attributes as $k => $v)
        {
            if ($this->est(self::ADHERENT) && starts_with($k, ["adh_", "mag_"]))
                $adh->$k = $v;
            else if (starts_with($k, "cop_"))
                $this->$k = $v;
        }

        $this->save();

        if ($this->est(self::ADHERENT))
        {
            $adh->adh_mel = $this->cop_mel;
            $adh->adh_motpasse = $this->cop_motpasse;
            $this->adherent()->save($adh);
        }
    }

}
