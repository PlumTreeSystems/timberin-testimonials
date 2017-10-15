<?php


class TimberinTestimonialsWidget extends WP_Widget {
    function __construct()
    {
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class.timberin-testimonials-admin.php';
        parent::__construct(
            'timberin_testimonials_widget',
            'Timberin testimonials',
            ['description' => __('Simple Widget to display testimonials', 'text_domain')]
        );

    }
    public function widget( $args, $instance ) {
        // outputs the content of the widget
        extract( $args );
        //$title = apply_filters( 'widget_title', $instance['title'] );
        //$num_display = apply_filters( 'widget_title', $instance['num_display'] );


        echo $before_widget;

        $this->print_testimonials();

        echo $after_widget;

    }
    public function print_testimonials(){
        $posts = get_posts(array(
                'post_type'   => TimberinTestimonialsAdmin::testimonial_type,
                'post_status' => 'publish',
                'posts_per_page' => -1,
            )
        );
        $testimonials = [];
        foreach($posts as $p){
            $testimonial = [];
            //get the meta you need form each post
            $testimonial['url'] = get_permalink($p->ID);
            $testimonial['title'] = $p->post_title;
            $testimonial['content'] = $this->get_excerpt($p->post_content, 205);
            $url = wp_get_attachment_url(get_post_meta($p->ID,"tt_img", true));
            $testimonial['thumbnail'] = $url ? $url : '';

            $testimonials[] = $testimonial;
        }
        ob_start();
        require_once plugin_dir_path( __FILE__ ) . '../views/timberin-testimonials.php';
        $view = ob_get_clean();
        return $view;
    }


    public function register(){
        register_widget('TimberinTestimonialsWidget');
    }

    public function register_shorcode(){
        add_shortcode('timberin_testimonials', [$this, 'print_testimonials']);
    }

    private function get_excerpt($content, $max_char) {
        $content = apply_filters('the_content', $content);
        $content = str_replace(']]>', ']]&gt;', $content);
        $content = strip_tags($content);
       if ((strlen($content)>$max_char) && ($espacio = strpos($content, " ", $max_char ))) {
            $content = substr($content, 0, $espacio);
           $excerpt = "<p> $content ... &nbsp; </p>";
        }
        else {
            $excerpt = "<p> $content &nbsp; </p>";
        }
        return $excerpt;
    }
}