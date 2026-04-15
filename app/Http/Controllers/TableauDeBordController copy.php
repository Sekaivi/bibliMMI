<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\ouvrages;
use App\Models\usagers;

class TableauDeBordController extends Controller
{

    protected $recherche_ouvrage = [
        'titre' => 'Titre,text',
        'serie' => 'Série,text',
        'auteur' => 'Auteur,text',
        'editeur' => 'Editeur,text',
        'min_pages' => 'Min Pages,number',
        'max_pages' => 'Max Pages,number',
        'avant' => 'Avant,date',
        'apres' => 'Après,date'
    ];

    protected $recherche_usagers = [
        'nom' => 'Nom,text',
        'prenom' => 'Prénom,text',
        'email' => 'E-mail,email'
    ];

    public function index()
    {

        $recherche_ouvrages = Session::get('recherche_ouvrages', array_fill_keys(array_keys($this->recherche_ouvrage), ''));
        $recherche_usagers  = Session::get('recherche_usagers', array_fill_keys(array_keys($this->recherche_usagers), ''));
        $recherche_ouvrages_results = Session::get('recherche_ouvrages_results', collect());
        $recherche_usagers_results  = Session::get('recherche_usagers_results', collect());

        return view('tableaudebord', [
            'ouvrages' => $recherche_ouvrages_results,
            'usagers'  => $recherche_usagers_results,
            'recherche_usagers' => $recherche_usagers,
            'recherche_ouvrages' => $recherche_ouvrages,
        ]);
    }

    public function search(Request $request)
    {
        $ouvrages = $this->queryOuvrages($request);
        $usagers  = $this->queryUsagers($request);

        $nothingSearched = $request->all() === [];
        if ($nothingSearched) {
            Session::put('recherche_ouvrages', []);
            Session::put('recherche_usagers', []);
            return redirect()->route('tableaudebord')
                ->with('erreur', 'Veuillez remplir au moins un champs de recherche');
        }

        $this->saveSearchSession($this->recherche_ouvrage, $request, 'recherche_ouvrages');
        $this->saveSearchSession($this->recherche_usagers, $request, 'recherche_usagers');
        Session::put('recherche_ouvrages_results', $ouvrages);
        Session::put('recherche_usagers_results', $usagers);

        return view('tableaudebord', [
            'ouvrages' => $ouvrages,
            'usagers'  => $usagers,
            'recherche_ouvrages' => Session::get('recherche_ouvrages'),
            'recherche_usagers' => Session::get('recherche_usagers'),
        ]);
    }

    private function queryOuvrages(Request $request)
    {
        $recherche_ouvrage = $this->recherche_ouvrage;

        $query = ouvrages::query();
        $hasInput = false;

        foreach ($recherche_ouvrage as $field => $details) {
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
                    case 'date':
                        if ($field === 'avant') {
                            $query->where('date_publication', '<=', $value);
                        } elseif ($field === 'apres') {
                            $query->where('date_publication', '>=', $value);
                        }
                        break;
                }
            }
        }
        return $hasInput ? $query->orderBy('titre', 'desc')->get() : collect();
    }

    private function queryUsagers(Request $request)
    {
        $recherche_usagers = $this->recherche_usagers;

        $query = usagers::query();
        $hasInput = false;

        foreach ($recherche_usagers as $field => $details) {
            [$nom, $type] = explode(',', $details);
            $value = trim($request->input($field, ''));
            if (!empty($value)) {
                $hasInput = true;
                if ($type === 'text' || $type === 'email') {
                    $query->where($field, 'like', '%' . $value . '%');
                }
            }
        }
        return $hasInput ? $query->orderBy('nom', 'asc')->get() : collect();
    }

    private function saveSearchSession(array $fields, Request $request, string $sessionKey)
    {
        $values = [];
        foreach ($fields as $key => $details) {
            $values[$key] = $request->input($key, '');
        }
        Session::put($sessionKey, $values);
    }
}
