<?php
if (isset($_POST['apifootball4wp_api_key'])) {
    update_option('apifootball4wp_api_key', sanitize_text_field($_POST['apifootball4wp_api_key']));
}

$apiKey = get_option('apifootball4wp_api_key', '');

?>
<div class="wrap">
    <h1 class="wp-heading-inline"><?php echo esc_html(get_admin_page_title()); ?></h1>

    <form method="post" action="">
        <?php settings_fields('api-football-settings-group'); ?>
        <?php do_settings_sections('api-football-settings-group'); ?>

        <table class="form-table">
            <tr valign="top">
                <th scope="row"><label for="apifootball4wp_api_key"><?php _e('API Key:', 'apifootball4wp'); ?></label></th>
                <td>
                    <input type="text" id="apifootball4wp_api_key" name="apifootball4wp_api_key" value="<?php echo esc_attr($apiKey); ?>" class="regular-text">
                </td>
            </tr>
        </table>

        <?php submit_button(__('Save Changes', 'apifootball4wp'), 'primary', 'submit', true); ?>
    </form>
</div>
