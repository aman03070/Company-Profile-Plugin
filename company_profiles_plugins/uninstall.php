<?php if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit; // Prevent unauthorized access
}

// Remove all posts of 'company_profile' type
$company_profiles = get_posts(array(
    'post_type'      => 'company_profile',
    'posts_per_page' => -1,
    'fields'         => 'ids'
));

if (!empty($company_profiles)) {
    foreach ($company_profiles as $post_id) {
        wp_delete_post($post_id, true); // Force delete
    }
}

// Optionally, remove custom metadata
global $wpdb;
$wpdb->query("DELETE FROM $wpdb->postmeta WHERE meta_key IN ('company_name', 'founded_year', 'industry', 'funding_status', 'supports_lgbtq', 'country', 'num_employees')");

?>