<?php

namespace App\Http\Controllers;


use App\Genre;
use App\User;

class GenreController extends Controller
{
    public function index()
    {
        if (!auth()->check() || !auth()->user()->est(User::RESPO_VENTE)) abort(403);

        return view ("genre-list", [
            'genres'=>Genre::all()
        ]);
    }

    public function add()
    {
        if (!auth()->check() || !auth()->user()->est(User::RESPO_VENTE)) abort(403);

        $data = $this->validate(request(), [
            "nom" => "required|string|max:50|unique:t_r_genre_gen,gen_libelle"
        ], [
            "nom.unique" => "Un genre du même nom existe déjà.",
            "nom.required" => "Veuillez saisir un nom de genre à ajouter."
        ]);

        $g = new Genre;
        $g->gen_libelle = $data["nom"];
        $g->save();

        return redirect(route("genres.list"));
    }
}
