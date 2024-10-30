<?php

/*
Plugin Name: Modasphere
Description: Modasphere is official plugin developed by Casting Networks LLC for Modasphere's clients, who would like to use Modasphere functionality on their web sites. This plugin is bridge between Modasphere and Wordpress systems. Its main feature is creating web site menu with talents divisions and displaying full information about talent galleries and profiles.
Version: 1.4.4
Author: Casting Networks
Author URI: https://modasphere.com/
License: GPL2

Copyright 2019 Modasphere by Casting Networks  (email: support@modasphere.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function modasphere_install()
{
    $post_id = get_option('modasphere_post_id', 0);

    if ($post_id == 0) {
        $post_data = array(
            'post_title' => 'Modasphere',
            'post_content' => '[modasphere]',
            'post_status' => 'publish',
            'menu_order' => 0,
            'comment_status' => 'closed',
            'post_author' => 1,
            'post_name' => 'modasphere',
            'post_parent' => 0,
            'post_type' => 'page'
        );

        $post_id = wp_insert_post(wp_slash($post_data));
        add_option('modasphere_post_id', $post_id);

    } else {
        wp_publish_post($post_id);
    }

    //    Auth options
    add_option('modasphere_domain', '');
    add_option('modasphere_user_api', '');
    add_option('modasphere_twitter_token', '');
    add_option('modasphere_agency_email', '');
    add_option('modasphere_page_name', 'modasphere');

    //    Menu options
    add_option('modasphere_select_menu', 'main');
    add_option('modasphere_visibility_menu_items', '');

    //    Talent list options
    add_option('modasphere_talent_list_col', '4');
    add_option('modasphere_talent_list_name_display', 'full');
    add_option('modasphere_talent_list_image_width', '250');
    add_option('modasphere_talent_list_image_height', '320');
    add_option('modasphere_talent_list_pagination_count', '8');
    add_option('modasphere_talent_list_cover_photo', 'talent');
    add_option('modasphere_talent_list_thumbnail_overlay', 'disabled');
    add_option('modasphere_synchronization_profile_start_time', '');
    add_option('modasphere_synchronization_photo_start_time', '');
    add_option('modasphere_synchronization_profile_recurrence', 'hourly');
    add_option('modasphere_synchronization_photo_recurrence', 'hourly');
    add_option('modasphere_synchronization_profile_frequency', '86400');

    //    Talent options
    add_option('modasphere_talent_type_gallery', 'fotorama');
    add_option('modasphere_talent_info_position', 'left');
    add_option('modasphere_talent_info_options_view', '');
    add_option('modasphere_fotorama_options', '');
    add_option('modasphere_talent_social', '');
    add_option('modasphere_photo_larger_side_length', '1150');
    add_option('modasphere_photo_short_side_length', '780');
    add_option('modasphere_favorites_page', '');
    add_option('modasphere_search_page', '');
    add_option('modasphere_search_field_options_view', '');
    add_option('modasphere_video_width', '100');
    add_option('modasphere_video_maxheight', '670');
    add_option('modasphere_video_maxwidth', '1140');
    add_option('modasphere_custom_css','');

    $home_path = wp_get_upload_dir();
    if (!is_dir($home_path['basedir'] . "/modasphere-img")) {
        mkdir($home_path['basedir'] . "/modasphere-img");
    }

    if (!is_dir($home_path['basedir'] . "/storage")) {
        mkdir($home_path['basedir'] . "/storage");
    }
}

function modasphere_deactivate()
{
    $post_id = get_option('modasphere_post_id', 0);
    wp_trash_post($post_id);
}

function modasphere_uninstall()
{
    $menu_locations = get_nav_menu_locations();
    $selected_menu = get_option('modasphere_select_menu', 'main');
    $id_menu = $menu_locations[$selected_menu];
    $menu_items = wp_get_nav_menu_items($id_menu);
    $visibility_menu_items = get_option('modasphere_visibility_menu_items', '');
    if ($visibility_menu_items != '') $visibility_menu_items = (array)json_decode($visibility_menu_items);
    foreach ((array)$menu_items as $key => $menu_item) {
        if (is_nav_menu_item($menu_item->ID)) {
            if ($visibility_menu_items['m' . $menu_item->post_name] == 'checked') {
                wp_delete_post($menu_item->ID);
            }
        }
    }

    $post_id = get_option('modasphere_post_id', 0);
    wp_delete_post($post_id, true);

    delete_option('modasphere_domain');
    delete_option('modasphere_twitter_token');
    delete_option('modasphere_select_menu');
    delete_option('modasphere_visibility_menu_items');
    delete_option('modasphere_division_col');
    delete_option('modasphere_talent_list_col');
    delete_option('modasphere_talent_list_name_display');
    delete_option('modasphere_talent_type_gallery');
    delete_option('modasphere_talent_info_position');
    delete_option('modasphere_talent_info_options_view');
    delete_option('modasphere_fotorama_options');
    delete_option('modasphere_talent_social');
    delete_option('modasphere_post_id');
    delete_option('modasphere_talent_list_image_width');
    delete_option('modasphere_talent_list_image_height');
    delete_option('modasphere_talent_list_pagination_count');
    delete_option('modasphere_talent_list_cover_photo');
    delete_option('modasphere_talent_list_thumbnail_overlay');
    delete_option('modasphere_synchronization_profile_start_time');
    delete_option('modasphere_synchronization_photo_start_time');
    delete_option('modasphere_synchronization_profile_recurrence');
    delete_option('modasphere_synchronization_photo_recurrence');
    delete_option('modasphere_photo_larger_side_length');
    delete_option('modasphere_photo_short_side_length');
    delete_option('modasphere_favorites_page');
    delete_option('modasphere_agency_email');
    delete_option('modasphere_search_page');
    delete_option('modasphere_search_field_options_view');
    delete_option('modasphere_synchronization_profile_frequency');
    delete_option('modasphere_video_width');
    delete_option('modasphere_video_maxheight');
    delete_option('modasphere_video_maxwidth');
    delete_option('modasphere_custom_css');
    delete_option('modasphere_page_name');

    include_once 'includes/functions.php';
    $home_path = wp_get_upload_dir();
    modasphere_delete($home_path['basedir'] . '/modasphere-img');
    modasphere_delete($home_path['basedir'] . '/storage');
}

register_activation_hook(__FILE__, 'modasphere_install');
register_deactivation_hook(__FILE__, 'modasphere_deactivate');
register_uninstall_hook(__FILE__, 'modasphere_uninstall');

add_action('wp_enqueue_scripts', 'modasphere_scripts');
function modasphere_scripts()
{
    wp_enqueue_style('bootstrap.min.css', plugin_dir_url(__FILE__) . 'bootstrap/css/bootstrap.min.css');
    wp_enqueue_style('all.min.css', plugin_dir_url(__FILE__) . 'assets/fontawesome/css/all.min.css');
    wp_enqueue_script('bootstrap.bundle.min.js', plugin_dir_url(__FILE__) . 'bootstrap/js/bootstrap.bundle.min.js', array(), false, true);
    wp_enqueue_script('bootstrap.min.js', plugin_dir_url(__FILE__) . 'bootstrap/js/bootstrap.min.js', array(), false, true);
    wp_enqueue_script('fotorama.js', plugin_dir_url(__FILE__) . 'gallery/fotorama/js/fotorama.js', array(), false, true);
    wp_enqueue_script('jquery.lazy.min.js', plugin_dir_url(__FILE__) . 'assets/jquery.lazy/jquery.lazy.min.js', array(), false, true);
    wp_enqueue_style('fotorama.css', plugin_dir_url(__FILE__) . 'gallery/fotorama/css/fotorama.css');
    wp_enqueue_style('modasphere.css', plugin_dir_url(__FILE__) . 'css/modasphere.css');

    wp_add_inline_style( 'modasphere.css', get_option('modasphere_custom_css', ''));
}

add_action('admin_enqueue_scripts', 'modasphere_admin_scripts');

function modasphere_admin_scripts()
{
    wp_enqueue_style('bootstrap.min.css', plugin_dir_url(__FILE__) . 'bootstrap/css/bootstrap.min.css');
    wp_enqueue_script('bootstrap.bundle.min.js', plugin_dir_url(__FILE__) . 'bootstrap/js/bootstrap.bundle.min.js');
    wp_enqueue_script('bootstrap.min.js', plugin_dir_url(__FILE__) . 'bootstrap/js/bootstrap.min.js');
    wp_enqueue_style('modasphere.css', plugin_dir_url(__FILE__) . 'css/modasphere.css');
}

function modasphere_admin_menu()
{
    add_menu_page('Modasphere', 'Modasphere', 8, 'modasphere', 'modasphere_editor', plugin_dir_url(__FILE__) . 'img/m20x20.png');
}

function modasphere_editor()
{
    include_once("includes/settings.php");
}

add_action('admin_menu', 'modasphere_admin_menu');

function modasphere_short()
{
    ob_start();
    include_once("includes/routs.php");
    return ob_get_clean();
}

add_shortcode('modasphere', 'modasphere_short');

add_action( 'upgrader_process_complete', 'modasphere_update_url', 10, 2 );
function modasphere_update_url(){
    delete_option('rewrite_rules');
}

function h1_modasphere_styles()
{
    $post_id = get_option('modasphere_post_id', 0);
    $post = get_post();
    if ($post_id == $post->ID) {
        $custom_css = "
		h1{
			display: none;
		}";
        wp_add_inline_style('modasphere.css', $custom_css);
    }
}

add_action('wp_enqueue_scripts', 'h1_modasphere_styles');

function modasphere_change_title($title)
{
    $mstitle = urldecode(get_query_var('mstitle'));
    if (!empty($mstitle)) {
        $mstitle = explode('-', $mstitle);
        array_pop($mstitle);
        $title = ucwords(implode(" ", $mstitle)) . $title;
    }
    return $title;
}

add_filter('pre_get_document_title', 'modasphere_change_title');


function modasphere_register_widget()
{
    register_widget('modasphere_search_widget');
}

add_action('widgets_init', 'modasphere_register_widget');

class modasphere_search_widget extends WP_Widget
{

    function __construct()
    {
        parent::__construct(
            'modasphere_search_widget',
            'Talent search widget',
            array('description' => 'Talent search widget on the site',)
        );
    }

    public function widget($args, $instance)
    {
        $title = apply_filters('widget_title', $instance['title']);
        echo $args['before_widget'];
        if (!empty($title))
            echo $args['before_title'] . $title . $args['after_title'];
        $post_id = get_option('modasphere_post_id', 0);
        ?>
        <form method="get"
              onsubmit="var sf=document.getElementById('modasphere-search').value; window.location.href = '<?= get_permalink($post_id) ?>ms_search/'+sf; return false;">
            <input type="hidden" name="page_id" value="<?= $post_id ?>">
            <input type="text" class="form-control" id="modasphere-search" name="ms_search"
                   value="" placeholder="Search">
        </form>
        <?php
        echo $args['after_widget'];
    }

    public function form($instance)
    {
        if (isset($instance['title']))
            $title = sanitize_text_field($instance['title']);
        else
            $title = 'Search talent';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
                   name="<?php echo $this->get_field_name('title'); ?>" type="text"
                   value="<?php echo esc_attr($title); ?>"/>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        return $instance;
    }

}

add_action('init', 'do_rewrite');
function do_rewrite()
{
    include_once 'includes/functions.php';
    $ms_page_name = modasphere_get_page_name();

    add_rewrite_rule($ms_page_name . '/ms_search/([^/]+)/(\d+)',
        'index.php?pagename=' . $ms_page_name . '&route=division&ms_search=$matches[1]&pg=$matches[2]',
        'top');
    add_rewrite_rule($ms_page_name . '/ms_search/([^/]+)',
        'index.php?pagename=' . $ms_page_name . '&route=division&ms_search=$matches[1]',
        'top');
    add_rewrite_rule($ms_page_name . '/favorites',
        'index.php?pagename=' . $ms_page_name . '&route=favorites',
        'top');
    add_rewrite_rule($ms_page_name . '/search',
        'index.php?pagename=' . $ms_page_name . '&route=search',
        'top');
    add_rewrite_rule($ms_page_name . '/([^/]+)/(\d+)',
        'index.php?pagename=' . $ms_page_name . '&route=division&division_name=$matches[1]&mstitle=$matches[1]&pg=$matches[2]',
        'top');
    add_rewrite_rule($ms_page_name . '/([^/]+)/([^/]+)/(\d+)',
        'index.php?pagename=' . $ms_page_name . '&route=talent&division_name=$matches[1]&talent=$matches[2]&mstitle=$matches[2]&pg=$matches[3]',
        'top');
    add_rewrite_rule($ms_page_name . '/([^/]+)/([^/]+)/([^/]+)/(\d+)',
        'index.php?pagename=' . $ms_page_name . '&route=talent&division_name=$matches[1]&talent=$matches[2]&mstitle=$matches[2]&gallery=$matches[3]&pg=$matches[4]',
        'top');
    add_rewrite_rule($ms_page_name . '/([^/]+)/([^/]+)/([^/]+)',
        'index.php?pagename=' . $ms_page_name . '&route=talent&division_name=$matches[1]&talent=$matches[2]&mstitle=$matches[2]&gallery=$matches[3]',
        'top');
    add_rewrite_rule($ms_page_name . '/([^/]+)/([^/]+)',
        'index.php?pagename=' . $ms_page_name . '&route=talent&division_name=$matches[1]&talent=$matches[2]&mstitle=$matches[2]',
        'top');
    add_rewrite_rule($ms_page_name . '/([^/]+)',
        'index.php?pagename=' . $ms_page_name . '&route=division&division_name=$matches[1]&mstitle=$matches[1]',
        'top');

    add_filter('query_vars', function ($vars) {
        $vars[] = 'route';
        $vars[] = 'division_name';
        $vars[] = 'mstitle';
        $vars[] = 'ms_search';
        $vars[] = 'pg';
        $vars[] = 'talent';
        $vars[] = 'gallery';
        return $vars;
    });
}

add_action('modasphere_profile_synchronization', 'do_profile_synchronization');
function do_profile_synchronization()
{
    include_once 'includes/functions.php';

    $user_api = get_option('modasphere_user_api', '');
    $set_domain = get_option('modasphere_domain', '');

    $contact_fields = modasphere_api_get_content($set_domain, $user_api, 'contactfields');
    $contact_fields = $contact_fields->objects;
    set_transient('contact_fields', json_encode($contact_fields), YEAR_IN_SECONDS);

    $api_result = modasphere_api_get_content($set_domain, $user_api, 'division');
    if ($api_result != false) {
        $divisions = $api_result->objects;
        $visibility_menu_items = (array)json_decode(get_option('modasphere_visibility_menu_items', ''));
        foreach ($divisions as $div) {
            if (is_array($visibility_menu_items) && $visibility_menu_items['m' . $div->id] == 'checked') {
                modasphere_talent_information($set_domain, $user_api, $div->id);
            }
        }
    }
}

add_action('modasphere_photo_synchronization', 'do_photo_synchronization');
function do_photo_synchronization()
{
    include_once 'includes/functions.php';

    $user_api = get_option('modasphere_user_api', '');
    $set_domain = get_option('modasphere_domain', '');

    $home_path = wp_get_upload_dir();
    if (!is_dir($home_path['basedir'] . "/storage")) {
        mkdir($home_path['basedir'] . "/storage");
    }
    modasphere_download_photo($set_domain, $user_api);
}

add_action('init', 'start_session', 1);

function start_session()
{
    if (!session_id()) {
        session_start();
    }
}

add_action('wp_ajax_modasphere_action_favorite', 'modasphere_action_favorite');
add_action('wp_ajax_nopriv_modasphere_action_favorite', 'modasphere_action_favorite');

function modasphere_action_favorite()
{
    $talent_id = sanitize_text_field($_POST['talent']);
    $content = sanitize_text_field($_POST['content']);
    $status = 0;

    if ($content == 'Add to favorites') {
        $_SESSION['modasphere_favorites'][] = $talent_id;
        $status = 1;
    } elseif ($content == 'Remove from favorites') {
        if (is_array($_SESSION['modasphere_favorites'])) {
            foreach ($_SESSION['modasphere_favorites'] as $key => $item) {
                if ($item == $talent_id) {
                    unset($_SESSION['modasphere_favorites'][$key]);
                    $status = count($_SESSION['modasphere_favorites']);
                }
            }
        }
    }
    echo $status;
    die;
}