<?php

/**
 * Created by PhpStorm.
 * User: McHeimech
 * Date: 7/26/2016
 * Time: 11:20 AM
 */

class Magnet_Fake_Page
{

    var $rel_article_slug = 'related-articles';
    var $rel_article_title = '&nbsp;';

    var $rel_entity_slug = 'related-entities';
    var $rel_entity_title = '&nbsp;';

    var $rel_entity_slug_new = 'entities';

    var $ping_status = 'open';

    /**
     * Class constructor
     */
    function __construct()
    {
        /**
         * We'll wait til WordPress has looked for posts, and then
         * check to see if the requested url matches our target.
         */
        add_filter( 'the_posts', array( &$this, 'detectPost' ) );
    }

    /**
     * Called by the 'detectPost' action
     */
    function createPost( $page_slug, $page_title, $page_id )
    {
        /**
         * Create a fake post.
         */
        $post = new stdClass;

        /**
         * The author ID for the post.  Usually 1 is the sys admin.  Your
         * plugin can find out the real author ID without any trouble.
         */
        $post -> post_author = 1;

        /**
         * The safe name for the post.  This is the post slug.
         */
        $post -> post_name = $page_slug;

        /**
         * Not sure if this is even important.  But gonna fill it up anyway.
         */
        $post -> guid = get_bloginfo( 'wpurl' ) . '/' .$page_slug;


        /**
         * The title of the page.
         */
        $post -> post_title = $page_title;

        /**
         * This is the content of the post.  This is where the output of
         * your plugin should go.  Just store the output from all your
         * plugin function calls, and put the output into this var.
         */
        $post -> post_content = $this -> getContent( $page_slug );

        /**
         * Fake post ID to prevent WP from trying to show comments for
         * a post that doesn't really exist.
         */
        $post -> ID = $page_id;

        /**
         * Static means a page, not a post.
         */
        $post -> post_status = 'static';

        /**
         * Turning off comments for the post.
         */
        $post -> comment_status = 'closed';

        /**
         * Let people ping the post?  Probably doesn't matter since
         * comments are turned off, so not sure if WP would even
         * show the pings.
         */
        $post -> ping_status = $this -> ping_status;

        $post -> comment_count = 0;

        $post -> post_type = 'page';

        /**
         * You can pretty much fill these up with anything you want.  The
         * current date is fine.  It's a fake post right?  Maybe the date
         * the plugin was activated?
         */
        $post -> post_date = current_time( 'mysql' );
        $post -> post_date_gmt = current_time( 'mysql', 1 );

        return( $post );
    }

    function getContent( $page_slug )
    {
        if ( '' != get_option( 'magnet_customer_id' ) && $this -> rel_article_slug == $page_slug ){
            return '<div data-widget-id="'. esc_attr( get_option( 'magnet_related_page_id' ) ) .'"></div>' . "\r\n";
        }elseif ( '' != get_option( 'magnet_customer_id' ) && $this -> rel_entity_slug == $page_slug  ){
            return '<div data-widget-id="'. esc_attr( get_option( 'magnet_related_entity_id' ) ) .'"></div>' . "\r\n";
        }elseif ( '' != get_option( 'magnet_customer_id' ) && $this -> rel_entity_slug_new == $page_slug  ){
            $content = '<div data-widget-id="'. esc_attr( get_option( 'magnet_related_entity_id' ) ) .'"></div><br />' . "\r\n";

            $follow_widget = '<div data-widget-id="'. esc_attr( get_option( 'magnet_entity_follow_widget_id' ) ) .'"></div><br />' . "\r\n";

            if ( '' != get_option( 'magnet_entity_follow_widget_id' ) ) {
                if ( get_option( 'magnet_entity_follow_widget_top' ) == 'on' )
                    $content = $follow_widget . $content;

                if ( get_option( 'magnet_entity_follow_widget_bottom' ) == 'on' )
                    $content = $content . $follow_widget;
            }
            return( $content );
        }
    }

    function detectPost( $posts ){
        global $wp;
        global $wp_query;
        /**
         * Check if the requested page matches our target
         */
        if ( strtolower( $wp -> request ) == strtolower( $this -> rel_article_slug ) || ( isset( $wp->query_vars['page_id'] ) && $wp -> query_vars[ "page_id" ] == $this -> rel_article_slug ) ) {
			$posts = array();
            $post = $this -> createPost( $this -> rel_article_slug, $this -> rel_article_title, -900 );
			$posts[] = $post;
			$wp_query->queried_object = $post;
			$wp_query -> is_page = true;
			//Not sure if this one is necessary but might as well set it like a true page
			$wp_query -> is_singular = false;
			$wp_query -> is_home = false;
			$wp_query -> is_archive = false;
			$wp_query -> is_category = false;
			//Longer permalink structures may not match the fake post slug and cause a 404 error so we catch the error here
			unset( $wp_query -> query[ "error" ] );
			$wp_query -> query_vars[ "error" ] = "";
			$wp_query -> is_404 = false;
        }elseif( strtolower( $wp -> request ) == strtolower( $this -> rel_entity_slug ) || ( isset( $wp->query_vars['page_id'] ) && $wp -> query_vars[ "page_id" ] == $this -> rel_entity_slug ) ) {
			$posts = array();
            $post = $this -> createPost( $this -> rel_entity_slug, $this -> rel_entity_title, -800 );
			$posts[] = $post;
			$wp_query->queried_object = $post;
			$wp_query -> is_page = true;
			//Not sure if this one is necessary but might as well set it like a true page
			$wp_query -> is_singular = false;
			$wp_query -> is_home = false;
			$wp_query -> is_archive = false;
			$wp_query -> is_category = false;
			//Longer permalink structures may not match the fake post slug and cause a 404 error so we catch the error here
			unset( $wp_query -> query[ "error" ] );
			$wp_query -> query_vars[ "error" ] = "";
			$wp_query -> is_404 = false;
        }elseif( ( strpos ( strtolower( $wp -> request ), strtolower( $this -> rel_entity_slug_new )) === 0 )  || ( isset( $wp -> query_vars['page_id'] ) && $wp -> query_vars[ "page_id" ] == $this -> rel_entity_slug_new ) ) {
			$posts = array();
            $post = $this -> createPost( $this -> rel_entity_slug_new, $this -> rel_entity_title, -700 );
			$posts[] = $post;
			$wp_query->queried_object = $post;
			$wp_query -> is_page = true;
			//Not sure if this one is necessary but might as well set it like a true page
			$wp_query -> is_singular = false;
			$wp_query -> is_home = false;
			$wp_query -> is_archive = false;
			$wp_query -> is_category = false;
			//Longer permalink structures may not match the fake post slug and cause a 404 error so we catch the error here
			unset( $wp_query -> query[ "error" ] );
			$wp_query -> query_vars[ "error" ] = "";
			$wp_query -> is_404 = false;
        }
			
        return $posts;
    }
}

/**
 * Create an instance of our class.
 */
new Magnet_Fake_Page();