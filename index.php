<?php

namespace Wealf;

class WordPress_Extend_Admin_List_Filter
{
    /**
     * Filters
     * 
     * @var array
     */
    private $filters = [];

    /**
     * Constructor
     * 
     * @return void
     */
    public function __construct()
    {
        // Require files
        $this->require_files();

        // Init hooks
        $this->init_hooks();
    }

    /**
     * Load all required files
     * 
     * @return void
     */
    public function require_files()
    {
        require_once 'inc/helpers.php';
        require_once 'inc/ajax.php';
        require_once 'inc/classes/field.php';
    }

    /**
     * Init hooks
     * 
     * @return void
     */
    public function init_hooks()
    {
        add_action('restrict_manage_posts', [$this, 'render_filters'], 10, 2);
        add_action('restrict_manage_users', [$this, 'render_filters'], 10, 1);
        add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);

        add_filter('parse_query', [$this, 'parse_query']);
        add_filter('pre_get_users', [$this, 'parse_query']);
    }

    /**
     * Load admin scripts and styles
     * 
     * @return void
     */
    public function admin_enqueue_scripts()
    {
        wp_enqueue_style('wealf-admin-tomselect', wealf_get_file('assets/css/tom-select.css'));
        wp_enqueue_style('wealf-admin-app', wealf_get_file('assets/css/app.css'));
        wp_enqueue_script('wealf-admin-tomselect', wealf_get_file('assets/js/tom-select.complete.min.js'), ['jquery'], false, true);

        wp_enqueue_script('wealf-admin-app', wealf_get_file('assets/js/app.js'), ['jquery'], false, true);

        wp_localize_script('wealf-admin-app', 'wealf', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'ajax_nonce' => wp_create_nonce('wealf_ajax_nonce'),
        ]);
    }

    /**
     * Add filter to filters
     * 
     * @param string $name Filter id
     * @param array $args Filter arguments
     * 
     * @return void
     */
    public function add_filter($name, $args = [])
    {
        $args['post_type'] = isset($args['post_type']) ? $args['post_type'] : 'post';
        $args['scope'] = isset($args['scope']) ? $args['scope'] : 'post_type';

        $this->filters[$name] = $args;
    }

    /**
     * Get filters
     * 
     * @param string $scope Filter scope
     * 
     * @return array
     */
    public function get_filters($scope = 'post_type')
    {
        return array_filter($this->filters, function ($filter) use ($scope) {
            return $filter['scope'] == $scope;
        });
    }

    /**
     * Render filters
     * 
     * @return void
     */
    public function render_filters($ctx)
    {
        global $typenow;
        global $pagenow;

        $scope = $pagenow == 'users.php' ? 'users' : 'post_type';

        $filters = $this->get_filters($scope);

        foreach ($filters as $name => $args) {
            if (($scope == 'users' && $ctx == 'top') || ($typenow == $args['post_type'])) {
                $field = new Field($name, $args);
                $field->render();
            }
        }

        if ($scope == 'users' && $ctx == 'top') {
            submit_button(__('Filter'), null, $ctx, false);
        }
    }

    /**
     * Prepare filter to be used in query
     * 
     * @param string $key Filter key
     * @param mixed $context Filter context
     * @param mixed $value Filter value
     * 
     * @return mixed
     */
    public function prepare_filter($key, $context, $value = null)
    {
        if (!is_array($context)) {
            $context = str_replace('{selected_value}', $value, $context);

            return $context;
        }

        foreach ($context as $context_key => $context_value) {
            $context[$context_key] = $this->prepare_filter($key, $context_value, $value);
        }

        return $context;
    }

    /**
     * Parse query
     * 
     * @param object $query Query object
     * 
     * @return object
     */
    public function parse_query($query)
    {
        global $pagenow;

        if (is_admin() && ($pagenow == 'edit.php' || $pagenow == 'users.php')) {
            $filters = $this->get_filters($pagenow == 'users.php' ? 'users' : 'post_type');

            foreach ($filters as $name => $args) {
                if (wealf_array_element($name, $_GET)) {
                    $filter = $args['filter'];

                    foreach ($filter as $key => $value) {
                        $filter[$key] = $this->prepare_filter($key, $value, wealf_array_element($name, $_GET));
                        if (isset($query->query_vars[$key])) {
                            $query->query_vars[$key] = array_merge($query->query_vars[$key], $filter[$key]);
                        } else {
                            $query->query_vars[$key] = $filter[$key];
                        }
                    }
                }
            }

            return $query;
        }
    }
}
