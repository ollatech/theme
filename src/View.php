<?php
namespace Olla\Theme;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Twig\Environment as TwigEnvironment;
use Olla\Prisma\Metadata;

final class View {

	protected $design;
	protected $metadata;
	
	public function __construct(Design $design, Metadata $metadata) {
		$this->design = $design;
		$this->metadata = $metadata;
	}

    public function render(array $args = [], array $response = []) {
    	$operation = $this->metadata->operation($args['operation_id']);
        return new JsonResponse($operation);
    }
}
