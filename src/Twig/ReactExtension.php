<?php
namespace Olla\Theme\Twig;

class ReactExtension extends \Twig_Extension
{
 

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('react_view', array($this, 'render'), array('is_safe' => array('html'))),
        );
    }

    public function render($componentName, array $props = [], array $context = [], array $options = [], bool $server_side = false) {
        $dom_id = 'sfreact-'.uniqid('reactRenderer', true);
        $str = '';
        $str .= '<div id="'.$dom_id.'"></div>';
        return $str;
    }


    public function getName()
    {
        return 'react_view_extension';
    }
}