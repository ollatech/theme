<?php
namespace Olla\Theme\Resolver;

use Olla\Prisma\Metadata;
use Symfony\Component\HttpFoundation\RequestStack;

final class ViewResolver implements Resolver
{
	protected $views = [];
	protected $requestStack;
    protected $negotiation;
    protected $formats;

    public function __construct(RequestStack $requestStack, Negotiation $negotiation) {
        $this->requestStack = $requestStack;
        $this->negotiation = $negotiation;
    }

	public function render(string $format, string $carrier, string $operationId, array $response = []) {
		if(isset($this->views[$format])) {
			return $this->views[$format]->render($carrier, $operationId, $response);
		}
		throw new Exception("Error Processing Request", 1);
	}
	public function addView(string $format, $serviceId) {
		$this->views[$format] = $serviceId;
	}
}
