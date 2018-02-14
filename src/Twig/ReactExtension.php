<?php

namespace Olla\Theme\Twig;


class ReactExtension extends \Twig_Extension
{
    /**
    * @return array
    */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('react_render', array($this, 'render'), array('is_safe' => array('html')))
        );
    }


    /**
     * @param string $componentName
     * @param array  $options
     *
     * @return string
     */
    public function render()
    {
        $str = '';
        $str .= '<div id="sdfsdfdsf">';
        $str = 'sdfdsfdsfdsfdf';
        $str .= '</div>';
        return $str;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'react_render_extension';
    }
}