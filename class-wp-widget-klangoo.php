<?php

/**
 * Created by PhpStorm.
 * User: McHeimech
 * Date: 7/21/2016
 * Time: 4:20 PM
 */

define ( "KLANGOO_WIDGET_BASE_ID", "klangoo" );
define ( "KLANGOO_CONTAINER_PREFIX", "inner-" );

class WP_Widget_Klangoo extends WP_Widget {

    private static $counter;
    function __construct() {
        $widget_ops = array( 'classname' => 'widget_klangoo', 'description' => __( "A klangoo widget for your site." ) );
        parent::__construct( KLANGOO_WIDGET_BASE_ID, _x( 'Klangoo Widget', 'Klangoo Widget' ), $widget_ops );
        $this -> plugin_directory = plugin_dir_path( __FILE__ );
    }

    function get_id( $tag ){

        $start_pos = strpos( $tag, "id=" );

        $container_id = null;
        if ( false !== $start_pos ){

            $end_pos = strpos($tag, " ", $start_pos);
            $extracted_id = str_replace( array( '"', "'" ), "", substr( $tag, $start_pos+3, $end_pos-$start_pos-3 ) );
            if ( '' != $extracted_id ){
                $container_id = $extracted_id;
            }
        }
        return $container_id;
    }

    function widget( $args, $instance ) {
        if ( !isset( WP_Widget_Klangoo::$counter ) ){
            WP_Widget_Klangoo::$counter = 1;
        }
        else{
            WP_Widget_Klangoo::$counter = WP_Widget_Klangoo::$counter + 1;
        }

        if ( trim( $instance[ "widget_id" ] ) == '' ){
            return;
        }

        /** This filter is documented in wp-includes/default-widgets.php */
        $title = apply_filters( 'widget_title', empty( $instance[ "title" ] ) ? '' : $instance[ "title" ], $instance, $this -> id_base );

        $widget_id = ! empty( $instance[ "widget_id" ] ) ? $instance[ "widget_id" ] : '';

        if (is_single() || 'recom' === substr( $widget_id, 0, 5 ) ) {

            $output = $args['before_widget'];

            if ( $title ) {
                $output .= $args['before_title'] . esc_html( $title ) . $args['after_title'];
            }

            $container = $this -> get_id( $args['before_widget'] );

            if ( $container == null ) {
                $container = KLANGOO_CONTAINER_PREFIX . $this -> id;
                $output .= '<div id="' . esc_attr( $container ) . '" data-widget-id="' . esc_attr( $widget_id ) . '"></div>';
            }

            $output .= '<div data-widget-id="' . esc_attr( $widget_id ) . '"></div>';

            $output .= $args['after_widget'];

            echo wp_kses_post( $output );
        }
    }

    function form( $instance ) {
        $instance = wp_parse_args( ( array ) $instance, array( 'widget_id' => '' ) );
        $widget_id = esc_attr( $instance[ "widget_id" ] );
        ?>
        <p>
            <label for="<?php echo esc_attr( $this -> get_field_id( 'widget_id' ) ); ?>"><?php esc_html_e( 'Widget ID:' ); ?>
                <input class="widefat" id="<?php echo esc_attr( $this -> get_field_id( 'widget_id' ) ); ?>" name="<?php echo esc_attr( $this -> get_field_name( 'widget_id' ) ); ?>" type="text" value="<?php echo esc_attr( $widget_id ); ?>" />
            </label>
        </p>
        <?php
    }

    function update( $new_instance, $old_instance ) {

        if ( '' == strip_tags( $new_instance[ "widget_id" ] ) ){
            return false;
        }

        $instance = $old_instance;
        $instance[ "widget_id" ] = strip_tags( $new_instance[ "widget_id" ] );

        return $instance;
    }
}


function WP_Widget_Klangoo() {
    return register_widget('WP_Widget_Klangoo');
}
/**
 * Since create_function() was deprecated in php 7.2 and removed in php 8
 * We added this logic
*/ 
if(phpversion() >= '8.0.2') {
    add_action ('widgets_init', 'WP_Widget_Klangoo');
    return;
}

add_action( 'widgets_init', create_function( '', 'return register_widget( "WP_Widget_Klangoo" );' ) );
