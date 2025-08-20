<?php
/*
Plugin Name: JOLT UploadFlex
Plugin URI: https://github.com/johnoltmans/JOLT-UploadFlex
Description: Moves new uploads to a custom folder such as /media or /assets, preserving standard year/month subfolders for compatibility.
Version: 1.2.1
Requires at least: 6.8
Requires PHP: 7.4
Author: John Oltmans
Author URI: https://www.johnoltmans.nl/
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Text Domain: simple-plugin-for-a-clean-wordpress-by-john-oltmans
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

// UF Media removed; only settings and upload folder functionality remains

// Settings page
add_action('admin_menu', function() {
    add_options_page(
        'JOLT UploadFlex Settings',
        'JOLT UploadFlex',
        'manage_options',
        'jolt-uploadflex',
        'jolt_uploadflex_settings_page'
    );
});

// hallo
function jolt_uploadflex_settings_page() {
    ?>
    <div class="wrap">
        <?php
        if ( ! function_exists( 'get_plugin_data' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        }
        $plugin_data = get_plugin_data(__FILE__);
        $version = $plugin_data['Version'];
        ?>
        <h1>JOLT UploadFlex v<?php echo esc_html($version); ?></h1>
        <div class="notice notice-warning">
            <p><strong>Note:</strong> Changing the upload folder does not move existing media files. Media files uploaded before this change may stop working. It is best to re-upload those files to the new folder.</p>
            <p>If you experience problems and cannot recover your images, deactivate this plugin to restore the default WordPress upload settings.</p>
            <p>The easiest way to move files is with an FTP client.</p>
        </div>
        <form method="post" action="options.php">
            <?php
            settings_fields('jolt_uploadflex_options');
            do_settings_sections('jolt-uploadflex');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Register settings and fields
add_action('admin_init', function() {
    register_setting('jolt_uploadflex_options', 'jolt_uploadflex_dir');
    add_settings_section(
        'jolt_uploadflex_main',
        'Upload Directory Setting',
        '',
        'jolt-uploadflex'
    );
    add_settings_field(
        'jolt_uploadflex_dir',
        'Upload directory name (e.g., "media")',
        function() {
            $value = esc_attr(get_option('jolt_uploadflex_dir', 'media'));
            echo '<input type="text" name="jolt_uploadflex_dir" value="' . $value . '" class="regular-text">';
        },
        'jolt-uploadflex',
        'jolt_uploadflex_main'
    );
});

// Filter upload directory to use custom folder, including year/month subfolders
add_filter('upload_dir', function($dirs) {
    $custom_dir = trim(get_option('jolt_uploadflex_dir', 'media'), '/');
    $subdir = isset($dirs['subdir']) ? $dirs['subdir'] : '';
    $full_path = ABSPATH . $custom_dir . $subdir;
    $full_url  = home_url("/" . $custom_dir . $subdir);
    if (!is_dir($full_path)) {
        if (!wp_mkdir_p($full_path)) {
            error_log("JOLT UploadFlex: Can't create directory: " . $full_path);
        }
    }
    $dirs['path']    = $full_path;
    $dirs['url']     = $full_url;
    $dirs['basedir'] = ABSPATH . $custom_dir;
    $dirs['baseurl'] = home_url("/" . $custom_dir);
    return $dirs;
}, 99);

add_filter('plugin_action_links_' . plugin_basename(__FILE__), function($links) {
    $settings_link = '<a href="' . admin_url('options-general.php?page=jolt-uploadflex') . '">Settings</a>';
    $links[] = $settings_link;
    return $links;
});