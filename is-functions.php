<?php

/*--------------------------------------------------------------
## Inject the Design Publishers CSS file.
--------------------------------------------------------------*/
add_action('wp_enqueue_scripts', 'callback_for_setting_up_scripts');

function callback_for_setting_up_scripts()
{
    wp_enqueue_style('main-css', IS_CSS_PLUGIN_URL . 'css/styles.custom.css');
}

/*--------------------------------------------------------------
## Wp-Color API / Add Menu
--------------------------------------------------------------*/

class CPA_Theme_Options
{
    /** Refers to a single instance of this class. */
    private static $instance = null;

    /* Saved colors */
    private $colors;

    /**
     * Creates or returns an instance of this class.
     *
     * @return CPA_Theme_Options A single instance of this class.
     */
    public static function get_instance()
    {
        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    private function __construct()
    {
        // Add the page to the admin menu
        add_action('admin_menu', array($this, 'add_page'));

        // Register page options
        add_action('admin_init', array($this, 'register_page_options'));

        // Css rules for Color Picker
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_style( 'page-css', IS_CSS_PLUGIN_URL . 'css/page.css');

        // Register javascript
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_js'));

        add_action('wp_ajax_update_css', [$this, 'ajax_update_css']);

        // Get registered option
        $this->colors = get_option('cpa_colors');
    }
    public function add_page()
    {
        // Add a new top level menu link to the ACP
        add_menu_page(
            'CSS Framework Template', // Title of the page
            'CSS Framework Template', // Text to show on the menu link
            'manage_options', // Capability requirement to see the link
            'css-admin-menu', // The 'slug' - file to display when clicking the link
            [$this, 'display_page_call'], // function call
            'dashicons-media-document' // menu icon
        );
    }

    public function display_page_call()
    {
        ?>
        
        <div class="is-css-wrap">
            <h2 class="is-css-title">Change CSS Framework Colors</h2>
            <p class="is-css-notice">*Please, do not change these colors unless instructed to.</p>
            <form method="POST" id="colorPickers" class="is-css-form">
            <?php foreach ($this->colors as $opt) { ?>
                <div class="is-css-color-aligner">

                <label><?= $opt ?></label>
                <input class="cpa-color-picker" id="<?= $opt ?>" name="<?= $opt ?>" value="<?= get_option($opt) ?>">
            </div>
        
                <?php }
        submit_button(); ?>
            </form>
        </div>
<?php
    }

    public function register_page_options()
    {
        // Add Section for option fields
        foreach ($this->colors as $opt) {
            register_setting('cpa_section', $opt);
        }
    }

    public function enqueue_admin_js($hook)
    {
        if ($hook !== 'toplevel_page_css-admin-menu') {
            return;
        }

        wp_enqueue_script('validator', IS_CSS_PLUGIN_URL . 'js/validator.min.js');

        // Make sure to add the wp-color-picker dependency to js file
        wp_enqueue_script('ajax-script', IS_CSS_PLUGIN_URL . 'js/ajax.js', ['jquery', 'wp-color-picker', 'validator']);
        // in JavaScript, object properties are accessed as ajax_object.ajax_url, ajax_object.we_value
        wp_localize_script('ajax-script', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
    }

    public function ajax_update_css()
    {
        $colors = $_POST['colors'];

        $json = [];

        foreach ($colors as $obj) {
            $name = $obj['name'];
            $value = $obj['value'];

            if (!$value) {
                continue;
            }

            $json[$name] = $value;

            update_option($name, $value);
        }

        try {
            $data = $this->compile_css($json);
            file_put_contents(plugin_dir_path(__FILE__) . 'css/styles.custom.css', $data);
            wp_send_json_success();
        } catch (Exception $e) {
            wp_send_json_error($e->getMessage(), 200);
        }
    }

    private function compile_css($obj)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://nwk8s445if.execute-api.us-east-1.amazonaws.com/v1",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($obj),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "x-api-key: VxSKCMh7o95qjpNqhVmZf1SiM1mGcj1T4OF4jqVY"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            throw new Exception($err);
        } else {
            $response = json_decode($response, true);
            if ($response['errorMessage']) {
                throw new Exception($response['errorMessage']);
            }

            return $response['body'];
        }
    }
}

CPA_Theme_Options::get_instance();
