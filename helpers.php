<?php
/**
 * Fonctions utilitaires partagées par toutes les pages.
 */

function initials(string $name): string
{
    $upperFirst = function (string $p): string {
        // Utilise mbstring si dispo (accents corrects), sinon fallback ASCII simple.
        return function_exists('mb_strtoupper')
            ? mb_strtoupper(mb_substr($p, 0, 1))
            : strtoupper(substr($p, 0, 1));
    };
    $parts = preg_split('/\s+/', trim($name));
    $letters = array_map($upperFirst, array_filter($parts));
    $result = implode('', array_slice($letters, 0, 2));
    return $result !== '' ? $result : '?';
}

function french_day(?int $timestamp = null): string
{
    $timestamp = $timestamp ?? time();
    $days = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
    return $days[(int) date('w', $timestamp)];
}

function french_date_short(?int $timestamp = null): string
{
    $timestamp = $timestamp ?? time();
    $months = ['', 'janvier', 'février', 'mars', 'avril', 'mai', 'juin',
               'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
    return date('d', $timestamp) . ' ' . $months[(int) date('n', $timestamp)];
}

function time_ago(?string $datetime): string
{
    if (!$datetime) {
        return '';
    }
    $diff = time() - strtotime($datetime);
    if ($diff < 60) {
        return "à l'instant";
    }
    if ($diff < 3600) {
        $m = (int) floor($diff / 60);
        return "il y a {$m} min";
    }
    if ($diff < 86400) {
        $h = (int) floor($diff / 3600);
        return "il y a {$h} h";
    }
    $days = (int) floor($diff / 86400);
    if ($days === 1) {
        return 'il y a 1 jour';
    }
    return "il y a {$days} jours";
}

/**
 * Retourne le SVG (chaîne) d'une icône ligne simple, taille 20x20, trait "currentColor".
 */
function icon_svg(string $name): string
{
    $icons = [
        'grid' => '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>',
        'alert' => '<path d="M10.3 3.6 2.7 16.5c-.4.7.1 1.5.9 1.5h13.8c.8 0 1.3-.8.9-1.5L10.6 3.6c-.4-.7-1.3-.7-1.7 0Z"/><line x1="12" y1="9" x2="12" y2="13"/><circle cx="12" cy="16" r="0.9" fill="currentColor" stroke="none"/>',
        'check' => '<rect x="3.5" y="3.5" width="17" height="17" rx="3"/><path d="M7.5 12l3 3 6-6"/>',
        'list' => '<line x1="4" y1="6" x2="9" y2="6"/><line x1="4" y1="12" x2="9" y2="12"/><line x1="4" y1="18" x2="9" y2="18"/><line x1="12" y1="6" x2="20" y2="6"/><line x1="12" y1="12" x2="20" y2="12"/><line x1="12" y1="18" x2="20" y2="18"/>',
        'gear' => '<circle cx="12" cy="12" r="3"/><path d="M19.4 13.5a1.7 1.7 0 0 0 .3 1.9l.1.1a2 2 0 1 1-2.9 2.9l-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.6v.2a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1.1-1.6 1.7 1.7 0 0 0-1.9.3l-.1.1a2 2 0 1 1-2.9-2.9l.1-.1a1.7 1.7 0 0 0 .3-1.9 1.7 1.7 0 0 0-1.6-1h-.2a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.6-1.1 1.7 1.7 0 0 0-.3-1.9l-.1-.1a2 2 0 1 1 2.9-2.9l.1.1a1.7 1.7 0 0 0 1.9.3h.1a1.7 1.7 0 0 0 1-1.6v-.2a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.6 1.7 1.7 0 0 0 1.9-.3l.1-.1a2 2 0 1 1 2.9 2.9l-.1.1a1.7 1.7 0 0 0-.3 1.9v.1a1.7 1.7 0 0 0 1.6 1h.2a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.6 1Z"/>',
        'help' => '<circle cx="12" cy="12" r="9"/><path d="M9.2 9a2.8 2.8 0 1 1 3.9 2.6c-.8.4-1.1.9-1.1 1.7v.4"/><circle cx="12" cy="16.5" r="0.9" fill="currentColor" stroke="none"/>',
        'logout' => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>',
        'search' => '<circle cx="10.5" cy="10.5" r="6.5"/><line x1="20" y1="20" x2="15.3" y2="15.3"/>',
        'bell' => '<path d="M6 8a6 6 0 1 1 12 0c0 4.5 1.5 6 1.5 6h-15S6 12.5 6 8Z"/><path d="M10 19a2 2 0 0 0 4 0"/>',
        'calendar' => '<rect x="3" y="4.5" width="18" height="16" rx="2"/><line x1="3" y1="9.5" x2="21" y2="9.5"/><line x1="8" y1="2.5" x2="8" y2="6.5"/><line x1="16" y1="2.5" x2="16" y2="6.5"/>',
        'clipboard' => '<rect x="5.5" y="4" width="13" height="17" rx="2"/><rect x="8.5" y="2" width="7" height="4" rx="1"/><line x1="8.5" y1="11" x2="15.5" y2="11"/><line x1="8.5" y1="15" x2="13" y2="15"/>',
        'plus' => '<line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>',
        'dots' => '<circle cx="5" cy="12" r="1.4" fill="currentColor" stroke="none"/><circle cx="12" cy="12" r="1.4" fill="currentColor" stroke="none"/><circle cx="19" cy="12" r="1.4" fill="currentColor" stroke="none"/>',
        'trash' => '<polyline points="4 7 6 7 20 7"/><path d="M9 7V4.5A1.5 1.5 0 0 1 10.5 3h3A1.5 1.5 0 0 1 15 4.5V7"/><path d="M18 7l-.8 12.5A2 2 0 0 1 15.2 21H8.8a2 2 0 0 1-2-1.9L6 7"/>',
        'edit' => '<path d="M12.5 5.5 16 3l3 3-2.5 3.5-9 9-4 1 1-4Z"/>',
        'invite' => '<path d="M12 8v8M8 12h8"/><circle cx="12" cy="12" r="9"/>',
    ];
    $body = $icons[$name] ?? '';
    return '<svg class="icon icon-' . htmlspecialchars($name) . '" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">' . $body . '</svg>';
}

/** Libellés/couleurs FR pour priorité et statut, réutilisés en PHP et exposés au JS. */
function priority_label(string $val): string
{
    return ['faible' => 'Faible', 'moderee' => 'Modérée', 'haute' => 'Haute'][$val] ?? $val;
}

function status_label(string $val): string
{
    return ['non_commence' => 'Non commencé', 'en_cours' => 'En cours', 'termine' => 'Terminé'][$val] ?? $val;
}
