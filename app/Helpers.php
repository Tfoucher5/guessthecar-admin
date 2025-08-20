<?php
// Créer le fichier app/helpers.php

if (!function_exists('getGuildName')) {
    /**
     * Récupérer le nom d'un serveur Discord à partir de son ID
     */
    function getGuildName($guildId)
    {
        if (!$guildId) {
            return 'Aucun serveur';
        }

        // Cache statique pour éviter les appels multiples dans la même requête
        static $guildCache = [];

        if (isset($guildCache[$guildId])) {
            return $guildCache[$guildId];
        }

        // Cache Laravel (Redis/File)
        $cacheKey = "guild_name_{$guildId}";
        $cached = \Cache::get($cacheKey);

        if ($cached) {
            $guildCache[$guildId] = $cached;
            return $cached;
        }

        // Appel API Discord
        try {
            $botToken = config('services.discord.bot_token');

            if (!$botToken) {
                $fallback = "Serveur #{$guildId}";
                $guildCache[$guildId] = $fallback;
                return $fallback;
            }

            $response = \Http::timeout(3)->withHeaders([
                'Authorization' => 'Bot ' . $botToken,
                'Content-Type' => 'application/json',
            ])->get("https://discord.com/api/v10/guilds/{$guildId}");

            if ($response->successful()) {
                $guildName = $response->json()['name'] ?? "Serveur #{$guildId}";

                // Cache pendant 24h
                \Cache::put($cacheKey, $guildName, 86400);
                $guildCache[$guildId] = $guildName;

                return $guildName;
            }

        } catch (\Exception $e) {
            \Log::warning("Erreur Discord API pour guild {$guildId}: " . $e->getMessage());
        }

        // Fallback si API échoue
        $fallback = "Serveur #{$guildId}";
        \Cache::put($cacheKey, $fallback, 3600); // Cache plus court pour retry
        $guildCache[$guildId] = $fallback;

        return $fallback;
    }
}

if (!function_exists('getGuildIcon')) {
    /**
     * Récupérer l'icône d'un serveur Discord (bonus)
     */
    function getGuildIcon($guildId)
    {
        if (!$guildId)
            return null;

        $cacheKey = "guild_icon_{$guildId}";
        $cached = \Cache::get($cacheKey);

        if ($cached !== null) {
            return $cached ?: null; // Retourne null si cache vide
        }

        try {
            $botToken = config('services.discord.bot_token');
            if (!$botToken)
                return null;

            $response = \Http::timeout(3)->withHeaders([
                'Authorization' => 'Bot ' . $botToken,
            ])->get("https://discord.com/api/v10/guilds/{$guildId}");

            if ($response->successful()) {
                $guild = $response->json();
                $iconUrl = $guild['icon']
                    ? "https://cdn.discordapp.com/icons/{$guildId}/{$guild['icon']}.png?size=64"
                    : null;

                \Cache::put($cacheKey, $iconUrl ?: '', 86400);
                return $iconUrl;
            }

        } catch (\Exception $e) {
            \Log::warning("Erreur Discord icon pour guild {$guildId}: " . $e->getMessage());
        }

        \Cache::put($cacheKey, '', 3600);
        return null;
    }
}