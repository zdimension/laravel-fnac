<?php

namespace App\Http\Controllers;

use App\Auteur;
use App\Avis;
use App\Livre;
use App\Genre;
use App\Photo;
use App\Format;
use App\Rubrique;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class LivreController extends Controller
{
    public function index() {
        $livres = Livre::query();

        if (!empty(request("genre"))) {
            $livres->where("gen_id",'=', request("genre"));
        }

        if (!empty(request("format"))) {
            $livres->where("for_id",'=', request("format"));
        }

        $livres = $livres->get();

        if (!empty(request("rubrique"))) {
            $livres = $livres->filter(function(Livre $livre) {
                return $livre->rubriques->contains(function(Rubrique $rub) {
                    return $rub->rub_id == request("rubrique");
                });
            });
        }

        if (!empty(request("auteur"))) {
            $livres = $livres->filter(function(Livre $livre) {
                return $livre->auteurs->contains(function(Auteur $auteur) {
                    return levenshtein($auteur->aut_nom, request("auteur")) < 4; // levenshtein = "distance" entre 2 chaînes. 0 = chaînes identiques
                });
            });
        }

    	return view ("livre-list", [
    	    'livres'=>$livres,
            'genres'=>Genre::all(),
            'formats'=>Format::all(),
            'rubriques'=>Rubrique::all()
            ]);
    }

    public function detail($id)
    {
        /*
        trouver les coordonnees du livre actuel
        */

        return view("livre-detail", ["livre" => Livre::find($id)]);
    }

    public function addPhotos($redirect=null)
    {
        if (!auth()->check() || !auth()->user()->est(User::RESPO_VENTE)) abort(403);

        foreach(request()->all() as $n => $val)
        {
            if (empty($val)) continue;

            $n = intval($n);
            // todo gérer non numérique
            $livre = Livre::find($n);

            if ($livre == null) continue;

            if (!in_array($val->getClientOriginalExtension(), ["jpg","jpeg","gif","png"])) continue;

            $val->store("photos", "public");
            $photo = new Photo;
            $photo->pho_url = $val->hashName();
            $livre->photos()->save($photo);
        }

        if ($redirect !== null)
        {
            session()->flash("info", "La photo a bien été ajoutée.");
            return redirect(route("livres.detail", ["id" => $redirect]));
        }

        session()->flash("info", "Les photos ont bien été ajoutées.");

        return redirect(route("livres.list"));
    }
}
