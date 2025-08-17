<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Détails de la Marque') }} : {{ $brand->name }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('admin.brands.edit', $brand) }}"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Modifier
                </a>
                <a href="{{ route('admin.brands.index') }}"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Retour à la liste
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Informations de la marque -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Logo et infos principales -->
                        <div>
                            @if($brand->logo_url)
                                <div class="mb-4">
                                    <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}"
                                        class="h-24 w-24 object-cover rounded-lg border">
                                </div>
                            @endif

                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Nom</dt>
                                    <dd class="text-lg font-semibold text-gray-900">{{ $brand->name }}</dd>
                                </div>

                                @if($brand->country)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Pays</dt>
                                        <dd class="text-sm text-gray-900">{{ $brand->country }}</dd>
                                    </div>
                                @endif

                                @if($brand->founded_year)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Année de fondation</dt>
                                        <dd class="text-sm text-gray-900">{{ $brand->founded_year }}</dd>
                                    </div>
                                @endif

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Ajoutée le</dt>
                                    <dd class="text-sm text-gray-900">{{ $brand->created_at->format('d/m/Y à H:i') }}
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Statistiques -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Statistiques</h3>
                            <div class="space-y-4">
                                <div class="bg-blue-50 p-4 rounded-lg">
                                    <dt class="text-sm font-medium text-blue-600">Total modèles</dt>
                                    <dd class="text-2xl font-bold text-blue-900">{{ $stats['total_models'] }}</dd>
                                </div>

                                <!-- Répartition par difficulté -->
                                @if($stats['total_models'] > 0)
                                    <div class="space-y-2">
                                        <h4 class="text-sm font-medium text-gray-700">Répartition par difficulté</h4>
                                        @foreach([1 => 'Facile', 2 => 'Moyen', 3 => 'Difficile'] as $level => $label)
                                            @php $count = $stats['by_difficulty'][$level] ?? 0 @endphp
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-600">{{ $label }}</span>
                                                <span
                                                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $level == 1 ? 'bg-green-100 text-green-800' : ($level == 2 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                    {{ $count }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                @if($stats['latest_model'])
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <dt class="text-sm font-medium text-gray-600">Dernier modèle ajouté</dt>
                                        <dd class="text-sm font-semibold text-gray-900">{{ $stats['latest_model']->name }}
                                        </dd>
                                        <dd class="text-xs text-gray-500">
                                            {{ $stats['latest_model']->created_at->format('d/m/Y') }}</dd>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Liste des modèles -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Modèles de cette marque</h3>
                        <a href="{{ route('admin.models.create', ['brand_id' => $brand->id]) }}"
                            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm">
                            Ajouter un modèle
                        </a>
                    </div>

                    @if($brand->models->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Image</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nom</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Année</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Difficulté</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($brand->models as $model)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($model->image_url)
                                                    <img src="{{ $model->image_url }}" alt="{{ $model->name }}"
                                                        class="h-12 w-12 rounded-lg object-cover">
                                                @else
                                                    <div class="h-12 w-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                                        <svg class="h-6 w-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd"
                                                                d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"
                                                                clip-rule="evenodd"></path>
                                                        </svg>
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $model->name }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-500">{{ $model->year ?? 'Non spécifiée' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $model->difficulty_color == 'green' ? 'bg-green-100 text-green-800' : ($model->difficulty_color == 'yellow' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                    {{ $model->difficulty_text }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                                <a href="{{ route('admin.models.show', $model) }}"
                                                    class="text-blue-600 hover:text-blue-900">Voir</a>
                                                <a href="{{ route('admin.models.edit', $model) }}"
                                                    class="text-indigo-600 hover:text-indigo-900">Modifier</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2h4a1 1 0 110 2h-1v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6H3a1 1 0 110-2h4z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun modèle</h3>
                            <p class="mt-1 text-sm text-gray-500">Cette marque n'a pas encore de modèles.</p>
                            <div class="mt-6">
                                <a href="{{ route('admin.models.create', ['brand_id' => $brand->id]) }}"
                                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                    Ajouter le premier modèle
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>