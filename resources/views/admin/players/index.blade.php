<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestion des Joueurs') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Filtres -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.players.index') }}" class="flex flex-wrap gap-4">
                        <div class="flex-1 min-w-64">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Rechercher un joueur..."
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <button type="submit"
                            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Filtrer
                        </button>
                        @if(request('search'))
                            <a href="{{ route('admin.players.index') }}"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Réinitialiser
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Liste des joueurs -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($players->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <a
                                                href="{{ request()->fullUrlWithQuery(['sort' => 'username', 'direction' => request('sort') == 'username' && request('direction') == 'asc' ? 'desc' : 'asc']) }}">
                                                Nom d'utilisateur
                                                @if(request('sort') == 'username')
                                                    <span class="ml-1">{{ request('direction') == 'asc' ? '↑' : '↓' }}</span>
                                                @endif
                                            </a>
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <a
                                                href="{{ request()->fullUrlWithQuery(['sort' => 'total_points', 'direction' => request('sort') == 'total_points' && request('direction') == 'asc' ? 'desc' : 'asc']) }}">
                                                Points totaux
                                                @if(request('sort') == 'total_points')
                                                    <span class="ml-1">{{ request('direction') == 'asc' ? '↑' : '↓' }}</span>
                                                @endif
                                            </a>
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <a
                                                href="{{ request()->fullUrlWithQuery(['sort' => 'games_played', 'direction' => request('sort') == 'games_played' && request('direction') == 'asc' ? 'desc' : 'asc']) }}">
                                                Parties jouées
                                                @if(request('sort') == 'games_played')
                                                    <span class="ml-1">{{ request('direction') == 'asc' ? '↑' : '↓' }}</span>
                                                @endif
                                            </a>
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Taux de victoire</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Streak actuel</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($players as $player)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $player->username }}</div>
                                                <div class="text-sm text-gray-500">ID: {{ $player->user_id }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ number_format($player->total_points_all, 1) }}</div>
                                                <div class="text-sm text-gray-500">Base:
                                                    {{ number_format($player->total_points, 1) }} | Diff:
                                                    {{ number_format($player->difficulty_points, 1) }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $player->games_played }}</div>
                                                <div class="text-sm text-gray-500">Gagnées: {{ $player->games_won }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $player->win_rate >= 70 ? 'bg-green-100 text-green-800' : ($player->win_rate >= 40 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                    {{ $player->win_rate }}%
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $player->current_streak }}</div>
                                                <div class="text-sm text-gray-500">Best: {{ $player->best_streak }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('admin.players.show', $player) }}"
                                                    class="text-blue-600 hover:text-blue-900">Voir détails</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $players->withQueryString()->links() }}
                        </div>
                    @else
                        <div class="text-center py-8">
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun joueur trouvé</h3>
                            <p class="mt-1 text-sm text-gray-500">Aucun joueur ne correspond à vos critères de recherche.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>