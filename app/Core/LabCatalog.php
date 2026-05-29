<?php

namespace App\Core;

/**
 * LabCatalog – single source of truth for every training lab.
 *
 * Scans labs directory once per request and merges the dynamic
 * level count with a static metadata registry (icon, colour, difficulty, etc.).
 * All views should call LabCatalog::all() instead of hard-coding lab lists.
 */
class LabCatalog
{
    /**
     * Static metadata that cannot reasonably be inferred from challenge maps.
     * Add a new entry here whenever a new lab directory is created.
     */
    private static array $meta = [
        'xss' => [
            'title'       => 'Cross-Site Scripting',
            'short'       => 'XSS',
            'icon'        => 'fa-code',
            'color'       => 'amber',
            'difficulty'  => 'Medium',
            'description' => 'Learn reflected, stored, and DOM-based XSS attacks in a simulated messaging console.',
            'tags'        => ['Reflected', 'Stored', 'DOM'],
            'patch_page'  => 'patch_xss',
        ],
        'sqli' => [
            'title'       => 'SQL Injection',
            'short'       => 'SQLi',
            'icon'        => 'fa-database',
            'color'       => 'red',
            'difficulty'  => 'Hard',
            'description' => 'Exploit insecure backend queries to bypass credentials or dump table values.',
            'tags'        => ['Auth Bypass', 'Union Extract'],
            'patch_page'  => 'patch_sqli',
        ],
        'file_upload' => [
            'title'       => 'File Infiltration',
            'short'       => 'Upload',
            'icon'        => 'fa-upload',
            'color'       => 'emerald',
            'difficulty'  => 'Medium',
            'description' => 'Evade filter limits to load web shells and invoke server-side control.',
            'tags'        => ['Extensions', 'MIME Spoof'],
            'patch_page'  => 'patch_fileupload',
        ],
        'csrf' => [
            'title'       => 'Request Forgery',
            'short'       => 'CSRF',
            'icon'        => 'fa-exchange-alt',
            'color'       => 'purple',
            'difficulty'  => 'Easy',
            'description' => 'Perform unauthorized state-changing requests by exploiting missing CSRF tokens.',
            'tags'        => ['Basic CSRF', 'Prediction'],
            'patch_page'  => null,
        ],
        'xxe' => [
            'title'       => 'XML External Entity',
            'short'       => 'XXE',
            'icon'        => 'fa-file-code',
            'color'       => 'orange',
            'difficulty'  => 'Hard',
            'description' => 'Exploit XML parsers to extract sensitive local files and execute OOB exfiltration.',
            'tags'        => ['Basic XXE', 'Blind OOB'],
            'patch_page'  => null,
        ],
        'ssrf' => [
            'title'       => 'Server-Side Request Forgery',
            'short'       => 'SSRF',
            'icon'        => 'fa-globe',
            'color'       => 'blue',
            'difficulty'  => 'Medium',
            'description' => 'Trick the server into making requests to internal services it should never reach.',
            'tags'        => ['Internal Scan', 'Loopback'],
            'patch_page'  => null,
        ],
        'idor' => [
            'title'       => 'Insecure Direct Object Ref',
            'short'       => 'IDOR',
            'icon'        => 'fa-id-card',
            'color'       => 'yellow',
            'difficulty'  => 'Easy',
            'description' => 'Access other users\' resources by manipulating object identifiers.',
            'tags'        => ['Profile Access', 'ID Enumeration'],
            'patch_page'  => null,
        ],
        'path_traversal' => [
            'title'       => 'Path Traversal',
            'short'       => 'Path',
            'icon'        => 'fa-folder-open',
            'color'       => 'teal',
            'difficulty'  => 'Easy',
            'description' => 'Break out of the intended directory to read sensitive system files.',
            'tags'        => ['Directory Escape', '/etc/passwd'],
            'patch_page'  => null,
        ],
        'deserialization' => [
            'title'       => 'Insecure Deserialization',
            'short'       => 'Deser',
            'icon'        => 'fa-cube',
            'color'       => 'indigo',
            'difficulty'  => 'Hard',
            'description' => 'Inject manipulated serialized objects to escalate privileges or execute code.',
            'tags'        => ['Object Injection', 'Role Escalation'],
            'patch_page'  => null,
        ],
        'jwt' => [
            'title'       => 'JSON Web Token Attacks',
            'short'       => 'JWT',
            'icon'        => 'fa-key',
            'color'       => 'cyan',
            'difficulty'  => 'Medium',
            'description' => 'Forge JWT tokens by exploiting algorithm confusion or weak secret keys.',
            'tags'        => ['None Algorithm', 'Weak Secret'],
            'patch_page'  => null,
        ],
    ];

    /** Color palette mapping to Tailwind class fragments used in views */
    public static array $colorMap = [
        'amber'   => ['bg' => 'bg-amber-500/10',   'border' => 'border-amber-500/20',   'text' => 'text-amber-500',   'badge_bg' => 'bg-amber-500/10',   'badge_text' => 'text-amber-400',   'badge_border' => 'border-amber-500/25'],
        'red'     => ['bg' => 'bg-red-500/10',     'border' => 'border-red-500/20',     'text' => 'text-red-500',     'badge_bg' => 'bg-red-500/10',     'badge_text' => 'text-red-400',     'badge_border' => 'border-red-500/25'],
        'emerald' => ['bg' => 'bg-emerald-500/10', 'border' => 'border-emerald-500/20', 'text' => 'text-emerald-500', 'badge_bg' => 'bg-emerald-500/10', 'badge_text' => 'text-emerald-400', 'badge_border' => 'border-emerald-500/25'],
        'purple'  => ['bg' => 'bg-purple-500/10',  'border' => 'border-purple-500/20',  'text' => 'text-purple-500',  'badge_bg' => 'bg-purple-500/10',  'badge_text' => 'text-purple-400',  'badge_border' => 'border-purple-500/25'],
        'orange'  => ['bg' => 'bg-orange-500/10',  'border' => 'border-orange-500/20',  'text' => 'text-orange-500',  'badge_bg' => 'bg-orange-500/10',  'badge_text' => 'text-orange-400',  'badge_border' => 'border-orange-500/25'],
        'blue'    => ['bg' => 'bg-blue-500/10',    'border' => 'border-blue-500/20',    'text' => 'text-blue-500',    'badge_bg' => 'bg-blue-500/10',    'badge_text' => 'text-blue-400',    'badge_border' => 'border-blue-500/25'],
        'yellow'  => ['bg' => 'bg-yellow-500/10',  'border' => 'border-yellow-500/20',  'text' => 'text-yellow-500',  'badge_bg' => 'bg-yellow-500/10',  'badge_text' => 'text-yellow-400',  'badge_border' => 'border-yellow-500/25'],
        'teal'    => ['bg' => 'bg-teal-500/10',    'border' => 'border-teal-500/20',    'text' => 'text-teal-500',    'badge_bg' => 'bg-teal-500/10',    'badge_text' => 'text-teal-400',    'badge_border' => 'border-teal-500/25'],
        'indigo'  => ['bg' => 'bg-indigo-500/10',  'border' => 'border-indigo-500/20',  'text' => 'text-indigo-500',  'badge_bg' => 'bg-indigo-500/10',  'badge_text' => 'text-indigo-400',  'badge_border' => 'border-indigo-500/25'],
        'cyan'    => ['bg' => 'bg-cyan-500/10',    'border' => 'border-cyan-500/20',    'text' => 'text-cyan-500',    'badge_bg' => 'bg-cyan-500/10',    'badge_text' => 'text-cyan-400',    'badge_border' => 'border-cyan-500/25'],
    ];

    /**
     * Build and return the full catalog.
     *
     * @return array<string, array{
     *   slug: string, title: string, short: string, icon: string,
     *   color: string, difficulty: string, description: string,
     *   tags: string[], patch_page: ?string,
     *   levels: array, total: int
     * }>
     */
    public static function all(): array
    {
        static $catalog = null;
        if ($catalog !== null) {
            return $catalog;
        }

        $catalog = [];
        $labsDir = defined('ROOT') ? ROOT . '/labs' : dirname(__DIR__, 2) . '/labs';

        if (!is_dir($labsDir)) {
            return $catalog;
        }

        foreach (scandir($labsDir) as $slug) {
            if ($slug === '.' || $slug === '..' || !is_dir("$labsDir/$slug")) {
                continue;
            }

            $mapFile = "$labsDir/$slug/challenge_map.php";
            if (!file_exists($mapFile)) {
                continue;
            }

            // Safely load the map; it may use $challengeMap variable or return directly
            $map = (function () use ($mapFile) {
                $challengeMap = null;
                $result = require $mapFile;
                // Some maps assign $challengeMap and return it; others just return
                if (is_array($result)) return $result;
                if (is_array($challengeMap)) return $challengeMap;
                return [];
            })();

            if (!is_array($map) || empty($map)) {
                continue;
            }

            // Count only lvlN keys (ignore numeric aliases like '1', '2')
            $levels = array_filter($map, fn($k) => preg_match('/^lvl\d+$/', $k), ARRAY_FILTER_USE_KEY);
            $total  = count($levels);

            $meta = self::$meta[$slug] ?? [
                'title'       => ucwords(str_replace('_', ' ', $slug)),
                'short'       => strtoupper($slug),
                'icon'        => 'fa-flask',
                'color'       => 'blue',
                'difficulty'  => 'Medium',
                'description' => "Security lab: $slug",
                'tags'        => [],
                'patch_page'  => null,
            ];

            $catalog[$slug] = array_merge($meta, [
                'slug'   => $slug,
                'levels' => $levels,
                'total'  => $total,
            ]);
        }

        return $catalog;
    }

    /**
     * Load a single challenge_map.php and return a normalised array.
     * Handles two authoring styles:
     *   a) return ['lvl1' => ClassName, ...];         (new style)
     *   b) $challengeMap = [...]; return $challengeMap; (old $-variable style)
     * Also handles rich maps: ['lvl1' => ['class' => ..., 'name' => ...]]
     */
    private static function loadMap(string $mapFile): array
    {
        // Use a helper function so the require runs in its own local scope
        // where $challengeMap is pre-declared and the file can assign it.
        $raw = self::requireMap($mapFile);

        if (!is_array($raw)) {
            return [];
        }

        // Normalise rich entries ['lvl1' => ['class' => ..., ...]] to just 'lvl1' => ClassName
        $normalised = [];
        foreach ($raw as $key => $value) {
            if (is_array($value) && isset($value['class'])) {
                $normalised[$key] = $value['class'];
            } elseif (is_string($value)) {
                $normalised[$key] = $value;
            }
        }

        return $normalised;
    }

    /**
     * Require a map file in a dedicated scope so that both variable-based
     * ($challengeMap = [...]) and direct-return (return [...]) maps work.
     */
    private static function requireMap(string $mapFile): array
    {
        // Pre-declare $challengeMap so the old-style files can assign to it
        $challengeMap = null;

        // Swallow errors from class_exists() calls inside old-style map files
        $prev = set_error_handler(static function () { return true; });
        try {
            $result = @require $mapFile;
        } catch (\Throwable $e) {
            $result = null;
        } finally {
            set_error_handler($prev);
        }

        if (is_array($result) && !empty($result)) {
            return $result;
        }
        if (is_array($challengeMap) && !empty($challengeMap)) {
            return $challengeMap;
        }

        return [];
    }

    /**
     * Return a single lab entry, or null if not found.
     */
    public static function get(string $slug): ?array
    {
        return self::all()[$slug] ?? null;
    }

    /**
     * Total number of challenges across all labs.
     */
    public static function grandTotal(): int
    {
        return array_sum(array_column(self::all(), 'total'));
    }
}
