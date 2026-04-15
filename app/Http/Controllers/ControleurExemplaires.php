<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\exemplaires;
use App\Models\ouvrages; // pour faire des queries sur le parent
use App\Models\usagers; // pour pouvoir checker les usagers de la bibliotheques

class ControleurExemplaires extends Controller
{
    public function index($ouvrage_id)
    {
        $exemplaires = exemplaires::where('ouvrage_id', $ouvrage_id)->orderBy('disponible', 'desc')->paginate(5);
        $ouvrage = ouvrages::find($ouvrage_id);
        return view('exemplaires.index', compact('exemplaires', 'ouvrage'));
    }

    public function create($ouvrage_id)
    {
        $ouvrage = ouvrages::find($ouvrage_id);
        $usagers = Usagers::select('id', 'nom', 'prenom')->orderBy('nom', 'desc')->get();
        return view('exemplaires.create', compact('ouvrage' , 'usagers'));
    }

    public function store(Request $request, $ouvrage_id)
    {
        $request->validate([
            'etat' => 'nullable|in:neuf,à réparer,à remplacer,retiré',
            'disponible' => 'nullable|boolean',
            'emprunteur_id' => 'nullable|exists:usagers,id',
            'date_retour_souhaitee' => 'nullable|date',
            'reserve' => 'nullable|boolean',
            'renouvellement' => 'nullable|boolean'
        ]);

        $request->merge(['ouvrage_id' => $ouvrage_id]);
        exemplaires::create($request->post());

        return redirect()->route('ouvrages.exemplaires.index', $ouvrage_id)->with('success', 'Exemplaire ajouté');
    }

    public function show(exemplaires $exemplaire, $ouvrage_id)
    {
        return view('exemplaires.show', compact('exemplaire'));
    }

    public function edit($ouvrage_id , exemplaires $exemplaire)
    {
        $ouvrage = ouvrages::find($ouvrage_id);
        $usagers = Usagers::select('id', 'nom', 'prenom')->orderBy('nom', 'desc')->get();
        return view('exemplaires.edit', compact('exemplaire','ouvrage' , 'usagers'));
    }

    public function update(Request $request, $ouvrage_id , exemplaires $exemplaire)
    {
        $request->validate([
            'etat' => 'required|in:neuf,à réparer,à remplacer,retiré',
            'disponible' => 'nullable|boolean',
            'emprunteur_id' => 'nullable|exists:usagers,id',
            'date_retour_souhaitee' => 'nullable|date',
            'reserve' => 'nullable|boolean',
            'renouvellement' => 'nullable|boolean'
        ]);

        $exemplaire->fill($request->post())->save();

        return redirect()->route('ouvrages.exemplaires.index', $ouvrage_id)->with('success', 'Exemplaire sauvegardé');
    }

    public function destroy($ouvrage_id , exemplaires $exemplaire )
    {
        $exemplaire->delete();
        return redirect()->route('ouvrages.exemplaires.index', $ouvrage_id)->with('success', 'Exemplaire supprimé');
    }
}
