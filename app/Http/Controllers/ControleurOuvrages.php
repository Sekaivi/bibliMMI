<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ouvrages;
use Illuminate\Support\Str;

class ControleurOuvrages extends Controller
{
    public function index()
    {
        $ouvrages = ouvrages::orderBy('titre', 'desc')->paginate(5);
        return view('ouvrages.index', compact('ouvrages'));
    }

    public function create()
    {
        return view('ouvrages.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'titre' => 'required',
            'auteur' => 'required',
            'editeur' => 'required',
            'pages' => 'required',
            'date_publication' => 'required',
            'couverture' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096'
        ]);

        $ouvrage = ouvrages::create($request->post());

        if ($request->hasFile('couverture')) {
            $titre = Str::slug($ouvrage->titre, '_'); // "Mon Livre" -> "Mon_Livre"
            $nomDeFichier = $ouvrage->id . '_' . $titre . '.' . $request->couverture->extension();
            $request->couverture->move(public_path('images'), $nomDeFichier);
            $ouvrage->update(['couverture' => $nomDeFichier]);
        }
        return redirect()->route('ouvrages.index')->with('success', 'Ouvrage ajouté');
    }

    public function show(ouvrages $ouvrage)
    {
        return view('ouvrages.show', compact('ouvrage'));
    }

    public function edit(ouvrages $ouvrage)
    {
        return view('ouvrages.edit', compact('ouvrage'));
    }

    public function update(Request $request, ouvrages $ouvrage)
    {
        $request->validate([
            'titre' => 'required',
            'auteur' => 'required',
            'editeur' => 'required',
            'pages' => 'required',
            'date_publication' => 'required',
            'couverture' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096'
        ]);


        $ouvrage->fill($request->post())->save();

        if ($request->hasFile('couverture')) {
            $titre = Str::slug($ouvrage->titre, '_'); // "Mon Livre" -> "Mon_Livre"
            $nomDeFichier = $ouvrage->id . '_' . $titre . '.' . $request->couverture->extension();
            $request->couverture->move(public_path('images'), $nomDeFichier);
            $ouvrage->update(['couverture' => $nomDeFichier]);
        }

        return redirect()->route('ouvrages.index')->with('success', 'Ouvrage sauvegardé');
    }

    public function delete_image(ouvrages $ouvrage)
    {
        $nomDeFichier = public_path('images/' . $ouvrage->couverture);

        if (file_exists($nomDeFichier)) {
            unlink($nomDeFichier); // deletes the file from the server
        }

        $ouvrage->update(['couverture' => '']);
        echo 'deleting image only' ;
        return redirect()->route('ouvrages.edit', $ouvrage->id)->with('success', 'Couverture supprimée');
    }

    public function destroy(ouvrages $ouvrage)
    {
        $nomDeFichier = public_path('images/' . $ouvrage->couverture);

        if (file_exists($nomDeFichier)) {
            unlink($nomDeFichier); // deletes the file from the server
        }
        $ouvrage->delete();
        echo 'deleting THE ROWWW' ;
        return redirect()->route('ouvrages.index')->with('success', 'Ouvrage supprimé');
    }
}
