<?php
/**
 * Plugin Name: Company Profile 
 * Description: Adds a custom post type for company profiles with specific meta fields.
 * Version: 1.0
 * Author: Aman Jaiswal
 */

if (!defined('ABSPATH')) {
    exit; 
}

// Register Custom Post Type
function cpcp_register_company_post_type() {
    $labels = array(
        'name'               => 'Company Profiles',
        'singular_name'      => 'Company Profile',
        'menu_name'          => 'Company Profiles',
        'name_admin_bar'     => 'Company Profile',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Company Profile',
        'new_item'           => 'New Company Profile',
        'edit_item'          => 'Edit Company Profile',
        'view_item'          => 'View Company Profile',
        'all_items'          => 'All Company Profiles',
        'search_items'       => 'Search Company Profiles',
        'not_found'          => 'No company profiles found',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'supports'           => array('title', 'editor', 'custom-fields'),
        'show_in_menu'       => true,
        'menu_icon'          => 'dashicons-businessman',
        'rewrite'            => array('slug' => 'company-profile'),
    );

    register_post_type('company_profile', $args);
}

add_action('init', 'cpcp_register_company_post_type');

function cpcp_register_company_profile_cpt() {
    $args = array(
        'labels' => array(
            'name' => 'Company Profiles',
            'singular_name' => 'Company Profile'
        ),
        'public' => true,
        'menu_icon' => 'dashicons-building',
        'supports' => array('title', 'editor', 'thumbnail'),
        'show_in_rest' => true,
    );
    register_post_type('company_profile', $args);
}
add_action('init', 'cpcp_register_company_profile_cpt');

// Add Meta Box
function cpcp_add_company_meta_boxes() {
    add_meta_box('company_profile_meta', 'Company Details', 'cpcp_company_meta_box_callback', 'company_profile', 'normal', 'high');
}
add_action('add_meta_boxes', 'cpcp_add_company_meta_boxes');

// Meta Box Callback
function cpcp_company_meta_box_callback($post) {
    wp_nonce_field('company_profile_nonce_action', 'company_profile_nonce');
    $meta = get_post_meta($post->ID);
    ?>
    <p>
        <label for="company_name">Company Name:</label>
        <input type="text" id="company_name" name="company_name" value="<?php echo esc_attr($meta['company_name'][0] ?? ''); ?>" class="widefat">
    </p>
    <p>
        <label for="founded_year">Founded in Year:</label>
        <input type="number" id="founded_year" name="founded_year" value="<?php echo esc_attr($meta['founded_year'][0] ?? ''); ?>" class="widefat">
    </p>
    <p>
        <label for="industry">Industry:</label>
        <select id="industry" name="industry" class="widefat">
            <?php $industries = ['Technology', 'Finance', 'Healthcare', 'Education', 'Retail']; ?>
            <?php foreach ($industries as $industry) : ?>
                <option value="<?php echo $industry; ?>" <?php selected($meta['industry'][0] ?? '', $industry); ?>><?php echo $industry; ?></option>
            <?php endforeach; ?>
        </select>
    </p>
    <p>
        <label>Funding Status:</label><br>
        <input type="radio" name="funding_status" value="Funded" <?php checked($meta['funding_status'][0] ?? '', 'Funded'); ?>> Funded
        <input type="radio" name="funding_status" value="Non-Funded" <?php checked($meta['funding_status'][0] ?? '', 'Non-Funded'); ?>> Non-Funded
    </p>
    <p>
        <label>Supports LGBTQ:</label><br>
        <input type="radio" name="supports_lgbtq" value="Yes" <?php checked($meta['supports_lgbtq'][0] ?? '', 'Yes'); ?>> Yes
        <input type="radio" name="supports_lgbtq" value="No" <?php checked($meta['supports_lgbtq'][0] ?? '', 'No'); ?>> No
    </p>
    <p>
        <label for="country">Country of Registration:</label>
        <select id="country" name="country" class="widefat">
            <?php $countries = ['USA', 'Canada', 'UK', 'India', 'Germany']; ?>
            <?php foreach ($countries as $country) : ?>
                <option value="<?php echo $country; ?>" <?php selected($meta['country'][0] ?? '', $country); ?>><?php echo $country; ?></option>
            <?php endforeach; ?>
        </select>
    </p>
    <p>
        <label for="num_employees">Number of Employees:</label>
        <select id="num_employees" name="num_employees" class="widefat">
            <?php $ranges = ['0-10', '10-20', '50-100', '100-500', '500+']; ?>
            <?php foreach ($ranges as $range) : ?>
                <option value="<?php echo $range; ?>" <?php selected($meta['num_employees'][0] ?? '', $range); ?>><?php echo $range; ?></option>
            <?php endforeach; ?>
        </select>
    </p>
    <?php
}

// Save Meta Box Data
function cpcp_save_company_meta($post_id) {
    // Security Check
    if (!isset($_POST['company_profile_nonce']) || !wp_verify_nonce($_POST['company_profile_nonce'], 'company_profile_nonce_action')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $fields = ['company_name', 'founded_year', 'industry', 'funding_status', 'supports_lgbtq', 'country', 'num_employees'];
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
        }
    }
}
add_action('save_post', 'cpcp_save_company_meta');

function cpcp_company_listing_shortcode() {
    ob_start(); 
    ?>
    <div id="company-listing-container">
        <!-- Search Filters -->
        <div class="company-filters">
            <input type="text" id="search_name" placeholder="Search by Company Name">
            <select id="search_industry">
                <option value="">Select Industry</option>
                <option value="Technology">Technology</option>
                <option value="Finance">Finance</option>
                <option value="Healthcare">Healthcare</option>
                <option value="Education">Education</option>
                <option value="Retail">Retail</option>
            </select>
            <select id="search_location">
                <option value="">Select Location</option>
                <option value="USA">USA</option>
                <option value="UK">UK</option>
                <option value="India">India</option>
                <option value="Canada">Canada</option>
                <option value="Australia">Australia</option>
            </select>
            <select id="sort_by">
                <option value="">Sort By</option>
                <option value="founded_year">Founded Year</option>
                <option value="num_employees">Number of Employees</option>
            </select>
            <button id="filter_button">Search</button>
        </div>

        <!-- Company Listings -->
        <div id="company-results"></div>
        <div id="pagination"></div>
    </div>

    <script>
        jQuery(document).ready(function($) {
            function load_companies(page = 1) {
                let name = $('#search_name').val();
                let industry = $('#search_industry').val();
                let location = $('#search_location').val();
                let sort = $('#sort_by').val();

                $.ajax({
                    url: '<?php echo site_url(); ?>/wp-json/cpcp/v1/companies',
                    type: 'GET',
                    data: {
                        search_name: name,
                        search_industry: industry,
                        search_location: location,
                        sort_by: sort,
                        page: page
                    },
                    beforeSend: function() {
                        $('#company-results').html('<p>Loading...</p>');
                    },
                    success: function(response) {
                        $('#company-results').html(response.html);
                        $('#pagination').html(response.pagination);
                    }
                });
            }

            $('#filter_button').on('click', function() {
                load_companies();
            });

            $(document).on('click', '.pagination-link', function(e) {
                e.preventDefault();
                let page = $(this).data('page');
                load_companies(page);
            });

            load_companies();
        });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('company_listing', 'cpcp_company_listing_shortcode');


function cpcp_fetch_companies() {
    $args = array(
        'post_type' => 'company_profile',
        'posts_per_page' => -1,
        'meta_query' => array('relation' => 'AND'),
    );

    if (!empty($_POST['search_name'])) {
        $args['meta_query'][] = array(
            'key' => 'company_name',
            'value' => sanitize_text_field($_POST['search_name']),
            'compare' => 'LIKE'
        );
    }

    if (!empty($_POST['search_industry'])) {
        $args['meta_query'][] = array(
            'key' => 'industry',
            'value' => sanitize_text_field($_POST['search_industry']),
            'compare' => '='
        );
    }

    if (!empty($_POST['search_location'])) {
        $args['meta_query'][] = array(
            'key' => 'country',
            'value' => sanitize_text_field($_POST['search_location']),
            'compare' => '='
        );
    }

    if (!empty($_POST['sort_by'])) {
        $args['meta_key'] = sanitize_text_field($_POST['sort_by']);
        $args['orderby'] = 'meta_value_num';
        $args['order'] = 'ASC';
    }

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        echo '<div class="company-list">';
        while ($query->have_posts()) {
            $query->the_post();
            $company_id = get_the_ID();
            $company_name = get_post_meta($company_id, 'company_name', true);
            $founded_year = get_post_meta($company_id, 'founded_year', true);
            $industry = get_post_meta($company_id, 'industry', true);
            $location = get_post_meta($company_id, 'country', true);
            $employees = get_post_meta($company_id, 'num_employees', true);
            
            echo '<div class="company-card">';
            echo '<h3><a href="' . get_permalink($company_id) . '">' . esc_html($company_name) . '</a></h3>';
            echo '<p><strong>Industry:</strong> ' . esc_html($industry) . '</p>';
            echo '<p><strong>Founded:</strong> ' . esc_html($founded_year) . '</p>';
            echo '<p><strong>Location:</strong> ' . esc_html($location) . '</p>';
            echo '<p><strong>Employees:</strong> ' . esc_html($employees) . '</p>';
            echo '</div>';
        }
        echo '</div>';
    } else {
        echo '<p>No companies found.</p>';
    }

    wp_die();
}
add_action('wp_ajax_cpcp_fetch_companies', 'cpcp_fetch_companies');
add_action('wp_ajax_nopriv_cpcp_fetch_companies', 'cpcp_fetch_companies');

function cpcp_enqueue_styles() {
    wp_enqueue_style('company-listing-style', plugin_dir_url(__FILE__) . 'css/style.css');
}
add_action('wp_enqueue_scripts', 'cpcp_enqueue_styles');

function cpcp_activate_plugin() {
    cpcp_register_company_post_type();
    flush_rewrite_rules(); // Ensures proper permalink structure
}

register_activation_hook(__FILE__, 'cpcp_activate_plugin');

function cpcp_deactivate_plugin() {
    unregister_post_type('company_profile');
    flush_rewrite_rules(); // Clears rewrite rules
}

register_deactivation_hook(__FILE__, 'cpcp_deactivate_plugin');


