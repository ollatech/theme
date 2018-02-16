<?php

namespace Olla\Theme\Twig;

/**
 * Class ReactRenderExtension
 */
class ReactExtension extends \Twig_Extension
{
    protected $serverSide = false;
    public function __construct()
    {

    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('react_component', array($this, 'render'), array('is_safe' => array('html'))),
        );
    }

    public function render(string $name, $props = [], $context = [], string $file = null) {
        $dom_id = 'sfreact-'.uniqid('reactRenderer', true);
        $str = '';
        $str .= '<div id="'.$dom_id.'"></div>';
        $str .= $this->broswer($dom_id, $name, json_encode($props), json_encode($context));
        $str .= '<div id="log"></div>';
        return $str;

    }
    /**
     * @return string
     */
    public function getName()
    {
        return 'twig_reactjs_extension';
    }
    
    protected function broswer($domId, $name, $props, $context)
    {
        $wrapperJs = <<<JS
(function() {
  return ReactOnApp.render(
    $domId, $name, $props, $context);
})();
JS;
return sprintf('<script type="application/json" >%s</script>',
                $wrapperJs
            );
        
    }
}
