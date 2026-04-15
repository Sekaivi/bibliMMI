<?php

namespace App\Http\Controllers;

use App\Models\exemplaires;
use App\Models\ouvrages;
use App\Models\usagers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class TableauDeBordController extends Controller
{

    protected $recherche_ouvrage = [ // variable decrivant la structure d'une possible recherche sur les ouvrages
        'titre' => 'Titre,text',
        'serie' => 'Série,text',
        'auteur' => 'Auteur,text',
        'editeur' => 'Editeur,text',
        'min_pages' => 'Min Pages,number',
        'max_pages' => 'Max Pages,number',
        'avant' => 'Avant,date',
        'apres' => 'Après,date'
    ];

    protected $recherche_usagers = [ // variable decrivant la structure d'une possible recherche sur les usagers
        'nom' => 'Nom,text',
        'prenom' => 'Prénom,text',
        'email' => 'E-mail,email'
    ];

    public function handleAny(Request $request, $action = null, $objet = null)
    {
        switch ($action) {
            case 'search':
                return $this->search($request);

            case 'reset_usager':
                Session::forget('usager_selected_id');
                return redirect()->route('tableaudebord.any');
            case 'usager':
                Session::forget('usager_selected_id');
                Session::put('usager_selected_id', $objet);
                return redirect()->route('tableaudebord.any');
            case 'ouvrage':
                Session::forget('ouvrage_selected_id');
                Session::put('ouvrage_selected_id', $objet);
                return redirect()->route('tableaudebord.any');
            case 'reset_ouvrage':
                Session::forget('ouvrage_selected_id');
                return redirect()->route('tableaudebord.any');
            case 'renouveler':
                $exemplaire = exemplaires::find($objet);
                if (!$exemplaire->renouvellement) {
                    $exemplaire->date_retour_souhaitee = $exemplaire->date_retour_souhaitee->addWeeks(2);
                    $exemplaire->renouvellement = true;
                    $exemplaire->save();
                    return redirect()->route('tableaudebord.any')
                        ->with([
                            'renew_msg' => 'Emprunt renouvelé avec succès !',
                            'exemplaire_renouvele' => $exemplaire->id
                        ]);
                } else {
                    return redirect()->route('tableaudebord.any')
                        ->with([
                            'renew_msg' => 'Cet emprunt a déjà été renouvelé.',
                            'exemplaire_renouvele' => $exemplaire->id
                        ]);
                }
                break;
            case 'retour':
                $exemplaire = exemplaires::find($objet);
                $exemplaire->disponible = true;
                $exemplaire->emprunteur_id = null;
                $exemplaire->date_retour_souhaitee = null;
                $exemplaire->renouvellement = false;
                $exemplaire->save();
                return redirect()->route('tableaudebord.any');
            case 'emprunt':
                $usager = Session::get('usager_selected_id', null);
                $exemplaire = exemplaires::find($objet);
                $exemplaire->disponible = false;
                $exemplaire->emprunteur_id = $usager;
                $exemplaire->date_retour_souhaitee = now()->addWeeks(2);
                $exemplaire->renouvellement = false;
                $exemplaire->save();
                return redirect()->route('tableaudebord.any');
            default:
                return $this->index();
        }
    }


    public function index()
    {
        $champsRechercheOuvrages = array_fill_keys(array_keys($this->recherche_ouvrage), '');
        $recherche_ouvrages = Session::get('recherche_ouvrages', $champsRechercheOuvrages);

        $champsRechercheUsagers = array_fill_keys(array_keys($this->recherche_usagers), '');
        $recherche_usagers = Session::get('recherche_usagers', $champsRechercheUsagers);

        $ouvrages = Session::has('resultats_ouvrages') ? collect(Session::get('resultats_ouvrages')) : null;
        $usagers  = Session::has('resultats_usagers') ? collect(Session::get('resultats_usagers')) : null;

        if ($ouvrages && $ouvrages->isEmpty() && $usagers && $usagers->isEmpty()) {
            Session::forget(['resultats_ouvrages', 'resultats_usagers']);
        }

        $usager_selected_id = Session::get('usager_selected_id', null);
        $usager_selected = usagers::withCount('exemplaires')->with(['exemplaires.ouvrage'])->find($usager_selected_id);

        $ouvrage_selected_id = Session::get('ouvrage_selected_id', null);
        $ouvrage_selected = ouvrages::withCount('exemplaires')->with(['exemplaires.emprunteur'])->find($ouvrage_selected_id);

        $viewData = [
            'recherche_usagers' => $recherche_usagers,
            'recherche_ouvrages' => $recherche_ouvrages,
            'usager_selected' => $usager_selected,
            'ouvrage_selected' => $ouvrage_selected,
            'usagers' => $usagers,
            'ouvrages' => $ouvrages,
        ];

        return view('tableaudebord', $viewData);
    }

    public function search(Request $request)
    {
        if (collect($request->except('_token'))->filter()->isEmpty()) {
            Session::forget(['resultats_ouvrages', 'resultats_usagers', 'recherche_usagers', 'recherche_ouvrages']);
            return redirect()->route('tableaudebord.any')
                ->with('erreur', 'Veuillez remplir au moins un champs de recherche');
        } // permet de verifier que des valeurs sont bien renvoyees au sein du champs de recherche
        $ouvrages = $this->queryOuvrages($request); // fonction de rechercher sur la BD pour les ouvrages
        $usagers  = $this->queryUsagers($request);   // fonction de rechercher sur la BD pour les usagers
        // pour mettre les resultats en session et les recuperer a chaque fois
        Session::put('resultats_ouvrages', $ouvrages); 
        Session::put('resultats_usagers', $usagers);
        $recherche_usagers = [];
        foreach ($this->recherche_usagers as $champs => $details) { // pour peupler $recherche_usagers avec les champs de recherche remplis
            $recherche_usagers[$champs] = $request->input($champs, '');
        }
        // avec la persistence, garder les champs de recherche concernant les usagers qui ont ete remplis
        Session::put('recherche_usagers', $recherche_usagers);
        // puis de meme avec les ouvrages

        $recherche_ouvrages = [];
        foreach ($this->recherche_ouvrage as $champs => $details) {
            $recherche_ouvrages[$champs] = $request->input($champs, '');
        }

        Session::put('recherche_ouvrages', $recherche_ouvrages);

        $usager_selected_id = Session::get('usager_selected_id', null);
        $usager_selected = usagers::withCount('exemplaires')->with(['exemplaires.ouvrage'])->find($usager_selected_id);

        $ouvrage_selected_id = Session::get('ouvrage_selected_id', null);
        $ouvrage_selected = ouvrages::withCount('exemplaires')->with(['exemplaires.emprunteur'])->find($ouvrage_selected_id);

        $viewData = [
            'recherche_usagers' => $recherche_usagers,
            'recherche_ouvrages' => $recherche_ouvrages,
            'usager_selected' => $usager_selected,
            'ouvrage_selected' => $ouvrage_selected,
            'usagers' => $usagers,
            'ouvrages' => $ouvrages,
        ];

        return view('tableaudebord', $viewData);
    }

    private function queryOuvrages(Request $request)
    {
        $recherche_ouvrage = $this->recherche_ouvrage; // propriete de classe decrivant la structure des champs de recherche d'ouvrage

        $query = ouvrages::query();
        $hasInput = false;

        foreach ($recherche_ouvrage as $field => $details) { // parcours les champs de recherche
            [$nom, $type] = explode(',', $details); // separe le nom du champs de recherche de son type defini
            $value = trim($request->input($field, ''));// recupere la valeur du champs
            if (!empty($value)) { // verifie que le champs n'est pas vide
                $hasInput = true; // flag pour confirmer qu'il est bien rempli
                switch ($type) {
                    case 'text': // si c'est une chaine de caracteres...
                        $query->where($field, 'like', '%' . $value . '%');
                        break;
                    case 'number': // si c'est un nombre...
                        if ($field === 'min_pages') {
                            $query->where('pages', '>=', $value);
                        } elseif ($field === 'max_pages') {
                            $query->where('pages', '<=', $value);
                        }
                        break;
                    case 'date': // si 'est une date...
                        if ($field === 'avant') {
                            $query->where('date_publication', '<=', $value);
                        } elseif ($field === 'apres') {
                            $query->where('date_publication', '>=', $value);
                        }
                        break;
                }
            }
        }
        return $hasInput ? $query->withCount('exemplaires')->orderBy('titre', 'desc')->get() : collect();
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

        return $hasInput ? $query->withCount('exemplaires')->orderBy('nom', 'asc')->get()  : collect();
    }
}
