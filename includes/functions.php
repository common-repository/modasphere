<?php
function modasphere_delete($path)
{
    if (is_dir($path) === true) {
        $files = array_diff(scandir($path), array('.', '..'));

        foreach ($files as $file) {
            modasphere_delete(realpath($path) . '/' . $file);
        }

        return rmdir($path);
    } else if (is_file($path) === true) {
        return unlink($path);
    }

    return false;
}

function modasphere_delete_files($path)
{
    if (is_dir($path) === true) {
        $files = array_diff(scandir($path), array('.', '..'));

        foreach ($files as $file) {
            $link = realpath($path) . '/' . $file;
            if(is_dir($link) === true){
                modasphere_delete($link);
            }else{
                unlink($link);
            }
        }
        return true;
    }
    return false;
}

function modasphere_api_get_content($domain, $user, $type_api, $pagination = false, $id_division = 0, $count = 1000, $offset = 0, $id_talent = 0, $gallery_id = 0, $search = '', $method = 'get')
{

    switch ($type_api) {
        case "contactfields":
            $url = 'https://' . $domain . '/api2/contact/field/list/';
            break;
        case "division":
            $url = 'https://' . $domain . '/api2/division/list/';
            break;
        case "contact_division":
            if ($pagination) {
                $url = 'https://' . $domain . '/api2/contact_division/list/?division_id=' . $id_division . '&count=' . $count . '&offset=' . $offset;
            } else {
                $url = 'https://' . $domain . '/api2/contact_division/list/?division_id=' . $id_division;
            }
            break;
        case "all_contacts":
            $url = 'https://' . $domain . '/api2/contact_division/list/?count=' . $count . '&offset=' . $offset;
            break;
        case "contact_gallery":
            $url = 'https://' . $domain . '/api2/contact/' . $id_talent . '/gallery/' . $gallery_id . '/galleryitem/list/?count=' . $count;
            break;
        case "contact_info":
            $url = 'https://' . $domain . '/api2/contact/' . $id_talent . '/';
            break;
        case "contact_profile":
            $url = 'https://' . $domain . '/api2/contact/' . $id_talent . '/profile/';
            break;
        case "search_contact":
            if ($pagination) {
                $url = 'https://' . $domain . '/api2/contact_division/list/?search=' . $search . '&count=' . $count . '&offset=' . $offset;
            } else {
                $url = 'https://' . $domain . '/api2/contact_division/list/?search=' . $search;
            }
            break;
        case "search_field":
            $url = 'https://' . $domain . '/api2/search_field/list/';
            break;
        case "search_fields":
            $url = 'https://' . $domain . '/api2/contact/list/';
            break;
    }

    if (!empty($url) && !empty($user)) {
        $args = array(
            'timeout' => 60,
            'headers' => array(
                'Authorization' => "Basic " . base64_encode($user)
            )
        );

        if ($method == 'get') {
            $results = wp_remote_get($url, $args);
        } else {
            $args['body'] = array('search_fields' => $search);
            $results = wp_remote_post($url, $args);
        }

        if (!is_wp_error($results) && $results["response"]["code"] == 200) {
            $result = json_decode($results["body"]);
            return $result;
        }
    }
    return false;
}

function modasphere_twitter_followers_count($screen_name, $twitter_token)
{
    $key = 'twitter_followers_count_' . $screen_name;
    $followers_count = get_transient($key);

    if ($followers_count !== false)
        return (int)$followers_count;
    else {
        $args = array(
            'httpversion' => '1.1',
            'blocking' => true,
            'headers' => array(
                'User-Agent' => "Modasphere Twitter App",
                'Authorization' => "Bearer $twitter_token"
            )
        );
        add_filter('https_ssl_verify', '__return_false');
        $api_url = "https://api.twitter.com/1.1/users/show.json?screen_name=$screen_name";
        $response = wp_remote_get($api_url, $args);

        if (is_wp_error($response)) {
            return (int)get_option($key);
        } else {
            $json = json_decode(wp_remote_retrieve_body($response));
            $count = $json->followers_count;
            set_transient($key, $count, 60 * 60 * 24);
            update_option($key, $count);
            return $count;
        }
    }
}

function modasphere_instagram_followers_count($instagram_id, $instagram_token)
{
    $key = 'instagram_followers_count_' . $instagram_id;
    $followers_count = get_transient($key);

    if ($followers_count !== false)
        return (int)$followers_count;
    else {
        $response = wp_remote_get("https://api.instagram.com/v1/users/" . $instagram_id . "/?access_token=" . $instagram_token);

        if (is_wp_error($response)) {
            return (int)get_option($key);
        } else {
            $json = json_decode($response['body']);
            $count = $json->data->counts->followed_by;

            set_transient($key, $count, 60 * 60 * 24);
            update_option($key, $count);
            return $count;
        }
    }
}

function modasphere_format_followers($followers)
{

    if ($followers >= 1000 && $followers < 1000000) {
        $followers = round(($followers / 1000), 1) . ' K';
    } elseif ($followers >= 1000000) {
        $followers = round(($followers / 1000000), 1) . ' M';
    } else {
        $followers = number_format($followers);
    }
    return $followers;
}

function modasphere_get_talent_info($domain, $user_api, $id_talent)
{

    $api_result = modasphere_api_get_content($domain, $user_api, 'contact_info', false, 0, 0, 0, $id_talent, 0);
    $result = array();
    $result['instagram_link'] = $api_result->social_instagram;
    $instagram_url_parse = parse_url($api_result->social_instagram, PHP_URL_PATH);
    $instagram_username = ($instagram_url_parse) ? '@' . str_replace('/', '', $instagram_url_parse) : '';
    $result['instagram_username'] = $instagram_username;
    $instagram_followers = modasphere_format_followers(modasphere_instagram_followers_count($api_result->social_instagram_id, $api_result->social_instagram_token));
    $result['instagram_followers'] = $instagram_followers;
    $social_twitter_token = get_option('modasphere_twitter_token', '');
    $result['twitter_link'] = $api_result->social_twitter;
    $result['twitter_username'] = (!empty($api_result->social_twitter_id)) ? '@' . $api_result->social_twitter_id : '';
    $twitter_followers = modasphere_format_followers(modasphere_twitter_followers_count($api_result->social_twitter_id, $social_twitter_token));
    $result['twitter_followers'] = $twitter_followers;
    $result['casting_networks_link'] = $api_result->social_casting_networks;
    $result['email'] = $api_result->email;
    $result['main_address'] = $api_result->main_address;
    $result['phone'] = $api_result->phone;
    return $result;
}

function modasphere_get_talent_profile($domain, $user_api, $id_talent, $opv, $limit = 0, $fields)
{
    $api_profile = (array)modasphere_api_get_content($domain, $user_api, 'contact_profile', false, 0, 0, 0, $id_talent, 0);
    $contact_fields = array();
    $count = 0;

    foreach ($fields as $field) {
        if ($opv[$field->name]) {
            if ($opv[$field->name] == 'checked') {
                if ($field->config == null) {
                    $count++;
                    if ($field->type == 'NullBooleanField') {
                        $contact_fields[$field->label] = ($api_profile[$field->name] == true) ? 'Yes' : 'No';
                    } else {
                        $contact_fields[$field->label] = $api_profile[$field->name];
                    }
                } elseif ($field->type == 'ChoiceField') {
                    foreach ($field->config as $conf_key => $conf) {
                        if ($conf_key == $api_profile[$field->name]) {
                            $count++;
                            $contact_fields[$field->label] = $conf;
                            break;
                        }
                    }
                } elseif ($field->type == 'MultiChoiceField' && $limit == 0) {
                    $multiField = array();
                    foreach ($field->config as $conf_key => $conf) {
                        if ($api_profile[$field->name] && is_array($api_profile[$field->name])) {
                            foreach ($api_profile[$field->name] as $select_multi) {
                                if ($conf_key == $select_multi) {
                                    $multiField[] = $conf;
                                }
                            }
                        }
                    }
                    $contact_fields[$field->label] = $multiField;
                }
            }
        }
        if ($count >= $limit && $limit != 0) break;
    }
    return $contact_fields;
}

function get_format_name($talent, $displaying, $link = false, $overlay = false)
{
    $response = '';
    if (!$overlay) {
        if ($displaying == 'full') {
            $response = ($link) ? $talent->name . "-" . $talent->surname : $talent->name . " " . $talent->surname;
        } elseif ($displaying == 'name_s') {
            $response = ($link) ? $talent->name . "-" . $talent->surname{0} : $talent->name . " " . $talent->surname{0} . ".";
        } elseif ($displaying == 'name') {
            $response = $talent->name;
        }
    } else {
        if ($displaying == 'full') {
            $response = '<span>' . $talent->name . '</span><span>' . $talent->surname . '</span>';
        } elseif ($displaying == 'name_s') {
            $response = '<span></span><span>' . $talent->name . ' ' . $talent->surname{0} . '.</span>';
        } elseif ($displaying == 'name') {
            $response = '<span></span><span>' . $talent->name . '</span>';
        }
    }
    return $response;
}

function modasphere_talent_information($domain, $user_api, $id_division)
{
    $name_display = get_option('modasphere_talent_list_name_display', '');
    $api_result = modasphere_api_get_content($domain, $user_api, 'contact_division', true, $id_division);
    $divisions = $api_result->objects;
    $current_sync_time = time();
    $profile_frequency = (int)get_option('modasphere_synchronization_profile_frequency', '3600');
    foreach ($divisions as $div) {
        $talent = $div->contact;
        $galleries = $div->galleries;

        $talent_link_name = preg_replace("/[^a-zA-Z0-9\s-]/", "",str_replace(' ', '', strtolower(get_format_name($talent, $name_display, true)))) . '-' . $talent->id;
        $t_obj = (array)json_decode(get_transient($talent_link_name));

        $division_galleries = (!empty($t_obj['division_galleries'])) ? (array)$t_obj['division_galleries'] : array();
        $division_galleries_count = count($division_galleries);
        foreach ($division_galleries as $dg_key => $dg) {
            if ($dg->division_id == $div->division->id) {
                if ($division_galleries_count > 1) {
                    unset($division_galleries[$dg_key]);
                    $division_galleries = array_values($division_galleries);
                } else {
                    $division_galleries = array();
                }
            }
        }

        if (empty($t_obj['current_sync_time']) || (($current_sync_time - $t_obj['current_sync_time']) > $profile_frequency) || !empty($division_galleries)) {

            $gallery = array();
            $gallery_default = array();
            $dimensions = array();

            $i = 0;
            foreach ($galleries as $gall) {
                $gallery[] = [
                    'id' => $gall->id,
                    'name' => addslashes($gall->name),
                    'type' => $gall->type,
                    'cover' => $gall->cover
                ];
                if ($i == 0) {
                    $gallery_default = [
                        'id' => $gall->id,
                        'name' => $gall->name,
                        'type' => $gall->type
                    ];
                }
                $i++;
            }
            $division_galleries[] = [
                'division_id' => $div->division->id,
                'gallery_default' => $gallery_default,
                'gallery' => $gallery,
            ];
            $talent_obj = [
                'id' => $talent->id,
                'name' => $talent->name,
                'surname' => $talent->surname,
                'cover' => $talent->cover,
                'division_galleries' => (array)$division_galleries,
                'dimensions' => $dimensions
            ];

            $talent_info = modasphere_get_talent_info($domain, $user_api, $talent->id);

            $contact_fields = json_decode(get_transient('contact_fields'));
            $id_division = $div->division->id;
            $options_view = get_option('modasphere_talent_info_options_view', '');
            if ($options_view != '') $options_view = json_decode($options_view);
            $opv = (array)$options_view->$id_division;
            if (is_array($opv)) {
                $talent_profile = modasphere_get_talent_profile($domain, $user_api, $talent->id, $opv, 6, $contact_fields);
                foreach ($talent_profile as $key => $info) {
                    $dimensions[$key] = $info;
                }
            }
            $talent_obj['dimensions'] = $dimensions;
            $talent_obj['current_sync_time'] = $current_sync_time;

            $talent_information = array_merge($talent_obj, $talent_info);

            set_transient($talent_link_name, json_encode($talent_information), YEAR_IN_SECONDS);
        }
    }
}

function modasphere_crop_image($photo, $id_talent, $photo_larger_side_length, $image_save_dir, $type_img = 'gallery', $cover_h = 250)
{
    $args = array(
        'timeout' => 60
    );
    $res = wp_remote_get($photo->url, $args);
    if ($res && $res["response"]["code"] == 200) {
        $img_str = $res['body'];

        $img_str_size = getimagesizefromstring($img_str);
        $old_img = imagecreatefromstring($img_str);
        if ($img_str_size[0] < $img_str_size[1]) {
            $ratio = $img_str_size[1] / $img_str_size[0];
            if($type_img == 'cover'){
                $new_width = $photo_larger_side_length;
                $new_height = $new_width * $ratio;
                if($new_height < $cover_h){
                    $new_height = $cover_h;
                    $new_width = $new_height / $ratio;
                }
            }else{
                $new_width = $cover_h;
                $new_height = $new_width * $ratio;
                if($new_height < $photo_larger_side_length){
                    $new_height = $photo_larger_side_length;
                    $new_width = $new_height / $ratio;
                }
            }
        } else {
            $ratio = $img_str_size[0] / $img_str_size[1];
            if ($type_img == 'cover') {
                $new_height = $cover_h;
                $new_width = $new_height * $ratio;
            } else {
                $new_width = $photo_larger_side_length;
                $new_height = $new_width / $ratio;
                if($new_height < $cover_h){
                    $new_height = $cover_h;
                    $new_width = $new_height * $ratio;
                }
            }
        }
        $img_in = imagecreatetruecolor($new_width, $new_height);
        imagecopyresampled($img_in, $old_img, 0, 0, 0, 0, $new_width, $new_height, $img_str_size[0], $img_str_size[1]);

        $width_new_img = imagesx($img_in);
        $height_new_img = imagesy($img_in);

        if ($img_in !== FALSE) {
            if ($type_img == 'cover') {
                $padding = ($width_new_img - $photo_larger_side_length) / 2;
                $img_out = imagecrop($img_in, ['x' => $padding, 'y' => 0, 'width' => $photo_larger_side_length, 'height' => $cover_h]);
                imagepng($img_out, $image_save_dir . '/modasphere-img/' . $photo->id . '/' . $photo->id . '.png');
                imagedestroy($img_out);
            } else {
                if ($width_new_img < $height_new_img){
                    $padding = ($width_new_img - $cover_h) / 2;
                    $img_out = imagecrop($img_in, ['x' => $padding, 'y' => 0, 'width' => $cover_h, 'height' => $photo_larger_side_length]);
                }else{
                    $img_out = imagecrop($img_in, ['x' => 0, 'y' => 0, 'width' => $photo_larger_side_length, 'height' => $cover_h]);
                }
                imagejpeg($img_out, $image_save_dir . '/storage/' . $id_talent . '/' . $photo->id . '.jpg');
                imagedestroy($img_out);
                modasphere_strip_image($image_save_dir . '/storage/' . $id_talent . '/' . $photo->id . '.jpg');
            }
            imagedestroy($img_in);
        }
        unset($img_str);
        unset($img_str_size);
    }
    unset($res);
}

function modasphere_download_photo($domain, $user_api)
{
    $upload_folder = wp_get_upload_dir();
    $image_save_dir = $upload_folder['basedir'];
    $photo_larger_side_length = get_option('modasphere_photo_larger_side_length', '1150');
    $photo_short_side_length = get_option('modasphere_photo_short_side_length', '780');
    $talent_images_width = get_option('modasphere_talent_list_image_width', '');
    $talent_images_height = get_option('modasphere_talent_list_image_height', '');
    $modasphere_cover_photo = get_option('modasphere_talent_list_cover_photo','talent');

    $api_result = modasphere_api_get_content($domain, $user_api, 'division');
    if ($api_result != false) {
        $divisions = $api_result->objects;
        $visibility_menu_items = (array)json_decode(get_option('modasphere_visibility_menu_items', ''));
        shuffle($divisions);
        foreach ($divisions as $div) {
            if (is_array($visibility_menu_items) && $visibility_menu_items['m' . $div->id] == 'checked') {
                $cd_result = modasphere_api_get_content($domain, $user_api, 'contact_division', false, $div->id);
                $contacts = $cd_result->objects;
                foreach ($contacts as $contact) {
                    $talent_id = $contact->contact->id;
                    $galleries = $contact->galleries;

                    if($modasphere_cover_photo == 'talent'){
                        $cover = $contact->contact->cover;
                    }else{
                        $cover = $galleries[0]->cover;
                    }

                    if (!file_exists($image_save_dir . '/modasphere-img/' . $cover->id . '/' . $cover->id . '.png')) {
                        mkdir($image_save_dir . "/modasphere-img/" . $cover->id);
                        modasphere_crop_image($cover, $talent_id, $talent_images_width, $image_save_dir, 'cover', $talent_images_height);
                    }

                    if (!file_exists($image_save_dir . '/storage/' . $talent_id)) {
                        mkdir($image_save_dir . "/storage/" . $talent_id);
                    }
                    if (!empty($galleries)) {
                        foreach ($galleries as $gl) {
                            if ($gl->type == 0) {
                                $current_gallery_result = modasphere_api_get_content($domain, $user_api, 'contact_gallery', false, 0, 100, 0, $talent_id, $gl->id);
                                $current_gallery = $current_gallery_result->objects;
                                if (!empty($current_gallery)) {
                                    foreach ($current_gallery as $gall) {
                                        if (!file_exists($image_save_dir . '/storage/' . $talent_id . '/' . $gall->photo->id . '.jpg')) {
                                            modasphere_crop_image($gall->photo, $talent_id, $photo_larger_side_length, $image_save_dir,'gallery', $photo_short_side_length);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

function modasphere_strip_image($original_file)
{

    if (extension_loaded('imagick')) {

        $image = new Imagick($original_file);
        $profile = $image->getImageProfiles('icc', true);
        $image->stripImage();
        if (!empty($profile)) {
            $image->profileImage('icc', $profile['icc']);
        }
        $image->writeImage($original_file);
        $image->clear();
        $image->destroy();
    }

}

function modasphere_favorite_build_email($favorite_links)
{
    $email_body = '
        <table style="max-width:100%;width:100%; font-size: 14px;" width="100%" border="0" cellpadding="0" cellspacing="0">
            <tbody>
                <tr><td style="text-align: center;">' . get_custom_logo() . '</td></tr>
                <tr><td style="padding: 50px 0 20px 0;">This message is to notify you that selects have been made on the website.</td></tr>';

    foreach ($favorite_links as $link) {
        $email_body .= '<tr><td><a href="' . $link['talent_link'] . '">' . $link['talent_name'] . '</a></td></tr>';
    }
    $email_body .= '<tr><td style="text-align: center;">
            <span style="border-top: 1px solid #808080; margin-top: 50px; display: block; width: 100%;">
                <a href="https://modasphere.com/">Modasphere</a> by Casting Networks</span>
            </td></tr>
            </tbody>
        </table>';

    return $email_body;
}

function modasphere_get_gallery_photo_pairs($gallery, $image_save_dir, $upload_dir, $id_talent)
{
    $pair = array();
    $result_gallery = array();
    foreach ($gallery as $gall) {
        $save_path = $image_save_dir . '/storage/' . $id_talent . '/' . $gall->photo->id . '.jpg';
        $upload_path = $upload_dir . '/storage/' . $id_talent . '/' . $gall->photo->id . '.jpg';
        if (file_exists($save_path)) {
            $item_img_size = getimagesize($upload_path);
        } else {
            $item_img_size = getimagesize($gall->photo->url);
        }
        if ($item_img_size[0] < $item_img_size[1]) {
            if (count($pair) < 2) {
                $pair[] = $gall;
            } else {
                $result_gallery[] = $pair;
                $pair = array();
                $pair[] = $gall;
            }
        } else {
            $result_gallery[] = $gall;
        }
    }
    if (!empty($pair)) {
        $result_gallery[] = $pair;
    }
    return $result_gallery;
}

function modasphere_get_page_name(){
    return strtolower(str_replace(' ','-', trim(get_option('modasphere_page_name', 'modasphere'))));
}

function get_offset($talent_count_of_page, $page){
    if ($page > 1){
        return (($page - 1) * $talent_count_of_page);
    }else{
        return 0;
    }
}