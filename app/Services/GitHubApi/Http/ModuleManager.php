<?php

namespace App\Services\GitHubApi\Http;

use App\Services\GitHubApi\Abstracts\HttpModule;
use App\Services\GitHubApi\Traits\ShortNames;
use InvalidArgumentException;

class ModuleManager
{
    use ShortNames;
    private array $modules = [];

    public function add(HttpModule|array $modules, bool $first = false): void
    {
        if (!is_array($modules)) {
            $modules = [$modules];
        }

        foreach ($modules as $module) {
            if (!$module instanceof HttpModule) {
                $type = is_object($module) ? get_class($module) : gettype($module);
                throw new InvalidArgumentException(sprintf("Invalid type in array. %s expected, %s given", HttpModule::class, $type));
            }
            $this->addModule($module);
            
            if ($first) {
                $this->modules = array_reverse($this->modules, true);
            }
        }
    }

    public function delete(string|HttpModule $module): bool
    {
        $name = is_string($module) ? $module : $module->className;

        if (! isset($this->modules[$name])) {
            return false;
        }

        unset($this->modules[$name]);
        return true;
    }

    public function clear()
    {
        $this->modules = [];
    }

    public function get(): array
    {
        return $this->modules;
    }

    private function addModule(HttpModule $module): void
    {
        $implements = class_implements($module);

        if (count($implements) > 0) {
            foreach ($implements as $interface) {
                if (str_contains($interface, 'GitHubApi\\Contracts\\Handles')) {
                    $this->modules[$module->className] = $module;
                    array_reverse($this->modules);
                    return;
                }
            }
        }

        throw new InvalidArgumentException('Module contains no implementations for handling middleware');
    }
}
