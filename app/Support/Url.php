<?php

namespace App\Support;

use Illuminate\Support\Str;

class Url
{
    /**
     * Query parameters that only exist for tracking and never change what a link points at.
     */
    private const TRACKING_PARAMS = ['fbclid', 'gclid', 'mc_cid', 'mc_eid', 'ref', 'ref_src'];

    /**
     * The host shown next to a story title, without "www.".
     */
    public static function domain(string $url): ?string
    {
        $host = parse_url($url, PHP_URL_HOST);

        return $host ? Str::chopStart(Str::lower($host), 'www.') : null;
    }

    /**
     * A fixed-length fingerprint of the normalised URL, stored and indexed for duplicate checks.
     */
    public static function hash(string $url): string
    {
        return sha1(static::key($url));
    }

    /**
     * A normalised form of a URL used to spot the same link being submitted twice:
     * scheme, "www.", fragment, trailing slash and tracking parameters are ignored.
     */
    public static function key(string $url): string
    {
        $parts = parse_url(trim($url));

        if (! $parts || ! isset($parts['host'])) {
            return Str::lower(trim($url));
        }

        $key = Str::chopStart(Str::lower($parts['host']), 'www.');

        if (isset($parts['port']) && ! in_array($parts['port'], [80, 443])) {
            $key .= ':'.$parts['port'];
        }

        $key .= rtrim($parts['path'] ?? '', '/');

        if (isset($parts['query'])) {
            parse_str($parts['query'], $query);
            $query = array_filter(
                $query,
                fn ($name) => ! Str::startsWith($name, 'utm_') && ! in_array($name, self::TRACKING_PARAMS),
                ARRAY_FILTER_USE_KEY,
            );
            ksort($query);

            if ($query) {
                $key .= '?'.http_build_query($query);
            }
        }

        return $key;
    }
}
