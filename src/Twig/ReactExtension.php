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
    public function render(string $js = null, array $props = [])
    {
        $data = array(
            'props' => $props,
            'dom_id' => 'sfreact-'.uniqid('reactRenderer', true)
        );

        $str = '';
        $str .= '<div id="'.$data['dom_id'].'">';
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