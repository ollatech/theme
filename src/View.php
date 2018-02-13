<?php
namespace Olla\Theme;


final class View {
	
    public function render(string $format, array $data, string $template = null, array $options = []) {
       
        return new JsonResponse(['hai' => true]);
    }
}
