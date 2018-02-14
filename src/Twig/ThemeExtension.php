<?php

namespace Olla\Theme\Twig;


class ThemeExtension extends \Twig_Extension
{
    /**
    * @return array
    */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('layout', array($this, 'js'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('theme_js', array($this, 'js'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('theme_css', array($this, 'css'), array('is_safe' => array('html')))
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
        return 'theme_extension';
    }
}