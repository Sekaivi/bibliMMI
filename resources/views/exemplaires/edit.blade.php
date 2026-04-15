@php
$champs = [
'etat' => ['label' => 'État', 'type' => 'select', 'options' => config('constantes.ouvrages_etats') , 'required'=>true],
'disponible' => ['label' => 'Disponible', 'type' => 'checkbox' , 'required'=>false],
'emprunteur_id' => [
'label' => 'Emprunteur',
'type' => 'select',
'options' => $usagers,
'required'=>false
],
'date_retour_souhaitee' => ['label' => 'Date de retour souhaitée', 'type' => 'date', 'required'=>false],
'reserve' => ['label' => 'Réservé', 'type' => 'checkbox', 'required'=>false],
'renouvellement' => ['label' => 'Renouvellement', 'type' => 'checkbox', 'required'=>false],
];
@endphp;

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
                    <h2 class="text-lg font-medium text-gray-900">Détails de l'Exemplaire</h2>
                    @if(session('status'))
                    <p class="mt-1 text-sm text-gray-600">
                        {{ session('status') }}
                    </p>
                    @endif

                    @if ($message = Session::get('success'))
                    <p class="mt-1 text-sm text-gray-600">
                        {{ $message }}
                    </p>
                    @endif

                    <form action="{{ route('ouvrages.exemplaires.update',[$ouvrage->id , $exemplaire->id]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        @foreach($champs as $cle => $details)
                        <div>
                            <label class="block font-medium text-sm text-gray-700" for="{{ $cle }}">
                                {{ $details['label'] }}
                            </label>

                            @php
                            $type = $details['type'];
                            @endphp

                            @if($type === 'select' && isset($details['options']))
                            @php
                                $required = $details['required'] ? 'required' : null;
                            @endphp
                            <select {{$required}} class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                id="{{ $cle }}" name="{{ $cle }}">
                                <option value="">Non emprunté</option>

                                @foreach($details['options'] as $option)
                                @if($cle === 'emprunteur_id')
                                <option value="{{ $option->id }}" {{ $exemplaire->emprunteur_id == $option->id ? 'selected' : '' }}>{{ $option->nom }} {{ $option->prenom }}</option>
                                @else
                                <option value="{{ $option }}" {{ $exemplaire->$cle == $option ? 'selected' : '' }}>{{ ucfirst($option) }}</option>
                                @endif
                                @endforeach
                            </select>
                            @elseif($type === 'checkbox')
                            <input {{ $exemplaire->$cle ? 'checked' : '' }} value="1" type="checkbox" id="{{ $cle }}" name="{{ $cle }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            @else
                            <input type="{{ $type }}" id="{{ $cle }}" name="{{ $cle }}" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full" value="{{$exemplaire->$cle}}">
                            @endif

                            @error($cle)
                            <p><strong>{{ $message }}</strong></p>
                            @enderror
                        </div>
                        @endforeach

                        <button type="submit" class="inline-flex items-right px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest" style="margin-top:20px;background-color:#3B71CA;">Enregistrer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <a class="inline-flex items-right px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest" href="{{ route('ouvrages.exemplaires.index' , $ouvrage->id) }}">
                Retour
            </a>
        </div>
    </div>

</x-app-layout>