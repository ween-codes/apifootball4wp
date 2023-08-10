<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Verify nonce
    if (isset($_POST['apifootball_import_nonce']) && wp_verify_nonce($_POST['apifootball_import_nonce'], 'apifootball_import_nonce')) {
        // Sanitize and save selected values
        $selectedCountry = sanitize_text_field($_POST['country']);
        $selectedSeason = sanitize_text_field($_POST['season']);

        update_option('apifootball4wp_selected_country', $selectedCountry);
        update_option('apifootball4wp_selected_season', $selectedSeason);

        echo '<div class="notice notice-success is-dismissible">';
        echo '<p>' . __('Selected values have been saved.', 'apifootball4wp') . '</p>';
        echo '</div>';
    }
}

?>
<div class="wrap">
    <h1 class="wp-heading-inline"><?php echo esc_html(get_admin_page_title()); ?></h1>

    <form id="apifootball-import-form" method="post">
        <table class="form-table">
            <tr>
                <th scope="row"><label for="country"><?php _e('Country', 'apifootball4wp'); ?></label></th>
                <td>
                    <?php
                    $apiKey = get_option('apifootball4wp_api_key');
                    $selectedCountry = get_option('apifootball4wp_selected_country', '');
                    if ($apiKey) {
                        $response = wp_remote_get('https://v3.football.api-sports.io/countries', [
                            'headers' => [
                                'x-rapidapi-key' => $apiKey,
                            ],
                        ]);

                        if (is_array($response) && !is_wp_error($response)) {
                            $responseBody = wp_remote_retrieve_body($response);
                            $data = json_decode($responseBody, true);

                            if ($data && isset($data['response'])) {
                                echo '<select id="country" name="country" class="regular-text">';
                                echo '<option>-- select --</option>';
                                foreach ($data['response'] as $country) {
                                    $selected = selected($selectedCountry, $country['code'], false);
                                    echo '<option value="' . esc_attr($country['code']) . '" ' . $selected . '>' . esc_html($country['name']) . '</option>';
                                }
                                echo '</select>';
                            }
                        }
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="season"><?php _e('Season', 'apifootball4wp'); ?></label></th>
                <td>
                    <?php
                    $selectedSeason = get_option('apifootball4wp_selected_season', '');
                    if ($apiKey) {
                        $response = wp_remote_get('https://v3.football.api-sports.io/leagues/seasons', [
                            'headers' => [
                                'x-rapidapi-key' => $apiKey,
                            ],
                        ]);

                        if (is_array($response) && !is_wp_error($response)) {
                            $responseBody = wp_remote_retrieve_body($response);
                            $data = json_decode($responseBody, true);

                            if ($data && isset($data['response'])) {
                                echo '<select id="season" name="season" class="regular-text">';
                                echo '<option>-- select --</option>';
                                foreach ($data['response'] as $season) {
                                    $selected = selected($selectedSeason, $season, false);
                                    echo '<option value="' . esc_attr($season) . '" ' . $selected . '>' . esc_html($season) . '</option>';
                                }
                                echo '</select>';
                            }
                        }
                    }
                    ?>
                </td>
            </tr>
        </table>
        <?php wp_nonce_field('apifootball_import_nonce', 'apifootball_import_nonce'); ?>
        <?php submit_button(__('Import/Update', 'apifootball4wp'), 'primary', 'submit', false); ?>
    </form>

</div>
