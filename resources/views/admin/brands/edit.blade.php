<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Modifier la Marque') }} : {{ $brand->name }}
            </h2>
            <a href="{{ route('admin.brands.index') }}"
                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Retour à la liste
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.brands.update', $brand) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Nom -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">
                                Nom de la marque *
                            </label>
                            <input type="text" id="name" name="name" value="{{ old('name', $brand->name) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-300 @enderror">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Pays -->
                        <div>
                            <label for="country" class="block text-sm font-medium text-gray-700">
                                Pays d'origine
                            </label>
                            <input type="text" id="country" name="country" value="{{ old('country', $brand->country) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('country') border-red-300 @enderror">
                            @error('country')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Année de fondation -->
                        <div>
                            <label for="founded_year" class="block text-sm font-medium text-gray-700">
                                Année de fondation
                            </label>
                            <input type="number" id="founded_year" name="founded_year"
                                value="{{ old('founded_year', $brand->founded_year) }}" min="1800" max="{{ date('Y') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('founded_year') border-red-300 @enderror">
                            @error('founded_year')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- URL du logo -->
                        <div>
                            <label for="logo_url" class="block text-sm font-medium text-gray-700">
                                URL du logo
                            </label>
                            <input type="url" id="logo_url" name="logo_url"
                                value="{{ old('logo_url', $brand->logo_url) }}"
                                placeholder="https://example.com/logo.png"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('logo_url') border-red-300 @enderror">
                            @error('logo_url')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Prévisualisation du logo -->
                        <div id="logo-preview" class="{{ $brand->logo_url ? '' : 'hidden' }}">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Prévisualisation du logo
                            </label>
                            <img id="logo-image" src="{{ $brand->logo_url }}" alt="Prévisualisation"
                                class="h-20 w-20 object-cover rounded-lg border">
                        </div>

                        <!-- Boutons -->
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('admin.brands.index') }}"
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
        // Prévisualisation du logo
        document.getElementById('logo_url').addEventListener('input', function () {
            const url = this.value;
            const preview = document.getElementById('logo-preview');
            const image = document.getElementById('logo-image');

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