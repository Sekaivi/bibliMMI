@php
$mainFields = ['titre', 'auteur' , 'nom' , 'prenom'];

$champs_recherche_ouvrages = array(
'titre'=>'Titre,text' ,
'auteur'=> 'Auteur,text',
'serie'=> 'Série,text',
'editeur'=>'Editeur,text',
'min_pages'=>'Min Pages,number',
'max_pages'=>'Max Pages,number',
'avant'=>'Avant,date',
'apres'=>'Après,date'
) ;

$champs_recherche_usagers = array(
'nom'=>'Nom,text' ,
'prenom'=> 'Prénom,text',
'email'=> 'E-mail,email'
) ;

$champs_ouvrages = array(
'titre'=>'Titre' ,
'auteur'=> 'Auteur'
) ;

$champs_usagers = array(
'nom'=>'Nom' ,
'prenom'=> 'Prénom',
'email'=> 'E-mail'
) ;

@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tableau de bord') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if (session('erreur'))
                    <p class="mt-1 text-sm text-gray-600">
                        {{ session('erreur') }}
                    </p>
                    @endif


                    <form action="{{ route('tableaudebord.any', ['action' => 'search']) }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="flex flex-col items-start">
                            <h2 class="text-lg font-medium text-gray-900">Recherche</h2>
                            <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded hover:bg-blue-900 text-sm">
                                Lancer la recherche
                            </button>
                        </div>
                        <div class="flex gap-8 w-full">

                            <div class="flex-1" id="bloc_ouvrage">
                                @foreach($champs_recherche_ouvrages as $cle => $details)
                                @php
                                [$nom, $type] = explode(',', $details);
                                $isAdvance = !in_array($cle, $mainFields);
                                @endphp
                                <div class="{{ $isAdvance && !request($cle) ? 'advanced-field hidden' : '' }}">
                                    <label class="block font-medium text-sm text-gray-700" for="{{ $cle }}">
                                        {{ $nom }}
                                    </label>
                                    <div class="flex justify-start items-center gap-2 mt-1">
                                        <input class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full" id="{{ $cle }}" name="{{ $cle }}" type="{{ $type }}" value="{{ $recherche_ouvrages[$cle] ?? '' }}" autofocus="autofocus" autocomplete="{{ $cle }}">
                                        @if($type === 'date')
                                        <button
                                            type="button"
                                            onclick="document.getElementById('{{ $cle }}').value='';"
                                            class="px-2 py-1 h-full bg-gray-200 text-gray-700 rounded hover:bg-gray-300 text-sm mt-1">
                                            Reinitialiser
                                        </button>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                                <button type="button" class="mt-2 toggle-advanced px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 text-sm">
                                    Recherche avancée
                                </button>
                            </div>

                            <div class="flex-1" id="bloc_usagers">
                                @foreach($champs_recherche_usagers as $cle => $details)
                                @php
                                [$nom, $type] = explode(',', $details);
                                $isAdvance = !in_array($cle, $mainFields);
                                @endphp
                                <div class="{{ $isAdvance && !request($cle) ? 'advanced-field hidden' : '' }}">
                                    <label class="block font-medium text-sm text-gray-700" for="{{ $cle }}">
                                        {{ $nom }}
                                    </label>
                                    <div class="flex justify-start items-center gap-2 mt-1">
                                        <input class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full" id="{{ $cle }}" name="{{ $cle }}" type="{{ $type }}" value="{{ $recherche_usagers[$cle] ?? '' }}" autofocus="autofocus" autocomplete="{{ $cle }}">
                                    </div>
                                </div>
                                @endforeach
                                <button type="button" class="mt-2 toggle-advanced px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 text-sm">
                                    Recherche avancée
                                </button>
                            </div>

                        </div>
                    </form>

                    <div class="flex flex-col w-full" id="resultats_recherche">
                        @if(($ouvrages?->isEmpty() ?? false) && ($usagers?->isEmpty() ?? false))
                        <p class="w-full text-center">Aucun résultat n'a pu correspondre à votre recherche</p>
                        @else

                        <div id="resultats_recherche_ouvrage">
                            @if(isset($ouvrages))
                            @if($ouvrages->count() > 0)
                            <h2 class="font-semibold text-lg">
                                Ouvrages Correspondants
                            </h2>
                            <hr />
                            @foreach ($ouvrages as $ouvrage)
                            <ul class="py-1 grid items-center grid-flow-col auto-cols-fr gap-2">
                                <li>
                                    @php
                                    $couverture = $ouvrage->couverture ;
                                    $couverture_path = public_path('images/' . $couverture) ;
                                    @endphp
                                    @if ($couverture!=null && file_exists($couverture_path))
                                    <img class="mx-auto w-20" src="{{ asset('images/' . $couverture) }}" alt="Couverture de {{ $ouvrage->titre }}" />
                                    @else
                                    <p class="text-xs font-semibold uppercase text-center">Pas d'image de couverture disponible</p>
                                    @endif
                                </li>
                                <li>{{ $ouvrage->titre }}</li>
                                <li>{{ $ouvrage->auteur }}</li>
                                <li class="flex gap-1">
                                    @if(isset($ouvrage_selected) && $ouvrage->id == $ouvrage_selected->id)
                                    <a title="Arreter la selection sur cet ouvrage" class="flex items-center font-mono text-white bg-yellow-600 hover:bg-yellow-700 font-bold px-2 rounded" href="{{ route('tableaudebord.any',['action' => 'reset_ouvrage']) }}">
                                        <p>X</p>
                                    </a>
                                    @endif
                                    <form action="{{ route('tableaudebord.any',['action' => 'ouvrage' , 'objet'=>$ouvrage->id]) }}" method="Post">
                                        @csrf
                                        <button class="rounded-md font-semibold text-xs text-white uppercase tracking-widest px-2 py-2 bg-emerald-600 hover:bg-emerald-700">{{ $ouvrage->exemplaires_count }} exemplaires</button>
                                    </form>

                                </li>
                                <li>
                                    <form class="flex gap-1" action="{{ route('ouvrages.destroy',$ouvrage->id) }}">
                                        <a class="bg-sky-600 hover:bg-sky-700 px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest" href="{{ route('ouvrages.edit',$ouvrage->id) }}">Modifier</a>
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-rose-500 hover:bg-rose-600 px-4 py-2 bg-danger border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest">Supprimer</button>
                                    </form>
                                </li>
                            </ul>
                            @if(isset($ouvrage_selected) && $ouvrage->id == $ouvrage_selected->id)
                            <h3 class="pl-4 font-semibold">Liste des exemplaires</h3>
                            @if($ouvrage_selected?->exemplaires->isNotEmpty())
                            @foreach($ouvrage_selected->exemplaires as $exemplaire)
                            <ul class="items-center py-1 grid grid-flow-col auto-cols-fr gap-2">
                                <li>
                                    Exemplaire n° {{ $exemplaire->id }}
                                </li>
                                <li class="first-letter:uppercase">{{ $exemplaire->etat }}</li>
                                @if($exemplaire->emprunteur_id == null)
                                <li>Disponible</li>
                                <li>
                                    @if(isset($usager_selected))
                                    <form action="{{ route('tableaudebord.any',['action' => 'emprunt' , 'objet'=>$exemplaire->id]) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="rounded-md font-semibold text-xs text-white uppercase tracking-widest px-2 py-2 bg-gray-950 hover:bg-gray-800">
                                            Emprunter à {{ $usager_selected->prenom . ' ' . $usager_selected->nom }}
                                        </button>
                                    </form>
                                    @endif
                                </li>

                                @else
                                <li> Emprunté par {{ $exemplaire->emprunteur->prenom . ' ' . $exemplaire->emprunteur->nom }}, retour le {{ $exemplaire->date_retour_souhaitee->format('d/m/Y') }}</li>
                                <li class="flex gap-1">
                                    <form action="{{ route('tableaudebord.any',['action' => 'renouveler' , 'objet'=>$exemplaire->id]) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="rounded-md font-semibold text-xs text-white uppercase tracking-widest px-2 py-2 bg-gray-950 hover:bg-gray-800">
                                            Renouvellement
                                        </button>
                                        @if(session('renew_msg') && $exemplaire->id == session('exemplaire_renouvele'))
                                        <p class="mt-1 text-sm text-gray-600">{{ session('renew_msg') }}</p>
                                        @endif
                                    </form>
                                    <form action="{{ route('tableaudebord.any',['action' => 'retour' , 'objet'=>$exemplaire->id]) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="rounded-md font-semibold text-xs text-white uppercase tracking-widest px-2 py-2 bg-gray-950 hover:bg-gray-800">
                                            Retour
                                        </button>
                                    </form>
                                </li>
                                @endif

                            </ul>
                            @endforeach
                            @else
                            <p class="text-center">
                                Cet ouvrage n'a encore aucun exemplaire d'enregistré. Vous pouvez en ajouter
                                <a href="{{route('ouvrages.exemplaires.index' , $ouvrage->id)}}" class="cursor-pointer underline">ici</a>
                            </p>
                            @endif
                            @endif
                            <hr />
                            @endforeach
                            @else
                            <p>Aucun ouvrage n'a pu correspondre à votre recherche</p>
                            @endif
                            @endif
                        </div>

                        <div id="resultats_recherche_usagers">
                            @if(isset($usagers))
                            @if($usagers->count() > 0)
                            <h2 class="font-semibold text-lg">
                                Usagers Correspondants
                            </h2>
                            <hr />
                            @foreach ($usagers as $usager)
                            <ul class="py-1 grid items-center grid-flow-col auto-cols-fr gap-2">
                                <li>{{ $usager->prenom . ' ' . $usager->nom  }}</li>
                                <li>{{ $usager->email }}</li>
                                <li class="flex gap-1">
                                    @if(isset($usager_selected) && $usager->id == $usager_selected->id)
                                    <a title="Arreter la selection sur cet utilisateur" class="flex items-center font-mono text-white bg-yellow-600 hover:bg-yellow-700 font-bold px-2 rounded" href="{{ route('tableaudebord.any',['action' => 'reset_usager']) }}">
                                        <p>X</p>
                                    </a>
                                    @endif
                                    <form action="{{ route('tableaudebord.any',['action' => 'usager' , 'objet'=>$usager->id]) }}" method="Post">
                                        @csrf
                                        <button class="rounded-md font-semibold text-xs text-white uppercase tracking-widest px-2 py-2 bg-emerald-600 hover:bg-emerald-700">{{ $usager->exemplaires_count }} ouvrages</button>
                                    </form>

                                </li>
                                <li>
                                    <form class="flex gap-1" action="{{ route('usagers.destroy',$usager->id) }}">
                                        <a class="bg-sky-600 hover:bg-sky-700 px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest" href="{{ route('usagers.edit',$usager->id) }}">Modifier</a>
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-rose-500 hover:bg-rose-600 px-4 py-2 bg-danger border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest">Supprimer</button>
                                    </form>
                                </li>

                            </ul>
                            @if(isset($usager_selected) && $usager->id == $usager_selected->id)
                            <h3 class="pl-4 font-semibold">Ouvrages empruntés</h3>
                            @if($usager_selected?->exemplaires->isNotEmpty())
                            @foreach($usager_selected->exemplaires as $exemplaire)
                            <ul class="items-center py-1 grid grid-flow-col auto-cols-fr gap-2">
                                <li>
                                    @php
                                    $couverture = $exemplaire->ouvrage->couverture ;
                                    $couverture_path = public_path('images/' . $couverture) ;
                                    @endphp
                                    @if ($couverture!=null && file_exists($couverture_path))
                                    <img class="mx-auto w-20" src="{{ asset('images/' . $couverture) }}" alt="Couverture de {{ $exemplaire->ouvrage->titre }}" />
                                    @else
                                    <p class="text-xs font-semibold uppercase text-center">Pas d'image de couverture disponible</p>
                                    @endif
                                </li>
                                <li>{{ $exemplaire->ouvrage->titre }}</li>
                                <li>{{ $exemplaire->ouvrage->auteur }}</li>
                                <li>Retour prévu le {{ $exemplaire->date_retour_souhaitee->format('d/m/Y') }}</li>
                                <li class="flex gap-1">
                                    <form action="{{ route('tableaudebord.any',['action' => 'renouveler' , 'objet'=>$exemplaire->id]) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="rounded-md font-semibold text-xs text-white uppercase tracking-widest px-2 py-2 bg-gray-950 hover:bg-gray-800">
                                            Renouvellement
                                        </button>
                                        @if(session('renew_msg'))
                                        <p class="mt-1 text-sm text-gray-600">{{ session('renew_msg') }}</p>
                                        @endif
                                    </form>
                                    <form action="{{ route('tableaudebord.any',['action' => 'retour' , 'objet'=>$exemplaire->id]) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="rounded-md font-semibold text-xs text-white uppercase tracking-widest px-2 py-2 bg-gray-950 hover:bg-gray-800">
                                            Retour
                                        </button>
                                    </form>
                                </li>
                            </ul>
                            @endforeach
                            @else
                            <p>Cet usager n'a encore emprunté aucun ouvrage</p>
                            @endif
                            @endif
                            <hr />
                            @endforeach
                            @else
                            <p>Aucun usager n'a pu correspondre à votre recherche</p>
                            @endif
                            @endif
                        </div>

                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
    </div>
</x-app-layout>


<script>
    document.querySelectorAll('.toggle-advanced').forEach(button => {
        button.addEventListener('click', () => {
            const parent = button.closest('div');
            parent.querySelectorAll('.advanced-field').forEach(el => {
                el.classList.toggle('hidden');
            });
        });
    });
</script>