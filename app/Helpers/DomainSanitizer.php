<?php
/**
 * DomainSanitizer.php
 *
 * @package   software
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   MIT
 */

namespace App\Helpers;

use App\Exceptions\InvalidUrlException;
use Illuminate\Support\Str;

class DomainSanitizer
{
    /**
     * @throws InvalidUrlException
     */
    public static function normalize(string $domain): string
    {
        $url    = parse_url($domain);
        $domain = ($url['host'] ?? '').($url['path'] ?? '');
        if (empty($domain)) {
            throw new InvalidUrlException(sprintf(
                '%s is not a valid URL.',
                $domain
            ));
        }

        $domain = Str::lower($domain);

        return static::untrailingSlash(static::stripWww($domain));
    }

    public static function stripWww(string $domain): string
    {
        if (str_starts_with($domain, 'www.')) {
            return substr($domain, 4);
        }

        return $domain;
    }

    public static function untrailingSlash(string $domain): string
    {
        return rtrim($domain, '/');
    }

    public static function isLocalUrl(string $url): bool
    {
        $url = trim(Str::lower($url));

        // Need to get the host...so let's add the scheme so we can use parse_url
        if (! str_starts_with($url, 'http://') && ! str_starts_with($url, 'https://')) {
            $url = 'https://'.$url;
        }

        $urlParts = parse_url($url);

        if (empty($urlParts['host'])) {
            return false;
        }

        if ($urlParts['host'] === 'localhost') {
            return true;
        }

        if (
            ip2long($urlParts['host']) !== false &&
            ! filter_var($urlParts['host'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)
        ) {
            return true;
        }

        foreach (['.dev', '.local', '.test'] as $tld) {
            if (str_ends_with($urlParts['host'], $tld)) {
                return true;
            }
        }

        if (substr_count($urlParts['host'], '.') > 1) {
            foreach (['dev.', '*.staging.', '*.test.', 'staging-*.', '*.wpengine.com'] as $subdomain) {
                if (str_contains($urlParts['host'], $subdomain)) {
                    return true;
                }
            }
        }

        return false;
    }
}
