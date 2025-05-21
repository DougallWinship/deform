<?php

declare(strict_types=1);

namespace Deform;

class Version
{
    /**
     * @return array [ 0 => {short version}, 1 => {full version} ]
     */
    public static function getGitVersions(): array
    {
        static $versions = null;
        if ($versions === null) {
            $full = trim(shell_exec('git describe --tags --always 2>/dev/null')) ?: '?';
            // Extract the "short" version by removing commit count and hash
            $short = preg_replace('/^v?([0-9]+\.[0-9]+\.[0-9]+).*$/', '$1', $full);
            $versions = [$short, $full];
        }
        return $versions;
    }
}
