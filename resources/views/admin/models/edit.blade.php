<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Modifier le Modèle') }} : {{ $model->name }}
            </h2>
            <a href="{{ route('admin.models.index') }}"
                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Retour à la liste
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.models.update', $model) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Nom -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">
                                Nom du modèle *
                            </label>
                            <input type="text" id="name" name="name" value="{{ old('name', $model->name) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-300 @enderror">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Marque -->
                        <div>
                            <label for="brand_id" class="block text-sm font-medium text-gray-700">
                                Marque *
                            </label>
                            <select id="brand_id" name="brand_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('brand_id') border-red-300 @enderror">
                                <option value="">Sélectionner une marque</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}" {{ old('brand_id', $model->brand_id) == $brand->id ? 'selected' : '' }}>
                                        {{ $brand->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('brand_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Année -->
                            <div>
                                <label for="year" class="block text-sm font-medium text-gray-700">
                                    Année de production
                                </label>
                                <input type="number" id="year" name="year" value="{{ old('year', $model->year) }}"
                                    min="1900" max="{{ date('Y') + 1 }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('year') border-red-300 @enderror">
                                @error('year')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Difficulté -->
                            <div>
                                <label for="difficulty_level" class="block text-sm font-medium text-gray-700">
                                    Niveau de difficulté *
                                </label>
                                <select id="difficulty_level" name="difficulty_level" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('difficulty_level') border-red-300 @enderror">
                                    <option value="">Sélectionner un niveau</option>
                                    @foreach($difficulties as $level => $label)
                                        <option value="{{ $level }}" {{ old('difficulty_level', $model->difficulty_level) == $level ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('difficulty_level')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">
                                    Facile: Modèles très connus • Moyen: Modèles assez connus • Difficile: Modèles rares
                                    ou peu connus
                                </p>
                            </div>
                        </div>

                        <!-- URL de l'image -->
                        <div>
                            <label for="image_url" class="block text-sm font-medium text-gray-700">
                                URL de l'image
                            </label>
                            <input type="url" id="image_url" name="image_url"
                                value="{{ old('image_url', $model->image_url) }}"
                                placeholder="https://example.com/car-image.jpg"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('image_url') border-red-300 @enderror">
                            @error('image_url')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Prévisualisation de l'image -->
                        <div id="image-preview" class="{{ $model->image_url ? '' : 'hidden' }}">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Prévisualisation de l'image
                            </label>
                            <img id="preview-image" src="{{ $model->image_url }}" alt="Prévisualisation"
                                class="h-48 w-auto object-cover rounded-lg border">
                        </div>

                        <!-- Boutons -->
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('admin.models.index') }}"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Annuler
                            </a>
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Prévisualisation de l'image
        document.getElementById('image_url').addEventListener('input', function () {
            const url = this.value;
            const preview = document.getElementById('image-preview');
            const image = document.getElementById('preview-image');

            if (url && isValidUrl(url)) {
                image.src = url;
                preview.classList.remove('hidden');

                image.onerror = function () {
                    preview.classList.add('hidden');
                };
            } else {
                preview.classList.add('hidden');
            }
        });

        function isValidUrl(string) {
            try {
                new URL(string);
                return true;
            } catch (_) {
                return false;
            }
        }
    </script>
</x-admin-layout>