<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Détails du Modèle') }} : {{ $model->name }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('admin.models.edit', $model) }}"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Modifier
                </a>
                <a href="{{ route('admin.models.index') }}"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Retour à la liste
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                        <!-- Image du modèle -->
                        <div>
                            @if($model->image_url)
                                <div class="aspect-w-16 aspect-h-9 mb-4">
                                    <img src="{{ $model->image_url }}" alt="{{ $model->name }}"
                                        class="w-full h-64 object-cover rounded-lg border">
                                </div>
                            @else
                                <div
                                    class="w-full h-64 bg-gray-200 rounded-lg border flex items-center justify-center mb-4">
                                    <div class="text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        <p class="mt-2 text-sm text-gray-500">Aucune image</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Informations du modèle -->
                        <div>
                            <dl class="space-y-6">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Nom du modèle</dt>
                                    <dd class="text-2xl font-bold text-gray-900">{{ $model->name }}</dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Marque</dt>
                                    <dd class="flex items-center mt-1">
                                        @if($model->brand->logo_url)
                                            <img src="{{ $model->brand->logo_url }}" alt="{{ $model->brand->name }}"
                                                class="h-8 w-8 rounded mr-3">
                                        @endif
                                        <div>
                                            <span
                                                class="text-lg font-semibold text-gray-900">{{ $model->brand->name }}</span>
                                            @if($model->brand->country)
                                                <span
                                                    class="text-sm text-gray-500 ml-2">({{ $model->brand->country }})</span>
                                            @endif
                                        </div>
                                    </dd>
                                </div>

                                @if($model->year)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Année de production</dt>
                                        <dd class="text-lg text-gray-900">{{ $model->year }}</dd>
                                    </div>
                                @endif

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Niveau de difficulté</dt>
                                    <dd class="mt-1">
                                        <span
                                            class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $model->difficulty_color == 'green' ? 'bg-green-100 text-green-800' : ($model->difficulty_color == 'yellow' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ $model->difficulty_text }}
                                        </span>
                                    </dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Ajouté le</dt>
                                    <dd class="text-sm text-gray-900">{{ $model->created_at->format('d/m/Y à H:i') }}
                                    </dd>
                                </div>

                                @if($model->updated_at != $model->created_at)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Dernière modification</dt>
                                        <dd class="text-sm text-gray-900">{{ $model->updated_at->format('d/m/Y à H:i') }}
                                        </dd>
                                    </div>
                                @endif
                            </dl>

                            <!-- Actions rapides -->
                            <div class="mt-8 space-y-3">
                                <a href="{{ route('admin.models.edit', $model) }}"
                                    class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-center block">
                                    Modifier ce modèle
                                </a>

                                <a href="{{ route('admin.brands.show', $model->brand) }}"
                                    class="w-full bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-center block">
                                    Voir la marque {{ $model->brand->name }}
                                </a>

                                <form action="{{ route('admin.models.destroy', $model) }}" method="POST" class="w-full">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-full bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce modèle ?')">
                                        Supprimer ce modèle
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>