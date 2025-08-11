<?php

namespace App\Utilities;

class TreeBuilder
{
    private array $globalCallbacks = ["treehouse"];
    /**
     * Build a multi dimensional array with associative arrays from filepaths
     * @param array $files <p>
     * The single dimensional array containing an array with paths 
     * implode as glue would be
     * the second parameter and thus, the bad prototype would be used.
     * </p>
     * @param array|null $array <p>
     * The array of strings to implode.
     * </p>
     * @return string a string containing a string representation of all the array
     * elements in the same order, with the glue string between each element.
     */
    public static function buildTree(array $files, ?array $applyGlobal = null, string $pathKey = 'path', bool $hasKeys = true): array
    {
        $tree = [];

        foreach ($files as $key => $file) {
            $parts = explode('/', $hasKeys ? $file[$pathKey] : $file);
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

    public static function buildTree5(array $items): array
    {
        $tree = [];

        foreach ($items as $item) {
            $parts = explode('/', $item['path']);
            $current = &$tree;

            foreach ($parts as $index => $part) {
                $isLast = $index === array_key_last($parts);
                $type = $isLast ? ($item['type'] === 'blob' ? 'File' : 'Folder') : 'Folder';

                $found = false;
                foreach ($current as &$child) {
                    if ($child['data']['name'] === $part) {
                        $current = &$child['children'];
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    $node = [
                        'key' => $type !== 'Folder' ? $item['path'] . ':' . $item['sha'] : ':folder:' . uniqid(), // folders hebben geen sha, dus gebruik iets unieks
                        'data' => [
                            'name' => $part,
                            'type' => $type,
                            'size' => ($type === 'File') ? $item['size'] : '-'
                        ]
                    ];
                    if ($type === 'Folder') {
                        $node['children'] = [];
                    }
                    $current[] = $node;

                    // Verplaats pointer naar nieuw toegevoegde node's children
                    if ($type === 'Folder') {
                        $current = &$current[array_key_last($current)]['children'];
                    }
                }
            }
        }

        // Recursieve sorteerfunctie
        $sortTree = function (&$nodes) use (&$sortTree) {
            foreach ($nodes as &$node) {
                if (isset($node['children'])) {
                    $sortTree($node['children']);
                }
            }

            usort($nodes, function ($a, $b) {
                // Folders eerst
                if ($a['data']['type'] === 'Folder' && $b['data']['type'] !== 'Folder') {
                    return -1;
                }
                if ($a['data']['type'] !== 'Folder' && $b['data']['type'] === 'Folder') {
                    return 1;
                }

                // Alfabetisch sorteren op naam
                return strcasecmp($a['data']['name'], $b['data']['name']);
            });
        };

        $sortTree($tree);

        return $tree;
    }
}
