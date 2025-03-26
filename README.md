Company Profile Manager

Description

Company Profile Manager is a custom WordPress plugin that allows users to create, manage, and display company profiles. It includes a custom post type, meta fields for company details, AJAX-based search and filtering, pagination, and a REST API for dynamic content loading.

Features

Custom post type: company_profile

Custom meta fields (Name, Founded Year, Industry, etc.)

User-friendly admin panel for adding/editing profiles

Shortcode [company_listing] to display company profiles

AJAX-based filtering and sorting

REST API endpoint for fetching data dynamically

Pagination for listing pages

Plugin installation & uninstallation support

Installation

1. Install via WordPress Admin

Download the plugin ZIP file.

Go to WordPress Admin Panel > Plugins > Add New.

Click Upload Plugin and select the ZIP file.

Click Install Now, then Activate.

2. Manual Installation

Upload the plugin folder to the /wp-content/plugins/ directory.

Activate the plugin in WordPress Admin Panel > Plugins.

Usage

1. Adding a Company Profile

Navigate to Company Profiles > Add New.

Enter the company details in the custom fields.

Click Publish to save the profile.

2. Displaying Company Profiles

Use the shortcode [company_listing] on any page or post.

The company profiles will be displayed with search and sorting functionality.

REST API Endpoint
Fetch company profiles:

GET /wp-json/cpcp/v1/companies

Parameters:

page (integer): Pagination support

Uninstallation

Deactivating the plugin will disable custom post types.

To remove data, uninstall the plugin completely from the WordPress admin panel.

Approach

1. Custom Post Type & Meta Fields

The plugin registers a custom post type company_profile using register_post_type().

Custom meta fields (like Founded Year, Industry) are added using add_meta_box().

2. Admin Panel & Validation

A user-friendly admin panel is created with proper data validation and sanitization to prevent security vulnerabilities.

3. Frontend Listing & AJAX Functionality

A shortcode [company_listing] is used to display company profiles.

AJAX is used for search, sorting, and pagination, ensuring a seamless experience.

4. REST API Integration

A custom REST API endpoint /wp-json/cpcp/v1/companies is created to fetch company profiles dynamically.

5. Installation & Cleanup

During plugin activation, flush_rewrite_rules() ensures the post type is registered properly.

On deactivation, custom database entries are handled gracefully.

This approach ensures scalability, performance, and security while keeping the user experience smooth.

