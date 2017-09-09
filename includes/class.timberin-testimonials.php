<?php


class TimberinTestimonials {

    /**
     * @var TimberinTestimonialsLoader
     */
    protected $loader;

    protected $plugin_slug;

    protected $version;

    public function __construct() {

        $this->plugin_slug = 'simple-testimonials-slug';
        $this->version = '0.1.0';
        $this->load_dependencies();
        $this->define_admin_hooks();

    }

    private function load_dependencies() {

        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class.timberin-testimonials-admin.php';

        require_once plugin_dir_path( __FILE__ ) . 'class.timberin-testimonials-loader.php';
        require_once plugin_dir_path( __FILE__ ) . 'class.timbern-testimonials-widget.php';
        $this->loader = new TimberinTestimonialsLoader();

    }

    public function install(){
        flush_rewrite_rules();
    }

    private function define_admin_hooks() {

        $admin = new TimberinTestimonialsAdmin( $this->get_version() );
        $widget = new TimberinTestimonialsWidget();
        $this->loader->add_action( 'init', $admin, 'register_post_type' );
        $this->loader->add_action( 'admin_init', $admin, 'register_settings' );
        $this->loader->add_action( 'admin_menu', $admin, 'register_export_page' );
        $this->loader->add_action( 'rest_api_init', $admin, 'register_endpoint' );
        $this->loader->add_action( 'admin_enqueue_scripts', $admin, 'enqueue_scrips' );
        $this->loader->add_action( 'wp_enqueue_scripts', $admin, 'enqueue_style' );
        $this->loader->add_action( 'save_post_pts_testimonials', $admin, 'save_testimonial' );
        $this->loader->add_action( 'widgets_init', $widget, 'register' );
        $widget->register_shorcode();
    }



    public function run() {
        $this->loader->run();
    }


    public function get_version() {
        return $this->version;
    }

}