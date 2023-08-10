<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://ween.codes
 * @since      1.0.0
 *
 * @package    Apifootball4wp
 * @subpackage Apifootball4wp/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Apifootball4wp
 * @subpackage Apifootball4wp/admin
 * @author     Ween Codes <weencodes@gmail.com>
 */
class Apifootball4wp_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/apifootball4wp-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/apifootball4wp-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function addPage()
    {
        add_menu_page(
            __('API-Football', 'apifootball4wp'),
            __('API-Football', 'apifootball4wp'),
            'manage_options',
            'api-football-settings',
            [$this, 'renderSettingsPage'],
            'dashicons-admin-generic'
        );

		add_submenu_page(
            'api-football-settings',
            __('Imports', 'apifootball4wp'),
            __('Imports', 'apifootball4wp'),
            'manage_options',
            'api-football-imports',
            [$this, 'renderImportsPage']
        );
    }

    public function renderSettingsPage()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('Access denied', 'apifootball4wp'));
        }

        if (isset($_POST['apifootball4wp_api_key'])) {
            update_option('apifootball4wp_api_key', sanitize_text_field($_POST['apifootball4wp_api_key']));
        }

        $apiKey = get_option('apifootball4wp_api_key', '');

		if ( $apiKey ) {
			$response = wp_remote_get('https://v3.football.api-sports.io/status', [
				'headers' => [
					'x-rapidapi-key' => $apiKey,
				],
			]);
	
			if (is_array($response) && !is_wp_error($response)) {
				$responseBody = wp_remote_retrieve_body($response);
				$data = json_decode($responseBody, true);
	
				if (is_array($data['errors']) && 0 !== count($data['errors'])) {
					echo '<div class="notice notice-error is-dismissible">';
					foreach ($data['errors'] as $error) {
						echo '<p>' . $error . '</p>';
					}
					echo '</div>';
				}

				if (is_array($data['response']) && 0 !== count($data['response'])) {
					$account = $data['response']['account'];
					$subscription = $data['response']['subscription'];
					$subscription = $subscription['active'] ? 'active' : 'inactive';
					$requests = $data['response']['requests'];

					echo '<div class="notice notice-success is-dismissible">';
					echo '<p>';
					printf( __('Welcome <strong>%s %s</strong>!', 'apifootball4wp'), $account['firstname'], $account['lastname'] );
					echo '<br />';
					printf( __('Your subscription is <strong>%s</strong>.', 'apifootball4wp'), $subscription );
					echo '<br />';
					printf( __('Your daily request limit is at: <strong>%d/%d</strong>', 'apifootball4wp'), $requests['current'], $requests['limit_day'] );
					echo '</p>';
					echo '</div>';
				}
			}
		}

        // Load the admin configs file
        require_once plugin_dir_path(__FILE__) . 'partials/apifootball4wp-admin-configs.php';
    }

	public function renderImportsPage()
	{
		?>
		<div class="wrap">
			<?php
			require_once plugin_dir_path(__FILE__) . 'partials/apifootball4wp-admin-imports.php';
			?>
		</div>
		<?php
	}


}
