<?php

namespace App\Http\Controllers;

use App\Commande;
use App\LigneCommande;
use App\Livre;
use App\Auteur;
use App\LivreAuteur;
use App\Magasin;
use App\User;
use Illuminate\Http\Request;
use App\Genre;
use App\Photo;
use App\Format;


class PanierController extends Controller
{
    private function init_panier()
    {
        if (session()->get("panier") === null || !is_array(session()->get("panier")))
        {
            session()->put("panier", []);
        }
    }

    public function panier()
    {
        if (!auth()->check() || !auth()->user()->est(User::ADHERENT)) abort(403);

        $this->init_panier();

        $data = [
            "panier" => array_map(function ($l)
            {
                return [Livre::find($l[0]), $l[1]];
            }, session()->get("panier"))
        ];

        switch(session()->get("liv_type"))
        {
            case 0: // point relais
                $data["cibles"] = auth()->user()->adherent->relais;
                break;
            case 1: // adresse
                $data["cibles"] = auth()->user()->adherent->adresses;
                break;
            case 2: // magasin
                $data["cibles"] = Magasin::all();
                break;
        }

        return view("panier", $data);
    }

    public function add($liv_id)
    {
        if (!auth()->check() || !auth()->user()->est(User::ADHERENT)) abort(403);

        $this->init_panier();

        $livre = Livre::find($liv_id);

        if ($livre === null) abort(404);

        $p = session()->get("panier");

        $id = false;

        foreach ($p as $i => [$lid, $qty])
        {
            if ($lid == $liv_id)
            {
                $id = $i;
            }
        }

        if ($id === false)
            $p[] = [$liv_id, 1];
        else
            $p[$id][1]++;

        session()->put("panier", $p);

        return redirect(route("panier.view"));
    }

    public function delete($liv_id)
    {
        if (!auth()->check() || !auth()->user()->est(User::ADHERENT)) abort(403);

        $this->init_panier();

        $livre = Livre::find($liv_id);

        if ($livre === null) abort(404);

        session()->put("panier", array_filter(session()->get("panier"), function ($x) use ($liv_id)
        {
            return $x[0] != $liv_id;
        }));

        return redirect(route("panier.view"));
    }

    public function quantite($id)
    {
        if (!auth()->check() || !auth()->user()->est(User::ADHERENT)) abort(403);

        $this->init_panier();

        if ($id > count(session()->get("panier"))) abort(404);

        $data = $this->validate(request(), [
            "qty" => "required|integer|min:0"
        ], [
            "qty.required" => "Veuillez saisir la quantité demandée.",
            "qty.min" => "La quantité doit être positive.",
            "qty.integer" => "La valeur entrée n'est pas un nombre."
        ]);

        $qty = intval($data["qty"]);

        $p = session()->get("panier");

        if ($qty == 0)
        {
            array_splice($p, $id, 1);
        }
        else
        {
            $p[$id][1] = $qty;
        }

        session()->put("panier", $p);

        return redirect(route("panier.view"));
    }

    public function order()
    {
        $data = $this->validate(request(), ["liv_type" => "required|integer|between:0,2"]);

        if (!request()->has("order"))
        {
            session()->put("liv_type", $data["liv_type"]);
            return redirect(route("panier.view"));
        }

        $ltype = intval($data["liv_type"]);

        $table = ["t_e_relais_rel", "t_e_adresse_adr", "t_r_magasin_mag"][$ltype];
        $idc = ["rel_id", "adr_id", "mag_id"][$ltype];

        $data = $this->validate(request(), [
            "liv_cible" => "required|integer|exists:$table,$idc"
        ], [
            "liv_cible.required" => "Vous devez choisir un emplacement de livraison.",
            "liv_cible.exists" => "Emplacement de livraison incorrect.",
        ]);

        $cible = $data["liv_cible"];

        $comm = new Commande;

        $comm->com_date = date("Y-m-d");

        $comm->$idc = $cible;

        auth()->user()->adherent->commandes()->save($comm);

        foreach(session()->get("panier") as [$liv, $qty])
        {
            $ligne = new LigneCommande;
            $ligne->livre()->associate($liv);
            $ligne->lec_quantite = $qty;

            $comm->lignes()->save($ligne);
        }

        session()->flash("info_comm", "La commande a bien été passée.");

        session()->put("panier", []);
        session()->put("liv_type", null);

        return redirect(route("compte.comm.list"));
    }
}
