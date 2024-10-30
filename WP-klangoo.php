<?php

/*
Plugin Name: Klangoo WordPress plugin
Plugin URI: http://magnet.klangoo.com/wp-plugin
Description: This plugin allows Klangoo clients to integrate our services inside their WordPress sites with ease.
Version: 1.1.1
Author: Klangoo
Author URI: http://klangoo.com
License: GPL2
*/

include_once( 'class-wp-widget-klangoo.php' );
include_once( 'class-fake-page.php' ); 

if ( ! defined( 'ABSPATH' ) ) exit;

function magnet_remove() {
    unregister_setting( 'magnet-settings-group', 'magnet_related_widget_id' );
    unregister_setting( 'magnet-settings-group', 'magnet_related_widget_order' );
    unregister_setting( 'magnet-settings-group', 'magnet_recommended_widget_id' );
    unregister_setting( 'magnet-settings-group', 'magnet_recommended_widget_order' );
    unregister_setting( 'magnet-settings-group', 'magnet_entities_widget_id' );
    unregister_setting( 'magnet-settings-group', 'magnet_entities_widget_order' );
    unregister_setting( 'magnet-settings-group', 'magnet_intext_widget_id' );
    unregister_setting( 'magnet-settings-group', 'magnet_intext_widget_order' );
    unregister_setting( 'magnet-settings-group', 'magnet_follow_widget_id' );
    unregister_setting( 'magnet-settings-group', 'magnet_follow_widget_order' );
    unregister_setting( 'magnet-settings-group', 'magnet_customer_id' );
    unregister_setting( 'magnet-settings-group', 'magnet_related_page_id' );
    unregister_setting( 'magnet-settings-group', 'magnet_related_entity_id' );
    unregister_setting( 'magnet-settings-group', 'magnet_entity_follow_widget_id' );
    unregister_setting( 'magnet-settings-group', 'magnet_entity_follow_widget_top' );
    unregister_setting( 'magnet-settings-group', 'magnet_entity_follow_widget_bottom' );
}

function magnet_head() {

    if ( is_single() ) {
        $page_type = "article";
    }else {
        $page_type = "main";
    }

    if( -900 != get_the_ID() && -800 != get_the_ID() && -700 != get_the_ID() ) {
        if ( "article" == $page_type ) {
            ?>
            <meta itemprop="identifier" content="<?php the_ID(); ?>"/>
            <meta itemprop="headline" content="<?php the_title(); ?>"/>
            <meta itemprop="description" content="<?php echo esc_attr( magnet_get_excerpt( get_the_ID() ) ); ?>"/>
            <meta itemprop="pageType" content="<?php echo esc_attr( $page_type ); ?>"/>
            <meta itemprop="datePublished" content="<?php $date = new DateTime( get_the_time('Y-m-d G:i:s+'.get_option('gmt_offset')) ); $date->setTimezone(new DateTimeZone('UTC')); echo $date->format('c'); ?>"/>
            <meta itemprop="dateModified" content="<?php $date = new DateTime( get_the_modified_date('Y-m-d G:i:s+'.get_option('gmt_offset')) ); $date->setTimezone(new DateTimeZone('UTC')); echo $date->format('c'); ?>"/>
            <meta itemprop="url" content="<?php echo esc_attr( get_permalink() ) ?>"/>
            <meta itemprop="inLanguage" content="<?php echo esc_attr( substr( get_locale(), 0, 2 ) ); ?>"/>
            <meta itemprop="author" content="<?php echo esc_attr( get_the_author_meta( 'display_name', get_post_field( 'post_author', get_the_ID() ) ) ) ?>"/>

            <?php
            if ( has_post_thumbnail() ) {
                $image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'medium', true );
                ?>
                <meta itemprop="thumbnailUrl" content="<?php echo esc_url( $image[ 0 ] ); ?>"/>
                <?php
            }
        }else{ ?>
            <meta itemprop="pageType" content="<?php echo esc_attr( $page_type ); ?>"/>
            <meta itemprop="inLanguage" content="<?php echo esc_attr( substr( get_locale(), 0, 2 ) ); ?>"/>
            <?php
        }
    }elseif ( -900 == get_the_ID() ){
        ?>
        <meta itemprop="pageType" content="relart" />
        <?php
    }elseif( -800 == get_the_ID() || -700 == get_the_ID() ){
        ?>
        <meta itemprop="pageType" content="entrelart" />
        <?php
    }
}

function magnet_get_excerpt( $post_id ) {
    $excerpt = '';

    if( $post = get_post( $post_id ) ) {
        $excerpt = $post->post_excerpt ? $post->post_excerpt : $post->post_content;

        $excerpt = strip_tags( $excerpt );
        $excerpt = strip_shortcodes( $excerpt );
        $excerpt = wp_trim_words( $excerpt );
    }

    return $excerpt;
}

function magnet_settings() {
    register_setting( 'magnet-settings-group', 'magnet_related_widget_id' );
    register_setting( 'magnet-settings-group', 'magnet_related_widget_order' );
    register_setting( 'magnet-settings-group', 'magnet_recommended_widget_id' );
    register_setting( 'magnet-settings-group', 'magnet_recommended_widget_order' );
    register_setting( 'magnet-settings-group', 'magnet_entities_widget_id' );
    register_setting( 'magnet-settings-group', 'magnet_entities_widget_order' );
    register_setting( 'magnet-settings-group', 'magnet_intext_widget_id' );
    register_setting( 'magnet-settings-group', 'magnet_intext_widget_order' );
    register_setting( 'magnet-settings-group', 'magnet_follow_widget_id' );
    register_setting( 'magnet-settings-group', 'magnet_follow_widget_order' );
    register_setting( 'magnet-settings-group', 'magnet_customer_id' );
    register_setting( 'magnet-settings-group', 'magnet_related_page_id' );
    register_setting( 'magnet-settings-group', 'magnet_related_entity_id' );
    register_setting( 'magnet-settings-group', 'magnet_entity_follow_widget_id' );
    register_setting( 'magnet-settings-group', 'magnet_entity_follow_widget_top' );
    register_setting( 'magnet-settings-group', 'magnet_entity_follow_widget_bottom' );
}

function magnet_menu() {
    add_menu_page( 'Magnet Settings', 'Magnet Settings', 'administrator', 'magnet-settings', 'magnet_settings_page', 'dashicons-admin-generic' );
}

function magnet_settings_page() {
    ?>
    <div class="wrap">
        <h2>Magnet settings</h2>
        <form method="post" action="options.php">
            <?php settings_fields( 'magnet-settings-group' ); ?>
            <?php do_settings_sections( 'magnet-settings-group' ); ?>
            <style>
                .magnet-table th{
                    width: auto;
                }
            </style>
            <table class="form-table magnet-table">
                <tr valign="top">
                    <th scope="row" style="width:250px;">Customer ID</th>
                    <td style="width:180px;"><input type="number" name="magnet_customer_id" placeholder="Customer ID" value="<?php echo esc_attr( get_option( 'magnet_customer_id' ) ); ?>" /></td>
                    <td scope="col"><b>Order</b></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Related articles widget ID</th>
                    <td><input type="text" name="magnet_related_widget_id" placeholder="Widget ID" value="<?php echo esc_attr( get_option( 'magnet_related_widget_id' ) ); ?>" /></td>
                    <td><input type="number" name="magnet_related_widget_order" placeholder="Order" value="<?php echo esc_attr( get_option( 'magnet_related_widget_order' ) ); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Recommended articles widget ID</th>
                    <td><input type="text" name="magnet_recommended_widget_id" placeholder="Widget ID" value="<?php echo esc_attr( get_option( 'magnet_recommended_widget_id' ) ); ?>" /></td>
                    <td><input type="number" name="magnet_recommended_widget_order" placeholder="Order" value="<?php echo esc_attr( get_option( 'magnet_recommended_widget_order' ) ); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Entities widget ID</th>
                    <td><input type="text" name="magnet_entities_widget_id" placeholder="Widget ID" value="<?php echo esc_attr( get_option( 'magnet_entities_widget_id' ) ); ?>" /></td>
                    <td><input type="number" name="magnet_entities_widget_order" placeholder="Order" value="<?php echo esc_attr( get_option( 'magnet_entities_widget_order' ) ); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Follow widget ID</th>
                    <td><input type="text" name="magnet_follow_widget_id" placeholder="Widget ID" value="<?php echo esc_attr( get_option( 'magnet_follow_widget_id' ) ); ?>" /></td>
                    <td><input type="number" name="magnet_follow_widget_order" placeholder="Order" value="<?php echo esc_attr( get_option( 'magnet_follow_widget_order' ) ); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">In-Text widget ID</th>
                    <td colspan="2"><input type="text" name="magnet_intext_widget_id" placeholder="Widget ID" value="<?php echo esc_attr( get_option( 'magnet_intext_widget_id' ) ); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Related articles page ID</th>
                    <td colspan="2"><input type="text" name="magnet_related_page_id" placeholder="Widget ID" value="<?php echo esc_attr( get_option( 'magnet_related_page_id' ) ); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Related entities page ID</th>
                    <td><input type="text" name="magnet_related_entity_id" placeholder="Widget ID" value="<?php echo esc_attr( get_option( 'magnet_related_entity_id' ) ); ?>" /></td>
                    <th scope="col">Show on</th>
                </tr>
                <tr valign="top">
                    <th scope="row">Related entities page follow widget id</th>
                    <td><input type="text" name="magnet_entity_follow_widget_id" placeholder="Widget ID" value="<?php echo esc_attr( get_option ( 'magnet_entity_follow_widget_id' ) ); ?>" /></td>
                    <td>
                        <label for="magnet_entity_follow_widget_top">Top</label>
                        <input type="checkbox" name="magnet_entity_follow_widget_top" id="magnet_entity_follow_widget_top"<?php echo ( esc_attr( get_option( 'magnet_entity_follow_widget_top' ) ) == 'on' ? ' checked="checked"' : ''); ?> />&nbsp;&nbsp;
                        <label for="magnet_entity_follow_widget_bottom">Bottom</label>
                        <input type="checkbox" name="magnet_entity_follow_widget_bottom" id="magnet_entity_follow_widget_bottom"<?php echo ( esc_attr( get_option( 'magnet_entity_follow_widget_bottom' ) ) == 'on' ? ' checked="checked"' : ''); ?> /></td>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

function magnet_add_widgets( $content ){
    if ( is_single() ) {
        $content = '<div itemprop="articleBody">' . $content . '</div>';

        $related_order     =  get_option( 'magnet_related_widget_order' );
        $recommended_order =  get_option( 'magnet_recommended_widget_order' );
        $entities_order    =  get_option( 'magnet_entities_widget_order' );
        $follow_order      =  get_option( 'magnet_follow_widget_order' );

        $widgets_order = array(
            "related"       => $related_order,
            "recommended"   => $recommended_order,
            "entities"      => $entities_order,
            "follow"        => $follow_order
        );

        asort( $widgets_order );

        foreach( $widgets_order as $widget_name => $widget_order ) {

            if ( "related" == $widget_name && '' != get_option( 'magnet_related_widget_id' ) ) {

                $content .= '<div data-widget-id="' . esc_attr( get_option( 'magnet_related_widget_id' ) ) . '"></div><br />' . "\r\n";

            } elseif ( "recommended" == $widget_name && '' != get_option( 'magnet_recommended_widget_id' ) ){

                $content .= '<div data-widget-id="' . esc_attr(get_option( 'magnet_recommended_widget_id' )) . '"></div><br />' . "\r\n";

            }elseif ( "entities" == $widget_name && '' != get_option( 'magnet_entities_widget_id' ) ) {

                $content .= '<div data-widget-id="' . esc_attr( get_option( 'magnet_entities_widget_id' ) ) . '"></div><br />' . "\r\n";

            } elseif ( "follow" == $widget_name && '' != get_option( 'magnet_follow_widget_id' ) ){

                $content .= '<div data-widget-id="' . esc_attr( get_option( 'magnet_follow_widget_id' ) ) . '"></div><br />' . "\r\n";

            }
        }

        if ( '' != get_option( 'magnet_intext_widget_id' ) ) {
            $content .= '<div data-widget-id="' . esc_attr( get_option( 'magnet_intext_widget_id' ) ) . '"></div><br />';
        }
    }

    return $content;
}

function magnet_add_script() {
    if ( '' != get_option( 'magnet_customer_id' ) ) {
        $plugin_version = get_file_data( __FILE__ , array( 'Version' => 'Version' ), 'plugin' );

        $output = '<!-- Klangoo Magnet Plugin, Version: ' . esc_html( $plugin_version['Version'] ) . "\r\n"
					. 'Related widget id: ' . esc_attr( get_option( 'magnet_related_widget_id' ) ) . "\r\n"
					. 'Recommended widget id: ' . esc_attr(get_option( 'magnet_recommended_widget_id' )) . "\r\n"
					. 'Entities widget id: ' . esc_attr( get_option( 'magnet_entities_widget_id' ) ) . "\r\n"
					. 'Follow widget id: ' . esc_attr( get_option( 'magnet_follow_widget_id' ) ) . "\r\n"
					. 'In-Text id: ' . esc_attr( get_option( 'magnet_intext_widget_id' ) ) . "\r\n"
					. 'Related articles page id: ' . esc_attr( get_option( 'magnet_related_page_id' ) ) . "\r\n"
					. 'Related entities page id: ' . esc_attr( get_option( 'magnet_related_entity_id' ) ) . "\r\n"
					. ' -->' . "\r\n";
        $output .= '<script src="//magnetapi.klangoo.com/w/Widgets_' . esc_attr( get_option( 'magnet_customer_id' ) ) . '.js" async></script>' . "\r\n";

        echo $output;
    }
}

function magnet_allowed_html( $allowed, $context ) {
    if( $context == 'post' ) {
        $allowed['div']['data-widget-id']	= true;
    }

    return $allowed;
}

/* Runs on plugin deactivation*/
register_deactivation_hook( __FILE__, 'magnet_remove' );

add_action( 'wp_head', 'magnet_head' );
add_action( 'admin_init', 'magnet_settings' );
add_action( 'admin_menu', 'magnet_menu' );
add_action( "wp_footer", "magnet_add_script" );

add_filter( "the_content", "magnet_add_widgets",11,2 );
add_filter( 'wp_kses_allowed_html', 'magnet_allowed_html', 10, 2 );