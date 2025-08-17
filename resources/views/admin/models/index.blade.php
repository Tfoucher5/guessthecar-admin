<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestion des Modèles') }}
            </h2>
            <a href="{{ route('admin.models.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Ajouter un modèle
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Filtres -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.models.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                        
                        <!-- Recherche -->
                        <div class="lg:col-span-2">
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}"
                                   placeholder="Rechercher un modèle..."
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <!-- Marque -->
                        <div>
                            <select name="brand_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Toutes les marques</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                                        {{ $brand->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Difficulté -->
                        <div>
                            <select name="difficulty_level" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Toutes les difficultés</option>
                                @foreach($difficulties as $level => $label)
                                    <option value="{{ $level }}" {{ request('difficulty_level') == $level ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Année de -->
                        <div>
                            <input type="number" 
                                   name="year_from" 
                                   value="{{ request('year_from') }}"
                                   placeholder="Année min"
                                   min="1900"
                                   max="{{ date('Y') + 1 }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <!-- Année à -->
                        <div>
                            <input type="number" 
                                   name="year_to" 
                                   value="{{ request('year_to') }}"
                                   placeholder="Année max"
                                   min="1900"
                                   max="{{ date('Y') + 1 }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <!-- Boutons -->
                        <div class="lg:col-span-6 flex gap-2">
                            <button type="submit" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Filtrer
                            </button>
                            @if(request()->hasAny(['search', 'brand_id', 'difficulty_level', 'year_from', 'year_to']))
                                <a href="{{ route('admin.models.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                    Réinitialiser
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- Liste des modèles -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($models->count() > 0)
                        <!-- Statistiques rapides -->
                        <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="bg-blue-50 p-4 rounded-lg text-center">
                                <div class="text-2xl font-bold text-blue-600">{{ $models->total() }}</div>
                                <div class="text-sm text-blue-600">Total modèles</div>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg text-center">
                                <div class="text-2xl font-bold text-green-600">{{ $models->where('difficulty_level', 1)->count() }}</div>
                                <div class="text-sm text-green-600">Faciles</div>
                            </div>
                            <div class="bg-yellow-50 p-4 rounded-lg text-center">
                                <div class="text-2xl font-bold text-yellow-600">{{ $models->where('difficulty_level', 2)->count() }}</div>
                                <div class="text-sm text-yellow-600">Moyens</div>
                            </div>
                            <div class="bg-red-50 p-4 rounded-lg text-center">
                                <div class="text-2xl font-bold text-red-600">{{ $models->where('difficulty_level', 3)->count() }}</div>
                                <div class="text-sm text-red-600">Difficiles</div>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'direction' => request('sort') == 'name' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="group inline-flex">
                                                Image/Nom
                                                @if(request('sort') == 'name')
                                                    <span class="ml-2">{{ request('direction') == 'asc' ? '↑' : '↓' }}</span>
                                                @endif
                                            </a>
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'brand_name', 'direction' => request('sort') == 'brand_name' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="group inline-flex">
                                                Marque
                                                @if(request('sort') == 'brand_name')
                                                    <span class="ml-2">{{ request('direction') == 'asc' ? '↑' : '↓' }}</span>
                                                @endif
                                            </a>
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'year', 'direction' => request('sort') == 'year' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="group inline-flex">
                                                Année
                                                @if(request('sort') == 'year')
                                                    <span class="ml-2">{{ request('direction') == 'asc' ? '↑' : '↓' }}</span>
                                                @endif
                                            </a>
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'difficulty_level', 'direction' => request('sort') == 'difficulty_level' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="group inline-flex">
                                                Difficulté
                                                @if(request('sort') == 'difficulty_level')
                                                    <span class="ml-2">{{ request('direction') == 'asc' ? '↑' : '↓' }}</span>
                                                @endif
                                            </a>
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'direction' => request('sort') == 'created_at' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="group inline-flex">
                                                Ajouté
                                                @if(request('sort') == 'created_at')
                                                    <span class="ml-2">{{ request('direction') == 'asc' ? '↑' : '↓' }}</span>
                                                @endif
                                            </a>
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($models as $model)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    @if($model->image_url)
                                                        <img src="{{ $model->image_url }}" alt="{{ $model->name }}" class="h-12 w-12 rounded-lg object-cover mr-4">
                                                    @else
                                                        <div class="h-12 w-12 bg-gray-200 rounded-lg flex items-center justify-center mr-4">
                                                            <svg class="h-6 w-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                                            </svg>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900">{{ $model->name }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    @if($model->brand->logo_url)
                                                        <img src="{{ $model->brand->logo_url }}" alt="{{ $model->brand->name }}" class="h-6 w-6 rounded mr-2">
                                                    @endif
                                                    <span class="text-sm text-gray-900">{{ $model->brand->name }}</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-500">{{ $model->year ?? 'Non spécifiée' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $model->difficulty_color == 'green' ? 'bg-green-100 text-green-800' : ($model->difficulty_color == 'yellow' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                    {{ $model->difficulty_text }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-500">{{ $model->created_at->format('d/m/Y') }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                                <a href="{{ route('admin.models.show', $model) }}" class="text-blue-600 hover:text-blue-900">Voir</a>
                                                <a href="{{ route('admin.models.edit', $model) }}" class="text-indigo-600 hover:text-indigo-900">Modifier</a>
                                                <form action="{{ route('admin.models.destroy', $model) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="text-red-600 hover:text-red-900"
                                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce modèle ?')">
                                                        Supprimer
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $models->withQueryString()->links() }}
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2h4a1 1 0 110 2h-1v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6H3a1 1 0 110-2h4z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun modèle trouvé</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                @if(request()->hasAny(['search', 'brand_id', 'difficulty_level', 'year_from', 'year_to']))
                                    Aucun modèle ne correspond à vos critères de recherche.
                                @else
                                    Commencez par créer un nouveau modèle.
                                @endif
                            </p>
                            <div class="mt-6">
                                <a href="{{ route('admin.models.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    Ajouter un modèle
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>