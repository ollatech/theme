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
    	print_r($args);
    	$operation = $this->operation($args['carrier'], $args['operation_id']);
        return new JsonResponse($operation);
    }

    public function operation(string $carrier, string $operationId) {
    	switch ($carrier) {
    		case 'restapi':
    			return $this->metadata->operation($carrier, $operationId);
    			break;
    		case 'admin':
    			return $this->metadata->admin($carrier, $operationId);
    			break;
    		case 'frontend':
    			return $this->metadata->frontend($carrier, $operationId);
    			break;
    		default:
    			break;
    	}
    	echo "error";
    }
}
