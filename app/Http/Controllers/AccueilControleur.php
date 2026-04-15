<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ouvrages;


class AccueilControleur extends Controller
{

    public function index()
    {
        return view('accueil');
    }

    function search(Request $request)
    {
        $recherche = [
            'titre' => 'Titre,text',
            'auteur' => 'Auteur,text',
        ];

        $hasInput = false;

        $query = ouvrages::query();

        foreach ($recherche as $field => $details) {
            [$nom, $type] = explode(',', $details);
            $value = trim($request->input($field, ''));
            if (!empty($value)) {
                $hasInput = true;
                switch ($type) {
                    case 'text':
                        $query->where($field, 'like', '%' . $value . '%');
                        break;
                    case 'number':
                        if ($field === 'min_pages') {
                            $query->where('pages', '>=', $value);
                        } elseif ($field === 'max_pages') {
                            $query->where('pages', '<=', $value);
                        }
                        break;
                }
            }
        }

        if (!$hasInput) {
            return redirect()->route('accueil')
                ->with('erreur', 'Veuillez remplir au moins un champs de recherche');
        }

        $ouvrages = $query->orderBy('titre', 'desc')->paginate(5);
        return view('accueil', compact('ouvrages'));
    }
}
