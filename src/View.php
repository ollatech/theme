<?php
namespace Olla\Theme;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

final class View {
	
    public function render(string $format, array $data, string $operationId) {
        return new JsonResponse(['hai' => true]);
    }
    
}
