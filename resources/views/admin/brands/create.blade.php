<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    ➕ Créer une nouvelle marque
                </h2>
                <p class="mt-1 text-sm text-gray-600">Ajoutez une nouvelle marque automobile à votre plateforme</p>
            </div>
            <a href="{{ route('admin.brands.index') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 transition ease-in-out duration-150">
                ← Retour à la liste
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <form method="POST" action="{{ route('admin.brands.store') }}" class="space-y-6 p-6">
                    @csrf

                    <!-- Nom de la marque -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            Nom de la marque *
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            placeholder="Ex: Toyota, BMW, Mercedes..."
                            class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('name') border-red-300 @enderror">
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Pays -->
                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700">
                            Pays d'origine
                        </label>
                        <input type="text" name="country" id="country" value="{{ old('country') }}"
                            placeholder="Ex: Japon, Allemagne, France..."
                            class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('country') border-red-300 @enderror">
                        @error('country')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Année de fondation -->
                    <div>
                        <label for="founded_year" class="block text-sm font-medium text-gray-700">
                            Année de fondation
                        </label>
                        <input type="number" name="founded_year" id="founded_year" value="{{ old('founded_year') }}"
                            min="1800" max="{{ date('Y') }}" placeholder="Ex: 1937"
                            class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('founded_year') border-red-300 @enderror">
                        @error('founded_year')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Informations supplémentaires -->
                    <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">
                                    Conseils pour une marque de qualité
                                </h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <ul class="list-disc list-inside space-y-1">
                                        <li>Utilisez le nom officiel de la marque</li>
                                        <li>Vérifiez l'orthographe avant de valider</li>
                                        <li>Les champs marqués d'un * sont obligatoires</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Boutons d'action -->
                    <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                        <a href="{{ route('admin.brands.index') }}"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Annuler
                        </a>
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            ✅ Créer la marque
                        </button>
                    </div>
                </form>
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