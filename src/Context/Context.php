<?php

namespace Olla\Theme;

use Symfony\Component\HttpFoundation\RequestStack;


class SymfonyContextProvider 
{
    private $requestStack;

    /**
     * __construct
     *
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * getContext
     *
     * @param boolean $serverSide whether is this a server side context
     * @return array the context information
     */
    public function getContext($serverSide)
    {
        $request = $this->requestStack->getCurrentRequest();
        return [
            'serverSide' => $serverSide,
            'href' => $request->getSchemeAndHttpHost().$request->getRequestUri(),
            'location' => $request->getRequestUri(),
            'scheme' => $request->getScheme(),
            'host' => $request->getHost(),
            'port' => $request->getPort(),
            'base' => $request->getBaseUrl(),
            'pathname' => $request->getPathInfo(),
            'search' => $request->getQueryString(),
        ];
    }
}
