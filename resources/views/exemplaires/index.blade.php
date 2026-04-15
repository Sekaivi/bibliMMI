@php
$champs = [
'etat' => ['label' => 'État', 'type' => 'text'],
'disponible' => ['label' => 'Disponible', 'type' => 'boolean'],
'emprunteur_id' => ['label' => 'Emprunteur', 'type' => 'relation', 'relation' => 'emprunteur'],
'date_retour_souhaitee' => ['label' => 'Date de retour souhaitée', 'type' => 'date'],
'reserve' => ['label' => 'Réservé', 'type' => 'boolean'],
'renouvellement' => ['label' => 'Renouvellement', 'type' => 'boolean'],
];

@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Exemplaires') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-lg font-medium text-gray-800">{{$ouvrage->titre}}</h2>
                    @if ($message = Session::get('success'))
                    <p class="mt-1 text-sm text-gray-600">
                        {{ $message }}
                    </p>
                    @endif

                    <img class="w-32" src="{{ asset('images/'.$ouvrage->couverture) }}" alt="Couverture de {{ $ouvrage->titre}}" />

                    <table class="min-w-full text-left text-sm font-light" style="width:100%;margin-bottom:20px;">
                        <thead class="border-b font-medium dark:border-neutral-500">
                            <tr>
                                @foreach($champs as $cle => $info)
                                <th scope="col" class="px-6 py-4">{{ $info['label'] }}</th>
                                @endforeach
                                <th scope="col" class="px-6 py-4">&nbsp;</th>
                            </tr>

                        </thead>
                        <tbody>
                            @foreach ($exemplaires as $exemplaire)
                            <tr>
                                @foreach ($champs as $cle => $info)
                                <td class="px-6 py-4 text-center">
                                    @switch($info['type'])
                                    @case('boolean')
                                    {{ $exemplaire->$cle ? 'Oui' : 'Non' }}
                                    @break

                                    @case('relation')
                                    {{ $exemplaire->{$info['relation']} ? $exemplaire->{$info['relation']}->nom . ' ' . $exemplaire->{$info['relation']}->prenom : '-' }}
                                    @break

                                    @case('date')
                                    {{ $exemplaire->$cle ? \Carbon\Carbon::parse($exemplaire->$cle)->format('d/m/Y') : '-' }}
                                    @break

                                    @default
                                    {{ $exemplaire->$cle }}
                                    @endswitch
                                </td>
                                @endforeach
                                <td class="whitespace-nowrap px-6 py-4" style="text-align:right;">
                                    <form action="{{ route('ouvrages.exemplaires.destroy',[$ouvrage->id , $exemplaire->id]) }}" method="Post">
                                        <a class="bg-sky-600 hover:bg-sky-700 px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest" href="{{ route('ouvrages.exemplaires.edit',[$ouvrage->id , $exemplaire->id]) }}">Modifier</a>
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-rose-500 hover:bg-rose-600 px-4 py-2 bg-danger border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {!! $exemplaires->links() !!}

                </div>
            </div>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <a class="inline-flex items-right px-4 py-2 bg-slate-950 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest" href="{{route('ouvrages.exemplaires.create' , $ouvrage->id)}}">
                Ajouter un ouvrage
            </a>
            <a class="inline-flex items-right px-4 py-2 bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest" href="{{ route('ouvrages.index') }}">
                Retour à la page ouvrage
            </a>
        </div>
    </div>

</x-app-layout>