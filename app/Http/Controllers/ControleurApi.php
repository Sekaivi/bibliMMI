<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\usagers;
use App\Models\exemplaires;


class ControleurApi extends Controller
{
    protected $session_duree = 1800; // 30min en secondes pour une session de connexion
    // fonction qui est appelee avec chaque requete
    public function check_token(Request $request)
    {
        $token = $request->apiToken;
        if (!$token) {
            return response()->json([
                'erreur' => true,
                'message' => 'Token manquant',
                'data' => $request,
            ], 400);
        }
        $decoded = $this->decode_token($token); // fonction qui decode le token
        $validity = $this->token_validity($decoded); // fonction qui verifie que le token est valide
        if (isset($valid['erreur'])) { // erreur renvoye avec sa raison s'il y a lieu
            return response()->json($validity, 401);
        }
        return response()->json($validity); // si tout est bon, l'usager peut continuer
    }

    public function connexion(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $email = $request->email;
        $mdp = $request->password;
        $token = null; // y revenir et faire en sorte qu'il soit recupere depuis la request ?

        $usager = usagers::where('email', $email)->first();
        if (!$usager) {
            return response()->json(
                [
                    'erreur' => true,
                    'message' => 'Utilisateur non trouvé'
                ],
                404
            );
        }

        if (!Hash::check($mdp, $usager->passe)) {
            return response()->json(
                [
                    'erreur' => true,
                    'message' => 'Mot de passe incorrect'
                ],
                401
            );
        }

        $usager->api_token = $this->generate_token($usager->id);
        $usager->save();
        return response()->json([
            'message' => 'Connexion réussie',
            'token' => $usager->api_token,
            'user' => [
                'id_dans_table' => $usager->id,
                'identifiant' => $usager->identifiant,
                'nom' => $usager->nom,
                'prenom' => $usager->prenom,
                'email' => $usager->email,
            ]
        ]);
    }

    public function deconnexion(Request $request)
    {

        // verifier que on a le token
        // verifier validite du token
        // all is good ? DECONNEXION !
        $response = $this->check_token($request);
        $response = $response->original;

        if (isset($response['erreur'])) {
            return $response;
        }

        if ($response['valid']) {
            $usager_id = $response['usager_id'];
            $usager = usagers::find($usager_id);
            $usager->api_token = null;
            $usager->save();
            return response()->json([
                'success' => true,
            ]);
        } else {
            // y a soit une erreur de token soit il est deja deco donc... jsute delete le token cote client s'il existe ^^
            return $response;
        }
    }

    public function get_emprunts(Request $request)
    {
        $response = $this->check_token($request);
        $response = $response->original;

        if (isset($response['erreur'])) {
            return $response;
        }

        if ($response['valid']) {
            $usager_id = $response['usager_id'];
            $usager = usagers::find($usager_id);
            $emprunts = $usager->exemplaires;
            foreach ($emprunts as $emprunt) {
                $emprunt['ouvrage'] = $emprunt->ouvrage;
                $emprunt->ouvrage->couverture = asset('images/' . $emprunt->ouvrage->couverture);
            }
            return response()->json([
                'success' => 'true',
                'emprunts' => $emprunts,
            ]);
        } else {
            return $response;
        }
        return response()->json([
            'test' => 'test of test yayay',
        ]);
    }

    public function renouveler_emprunts(Request $request)
    {
        $response = $this->check_token($request);
        $response = $response->original;

        if (isset($response['erreur'])) {
            return $response;
        }
        $exemplaire_id = $request->data['id'];
        $exemplaire = exemplaires::find($exemplaire_id);
        if (isset($exemplaire)) {
            if (!$exemplaire->renouvellement) {
                $exemplaire->date_retour_souhaitee = $exemplaire->date_retour_souhaitee->addWeeks(2);
                $exemplaire->renouvellement = true;
                $exemplaire->save();
                return response()->json([
                    'success' => 'true',
                    'message' => 'Emprunt renouvelé avec succès !',
                    'retour' => $exemplaire->date_retour_souhaitee,
                ]);
            } else {
                return response()->json([
                    'erreur' => 'true',
                    'message' => 'Cet emprunt a deja ete renouvele!'
                ]);
            }
        } else {
            return response()->json([
                'erreur' => 'true',
                'message' => `L'identifiant renvoye ne correspond a aucun emprunt`,
            ]);
        }
    }

    public function update_profile(Request $request)
    {
        $response = $this->check_token($request);
        $response = $response->original;

        if (isset($response['erreur'])) {
            return $response;
        }   

        if ($response['valid']) {
            $usager_id = $response['usager_id'];
            $usager = usagers::find($usager_id);
            $formData = $request->data;

            // DONNEES A UPDATE !
            $nom = $formData['nom'] ;
            $prenom = $formData['prenom'] ;
            $email = $formData['email'] ;
            $identifiant = $formData['identifiant'] ;

            $usager->nom = $nom ;
            $usager->prenom = $prenom ;
            $usager->email = $email ;
            $usager->identifiant = $identifiant ;

            if(isset($formData['password'])){
                $password = $formData['password'] ;
                $usager->passe = Hash::make($password);
            }

            $usager->save() ;

            return response()->json([
                'success' => 'true',
                'message' => 'Profil mis a jour avec succes !'
            ]);
        } else {
            return response()->json([
                'erreur' => 'true',
                'message' => 'Erreur ??',
            ]);
        }
    }

    private function generate_token($id_usager)
    {
        $randomStr = Str::random(20);
        $rawToken = now()->format('Y-m-d_H-i-s') . '.' . $id_usager . '.' . $randomStr;
        $token = base64_encode($rawToken);
        return $token;
    }

    private function decode_token($token)
    {
        $decoded = base64_decode($token);
        $parts = explode('.', $decoded);
        if (count($parts) !== 3) {
            return [
                'erreur' => true,
                'message' => 'Token invalide (parties manquantes)'
            ];
        }

        [$dateStr, $id_usager, $randomstr] = $parts;

        return [
            'created_at' => $dateStr,
            'usager_id' => $id_usager,
            'random' => $randomstr,
        ];
    }

    private function token_validity(array $tokenData)
    {
        if (!isset($tokenData['created_at'], $tokenData['usager_id'], $tokenData['random'])) {
            return [
                'erreur' => true,
                'message' => 'Token invalide (elements manquants)'
            ];
        }


        $dateStr = $tokenData['created_at'];
        $usager_id = $tokenData['usager_id'];

        $usager = usagers::find($usager_id);
        if (!$usager) {
            return [
                'erreur' => true,
                'message' => 'Le token contient des éléments invalides (id)'
            ];
        }

        $db_usagerTokenData = $this->decode_token($usager->api_token);

        if ($tokenData !== $db_usagerTokenData) {
            return [
                'erreur' => true,
                'message' => 'Ce token nexiste pas bro',
            ];
        }


        $date = \DateTime::createFromFormat('Y-m-d_H-i-s', $dateStr);
        $dateIsValid = $date && $date->format('Y-m-d_H-i-s') === $dateStr;
        if (!$dateIsValid) {
            return [
                'erreur' => true,
                'message' => 'Le token contient des éléments invalides (date incorrecte)'
            ];
        }

        $now = new \DateTime();
        $interval = $now->getTimestamp() - $date->getTimestamp();

        if ($interval > $this->session_duree) { // en secondes
            $usager->api_token = null;
            $usager->save();
            return [
                'erreur' => true,
                'message' => 'Votre session a expire, veuillez vous reconnecter'
            ];
        }

        return [
            'valid' => true,
            'usager_id' => $usager->id,
            'identifiant' => $usager->identifiant,
            'nom' => $usager->nom,
            'prenom' => $usager->prenom,
            'email' => $usager->email,
            'blocage' => $usager->blocage,
            'created_at' => $date,
            'temps_ecoule' => $interval,
            'temps_restant' => $this->session_duree - $interval
        ];
    }

    public function test_api()
    {
        return response()->json([
            'message' => "YAY L'API FONCTIONNE",
        ]);
    }
}
