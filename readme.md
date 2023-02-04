# WordPress Extend Admin List Filter

This library allows you to add a filter to the admin list view.

## Installation

1. Download the latest release from [here](https://)

2. Move the `wp-extend-admin-list-filter` folder to your theme folder.

3. Add the following code to your theme's `functions.php` file:

```php
require_once( 'wp-extend-admin-list-filter/index.php' );
```

## Images
<img width="645" alt="Screenshot 2023-02-05 at 00 05 21" src="https://user-images.githubusercontent.com/7347594/216789574-35ebed6f-f9e9-4761-a508-042e40a56d77.png">

<img width="646" alt="Screenshot 2023-02-05 at 00 06 37" src="https://user-images.githubusercontent.com/7347594/216789613-bc8358ae-de49-4f51-b2f8-1523083fb574.png">

## Usage

### Add a filter (Basic)

```php
$filter_instance = new WordPress_Extend_Admin_List_Filter();

// Add a filter to the post list view, it's will filter by the 'status' meta.

$filters->add_filter('filter_by_status', [
    'post_type' => 'post',
    'label' => 'Select Status',
    'options' => [
        'all' => 'All',
        'active' => 'Active',
        'inactive' => 'Inactive'
    ],
    'filter' => [
        'meta_query' => [
            [
                'key' => 'status',
                'value' => '{selected_value}', // {selected_value} will be replaced with the selected value from the filter.
                'compare' => '='
            ]
        ]
    ]
]);
```

### Add a filter (Users list)

````php
$filter_instance = new WordPress_Extend_Admin_List_Filter();

// Add a filter to the users list view, it's will filter by the 'status' meta.

$filters->add_filter('filter_by_status', [
    'scope' => 'user', // Set the scope to 'user' and the filter will be added to the users list.
    'label' => 'Select User Status',
    'options' => [
        'all' => 'All',
        'active' => 'Active',
        'inactive' => 'Inactive'
    ],
    'ui' => true, // Set to true to use the tomselect.js library.

    // The filter that will be used to filter the users.
    'filter' => [
        // You can use User_Query arguments here.
        'meta_query' => [
            [
                'key' => 'status',
                'value' => '{selected_value}',
                'compare' => '='
            ]
        ]
    ],
]);

$filters->add_filter('filter_by_search_term', [
    'scope' => 'users',
    'type' => 'text',
    'label' => 'Search Term'
    'filter' => [
        'search' => '*{selected_value}*',
        'search_columns' => [
            'user_login',
            'user_email',
            'user_nicename',
            'user_url',
            'display_name',
        ],
    ],
]);
````

### Add a filter (AJAX)

```php
$filter_instance = new WordPress_Extend_Admin_List_Filter();

$filters->add_filter('filter_user', [
    'post_type' => 'post',
    'label' => 'Select User',
    'callback' => 'get_user_ajax', // The callback function that will be called to get the options.
    'ajax' => true, // Set to true to use the ajax functionality.
    'ui' => true, // Set to true to use the tomselect.js library.

    // The options that will be passed to the tomselect.js library.
    'ui_options' => [
        'valueField' => 'id',
        'labelField' => 'display_name',
        'searchField' => ['display_name', 'id'],
    ],

    // The filter that will be used to filter the posts.
    'filter' => [
        // You can use WP_Query arguments here.
        'meta_query' => [
            [
                'key' => 'user',
                'value' => '{selected_value}',
                'compare' => '=',
            ]
        ],
    ],
]);

// Ajax callback function.
function get_user_ajax()
{
    $users = get_users([
        'fields' => ['ID', 'user_email', 'display_name'],
    ]);

    wp_send_json_success($users);
}
````

### All options

| Option       | Type      | Description                                                   | Default     | Required |
| ------------ | --------- | ------------------------------------------------------------- | ----------- | -------- |
| `post_type`  | `string`  | The post type to add the filter to.                           | `post`      | No       |
| `scope`      | `string`  | The scope of the filter, you can use 'post_type' or 'user'.   | `post_type` | No       |
| `type`       | `string`  | The type of the filter, you can use 'select' or 'input'.      | `select`    | No       |
| `label`      | `string`  | The label of the filter.                                      | `Select`    | No       |
| `options`    | `array`   | The options of the filter.                                    | `[]`        | No       |
| `callback`   | `string`  | The callback function that will be called to get the options. | `null`      | No       |
| `ajax`       | `boolean` | Set to true to use the ajax functionality.                    | `false`     | No       |
| `ui`         | `boolean` | Set to true to use the tomselect.js library.                  | `false`     | No       |
| `ui_options` | `array`   | The options that will be passed to the tomselect.js library.  | `[]`        | No       |
| `filter`     | `array`   | The filter that will be used to filter the posts.             | `[]`        | No       |
| `class`      | `string`  | The class of input element.                                   | `null`      | No       |
