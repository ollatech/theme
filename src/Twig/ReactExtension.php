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
    public function reactRenderComponent($componentName, array $options = array())
    {
        $props = isset($options['props']) ? $options['props'] : array();
        $propsArray = is_array($props) ? $props : json_decode($props);

        $str = '';
        $data = array(
            'component_name' => $componentName,
            'props' => $propsArray,
            'dom_id' => 'sfreact-'.uniqid('reactRenderer', true),
            'trace' => $this->shouldTrace($options),
        );


        if ($this->shouldRenderClientSide($options)) {
            $str .= $this->renderContext();
            $str .=  sprintf(
                '<script type="application/json" class="js-react-on-rails-component" data-component-name="%s" data-dom-id="%s">%s</script>',
                $data['component_name'],
                $data['dom_id'],
                json_encode($data['props'])
            );
        }
        $str .= '<div id="'.$data['dom_id'].'">';
        if ($this->shouldRenderServerSide($options)) {
            $rendered = $this->renderer->render(
                $data['component_name'],
                json_encode($data['props']),
                $data['dom_id'],
                $this->registeredStores,
                $data['trace']
            );
            $str .= $rendered['evaluated'].$rendered['consoleReplay'];
        }
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