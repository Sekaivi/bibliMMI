@php
$champs = array(
'titre'=>'Titre,text,required' ,
'auteur'=> 'Auteur,text,required',
'editeur'=>'Editeur,text,required',
'serie'=> 'Série,text',
'pages'=>'Pages,number,required',
'date_publication'=>'Date de publication,date,required',
'couverture'=>'Image de couverture,file'
)
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ouvrages') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-lg font-medium text-gray-900">Détails de l'Ouvrage</h2>
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

                    @php
                    $couverture_path = public_path('images/' . $ouvrage->couverture) ;
                    @endphp
                    @if ($ouvrage->couverture!=null && file_exists($couverture_path))
                        <div class="flex flex-col items-center gap-2">

                            <form action="{{ route('ouvrages.delete_image',$ouvrage->id) }}" method="Post">
                                @csrf
                                <button type="submit" class="bg-rose-500 hover:bg-rose-600 px-4 py-2 bg-danger border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest">Supprimer couverture</button>
                            </form>
                            <img class="w-32" src="{{ asset('images/'.$ouvrage->couverture) }}" alt="Couverture de {{ $ouvrage->titre}}" />
                            <p class="text-gray-600 text-xs font-semibold uppercase text-center">Image de couverture actuelle</p>
                        </div>
                    @endif

                    <form action="{{ route('ouvrages.update',$ouvrage->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        @foreach($champs as $cle=>$details)
                        @php
                        $parts = explode(',', $details);
                        $parts = array_pad($parts, 3, null); // ensures 3 elements
                        [$nomchamp, $type, $required] = $parts;
                        @endphp
                        <div>
                            <label class="block font-medium text-sm text-gray-700" for="{{ $cle }}">
                                {{ $nomchamp }}
                            </label>
                            <input class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full" id="{{ $cle }}" name="{{ $cle }}" type="{{ $type }}" value="{{ $ouvrage->$cle }}" {{ $required }} autofocus="autofocus" autocomplete="{{ $cle }}">
                        </div>
                        @error($cle)
                        <p><strong>{{ $message }}</strong></p>
                        @enderror
                        @endforeach

                        <button type="submit" class="inline-flex items-right px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest" style="margin-top:20px;background-color:#3B71CA;">Enregistrer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <a class="inline-flex items-right px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest" href="{{ route('ouvrages.index') }}">
                Retour
            </a>
        </div>
    </div>

</x-app-layout>