<?php


class TimberinTestimonialsAdmin {
    const testimonial_type = 'pts_testimonials';
    public static $fields = ['tt_client', 'tt_address', 'tt_category', 'tt_img', 'tt_coord_long', 'tt_coord_lat'];
    public static $categories = [
        'hot_tub' => 'Hot tub',
        'sauna' => 'Sauna',
    ];
    protected $version;

    private $post = null;

    public function __construct( $version ) {
        $this->version = $version;
    }

    public function register_settings(){
        register_setting('timberin-testimonials-settings-group', 'google_api_key');
    }

    public function enqueue_scrips() {
        wp_enqueue_script(
            'timberin-testimonials-admin',
            plugin_dir_url( __FILE__ ) . 'js/timberin-testimonials.js',
            ['jquery'],
            $this->version,
            false
        );
    }

    public function enqueue_style() {
        wp_enqueue_style(
            'timberin-testimonials-admin-style',
            plugin_dir_url( __FILE__ ) . 'style/index.css',
            [],
            $this->version,
            false
        );
    }



    public function register_post_type() {

        $args = [
            'labels' => [
                'name' => 'Timberin testimonials',
                'singular_name' => 'Testimonial',
                'all_items' => 'All testimonials'
            ],
            'public' => true,
            'register_meta_box_cb' => [ $this, 'add_meta_box'],



        ];
        register_post_type( self::testimonial_type, $args);
    }

    public function add_meta_box($post) {
        $this->post = $post;
        add_meta_box(
            'timberin-testimonial-admin',
            'Testimonial meta',
            [ $this, 'render_meta_box' ],
            self::testimonial_type,
            'normal',
            'high'
        );
    }



    public function save_testimonial($post_id, $post){
        if ( !isset( $_POST['timberin_testimonial_nonce'] ) || !wp_verify_nonce( $_POST['timberin_testimonial_nonce'], plugin_basename( __FILE__ ) ) ){
            return $post_id;
        }
        foreach(self::$fields as $field){
            if(isset( $_POST[$field] )){
                $value = wp_filter_post_kses($_POST[$field]);
                update_post_meta($post->ID, $field, $value);
            }
        }
        if (isset( $_POST['tt_img'] )){
            set_post_thumbnail($post, $_POST['tt_img']);
        }

    }

    public function register_endpoint(){
        register_rest_route( 'timberin', '/testimonials', [
            'methods' => 'GET',
            'callback' => [ $this, 'get_all_testimonials' ],
        ]);
        remove_filter( 'rest_pre_serve_request', 'rest_send_cors_headers' );
        add_filter( 'rest_pre_serve_request', function( $value ) {
            header( 'Access-Control-Allow-Origin: *' );
            header( 'Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE' );
            header( 'Access-Control-Allow-Credentials: true' );

            return $value;

        });
    }

    private function resolve_coords($adr){
        $lng = '';
        $lat = '';
        $address = urlencode($adr);
        $apiKey = get_option('google_api_key');
        $url = "https://maps.googleapis.com/maps/api/geocode/json?key=$apiKey&address=$address";
        $resp_json = file_get_contents($url);
        $resp = json_decode($resp_json, true);
        if($resp['status']=='OK'){
            $lat = $resp['results'][0]['geometry']['location']['lat'];
            $lng = $resp['results'][0]['geometry']['location']['lng'];
        }
        return [
            'lng' => $lng,
            'lat' => $lat
        ];
    }

    /**
     * Simple address extractor from title if title ends with address
     * @param $str
     */
    private function get_address($str){
        $address = '';
        $lat = '';
        $lng = '';
        $exploded = explode(',', $str);
        $size = sizeof($exploded);
        if($size > 2){
            $address = trim($exploded[$size - 2]).', '.trim($exploded[$size - 1]);
            $coords = $this->resolve_coords($address);
            $lat = $coords['lat'];
            $lng = $coords['lng'];
        }
        return [
            'tt_address' => $address,
            'tt_coord_long' => $lng,
            'tt_coord_lat' => $lat,
        ];
    }

    public function import_testimonials(){
        $posts = get_posts(array(
                'post_type'   => 'bl_testimonials',
                'post_status' => 'publish',
                'posts_per_page' => -1,
            )
        );
        foreach ($posts as $post){
            $img = get_post_meta($post->ID, "_thumbnail_id", true);
            $p = get_object_vars($post);
            $p['ID'] = 0;
            $p['post_type'] = self::testimonial_type;
            $p['meta_input'] = $this->get_address($post->post_title);
            $p['meta_input']['tt_img'] = $img;
            $postId = wp_insert_post($p);
            if ($postId > 0 && $img){
                set_post_thumbnail($postId, $img);
            }
        }


        return sizeof($posts).' testimonials imported!';
    }

    public function register_export_page(){

        add_submenu_page('edit.php?post_type='.self::testimonial_type,
            'Settings',
            'Settings',
            'edit_posts',
            'pts-testimonial-settings',
            function (){
                $import = '';
                if (isset($_POST['pts_import_testimonials'])){
                    $import = $this->import_testimonials();
                }
                $url = get_home_url()."/wp-json/timberin/testimonials";
                require_once plugin_dir_path( __FILE__ ) . 'views/timberin-testimonials-settings.php';
        });
    }



    public function get_all_testimonials(){
        $posts = get_posts(array(
                'post_type'   => self::testimonial_type,
                'post_status' => ['publish', 'pending'],
                'posts_per_page' => -1,
            )
        );
        $testimonials = [];
        foreach($posts as $p){
            $testimonial = [];
            //get the meta you need form each post
            $testimonial['id'] = $p->ID;
            $testimonial['location'] = [
                'lat' => get_post_meta($p->ID,"tt_coord_lat", true),
                'lng' => get_post_meta($p->ID,"tt_coord_long", true)
            ];
            $testimonial['title'] = $p->post_title;
            $testimonial['client'] = get_post_meta($p->ID,"tt_client", true);
            $testimonial['testimonial'] = $p->post_content;
            $testimonial['address'] = get_post_meta($p->ID,"tt_address", true);
            $testimonial['category'] = get_post_meta($p->ID,"tt_category", true);
            $url = wp_get_attachment_url(get_post_meta($p->ID,"tt_img", true));
            $testimonial['image'] = $url ? $url : '';

            $testimonials[] = $testimonial;
        }
        return $testimonials;
    }


    public function render_meta_box() {
        wp_enqueue_media();
        $values = $this->get_meta_values($this->post->ID);
        $categories = self::$categories;
        echo '<input type="hidden" name="timberin_testimonial_nonce" id="timberin_testimonial_nonce" value="' .
            wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
        require_once plugin_dir_path( __FILE__ ) . 'views/timberin-testimonials.php';
    }

    private function get_meta_values($post_id){

        $values = [];
        foreach (self::$fields as $field){
            $values[$field] = get_post_meta($post_id, $field, true);
        }
        return $values;
    }

}