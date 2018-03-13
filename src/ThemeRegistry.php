<?php

namespace Olla\Theme;


class ThemeRegistry
{
    /**
     * @var array
     */
    protected $themes = [];

    public function addTheme(string $name, $service) {
    	$this->themes[$name] = $service;
    }

    public function get(string $name) {
        return $this->themes[$name];
    }

    public function has(string $name) {
        if(isset($this->themes[$name])) {
            return true;
        }
        return false;
    }

    public function all() {
        return $this->themes;
    }
}