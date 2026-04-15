<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\usagers;

class ControleurUsager extends Controller
{
    public function index()
    {
        $usagers = usagers::orderBy('nom', 'desc')->paginate(5);
        return view('usagers.index', compact('usagers')); // compact permis de definir le tableau associatif qu'on va recevoir comme ayant pour nom "usagers"
    }

    public function create()
    {
        return view('usagers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required',
            'prenom' => 'required',
            'email' => 'required',
            'identifiant' => 'required',
            'passe' => 'required',
            'blocage' => 'required'
        ]);

        $data = $request->all();
        $data['passe'] = Hash::make($request->passe);

        usagers::create($data);

        return redirect()->route('usagers.index')->with('success', 'Compte usager créé');
    }

    public function show(usagers $usager)
    {
        return view('usagers.show', compact('usager'));
    }

    public function edit(usagers $usager)
    {
        return view('usagers.edit', compact('usager'));
    }

    public function update(Request $request, usagers $usager)
    {
        $request->validate([
            'nom' => 'required',
            'prenom' => 'required',
            'email' => 'required',
            'identifiant' => 'required',
            'passe' => 'required',
            'blocage' => 'required'
        ]);

        $data = $request->all();
        $data['passe'] = Hash::make($request->passe);

        $usager->fill($data)->save();

        return redirect()->route('usagers.index')->with('success', 'Compte usager sauvegardé');
    }

    public function destroy(usagers $usager)
    {
        $usager->delete();
        return redirect()->route('usagers.index')->with('success', 'Compte usager supprimé');
    }
}
