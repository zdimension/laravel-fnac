<?php

namespace App\Http\Controllers;



use App\Adresse;
use App\Magasin;
use App\PointRelais;
use App\User;
use Illuminate\Validation\Rule;

class AdherentController extends Controller
{
    public function listRelais()
    {
        if (!auth()->check() || !auth()->user()->est(User::ADHERENT)) abort(403);

        return view ("compte-relais-list", [
            'relais' => PointRelais::all(),
            'relais_adh' => auth()->user()->adherent->relais
        ]);
    }

    public function addRelais()
    {
        if (!auth()->check() || !auth()->user()->est(User::ADHERENT)) abort(403);

        $id = request()->get("id");

        if (empty($id)) abort(404);

        $id = intval($id);

        $rel = PointRelais::find($id);

        if ($rel === null) abort(404);

        if (!auth()->user()->adherent->relais->contains($rel))
        {
            auth()->user()->adherent->relais()->attach($rel);
        }

        session()->flash("info_rel", "Le point relais a bien été ajouté.");

        return redirect(route("compte.relais.list"));
    }

    public function deleteRelais($id)
    {
        if (!auth()->check() || !auth()->user()->est(User::ADHERENT)) abort(403);

        $rel = PointRelais::find($id);

        if ($rel === null) abort(404);

        if (auth()->user()->adherent->relais->contains($rel))
        {
            auth()->user()->adherent->relais()->detach($rel);
        }

        session()->flash("info_rel", "Le point relais a bien été supprimé.");

        return redirect(route("compte.relais.list"));
    }

    public function listAdr()
    {
        if (!auth()->check() || !auth()->user()->est(User::ADHERENT)) abort(403);

        return view ("compte-adr-list", [
            'adresses' => auth()->user()->adherent->adresses
        ]);
    }

    public function addAdr()
    {
        if (!auth()->check() || !auth()->user()->est(User::ADHERENT)) abort(403);

        $data = $this->validate(request(), [
            "adr_nom" => "required|string|max:50|unique:t_e_adresse_adr,adr_nom,NULL,id,adh_id," . auth()->user()->adherent->adh_id,
            "adr_cp" => "required|string|max:10",
            "adr_ville" => "required|string|max:100",
            "adr_rue" => "required|string|max:200",
            "adr_complementrue" => "nullable|string|max:200",
            "adr_latitude" => "required|numeric",
            "adr_longitude" => "required|numeric",
            "adr_type" => "required|in:Livraison,Facturation"
        ], [
            "adr_nom.unique" => "Une adresse portant ce nom existe déjà.",
            "adr_type.required" => "Veuillez sélectionner le type de l'adresse."
        ]);

        $adr = new Adresse;

        foreach($data as $k => $v)
        {
            $adr->$k = $v;
        }

        $adr->pay_id = 1;

        auth()->user()->adherent->adresses()->save($adr);

        session()->flash("info_adr", "L'adresse a bien été ajoutée.");

        return redirect(route("compte.adr.list"));
    }

    public function deleteAdr($id)
    {
        if (!auth()->check() || !auth()->user()->est(User::ADHERENT)) abort(403);

        $adr = Adresse::find($id);

        if ($adr === null) abort(404);

        if ($adr->adh_id == auth()->user()->adherent->adh_id)
        {
            $adr->delete();
        }

        session()->flash("info_adr", "L'adresse a bien été supprimée.");

        return redirect(route("compte.adr.list"));
    }

    public function voirProfil()
    {
        if (!auth()->check() || !auth()->user()->est(User::ADHERENT)) abort(403);

        return view("auth.register", ["mags" => Magasin::all(), "edit" => true]);
    }

    public function modifierProfil()
    {
        $data = $this->validate(request(), [
            'cop_mel' => 'required|string|email|max:80|unique:t_e_compte_cop,cop_mel,' . auth()->user()->cop_id . ",cop_id",
            'cop_motpasse' => 'nullable|string|min:8|confirmed',
            'adh_pseudo' => 'required|string|max:20',
            'adh_civilite' => 'required|string|in:M.,Mme,Mlle',
            'adh_nom' => 'required|string|max:50',
            'adh_prenom' => 'required|string|max:50',
            'adh_telfixe' => 'max:15|required_without_all:adh_telportable',
            'adh_telportable' => 'max:15|required_without_all:adh_telfixe',
            'mag_id' => 'nullable|exists:t_r_magasin_mag,mag_id',
            'password_old' => [
                'required',
                function ($_, $val, $fail) {
                    if (!password_verify($val, auth()->user()->cop_motpasse)) {
                        $fail("L'ancien mot de passe est incorrect.");
                    }
                }
            ]
        ], [
            "cop_motpasse.confirmed" => "Les mots de passe ne correspondent pas.",
            "cop_mel.unique" => "Un compte avec cette adresse e-mail existe déjà.",
            "cop_mel.email" => "L'adresse e-mail n'est pas valide.",
            "mag_id.exists" => "Le magasin sélectionné n'existe pas.",
            "adh_civilite.required" => "Vous devez sélectionnez une civilité."
        ]);

        if ($data["cop_motpasse"] === null)
            unset($data["cop_motpasse"]);
        else
            $data["cop_motpasse"] = bcrypt($data["cop_motpasse"]);

        auth()->user()->update($data);

        session()->flash("info_adh", "Les modifications ont bien été enregistrées.");

        return $this->voirProfil();
    }
}
