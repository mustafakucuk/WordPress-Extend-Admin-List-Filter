<?php

namespace Wealf;

/**
 * Class Field
 * 
 * @package Wealf
 */

class Field
{
    /**
     * Field name
     * 
     * @var string
     */
    private $name;

    /**
     * Field arguments
     * 
     * @var array
     */
    private $args = [];

    /**
     * Allowed field types
     * 
     * @var array
     */
    private $allowed_types = ['select', 'text'];

    /**
     * Default field arguments
     * 
     * @var array
     */
    private $default_args = [
        'label' => '',
        'type' => 'select',
        'ui' => false,
        'ui_options' => [],
        'ajax' => false,
        'callback' => '',
        'options' => [],
        'class' => 'wealf-filter-field',
        'post_type' => 'post',
    ];

    /**
     * Field constructor
     * 
     * @param string $name Field name
     * @param array $args Field arguments
     */
    public function __construct($name, $args)
    {
        // Add default label
        $this->default_args['label'] = esc_html__('Filter', 'wealf');

        // Set name
        $this->name = $name;

        // Filter arguments and set them
        $this->args = $this->filter_args($args);
    }

    /**
     * Filter arguments
     * 
     * @param array $args Field arguments
     * 
     * @return array
     */
    public function filter_args($args)
    {
        $args = wp_parse_args($args, $this->default_args);

        // check type and set default if not allowed
        if (!in_array($args['type'], $this->allowed_types)) {
            $args['type'] = $this->default_args['type'];
        }

        // set ui to true if ajax is true
        if (!wealf_array_element('ui', $args) && wealf_array_element('ajax', $args)) {
            $args['ui'] = true;
        }

        return $args;
    }

    /**
     * Render field
     * 
     * @param bool $echo Echo or return
     * 
     * @return string
     */
    public function render($echo = true)
    {
        $type = $this->args['type'];
        $html = '';

        if (method_exists($this, $type)) {
            $html = $this->$type();
        }


        if ($echo) {
            echo $html;
        }

        return $html;
    }

    /**
     * Render select field
     * 
     * @return string
     */
    public function select()
    {
        $options = $this->args['options'];
        $ui = $this->args['ui'];
        $ui_options = $this->args['ui_options'];
        $callback = $this->args['callback'];
        $name = $this->name;
        $label = $this->args['label'];
        $ajax = $this->args['ajax'];
        $class = $this->args['class'];

        if ($ui) {
            $class = $class . ' wealf-select-ui';
        }

        $selected_value = wealf_array_element($name, $_GET);

        $ui_options = wp_parse_args($ui_options, [
            'placeholder' => $label,
            'create' => false,
        ]);

        $html = sprintf(
            '<select name="%s" id="%s" class="%s" data-is-ajax="%s" data-callback="%s" data-ui-options=\'%s\' data-selected="%s">',
            $name,
            $name,
            $class,
            $ajax ? 'true' : 'false',
            $callback,
            json_encode($ui_options),
            $selected_value
        );

        if (!$ajax) {
            $html .= sprintf('<option value="">%s</option>', $label);
            foreach ($options as $key => $value) {
                $selected = selected($key, wealf_array_element($name, $_GET), false);
                $html .= sprintf('<option value="%s" %s>%s</option>', $key, $selected, $value);
            }
        }

        $html .= '</select>';

        return $html;
    }

    /**
     * Render text field
     * 
     * @return string
     */
    public function text()
    {
        $name = $this->name;
        $label = $this->args['label'];
        $class = $this->args['class'];
        $value = wealf_array_element($name, $_GET);

        $html = sprintf('<input type="text" name="%s" id="%s" value="%s" placeholder="%s" class="%s">', $name, $name, $value, $label, $class);

        return $html;
    }
}
