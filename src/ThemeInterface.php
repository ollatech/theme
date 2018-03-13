<?php
namespace Olla\Theme;


interface ThemeInterface {
	public function name();
	public function assets(): array;
	public function paths(): array;
}

