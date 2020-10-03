<?php


namespace App\Http\Controllers;


use App\Livre;
use App\Photo;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    public function voir($id)
    {
        $photo = Photo::find($id);

        if ($photo == null) return null;

        $url = $photo->pho_url;

        if (strpos($url, ".") === false)
            $url .= ".jpg";

        return Storage::get("photos/" . $url, "public");
    }
}
