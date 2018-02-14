<?php
namespace Olla\Theme;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Twig\Environment as TwigEnvironment;
use Olla\Theme\Metadata;
final class View {
	
	protected $design;
	public function __construct(Design $design, Metadata $metadata) {
		$this->design = $design;
	}
	
    public function render(string $format, array $data, string $operationId) {
        return new JsonResponse(['hai' => $operationId]);
    }
}
