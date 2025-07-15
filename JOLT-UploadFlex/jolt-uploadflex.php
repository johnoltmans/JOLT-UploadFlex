<?php
/*
Plugin Name: JOLT™ UploadFlex
Plugin URI: https://github.com/johnoltmans/JOLT-UploadFlex
Description: Moves new uploads to a custom folder such as /media or /assets.
Version: 1.6
Author: John Oltmans
Author URI: https://www.johnoltmans.nl
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

add_action('admin_menu', function() {
    add_options_page(
        'JOLT UploadFlex Settings',
        'JOLT UploadFlex',
        'manage_options',
        'jolt-uploadflex',
        'jolt_uploadflex_settings_page'
    );
});

function jolt_uploadflex_settings_page() {
    ?>
    <div class="wrap">
        <?php
// Get the current plugin version from the plugin header
$plugin_data = get_plugin_data(__FILE__);
$version = $plugin_data['Version'];
?>
<h1>JOLT Uploadflex v<?php echo esc_html($version); ?></h1>

        <div class="notice notice-warning">
    <p><strong>Warning:</strong> Changing the upload folder will not move existing media files. Images or media uploaded before the change may stop working. You might need to re-upload those files to the new folder.</p>
    <p>If you encounter issues and cannot recover your existing images, simply <a href="<?php echo admin_url('plugins.php'); ?>"><strong>deactivate this plugin</strong></a> to restore WordPress' default upload settings.</p>
    <p>The best way to move over the files is by using a <a href="https://github.com/johnoltmans/WordPress-Plugins/wiki/How-to-use-a-FTP-Client" target="blank">FTP client.</a></p>
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

add_filter('upload_dir', function($dirs) {
    $custom_dir = trim(get_option('jolt_uploadflex_dir', 'media'), '/');
    $dirs['path']    = ABSPATH . $custom_dir;
    $dirs['url']     = home_url("/" . $custom_dir);
    $dirs['basedir'] = ABSPATH . $custom_dir;
    $dirs['baseurl'] = home_url("/" . $custom_dir);
    return $dirs;
});

add_filter('plugin_action_links_' . plugin_basename(__FILE__), function($links) {
    $settings_link = '<a href="' . admin_url('options-general.php?page=jolt-uploadflex') . '">Settings</a>';
    $links[] = $settings_link;  // Voeg toe achteraan
    return $links;
});
