<?php
include_once 'functions.php';
$user_api = get_option('modasphere_user_api', '');
$set_domain = get_option('modasphere_domain', '');

if (!empty($_POST)) {
//    ------------------- Save talent list settings -------------------
    if (isset($_POST['talent-list-set'])) {

        update_option('modasphere_talent_list_col', sanitize_text_field($_POST['talent_list_columns']));
        update_option('modasphere_talent_list_name_display', sanitize_text_field($_POST['talent_list_name_display']));
        update_option('modasphere_talent_list_cover_photo', sanitize_text_field($_POST['talent_list_cover_photo']));
        $old_width = get_option('modasphere_talent_list_image_width', '');
        $old_height = get_option('modasphere_talent_list_image_height', '');
        if ($old_width != sanitize_text_field($_POST['talent_list_image_width']) || $old_height != sanitize_text_field($_POST['talent_list_image_height'])) {
            $home_path = wp_get_upload_dir();
            modasphere_delete_files($home_path['basedir'] . '/modasphere-img');
        }
        $modasphere_image_with = intval(sanitize_text_field($_POST['talent_list_image_width']));
        $modasphere_image_height = intval(sanitize_text_field($_POST['talent_list_image_height']));

        if ($modasphere_image_with && $modasphere_image_height) {
            update_option('modasphere_talent_list_image_width', $modasphere_image_with);
            update_option('modasphere_talent_list_image_height', $modasphere_image_height);
        }

        $modasphere_pagination_count = intval(sanitize_text_field($_POST['talent_list_pagination_count']));

        if ($modasphere_pagination_count) {
            update_option('modasphere_talent_list_pagination_count', $modasphere_pagination_count);
        }
    } elseif (isset($_POST['talent-set']) || isset($_POST['talent-set-all'])) {
//    ------------------- Save talent settings -------------------
        $contact_fields = modasphere_api_get_content($set_domain, $user_api, 'contactfields');
        $contact_fields = $contact_fields->objects;
        set_transient('contact_fields', json_encode($contact_fields), YEAR_IN_SECONDS);

        update_option('modasphere_talent_type_gallery', sanitize_text_field($_POST['TypeGallery']));
        if (sanitize_text_field($_POST['TypeGallery']) == 'list') {
            update_option('modasphere_talent_info_position', 'top');
        } else {
            update_option('modasphere_talent_info_position', sanitize_text_field($_POST['talent_info_position']));
        }

        $photo_larger_side_length = get_option('modasphere_photo_larger_side_length', '1150');
        $photo_short_side_length = get_option('modasphere_photo_short_side_length', '780');
        if (sanitize_text_field($_POST['photo_larger_side_length']) != $photo_larger_side_length || sanitize_text_field($_POST['photo_short_side_length']) != $photo_short_side_length) {
            $home_path = wp_get_upload_dir();
            modasphere_delete($home_path['basedir'] . '/storage');
        }
        update_option('modasphere_photo_larger_side_length', sanitize_text_field($_POST['photo_larger_side_length']));
        update_option('modasphere_photo_short_side_length', sanitize_text_field($_POST['photo_short_side_length']));
        update_option('modasphere_video_width', sanitize_text_field($_POST['video_data_width']));
        update_option('modasphere_video_maxheight', sanitize_text_field($_POST['video_data_maxheight']));
        update_option('modasphere_video_maxwidth', sanitize_text_field($_POST['video_data_maxwidth']));

        $modasphere_visibility_menu = get_option('modasphere_visibility_menu_items', '');
        if ($modasphere_visibility_menu != '') {
            $modasphere_visibility_menu = (array)json_decode($modasphere_visibility_menu);

            $api_result = modasphere_api_get_content($set_domain, $user_api, 'contactfields');
            $talent_info_options_view = array();
            $talent_social = array();
            $talent_thumbnail_overlay = array();
            foreach ($modasphere_visibility_menu as $key => $mvm) {
                if ($mvm == 'checked') {
                    if(isset($_POST['talent-set-all']) && !empty($_POST['div-active'])){
                        $div_active = sanitize_text_field($_POST['div-active']);
                    }else{
                        $div_active = substr($key, 1);
                    }
                    $div_id = substr($key, 1);
                    if ($api_result != false) {
                        $contact_fields = $api_result->objects;
                        foreach ($contact_fields as $cont) {
                            $talent_info_options_view[$div_id][$cont->name] = (isset($_POST[$div_active . '_m_o_d_' . $cont->name])) ? sanitize_text_field($_POST[$div_active . '_m_o_d_' . $cont->name]) : '';
                        }
                    }
                    $talent_social[$div_id]['talent_instagram'] = (isset($_POST[$div_active . '_m_o_d_talent_instagram'])) ? sanitize_text_field($_POST[$div_active . '_m_o_d_talent_instagram']) : '';
                    $talent_social[$div_id]['talent_instagram_username'] = (isset($_POST[$div_active . '_m_o_d_talent_instagram_username'])) ? sanitize_text_field($_POST[$div_active . '_m_o_d_talent_instagram_username']) : '';
                    $talent_social[$div_id]['talent_instagram_f_count'] = (isset($_POST[$div_active . '_m_o_d_talent_instagram_f_count'])) ? sanitize_text_field($_POST[$div_active . '_m_o_d_talent_instagram_f_count']) : '';
                    $talent_social[$div_id]['talent_twitter'] = (isset($_POST[$div_active . '_m_o_d_talent_twitter'])) ? sanitize_text_field($_POST[$div_active . '_m_o_d_talent_twitter']) : '';
                    $talent_social[$div_id]['talent_twitter_username'] = (isset($_POST[$div_active . '_m_o_d_talent_twitter_username'])) ? sanitize_text_field($_POST[$div_active . '_m_o_d_talent_twitter_username']) : '';
                    $talent_social[$div_id]['talent_twitter_f_count'] = (isset($_POST[$div_active . '_m_o_d_talent_twitter_f_count'])) ? sanitize_text_field($_POST[$div_active . '_m_o_d_talent_twitter_f_count']) : '';
                    $talent_social[$div_id]['talent_casting_networks'] = (isset($_POST[$div_active . '_m_o_d_talent_casting_networks'])) ? sanitize_text_field($_POST[$div_active . '_m_o_d_talent_casting_networks']) : '';
                    $talent_social[$div_id]['talent_social_link_disable'] = (isset($_POST[$div_active . '_m_o_d_talent_social_link_disable'])) ? sanitize_text_field($_POST[$div_active . '_m_o_d_talent_social_link_disable']) : '';
                    $talent_thumbnail_overlay[$div_id] = (isset($_POST['talent_' . $div_active . '_thumbnail_overlay'])) ? sanitize_text_field($_POST['talent_' . $div_active . '_thumbnail_overlay']) : '';
                }
            }
            update_option('modasphere_talent_info_options_view', json_encode($talent_info_options_view));
            update_option('modasphere_talent_social', json_encode($talent_social));
            update_option('modasphere_talent_list_thumbnail_overlay', json_encode($talent_thumbnail_overlay));
        }
        $fotorama_options = array();
        $fotorama_options['fullscreen'] = sanitize_text_field($_POST['fullscreen']);
//        $fotorama_options['thumbnails'] = sanitize_text_field($_POST['thumbnails']);
        $fotorama_options['loop'] = sanitize_text_field($_POST['loop']);
        $fotorama_options['autoplay'] = sanitize_text_field($_POST['autoplay']);
        $fotorama_options['keyboard'] = sanitize_text_field($_POST['keyboard']);
        $fotorama_options['two_photo_on_slide'] = sanitize_text_field($_POST['two_photo_on_slide']);
        $fotorama_options['thumbnails'] = ($fotorama_options['two_photo_on_slide'] == 'checked') ? '' : sanitize_text_field($_POST['thumbnails']);
        $fotorama_options['transition'] = sanitize_text_field($_POST['transition']);

        update_option('modasphere_fotorama_options', json_encode($fotorama_options));

        $modashere_search_page = get_option('modasphere_search_page', '');
        if ($modashere_search_page == 'checked') {
            $api_search_field = modasphere_api_get_content($set_domain, $user_api, 'search_field');
            $search_field_options_view = array();
            $id_option = 'search';
            if ($api_search_field != false) {
                $search_fields = $api_search_field->objects;
                foreach ($search_fields as $sf) {
                    if (isset($_POST[$id_option . '_as_' . $sf->contact_field->name . '_' . $sf->id])) {
                        $search_field_options_view[$sf->contact_field->name] = array(
                            'status' => sanitize_text_field($_POST[$id_option . '_as_' . $sf->contact_field->name . '_' . $sf->id]),
                            'field_info' => $sf
                        );
                    }
                }
            }
            update_option('modasphere_search_field_options_view', json_encode($search_field_options_view));
        }

    } elseif (isset($_POST['visibility-menu-set'])) {
        //    ------------------- Save visibility of menu items -------------------
        $post_id = get_option('modasphere_post_id', 0);
        if ($post_id != 0) {
            $ms_new_page_name = sanitize_text_field($_POST['page_name']);
            update_option('modasphere_page_name', $ms_new_page_name);

            $ms_page_slug = modasphere_get_page_name();
            $post_data = array(
                'ID' => $post_id,
                'post_title' => $ms_new_page_name,
                'post_content' => '[modasphere]',
                'post_status' => 'publish',
                'menu_order' => 0,
                'comment_status' => 'closed',
                'post_author' => 1,
                'post_name' => $ms_page_slug,
                'post_parent' => 0,
                'post_type' => 'page'
            );
            $post_id = wp_insert_post(wp_slash($post_data));
        }

        $api_result = modasphere_api_get_content($set_domain, $user_api, 'division');
        if ($api_result != false) {
            $divisions = $api_result->objects;
            $visibility_menu_items = array();
            foreach ($divisions as $div) {
                $visibility_menu_items['m' . $div->id] = sanitize_text_field($_POST['m' . $div->id]);
            }
            update_option('modasphere_visibility_menu_items', json_encode($visibility_menu_items));

            require_once(ABSPATH . 'wp-admin/includes/nav-menu.php');

            $menu_locations = get_nav_menu_locations();
            $selected_menu = get_option('modasphere_select_menu', 'main');
            $id_menu = $menu_locations[$selected_menu];
            $menu_items = wp_get_nav_menu_items($id_menu);

            foreach ((array)$menu_items as $key => $menu_item) {
                if (is_nav_menu_item($menu_item->ID)) {
                    wp_delete_post($menu_item->ID);
                }
            }

            $fl_search = 0;
            $fl2_search = 0;
            $fl3_search = 0;

            $talent_count = get_option('modasphere_talent_list_pagination_count', '');
            foreach ($divisions as $div) {
                if ($div->parent_id == '' && is_array($visibility_menu_items) && $visibility_menu_items['m' . $div->id] == 'checked') {
                    foreach ((array)$menu_items as $key => $menu_item) {
                        if (is_nav_menu_item($menu_item->ID) && $menu_item->post_name == $div->id) {
                            $fl_search++;
                        }
                    }
                    if ($fl_search == 0) {
                        $args = array(
                            array(
                                'menu-item-db-id' => '',
                                'menu-item-object-id' => '2',
                                'menu-item-object' => 'custom',
                                'menu-item-parent-id' => 0,
                                'menu-item-position' => 0,
                                'menu-item-type' => 'custom',
                                'menu-item-title' => $div->name,
                                'menu-item-url' => get_permalink($post_id) . str_replace(' ', '-', $div->name) . '-' . $div->id,
                                'menu-item-description' => '',
                                'menu-item-attr-title' => '',
                                'menu-item-target' => '',
                                'menu-item-classes' => '',
                                'menu-item-xfn' => ''
                            )
                        );

                        $items_id = wp_save_nav_menu_items($id_menu, $args);
                        if ($items_id) {
                            $items_post = array(
                                'ID' => $items_id[0],
                                'post_status' => 'publish',
                                'post_name' => $div->id
                            );
                            wp_update_post($items_post);
                        }

                        foreach ($divisions as $div2) {
                            if ($div2->parent_id != '' && $div2->parent_id == $div->id && is_array($visibility_menu_items) && $visibility_menu_items['m' . $div2->id] == 'checked') {
                                foreach ((array)$menu_items as $key => $menu_item) {
                                    if (is_nav_menu_item($menu_item->ID) && $menu_item->post_name == $div2->id) {
                                        $fl2_search++;
                                    }
                                }
                                if ($fl2_search == 0) {
                                    $args = array(
                                        array(
                                            'menu-item-db-id' => '',
                                            'menu-item-object-id' => '2',
                                            'menu-item-object' => 'custom',
                                            'menu-item-parent-id' => $items_id[0],
                                            'menu-item-position' => 0,
                                            'menu-item-type' => 'custom',
                                            'menu-item-title' => $div2->name,
                                            'menu-item-url' => get_permalink($post_id) . str_replace(' ', '-', $div2->name) . '-' . $div2->id,
                                            'menu-item-description' => '',
                                            'menu-item-attr-title' => '',
                                            'menu-item-target' => '',
                                            'menu-item-classes' => '',
                                            'menu-item-xfn' => ''
                                        )
                                    );
                                    $items_id2 = wp_save_nav_menu_items($id_menu, $args);
                                    if ($items_id2) {
                                        $items_post = array(
                                            'ID' => $items_id2[0],
                                            'post_status' => 'publish',
                                            'post_name' => $div2->id,
                                        );
                                        wp_update_post($items_post);
                                    }

                                    foreach ($divisions as $div3) {
                                        if ($div3->parent_id != '' && $div3->parent_id == $div2->id && is_array($visibility_menu_items) && $visibility_menu_items['m' . $div3->id] == 'checked') {
                                            foreach ((array)$menu_items as $key => $menu_item) {
                                                if (is_nav_menu_item($menu_item->ID) && $menu_item->post_name == $div3->id) {
                                                    $fl3_search++;
                                                }
                                            }
                                            if ($fl3_search == 0) {
                                                $args = array(
                                                    array(
                                                        'menu-item-db-id' => '',
                                                        'menu-item-object-id' => '2',
                                                        'menu-item-object' => 'custom',
                                                        'menu-item-parent-id' => $items_id2[0],
                                                        'menu-item-position' => 0,
                                                        'menu-item-type' => 'custom',
                                                        'menu-item-title' => $div3->name,
                                                        'menu-item-url' => get_permalink($post_id) . str_replace(' ', '-', $div3->name) . '-' . $div3->id,
                                                        'menu-item-description' => '',
                                                        'menu-item-attr-title' => '',
                                                        'menu-item-target' => '',
                                                        'menu-item-classes' => '',
                                                        'menu-item-xfn' => ''
                                                    )
                                                );

                                                $items_id3 = wp_save_nav_menu_items($id_menu, $args);
                                                if ($items_id3) {
                                                    $items_post = array(
                                                        'ID' => $items_id3[0],
                                                        'post_status' => 'publish',
                                                        'post_name' => $div3->id,
                                                    );
                                                    wp_update_post($items_post);
                                                }
                                            }
                                            $fl3_search = 0;
                                        }
                                    }
                                }
                                $fl2_search = 0;
                            }
                        }
                    }
                    $fl_search = 0;
                }
            }
        }

        $modasphere_visibility_menu = get_option('modasphere_visibility_menu_items', '');
        if ($modasphere_visibility_menu != '') {
            $modasphere_visibility_menu = (array)json_decode($modasphere_visibility_menu);
            //---------- Set / unset favorites menu ---------
            $modashere_favorites_page = sanitize_text_field($_POST['use_favorites_page']);
            require_once(ABSPATH . 'wp-admin/includes/nav-menu.php');
            $menu_locations = get_nav_menu_locations();
            $selected_menu = get_option('modasphere_select_menu', 'main');
            $id_menu = $menu_locations[$selected_menu];
            $menu_items = wp_get_nav_menu_items($id_menu);

            foreach ((array)$menu_items as $key => $menu_item) {
                if (is_nav_menu_item($menu_item->ID)) {
                    if ($menu_item->post_name === 'modasphere_favorites_page') {
                        wp_delete_post($menu_item->ID);
                    }
                }
            }

            if ($modashere_favorites_page === 'checked') {
                $fl_search = 0;
                foreach ((array)$menu_items as $key => $menu_item) {
                    if (is_nav_menu_item($menu_item->ID) && $menu_item->post_name === 'modasphere_favorites_page') {
                        $fl_search++;
                    }
                }
                if ($fl_search == 0) {
                    $args = array(
                        array(
                            'menu-item-db-id' => '',
                            'menu-item-object-id' => '2',
                            'menu-item-object' => 'custom',
                            'menu-item-parent-id' => 0,
                            'menu-item-position' => 0,
                            'menu-item-type' => 'custom',
                            'menu-item-title' => 'Favorites',
                            'menu-item-url' => get_permalink($post_id) . 'favorites',
                            'menu-item-description' => '',
                            'menu-item-attr-title' => '',
                            'menu-item-target' => '',
                            'menu-item-classes' => '',
                            'menu-item-xfn' => ''
                        )
                    );
                    $items_id = wp_save_nav_menu_items($id_menu, $args);
                    if ($items_id) {
                        $items_post = array(
                            'ID' => $items_id[0],
                            'post_status' => 'publish',
                            'post_name' => 'modasphere_favorites_page'
                        );
                        wp_update_post($items_post);
                    }
                }
            }
            $modasphere_visibility_menu['m9999999'] = $modashere_favorites_page;
            update_option('modasphere_visibility_menu_items', json_encode($modasphere_visibility_menu));
            update_option('modasphere_favorites_page', $modashere_favorites_page);

            //---------- Set / unset search menu ---------
            $modashere_search_page = sanitize_text_field($_POST['use_search_page']);
            require_once(ABSPATH . 'wp-admin/includes/nav-menu.php');
            $menu_locations = get_nav_menu_locations();
            $selected_menu = get_option('modasphere_select_menu', 'main');
            $id_menu = $menu_locations[$selected_menu];
            $menu_items = wp_get_nav_menu_items($id_menu);

            foreach ((array)$menu_items as $key => $menu_item) {
                if (is_nav_menu_item($menu_item->ID)) {
                    if ($menu_item->post_name === 'modasphere_search_page') {
                        wp_delete_post($menu_item->ID);
                    }
                }
            }

            if ($modashere_search_page === 'checked') {
                $fl_search = 0;
                foreach ((array)$menu_items as $key => $menu_item) {
                    if (is_nav_menu_item($menu_item->ID) && $menu_item->post_name === 'modasphere_search_page') {
                        $fl_search++;
                    }
                }
                if ($fl_search == 0) {
                    $args = array(
                        array(
                            'menu-item-db-id' => '',
                            'menu-item-object-id' => '2',
                            'menu-item-object' => 'custom',
                            'menu-item-parent-id' => 0,
                            'menu-item-position' => 0,
                            'menu-item-type' => 'custom',
                            'menu-item-title' => 'Search',
                            'menu-item-url' => get_permalink($post_id) . 'search',
                            'menu-item-description' => '',
                            'menu-item-attr-title' => '',
                            'menu-item-target' => '',
                            'menu-item-classes' => '',
                            'menu-item-xfn' => ''
                        )
                    );
                    $items_id = wp_save_nav_menu_items($id_menu, $args);
                    if ($items_id) {
                        $items_post = array(
                            'ID' => $items_id[0],
                            'post_status' => 'publish',
                            'post_name' => 'modasphere_search_page'
                        );
                        wp_update_post($items_post);
                    }
                }
            }
            update_option('modasphere_search_page', $modashere_search_page);
        }
        delete_option('rewrite_rules');

    } elseif (isset($_POST['custom-css-set'])) {
        $modasphere_custom_css = sanitize_textarea_field($_POST['modasphere_custom_css']);
        update_option('modasphere_custom_css', $modasphere_custom_css);
    } elseif (isset($_POST['sync-schedule-set'])) {
//    ------------------- Set time and repeat synchronization -------------------
        $synchronization_profile_start_time = get_option('modasphere_synchronization_profile_start_time', '');
        $synchronization_photo_start_time = get_option('modasphere_synchronization_photo_start_time', '');
        $synchronization_profile_recurrence = get_option('modasphere_synchronization_profile_recurrence', '');
        $synchronization_photo_recurrence = get_option('modasphere_synchronization_photo_recurrence', '');

        $new_profile_start_time = sanitize_text_field($_POST['sync_profile_start_time']);
        $new_photo_start_time = sanitize_text_field($_POST['sync_photo_start_time']);
        $new_profile_recurrence = sanitize_text_field($_POST['sync_profile_recurrence']);
        $new_photo_recurrence = sanitize_text_field($_POST['sync_photo_recurrence']);

        $utc_time = strtotime(current_time('mysql', 1));
        $local_time = strtotime(current_time('mysql', 0));
        $time_diff = $utc_time - $local_time;

        if ($synchronization_profile_start_time != $new_profile_start_time || $synchronization_profile_recurrence != $new_profile_recurrence) {
            wp_clear_scheduled_hook('modasphere_profile_synchronization');
            wp_schedule_event(strtotime($new_profile_start_time) + $time_diff, $new_profile_recurrence, 'modasphere_profile_synchronization');
            update_option('modasphere_synchronization_profile_start_time', $new_profile_start_time);
            update_option('modasphere_synchronization_profile_recurrence', $new_profile_recurrence);
        }

        if ($synchronization_photo_start_time != $new_photo_start_time || $synchronization_photo_recurrence != $new_photo_recurrence) {
            wp_clear_scheduled_hook('modasphere_photo_synchronization');
            wp_schedule_event(strtotime($new_photo_start_time) + $time_diff, $new_photo_recurrence, 'modasphere_photo_synchronization');
            update_option('modasphere_synchronization_photo_start_time', $new_photo_start_time);
            update_option('modasphere_synchronization_photo_recurrence', $new_photo_recurrence);
        }

        $sync_profile_frequency = sanitize_text_field($_POST['sync_profile_frequency']);
        update_option('modasphere_synchronization_profile_frequency', $sync_profile_frequency);

    } else {
        //    ------------------- Save auth sections -------------------
        update_option('modasphere_domain', sanitize_text_field($_POST['domain']));
        update_option('modasphere_user_api', sanitize_text_field($_POST['login']));
        $user_api = sanitize_text_field($_POST['login']);
        update_option('modasphere_select_menu', sanitize_text_field($_POST['modasphere_select_menu']));
        update_option('modasphere_twitter_token', $_POST['twitter_token']);
        update_option('modasphere_agency_email', $_POST['agency_email']);

        $set_domain = get_option('modasphere_domain', '');
    }
} else {
    $page_title = get_the_title(get_option('modasphere_post_id', 0));
}
?>
<div class="modasphere-admin">
    <div class="row ">
        <div class="col-md-4">
            <div class="card" style="max-width: 100%;">
                <div class="card-header mb-3">Auth</div>
                <form method="post">
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <?php $set_domain = get_option('modasphere_domain', ''); ?>
                            <label for="domain">Domain</label>
                            <input type="text" class="form-control" id="domain" name="domain"
                                   value="<?= esc_attr($set_domain) ?>"
                                   placeholder="Enter domain">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="user">User</label>
                            <input type="text" class="form-control" id="user" name="login" aria-describedby="userHelp"
                                   value="<?= esc_attr($user_api) ?>" placeholder="Enter user">
                            <small id="userHelp" class="form-text text-muted">We'll never share your user with anyone
                                else.
                            </small>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="modasphere-select-menu">Select menu</label>
                            <select class="form-control custom-select" name="modasphere_select_menu"
                                    id="modasphere-select-menu">
                                <option value=""></option>
                                <?php
                                $selected_menu = get_option('modasphere_select_menu', 'main');
                                $locations = get_nav_menu_locations();
                                foreach ($locations as $key_menu => $value_menu) {
                                    if ($key_menu == $selected_menu) {
                                        echo '<option selected value="' . $key_menu . '">' . $key_menu . '</option>';
                                    } else {
                                        echo '<option value="' . $key_menu . '">' . $key_menu . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <?php $ms_twitter_token = get_option('modasphere_twitter_token', ''); ?>
                            <label for="twitter_token">Twitter token</label>
                            <input type="text" class="form-control" id="twitter_token" name="twitter_token"
                                   value="<?= esc_attr($ms_twitter_token) ?>">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <?php $ms_agency_email = get_option('modasphere_agency_email', ''); ?>
                            <label for="agency_email">Agency email</label>
                            <input type="text" class="form-control" id="agency_email" name="agency_email"
                                   value="<?= esc_attr($ms_agency_email) ?>">
                        </div>
                    </div>
                    <button type="submit" name="auth-set" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card" style="max-width: 100%;">
                <form method="post">
                    <div class="card-header mb-3">
                        Menu and page name
                    </div>
                    <div class="container" data-spy="scroll">
                        <?php
                        $modasphere_visibility_menu = get_option('modasphere_visibility_menu_items', '');
                        if ($modasphere_visibility_menu != '') $modasphere_visibility_menu = (array)json_decode($modasphere_visibility_menu);
                        $api_result_division = modasphere_api_get_content($set_domain, $user_api, 'division');
                        if ($api_result_division != false) {
                            $divisions = $api_result_division->objects;
                            $level2 = 0;
                            $level3 = 0;
                            ?>
                            <nav class="nav flex-column">
                                <?php
                                foreach ($divisions as $dev) {
                                    if ($dev->parent_id == '') {
                                        ?>
                                        <div class="nav-link custom-control custom-checkbox pt-0 pb-0">
                                            <input type="checkbox" class="custom-control-input" id="<?= $dev->id ?>"
                                                   name="m<?= $dev->id ?>" value="checked"
                                                <?php
                                                if (is_array($modasphere_visibility_menu)) echo $modasphere_visibility_menu['m' . $dev->id];
                                                ?>
                                            >
                                            <label class="custom-control-label" for="<?= $dev->id ?>">
                                                <?= $dev->name ?>
                                            </label>
                                        </div>
                                        <?php
                                        foreach ($divisions as $dev2) {
                                            if ($dev2->parent_id != '' && $dev2->parent_id == $dev->id) {
                                                if ($level2 == 0) {
                                                    ?>
                                                    <nav class="nav flex-column" style="margin-left:4%">
                                                    <?php
                                                    $level2++;
                                                }
                                                ?>
                                                <div class="nav-link custom-control custom-checkbox pl-4 pt-0 pb-0">
                                                    <input type="checkbox" class="custom-control-input"
                                                           id="<?= $dev2->id ?>"
                                                           name="m<?= $dev2->id ?>" value="checked"
                                                        <?php
                                                        if (is_array($modasphere_visibility_menu)) echo $modasphere_visibility_menu['m' . $dev2->id];
                                                        ?>
                                                    >
                                                    <label class="custom-control-label" for="<?= $dev2->id ?>">
                                                        <?= $dev2->name ?>
                                                    </label>
                                                </div>
                                                <?php
                                                foreach ($divisions as $dev3) {
                                                    if ($dev3->parent_id != '' && $dev3->parent_id == $dev2->id) {
                                                        if ($level3 == 0) {
                                                            ?>
                                                            <nav class="nav flex-column" style="margin-left:4%">
                                                            <?php
                                                            $level3++;
                                                        } ?>
                                                        <div class="nav-link custom-control custom-checkbox pl-5 pt-0 pb-0">
                                                            <input type="checkbox" class="custom-control-input"
                                                                   id="<?= $dev3->id ?>"
                                                                   name="m<?= $dev3->id ?>" value="checked"
                                                                <?php
                                                                if (is_array($modasphere_visibility_menu)) echo $modasphere_visibility_menu['m' . $dev3->id];
                                                                ?>
                                                            >
                                                            <label class="custom-control-label" for="<?= $dev3->id ?>">
                                                                <?= $dev3->name ?>
                                                            </label>
                                                        </div>
                                                        <?php
                                                    }
                                                }
                                                if ($level3 > 0) {
                                                    ?>
                                                    </nav>
                                                    <?php
                                                    $level3 = 0;
                                                }
                                            }
                                        }
                                        if ($level2 > 0) {
                                            ?>
                                            </nav>
                                            <?php
                                            $level2 = 0;
                                        }
                                    }
                                }
                                $favorites_page = get_option('modasphere_favorites_page', '');
                                $search_page = get_option('modasphere_search_page', '');
                                ?>
                                <div class="nav-link custom-control custom-checkbox pt-0 pb-0">
                                    <input type="checkbox" class="custom-control-input" id="use-favorites-page"
                                           name="use_favorites_page"
                                           value="checked" <?= $favorites_page ?>>
                                    <label class="custom-control-label"
                                           for="use-favorites-page">Use Favorites Page</label>
                                </div>
                                <div class="nav-link custom-control custom-checkbox pt-0 pb-0">
                                    <input type="checkbox" class="custom-control-input" id="use-search-page"
                                           name="use_search_page"
                                           value="checked" <?= $search_page ?>>
                                    <label class="custom-control-label"
                                           for="use-search-page">Use Search Page</label>
                                </div>
                            </nav>
                            <?php
                        }
                        ?>
                    </div>
                    <br>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <?php $ms_page_name = get_option('modasphere_page_name', 'modasphere'); ?>
                            <label for="agency_email">Page name</label>
                            <input type="text" class="form-control" id="page_name" name="page_name"
                                   value="<?= esc_attr($ms_page_name) ?>">
                        </div>
                    </div>
                    <small id="menu_save_help" class="form-text text-muted">After saving, the old menu will be
                        deleted!
                    </small>
                    <button type="submit" name="visibility-menu-set" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card" style="max-width: 100%;">
                <form method="post">
                    <div class="card-header mb-3">
                        Talent list settings
                    </div>
                    <div class="form-group row">
                        <label for="talent-list-columns" class="col-md-5">Talent columns</label>
                        <div class="col-md-5">
                            <select class="form-control custom-select" name="talent_list_columns"
                                    id="talent-list-columns">
                                <option value=""></option>
                                <option <?php if (get_option('modasphere_talent_list_col', '4') == '6') echo 'selected'; ?>
                                        value="6">
                                    2
                                </option>
                                <option <?php if (get_option('modasphere_talent_list_col', '4') == '4') echo 'selected'; ?>
                                        value="4">
                                    3
                                </option>
                                <option <?php if (get_option('modasphere_talent_list_col', '4') == '3') echo 'selected'; ?>
                                        value="3">
                                    4
                                </option>
                                <option <?php if (get_option('modasphere_talent_list_col', '4') == '2') echo 'selected'; ?>
                                        value="2">
                                    6
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="talent-list-name-display" class="col-md-5">Talent name display</label>
                        <div class="col-md-5">
                            <select class="form-control custom-select" name="talent_list_name_display"
                                    id="talent-list-name-display">
                                <option value=""></option>
                                <option <?php if (get_option('modasphere_talent_list_name_display', 'full') == 'full') echo 'selected'; ?>
                                        value="full">
                                    Full Name
                                </option>
                                <option <?php if (get_option('modasphere_talent_list_name_display', 'full') == 'name_s') echo 'selected'; ?>
                                        value="name_s">
                                    Name S.
                                </option>
                                <option <?php if (get_option('modasphere_talent_list_name_display', 'full') == 'name') echo 'selected'; ?>
                                        value="name">
                                    Name
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="talent-list-image-width" class="col-md-5">Talent Cover Width</label>
                        <div class="form-group col-md-5 mb-0">
                            <?php $talent_images_width = get_option('modasphere_talent_list_image_width', ''); ?>
                            <input type="text" class="form-control" id="talent-list-image-width"
                                   name="talent_list_image_width" value="<?= esc_attr($talent_images_width) ?>"
                                   placeholder="Enter width">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="talent-list-image-height" class="col-md-5">Talent Cover Height</label>
                        <div class="form-group col-md-5 mb-0">
                            <?php $talent_images_height = get_option('modasphere_talent_list_image_height', ''); ?>
                            <input type="text" class="form-control" id="talent-list-image-height"
                                   name="talent_list_image_height" value="<?= esc_attr($talent_images_height) ?>"
                                   placeholder="Enter height">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="talent-list-pagination-count" class="col-md-5">Number of talents per page</label>
                        <div class="form-group col-md-5 mb-0">
                            <?php $talent_list_pagination_count = get_option('modasphere_talent_list_pagination_count', '8'); ?>
                            <input type="text" class="form-control" id="talent-list-pagination-count"
                                   name="talent_list_pagination_count"
                                   value="<?= esc_attr($talent_list_pagination_count) ?>"
                                   placeholder="Enter number">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="talent-list-cover-photo" class="col-md-5">Cover Photo</label>
                        <div class="col-md-5">
                            <select class="form-control custom-select" name="talent_list_cover_photo"
                                    id="talent-list-cover-photo">
                                <option value=""></option>
                                <option <?php if (get_option('modasphere_talent_list_cover_photo', 'talent') == 'talent') echo 'selected'; ?>
                                        value="talent">
                                    Talent Cover
                                </option>
                                <option <?php if (get_option('modasphere_talent_list_cover_photo', 'talent') == 'gallery') echo 'selected'; ?>
                                        value="gallery">
                                    Gallery Cover
                                </option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" name="talent-list-set" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card" style="max-width: 100%;">
                <div class="card-header">
                    Styles
                </div>
                <div class="card-body">
                    <form method="post">
                        <div class="form-group">
                            <label for="modasphere-custom-css">Custom CSS</label>
                            <textarea class="form-control" id="modasphere-custom-css" name="modasphere_custom_css"
                                      rows="7"><?= trim(get_option('modasphere_custom_css', '')) ?></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" name="custom-css-set" class="btn btn-primary mt-2">Save
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card" style="max-width: 100%;">
                <div class="card-header">
                    Synchronization Schedule <br>(<?= current_time('mysql',0) ?>)
                </div>
                <div class="card-body">
                    <?php
                    if (!extension_loaded('imagick')) echo '<span class="badge badge-warning">Your hosting does not have the Imagick extension installed. This extension is necessary for more efficient work with images.</span>';
                    ?>
                    <form method="post">
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="card-title">Talent profile</h5>
                                <div class="form-row">
                                    <div class="form-group col-md-7">
                                        <?php $synchronization_profile_start_time = get_option('modasphere_synchronization_profile_start_time', ''); ?>
                                        <label for="sync-profile-start-time">Start time</label>
                                        <input type="datetime-local" class="form-control" id="sync-profile-start-time"
                                               name="sync_profile_start_time"
                                               value="<?= esc_attr($synchronization_profile_start_time) ?>">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="sync-profile-recurrence">Recurrence</label>
                                        <select class="form-control custom-select" name="sync_profile_recurrence"
                                                id="sync-profile-recurrence">
                                            <option value=""></option>
                                            <option <?php if (get_option('modasphere_synchronization_profile_recurrence', '') == 'hourly') echo 'selected'; ?>
                                                    value="hourly">
                                                Hourly
                                            </option>
                                            <option <?php if (get_option('modasphere_synchronization_profile_recurrence', '') == 'twicedaily') echo 'selected'; ?>
                                                    value="twicedaily">
                                                Twice Daily
                                            </option>
                                            <option <?php if (get_option('modasphere_synchronization_profile_recurrence', '') == 'daily') echo 'selected'; ?>
                                                    value="daily">
                                                Daily
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="sync-profile-frequency" class="col-md-7 col-form-label">Talent Profile
                                        Update Frequency</label>
                                    <div class="col-md-4">
                                        <select class="form-control custom-select" name="sync_profile_frequency"
                                                id="sync-profile-frequency">
                                            <option value=""></option>
                                            <option <?php if (get_option('modasphere_synchronization_profile_frequency', '') == '3600') echo 'selected'; ?>
                                                    value="3600">
                                                Hourly
                                            </option>
                                            <option <?php if (get_option('modasphere_synchronization_profile_frequency', '') == '86400') echo 'selected'; ?>
                                                    value="86400">
                                                Daily
                                            </option>
                                            <option <?php if (get_option('modasphere_synchronization_profile_frequency', '') == '604800') echo 'selected'; ?>
                                                    value="604800">
                                                Weekly
                                            </option>
                                            <option <?php if (get_option('modasphere_synchronization_profile_frequency', '') == '2592000') echo 'selected'; ?>
                                                    value="2592000">
                                                Monthly
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <?php
                                $utc_time = strtotime(current_time('mysql', 1));
                                $local_time = strtotime(current_time('mysql', 0));
                                $time_diff = $utc_time - $local_time;
                                if (wp_next_scheduled('modasphere_profile_synchronization')) {
                                    ?>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <span class="badge badge-light">
                                            Next synchronization:
                                            <?= date("d.m.Y H:i", wp_next_scheduled('modasphere_profile_synchronization') - $time_diff) ?>
                                            </span>
                                        </div>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                            <div class="col-md-6">
                                <h5 class="card-title">Talent galleries</h5>
                                <div class="form-row">
                                    <div class="form-group col-md-7">
                                        <?php $synchronization_photo_start_time = get_option('modasphere_synchronization_photo_start_time', ''); ?>
                                        <label for="sync-photo-start-time">Start time</label>
                                        <input type="datetime-local" class="form-control" id="sync-photo-start-time"
                                               name="sync_photo_start_time"
                                               value="<?= esc_attr($synchronization_photo_start_time) ?>">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="sync-photo-recurrence">Recurrence</label>
                                        <select class="form-control custom-select" name="sync_photo_recurrence"
                                                id="sync-photo-recurrence">
                                            <option value=""></option>
                                            <option <?php if (get_option('modasphere_synchronization_photo_recurrence', '') == 'hourly') echo 'selected'; ?>
                                                    value="hourly">
                                                Hourly
                                            </option>
                                            <option <?php if (get_option('modasphere_synchronization_photo_recurrence', '') == 'twicedaily') echo 'selected'; ?>
                                                    value="twicedaily">
                                                Twice Daily
                                            </option>
                                            <option <?php if (get_option('modasphere_synchronization_photo_recurrence', '') == 'daily') echo 'selected'; ?>
                                                    value="daily">
                                                Daily
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <?php
                                if (wp_next_scheduled('modasphere_photo_synchronization')) {
                                    ?>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <span class="badge badge-light">
                                            Next synchronization:
                                            <?= date("d.m.Y H:i", wp_next_scheduled('modasphere_photo_synchronization') - $time_diff) ?>
                                            </span>
                                        </div>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" name="sync-schedule-set" class="btn btn-primary mt-2">Save
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card" style="max-width: 100%;">
                <form method="post">
                    <div class="card-header mb-3">
                        Talent settings
                    </div>
                    <fieldset class="form-group">
                        <div class="row">
                            <legend class="col-form-label col-md-2 pt-0">View photo</legend>
                            <div class="col-md-2">
                                <div class="form-check">
                                    <input class="form-check-input mt-1" type="radio" name="TypeGallery" id="Slider"
                                           value="slider" <?php if (get_option('modasphere_talent_type_gallery', '') == 'slider') echo 'checked'; ?>>
                                    <label class="form-check-label pl-4" for="Slider">
                                        Slider
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input mt-1" type="radio" name="TypeGallery" id="Fotorama"
                                           value="fotorama" <?php if (get_option('modasphere_talent_type_gallery', '') == 'fotorama') echo 'checked'; ?>>
                                    <label class="form-check-label pl-4" for="Fotorama">
                                        Fotorama
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input mt-1" type="radio" name="TypeGallery" id="List"
                                           value="list" <?php if (get_option('modasphere_talent_type_gallery', '') == 'list') echo 'checked'; ?>>
                                    <label class="form-check-label pl-4" for="List">
                                        List
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-8 border-left">
                                <div class="card-header">Fotorama options</div>
                                <div class="mt-2">
                                    <?php
                                    $fotorama_get_options = get_option('modasphere_fotorama_options', '');
                                    if ($fotorama_get_options != '') $fotorama_get_options = (array)json_decode($fotorama_get_options);
                                    ?>
                                    <div class="custom-control custom-checkbox modasphere-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="fullscreen"
                                               name="fullscreen"
                                               value="checked" <?php if (is_array($fotorama_get_options)) echo $fotorama_get_options['fullscreen']; ?> >
                                        <label class="custom-control-label"
                                               for="fullscreen">fullscreen</label>
                                    </div>
                                    <div class="custom-control custom-checkbox modasphere-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="thumbnails"
                                               name="thumbnails"
                                               value="checked" <?php if (is_array($fotorama_get_options)) echo $fotorama_get_options['thumbnails']; ?> >
                                        <label class="custom-control-label"
                                               for="thumbnails">thumbnails</label>
                                    </div>
                                    <div class="custom-control custom-checkbox modasphere-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="loop"
                                               name="loop"
                                               value="checked" <?php if (is_array($fotorama_get_options)) echo $fotorama_get_options['loop']; ?> >
                                        <label class="custom-control-label"
                                               for="loop">loop</label>
                                    </div>
                                    <div class="custom-control custom-checkbox modasphere-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="autoplay"
                                               name="autoplay"
                                               value="checked" <?php if (is_array($fotorama_get_options)) echo $fotorama_get_options['autoplay']; ?> >
                                        <label class="custom-control-label"
                                               for="autoplay">autoplay</label>
                                    </div>
                                    <div class="custom-control custom-checkbox modasphere-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="keyboard"
                                               name="keyboard"
                                               value="checked" <?php if (is_array($fotorama_get_options)) echo $fotorama_get_options['keyboard']; ?> >
                                        <label class="custom-control-label"
                                               for="keyboard">keyboard</label>
                                    </div>
                                    <div class="custom-control custom-checkbox modasphere-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="two-photos-slide"
                                               name="two_photo_on_slide"
                                               value="checked" <?php if (is_array($fotorama_get_options)) echo $fotorama_get_options['two_photo_on_slide']; ?> >
                                        <label class="custom-control-label" id="two-photos-slide-label"
                                               for="two-photos-slide">two photos on a slide</label>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <label for="transition" class="mb-0">transition</label>
                                    <select class="form-control custom-select col-md-2" name="transition"
                                            id="transition">
                                        <option value=""></option>
                                        <option <?php if (is_array($fotorama_get_options) && $fotorama_get_options['transition'] == 'slide') echo 'selected'; ?>
                                                value="slide">
                                            slide
                                        </option>
                                        <option <?php if (is_array($fotorama_get_options) && $fotorama_get_options['transition'] == 'crossfade') echo 'selected'; ?>
                                                value="crossfade">
                                            crossfade
                                        </option>
                                    </select>
                                </div>
                                <div class="card-header mt-2">Video</div>
                                <div class="form-group row mt-2">
                                    <label for="video-data-width" class="col-md-2">Width(%):</label>
                                    <div class="col-md-2">
                                        <?php $video_data_width = get_option('modasphere_video_width', '100'); ?>
                                        <input type="number" class="form-control" id="video-data-width"
                                               name="video_data_width" min="10" max="100"
                                               value="<?= esc_attr($video_data_width) ?>">
                                    </div>
                                    <label for="video-data-maxheight" class="col-md-2">Max height(px):</label>
                                    <div class="col-md-2">
                                        <?php $video_data_maxheight = get_option('modasphere_video_maxheight', '670'); ?>
                                        <input type="number" class="form-control" id="video-data-maxheight"
                                               name="video_data_maxheight" min="100" max="700"
                                               value="<?= esc_attr($video_data_maxheight) ?>">
                                    </div>
                                    <label for="video-data-maxwidth" class="col-md-2">Max width(px):</label>
                                    <div class="col-md-2">
                                        <?php $video_data_maxwidth = get_option('modasphere_video_maxwidth', '1140'); ?>
                                        <input type="number" class="form-control" id="video-data-maxwidth"
                                               name="video_data_maxwidth" min="400" max="1200"
                                               value="<?= esc_attr($video_data_maxwidth) ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <div class="form-group row">
                        <label for="photo-larger-side-length" class="col-md-2">Larger side length</label>
                        <div class="col-md-2">
                            <?php $photo_larger_side_length = get_option('modasphere_photo_larger_side_length', '1150'); ?>
                            <input type="number" class="form-control" id="photo-larger-side-length"
                                   name="photo_larger_side_length" min="400" max="2048"
                                   value="<?= esc_attr($photo_larger_side_length) ?>"
                                   placeholder="Enter number">
                        </div>
                        <label for="photo-short-side-length" class="col-md-2">Short side length</label>
                        <div class="col-md-2">
                            <?php $photo_short_side_length = get_option('modasphere_photo_short_side_length', '780'); ?>
                            <input type="number" class="form-control" id="photo-short-side-length"
                                   name="photo_short_side_length" min="400" max="2048"
                                   value="<?= esc_attr($photo_short_side_length) ?>"
                                   placeholder="Enter number">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="talent-info-position" class="col-md-2">Talent info position</label>
                        <div class="col-md-2">
                            <select class="form-control custom-select" name="talent_info_position"
                                    id="talent-info-position">
                                <option value=""></option>
                                <option <?php if (get_option('modasphere_talent_info_position', 'left') == 'left') echo 'selected'; ?>
                                        value="left">
                                    Left
                                </option>
                                <option <?php if (get_option('modasphere_talent_info_position', 'left') == 'top') echo 'selected'; ?>
                                        value="top">
                                    Top
                                </option>
                                <option <?php if (get_option('modasphere_talent_info_position', 'left') == 'right') echo 'selected'; ?>
                                        value="right">
                                    Right
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <nav>
                                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                    <?php
                                    $active_tab_link = 'active';
                                    $tab_aria_selected = 'true';
                                    $div_active = 0;
                                    if ($api_result_division != false) {
                                        $divisions = $api_result_division->objects;

                                        if (get_option('modasphere_favorites_page')) {
                                            $divisions[] = (object)array('id' => 9999999, 'name' => 'Favorites/Search widget');
                                        }

                                        foreach ($divisions as $dev) {
                                            if (is_array($modasphere_visibility_menu) && $modasphere_visibility_menu['m' . $dev->id] == 'checked') {
                                                ?>
                                                <a class="nav-item nav-link <?= $active_tab_link ?>"
                                                   id="nav-<?= $dev->id ?>-tab"
                                                   data-divactive="<?= $dev->id ?>"
                                                   data-toggle="tab" href="#nav-<?= $dev->id ?>" role="tab"
                                                   aria-controls="nav-<?= $dev->id ?>"
                                                   aria-selected="<?= $tab_aria_selected ?>"><?= $dev->name ?></a>
                                                <?php
                                                $active_tab_link = '';
                                                $div_active = (empty($div_active))? $dev->id: $div_active;
                                                $tab_aria_selected = 'false';
                                            }
                                        }
                                    }
                                    $modashere_search_page = get_option('modasphere_search_page', '');
                                    if ($modashere_search_page != '') {
                                        ?>
                                        <a class="nav-item nav-link"
                                           id="nav-search-tab"
                                           data-toggle="tab" href="#nav-search" role="tab"
                                           aria-controls="nav-search"
                                           aria-selected="false">Search Settings</a>
                                        <?php
                                    }
                                    $active_tab_link = 'show active';
                                    ?>
                                </div>
                            </nav>
                            <div class="tab-content" id="nav-tabContent">
                                <?php
                                $api_result = modasphere_api_get_content($set_domain, $user_api, 'contactfields');
                                if ($api_result != false) {
                                    $contact_fields = $api_result->objects;
                                }
                                $options_view = get_option('modasphere_talent_info_options_view', '');
                                if ($options_view != '') $options_view = json_decode($options_view);

                                if ($api_result_division != false) {
                                    foreach ($divisions as $dev) {
                                        if (is_array($modasphere_visibility_menu) && $modasphere_visibility_menu['m' . $dev->id] == 'checked') {
                                            $division_id = $dev->id;
                                            ?>
                                            <div class="tab-pane fade <?= $active_tab_link ?>" id="nav-<?= $dev->id ?>"
                                                 role="tabpanel"
                                                 aria-labelledby="nav-<?= $dev->id ?>-tab">
                                                <div class="form-group row ml-0">
                                                    <label for="talent-<?= $division_id ?>-thumbnail-overlay"
                                                           class="mt-3">Talent thumbnail overlay</label>
                                                    <div class="col-md-2 mt-3">
                                                        <?php
                                                        $talent_thumbnail_overlay = get_option('modasphere_talent_list_thumbnail_overlay', '');
                                                        if ($talent_thumbnail_overlay != '') $talent_thumbnail_overlay = json_decode($talent_thumbnail_overlay);

                                                        $tto = $talent_thumbnail_overlay->$division_id;
                                                        ?>
                                                        <select class="form-control custom-select"
                                                                name="talent_<?= $division_id ?>_thumbnail_overlay"
                                                                id="talent-<?= $division_id ?>-thumbnail-overlay">
                                                            <option value=""></option>
                                                            <option <?php if ($tto == 'disabled') echo 'selected'; ?>
                                                                    value="disabled">
                                                                Disabled
                                                            </option>
                                                            <option <?php if ($tto == 'dimensions') echo 'selected'; ?>
                                                                    value="dimensions">
                                                                Dimensions
                                                            </option>
                                                            <option <?php if ($tto == 'social') echo 'selected'; ?>
                                                                    value="social">
                                                                Social Media
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="border-bottom mb-3 mt-3">
                                                    <?php
                                                    $talent_social = get_option('modasphere_talent_social', '');
                                                    if ($talent_social != '') $talent_social = json_decode($talent_social);
                                                    $talent_s = (array)$talent_social->$division_id;
                                                    ?>
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input"
                                                                       id="<?= $division_id ?>_m_o_d_talent-instagram"
                                                                       name="<?= $division_id ?>_m_o_d_talent_instagram"
                                                                       value="checked" <?= $talent_s['talent_instagram'] ?> >
                                                                <label class="custom-control-label"
                                                                       for="<?= $division_id ?>_m_o_d_talent-instagram">Fetch
                                                                    Talent Instagram</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input"
                                                                       id="<?= $division_id ?>_m_o_d_talent-instagram-username"
                                                                       name="<?= $division_id ?>_m_o_d_talent_instagram_username"
                                                                       value="checked" <?= $talent_s['talent_instagram_username'] ?> >
                                                                <label class="custom-control-label ml-4"
                                                                       for="<?= $division_id ?>_m_o_d_talent-instagram-username">
                                                                    Display Username</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input"
                                                                       id="<?= $division_id ?>_m_o_d_talent-instagram-f-count"
                                                                       name="<?= $division_id ?>_m_o_d_talent_instagram_f_count"
                                                                       value="checked" <?= $talent_s['talent_instagram_f_count'] ?> >
                                                                <label class="custom-control-label ml-4"
                                                                       for="<?= $division_id ?>_m_o_d_talent-instagram-f-count">
                                                                    Display Follower Count</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input"
                                                                       id="<?= $division_id ?>_m_o_d_talent-twitter"
                                                                       name="<?= $division_id ?>_m_o_d_talent_twitter"
                                                                       value="checked" <?= $talent_s['talent_twitter'] ?> >
                                                                <label class="custom-control-label"
                                                                       for="<?= $division_id ?>_m_o_d_talent-twitter">Fetch
                                                                    Talent Twitter</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input"
                                                                       id="<?= $division_id ?>_m_o_d_talent-twitter-username"
                                                                       name="<?= $division_id ?>_m_o_d_talent_twitter_username"
                                                                       value="checked" <?= $talent_s['talent_twitter_username'] ?> >
                                                                <label class="custom-control-label ml-4"
                                                                       for="<?= $division_id ?>_m_o_d_talent-twitter-username">
                                                                    Display Username</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input"
                                                                       id="<?= $division_id ?>_m_o_d_talent-twitter-f-count"
                                                                       name="<?= $division_id ?>_m_o_d_talent_twitter_f_count"
                                                                       value="checked" <?= $talent_s['talent_twitter_f_count'] ?> >
                                                                <label class="custom-control-label ml-4"
                                                                       for="<?= $division_id ?>_m_o_d_talent-twitter-f-count">
                                                                    Display Follower Count</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input"
                                                                       id="<?= $division_id ?>_m_o_d_talent-casting-networks"
                                                                       name="<?= $division_id ?>_m_o_d_talent_casting_networks"
                                                                       value="checked" <?= $talent_s['talent_casting_networks'] ?> >
                                                                <label class="custom-control-label"
                                                                       for="<?= $division_id ?>_m_o_d_talent-casting-networks">Fetch
                                                                    Talent Casting Networks</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input"
                                                                       id="<?= $division_id ?>_m_o_d_talent-social-link-disable"
                                                                       name="<?= $division_id ?>_m_o_d_talent_social_link_disable"
                                                                       value="checked" <?= $talent_s['talent_social_link_disable'] ?> >
                                                                <label class="custom-control-label"
                                                                       for="<?= $division_id ?>_m_o_d_talent-social-link-disable">Disable
                                                                    links to social
                                                                    pages</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                                if (isset($contact_fields) && is_array($contact_fields)) {

                                                    $opv = (array)$options_view->$division_id;
                                                    foreach ($contact_fields as $cf) {
                                                        ?>
                                                        <div class="custom-control custom-checkbox modasphere-checkbox">
                                                            <input type="checkbox" class="custom-control-input"
                                                                   id="<?= $division_id . '_m_o_d_' . $cf->label ?>"
                                                                   name="<?= $division_id . '_m_o_d_' . $cf->name ?>"
                                                                   value="checked" <?php if (is_array($opv)) echo $opv[$cf->name]; ?> >
                                                            <label class="custom-control-label"
                                                                   for="<?= $division_id . '_m_o_d_' . $cf->label ?>"><?= $cf->label ?></label>
                                                        </div>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </div>
                                            <?php
                                            $active_tab_link = '';
                                        }
                                    }
                                }
                                $api_search_field = modasphere_api_get_content($set_domain, $user_api, 'search_field');
                                if ($api_search_field != false) {
                                    $contact_field_list = $api_search_field->objects;
                                }
                                if ($modashere_search_page != '') {
                                    ?>
                                    <div class="tab-pane fade mt-3" id="nav-search"
                                         role="tabpanel"
                                         aria-labelledby="nav-search-tab">
                                        <?php
                                        if (isset($contact_field_list) && is_array($contact_field_list)) {
                                            $id_option = 'search';

                                            $sf_options_view = get_option('modasphere_search_field_options_view', '');
                                            if ($sf_options_view != '') $sf_options_view = json_decode($sf_options_view);

                                            $opv = (array)$sf_options_view;

                                            foreach ($contact_field_list as $cfl) {
                                                if ($cfl->contact_field->name != 'loaction') {
                                                    ?>
                                                    <div class="custom-control custom-checkbox modasphere-checkbox">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="<?= $id_option . '_as_' . $cfl->contact_field->name ?>"
                                                               name="<?= $id_option . '_as_' . $cfl->contact_field->name . '_' . $cfl->id ?>"
                                                               value="checked" <?php if (is_array($opv)) echo $opv[$cfl->contact_field->name]->status; ?> >
                                                        <label class="custom-control-label"
                                                               for="<?= $id_option . '_as_' . $cfl->contact_field->name ?>"><?= $cfl->contact_field->label ?></label>
                                                    </div>
                                                    <?php
                                                }
                                            }
                                        }
                                        ?>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
<!--                    <button type="submit" name="talent-set" class="btn btn-primary">Save</button>-->
                    <div class="btn-group">
                        <button type="submit" name="talent-set" class="btn btn-primary">Save</button>
                        <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <div class="dropdown-menu">
                            <input type="hidden" id="div-active" name="div-active" value="<?= $div_active ?>">
                            <button type="submit" name="talent-set-all" class="dropdown-item">Save For All</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    jQuery(document).ready(function () {
        jQuery("#two-photos-slide-label").click(function () {
            if (jQuery("#two-photos-slide").prop("checked") == false) {
                jQuery("#thumbnails").prop("checked", false).attr("disabled", true);
            } else {
                jQuery("#thumbnails").attr("disabled", false);
            }
        });
        if (jQuery("#two-photos-slide").prop("checked") == true) {
            jQuery("#thumbnails").attr("disabled", true);
        }
        jQuery(".nav-item").click(function () {
            let div_id = jQuery(this).data("divactive");
            jQuery("#div-active").val(div_id);
        });
    });
</script>