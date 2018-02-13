<?php
namespace Olla\Theme;


final class View {
	
    public function render(string $format, array $data, string $operationId) {
        return new JsonResponse(['hai' => true]);
    }
    
}
