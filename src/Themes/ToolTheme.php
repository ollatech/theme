<?php
namespace Olla\Theme\Themes;

use Olla\Theme\ThemeInterface;


final class ToolTheme  implements ThemeInterface{
    protected $assets;
	public function __construct(array $assets = []) {
        $this->assets = $assets;
	}
    public function name() {
        return "tool_theme";
    }
	public function paths():array {
        return [];
    }
    public function assets():array {
        return [];
    }
    public function contexts():array {
        return [];
    }
}
