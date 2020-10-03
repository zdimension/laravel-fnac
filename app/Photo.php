<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Photo extends Model
{
    protected $table = 't_e_photo_pho';
    protected $primaryKey ='pho_id';
    public $timestamps =false;

    public function livre()
    {
        return $this->belongsTo('App\Livre');
    }

    public function url()
    {
        $url = $this->pho_url;

        if (strpos($url, ".") === false)
            $url .= ".jpg";

        return asset("storage/photos/" . $url);
    }
}
