<?php

namespace App\Http\Controllers;


use App\Commande;
use App\User;

class CommandeController extends Controller
{
    public function listOrder()
    {
        if (!auth()->check() || !auth()->user()->est(User::RESPO_ADH)) abort(403);

        return view("commande-list", ["comms" => Commande::query()->orderByDesc("com_date")->get()]);
    }

    public function listOrderUser()
    {
        if (!auth()->check() || !auth()->user()->est(User::ADHERENT)) abort(403);

        return view("commande-list", ["comms" => auth()->user()->adherent->commandes]);
    }
}
