<?php


namespace App\Http\Controllers;


use App\Avis;
use App\User;
use App\Livre;
use App\AvisAbusif;

class AvisController extends Controller
{
    public function vote($id)
    {
        if (!auth()->check() || !auth()->user()->est(User::ADHERENT)) abort(403);

        if (in_array($id, session()->get("votes"))) abort(403);

        $avis = Avis::find($id);

        if ($avis === null) abort(404);

        if ($avis->auteur == auth()->user()->adherent) abort(403);

        if (request()->has("oui"))
            $avis->avi_nbutileoui++;

        else if (request()->has("non"))
            $avis->avi_nbutilenon++;

        $avis->save();

        session()->push("votes", $avis->avi_id);

        return redirect(route("livres.detail", ["id" => $avis->liv_id]) . "#comm" . $id);
    }

    public function abusif(int $id)
    {
        if (!auth()->check() || !auth()->user()->est(User::ADHERENT)) abort(403);

        $avis = Avis::find($id);

        if ($avis == null) abort(404);

        $adh = auth()->user()->adherent->adh_id;

        $abusif = $avis->signalements->firstWhere("adh_id", $adh);

        if ($abusif === null)
        {
            $abusif = new AvisAbusif;
            $abusif->adh_id = $adh;
            $avis->signalements()->save($abusif);
        }
        else
        {
            $abusif->delete();
        }

        session()->flash("info_sign", "Merci pour votre signalement. Le service communication passera ce commentaire en revue dans les plus brefs délais.");

        return redirect(route("livres.detail", ["id" => $avis->liv_id]) . "#comm" . $id);
    }

    public function delete($id)
    {
        if (!auth()->check() || (!auth()->user()->est(User::RESPO_COMM) && !auth()->user()->est(User::ADHERENT))) abort(403);

        $avis = Avis::find($id);

        if ($avis === null) abort(404);

        if (auth()->user()->est(User::ADHERENT) && $avis->auteur != auth()->user()->adherent) abort(403);

        $avis->delete();

        if (request()->has("redirect"))
            return redirect(request()->get("redirect"));

        return redirect(route("livres.detail", ["id" => $avis->liv_id]) . "#commentaires");
    }

    public function add($liv_id)
    {
        if (!auth()->check() || !auth()->user()->est(User::ADHERENT)) abort(403);

        $livre = Livre::find($liv_id);

        if ($livre === null) abort(404);

        if (!auth()->user()->adherent->livresCommandes->contains($livre)) abort(403);

        $data = $this->validate(request(), [
            "avi_titre" => "required|string|max:100",
            "avi_detail" => "required|string|max:2000",
            "avi_note" => "required|integer|between:1,5"
        ]);

        $avis = new Avis;

        foreach($data as $k => $v)
            $avis->$k = $v;

        $avis->avi_nbutileoui = 0;
        $avis->avi_nbutilenon = 0;

        $avis->auteur()->associate(auth()->user()->adherent);
        $avis->livre()->associate($livre);
        $avis->save();

        return redirect(route("livres.detail", ["id" => $avis->liv_id]) . "#comm" . $avis->avi_id);
    }

    public function voirAbusifs()
    {
        if (!auth()->check() || !auth()->user()->est(User::RESPO_COMM)) abort(403);

        return view("avis-abusifs", ["comms" => Avis::has("signalements")->get()]);
    }
}
