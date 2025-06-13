<?php

namespace App\Utilities;

class TreeBuilder
{
    public static function buildTree($files)
    {
        $tree = [];

        foreach ($files as $file) {
            $parts = explode('/', $file['path']);
            $current = &$tree;

            foreach ($parts as $part) {
                if (!isset($current[$part])) {
                    $current[$part] = [];
                }
                $current = &$current[$part];
            }
            $current['?'] = $file['sha'];
            $current['*'] = $file['path'];
        }

        return $tree;
    }
}
