<?php

namespace App\Utilities;

class TreeBuilder
{
    public static function buildTree($files)
    {
        $tree = [];

        foreach ($files as $key => $file) {
            $parts = explode('/', $file['path']);
            $current = &$tree;

            foreach ($parts as $part) {
                if (! isset($current[$part])) {
                    $current[$part] = [];
                }
                $current = &$current[$part];
            }
            $current = $key;
        }

        return $tree;
    }
}
