<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Usagers') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-lg font-medium text-gray-800">Usagers</h2>
                    @if ($message = Session::get('success'))
                    <p class="mt-1 text-sm text-gray-600">
                        {{ $message }}
                    </p>
                    @endif

                    <table class="min-w-full text-left text-sm font-light" style="width:100%;margin-bottom:20px;">
                        <thead class="border-b font-medium dark:border-neutral-500">
                            <tr>
                                <th scope="col" class="px-6 py-4">Nom</th>
                                <th scope="col" class="px-6 py-4">Prénom</th>
                                <th scope="col" class="px-6 py-4">E-mail</th>
                                <th scope="col" class="px-6 py-4">Identifiant</th>
                                <th scope="col" class="px-6 py-4">ID</th>
                                <th scope="col" class="px-6 py-4">Accès bloqué</th>
                                <th scope="col" class="px-6 py-4">&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($usagers as $usager)
                            <tr class="border-b dark:border-neutral-500">
                                <td class="whitespace-nowrap px-6 py-4">{{ $usager->nom }}</td>
                                <td class="whitespace-nowrap px-6 py-4">{{ $usager->prenom }}</td>
                                <td class="whitespace-nowrap px-6 py-4">{{ $usager->email }}</td>
                                <td class="whitespace-nowrap px-6 py-4">{{ $usager->identifiant }}</td>
                                <td class="whitespace-nowrap px-6 py-4">{{ $usager->id }}</td>
                                <td class="whitespace-nowrap px-6 py-4">{{ ($usager->blocage==0?"Non":"Oui") }}</td>
                                <td class="whitespace-nowrap px-6 py-4" style="text-align:right;">
                                    <form action="{{ route('usagers.destroy',$usager->id) }}" method="Post">
                                        <a class="bg-sky-600 hover:bg-sky-700 px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest" href="{{ route('usagers.edit',$usager->id) }}">Modifier</a>
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-rose-500 hover:bg-rose-600 px-4 py-2 bg-danger border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {!! $usagers->links() !!}

                </div>
            </div>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <a class="inline-flex items-right px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest" href="{{ route('usagers.create') }}">
                Nouvel usager
            </a>
        </div>
    </div>

</x-app-layout>