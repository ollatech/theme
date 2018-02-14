<?php
namespace Olla\Theme;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Twig\Environment as TwigEnvironment;
use Olla\Prisma\Metadata;

final class View {
    
    protected $twig;
	protected $design;
	protected $metadata;
	
	public function __construct(TwigEnvironment $twig, Design $design, Metadata $metadata) {
        $this->twig = $twig;
		$this->design = $design;
		$this->metadata = $metadata;
	}

    

    public function render(array $args = [], array $response = []) {
        $operationId = $args['operation_id'];
        $carrier = $args['carrier'];

    	$operation = $this->operation($carrier, $operationId);
        if(null === $template = $operation->getTemplate()) {
            $template = 'common';
        }

        if(null === $js = $operation->getJs()) {
            $js = null;
        }
        $design  = $this->design->get($carrier, $template, $js);

        $data = [
            'js' => $js,
            'data' => $response
        ];

        $template = $this->twig->render($design, $data);
        return  Response::create($template);
    }


    public function operation(string $carrier, string $operationId) {
    	switch ($carrier) {
    		case 'restapi':
    			return $this->metadata->operation($operationId);
    		case 'admin':
    			return $this->metadata->admin($operationId);
    		case 'frontend':
    			return $this->metadata->frontend($operationId);
    		default:
    			break;
    	}
    	echo "error";
    }
}
