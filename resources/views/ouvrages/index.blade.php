@php
$champs = array(
'titre'=>'Titre' ,
'auteur'=> 'Auteur',
'editeur'=>'Editeur',
'serie'=> 'Série',
'pages'=>'Pages',
'date_publication'=>'Date de publication'
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
                    <h2 class="text-lg font-medium text-gray-800">Ouvrages</h2>
                    @if ($message = Session::get('success'))
                    <p class="mt-1 text-sm text-gray-600">
                        {{ $message }}
                    </p>
                    @endif

                    <table class="min-w-full text-left text-sm font-light" style="width:100%;margin-bottom:20px;">
                        <thead class="border-b font-medium dark:border-neutral-500">
                            <tr>
                                @foreach($champs as $cle=>$details)
                                <th scope="col" class="px-6 py-4">{{$details}}</th>
                                @endforeach
                                <th scope="col" class="px-6 py-4">Exemplaires</th>
                                <th scope="col" class="px-6 py-4">Couverture</th>
                                <th scope="col" class="px-6 py-4">&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ouvrages as $ouvrage)
                            <tr class="border-b dark:border-neutral-500">

                                @foreach($champs as $cle=>$details)
                                <td class="text-center px-6 py-4">{{ $ouvrage->$cle }}</td>
                                @endforeach

                                <td class="text-center underline px-6 py-4">
                                    <a href="{{route('ouvrages.exemplaires.index' , $ouvrage->id)}}" class="cursor-pointer">Exemplaires ({{$ouvrage->exemplaires()->count( )}})</a>
                                </td>

                                <td class="px-4 py-2">
                                    @php
                                    $couverture_path = public_path('images/' . $ouvrage->couverture) ;
                                    @endphp
                                    @if ($ouvrage->couverture!=null && file_exists($couverture_path))
                                    <img class="mx-auto w-20" src="{{ asset('images/'.$ouvrage->couverture) }}" alt="Couverture de {{ $ouvrage->titre}}" />
                                    @else
                                    <p class="text-xs font-semibold uppercase text-center">Pas d'image de couverture disponible</p>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-6 py-4" style="text-align:right;">
                                    <form action="{{ route('ouvrages.destroy',$ouvrage->id) }}" method="Post">
                                        <a class="bg-sky-600 hover:bg-sky-700 px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest" href="{{ route('ouvrages.edit',$ouvrage->id) }}">Modifier</a>
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-rose-500 hover:bg-rose-600 px-4 py-2 bg-danger border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {!! $ouvrages->links() !!}

                </div>
            </div>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <a class="inline-flex items-right px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest" href="{{ route('ouvrages.create') }}">
                Ajouter un ouvrage
            </a>
        </div>
    </div>

</x-app-layout>