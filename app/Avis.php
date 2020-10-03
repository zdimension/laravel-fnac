<?php


namespace App;
use Illuminate\Database\Eloquent\Model;

class Avis extends Model
{
    protected $table = 't_e_avis_avi';
    protected $primaryKey = 'avi_id';
    public $timestamps = false;

    public function auteur()
    {
        return $this->belongsTo('App\Adherent', "adh_id");
    }

    public function livre()
    {
        return $this->belongsTo('App\Livre', "liv_id");
    }

    protected $appends = ["score"];

    public function getScoreAttribute()
    {
        // borne inférieure de l'intervalle de confiance de Wilson pour un paramètre de Bernoulli
        // en gros ça donne un score relatif de l'avis
        // score élevé = avis utile, score bas = avis peu utile
        // c'est plus fiable que de juste faire OUI - NON ou OUI / (OUI + NON)
        $pos = $this->avi_nbutileoui;
        $neg = $this->avi_nbutilenon;
        $tot = $pos + $neg;
        if ($tot == 0) return 0;
        return (($pos + 1.9208) / $tot -
                1.96 * sqrt(($pos * $neg) / $tot + 0.9604) /
                $tot) / (1 + 3.8416 / $tot);
    }

    public function signalements()
    {
        return $this->hasMany('App\AvisAbusif', 'avi_id');
    }

    public static function boot()
    {
        parent::boot();

        static::deleting(function($avis) {
            $avis->signalements()->delete();
        });
    }
}
