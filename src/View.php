<?php
namespace Olla\Theme;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Twig\Environment as TwigEnvironment;

final class View {
	
    public function render(string $format, array $data, string $operationId) {
    	print_r($operationId);
        return new JsonResponse(['hai' => true]);
    }
    
}
