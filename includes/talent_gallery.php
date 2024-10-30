<?php
ini_set("memory_limit", -1);
include_once 'functions.php';
$talent = get_query_var('talent');
$current_gallery = get_query_var('gallery');
$divisions_name = get_query_var('division_name');
$page = get_query_var('pg');

$chunks_division = explode('-', $divisions_name);
$name_of_division = array_shift($chunks_division);
$id_division = array_pop($chunks_division);
$chunks_talent = explode('-', $talent);
$id_talent = array_pop($chunks_talent);
$modasphere_favorites_page = get_option('modasphere_favorites_page', '');
if ($modasphere_favorites_page == 'checked') {
    $talent_in_session = false;
    if (isset($_SESSION['modasphere_favorites'])) {
        $talent_in_session = in_array($id_talent, $_SESSION['modasphere_favorites']);
    }
    $heard = ($talent_in_session) ? '<i id="modasphere-favorite-heart" class="fas fa-heart ml-3"></i>' : '<i id="modasphere-favorite-heart" class="far fa-heart ml-3"></i>';
    $favorite_content = ($talent_in_session) ? '<p id="modasphere-actions-favorites" data-talent-id="' . $id_talent . '"><strong>Remove from favorites</strong></p>' : '<p id="modasphere-actions-favorites" data-talent-id="' . $id_talent . '"><strong>Add to favorites</strong></p>';
} else {
    $heard = '';
    $favorite_content = '';
}

if ($page != '') {
    if ($name_of_division === 'ms') {
        $back_link = get_permalink() . "ms_search/" . $id_division . '/' . $page;
    } else {
        $back_link = get_permalink() . $divisions_name . '/' . $page;
    }
    $offset_gallery = '/' . $page;
} else {
    if ($name_of_division === 'ms') {
        if ($id_division == 'favorite') $back_link = get_permalink() . "favorites";
        else $back_link = get_permalink() . "ms_search/" . $id_division;
        $id_division = 9999999;
    } else {
        $back_link = get_permalink() . $divisions_name;
    }
    $offset_gallery = '';
}
$click_pl = '';
if ($_SERVER['HTTP_REFERER'] == get_permalink() . 'search/' || $_SERVER['HTTP_REFERER'] == get_permalink() . 'search') {
    $click_pl = 'onclick="document.getElementById(\'SearchForm\').submit();"';
    $parent_link = '<a href="#" ' . $click_pl . '><strong>Back</strong></a>';
} else {
    $parent_link = '<a href="' . $back_link . '"><strong>Back</strong></a>';
}
$dir = plugins_url("modasphere/images/");
$gallery_item_id = 0;
$name_display = get_option('modasphere_talent_list_name_display', '');

$talent_obj = json_decode(get_transient($talent));
$division_galleries = $talent_obj->division_galleries;

if ($division_galleries && is_array($division_galleries)) {
    $nav_gallery = '<ul class="nav nav-tabs">';
    $active_link_count = 0;
    $gallery_cover = '';

    if ($name_of_division != 'ms') {
        foreach ($division_galleries as $div_g) {
            if ($div_g->division_id == $id_division) {
                $galleries = $div_g->gallery;
                $gallery_item_id = $div_g->gallery_default->id;
                $gallery_item_type = $div_g->gallery_default->type;
                foreach ($galleries as $item) {
                    $active_link = ($item->id == $gallery_item_id && empty($current_gallery)) ? 'active' : '';
                    if (!empty($current_gallery) && $current_gallery == str_replace(' ', '-', strtolower($item->name))) {
                        $gallery_item_id = $item->id;
                        $gallery_item_type = $item->type;
                        $active_link = 'active';
                    }
                    if ($active_link == 'active') {
                        $gallery_cover = $item->cover;
                    }
                    $nav_gallery .= '<li class="nav-item">
        <a class="nav-link ' . $active_link . '" href="' . get_permalink() . $divisions_name . '/' . $talent . '/' .
                        str_replace(' ', '-', strtolower($item->name)) . $offset_gallery . '">' . urldecode($item->name) . '</a>
        </li>';
                }
            }
        }
    } else {
        foreach ($division_galleries as $div_g) {
            $galleries = $div_g->gallery;
            $gallery_item_id = $div_g->gallery_default->id;
            $gallery_item_type = $div_g->gallery_default->type;
            foreach ($galleries as $item) {
                $active_link = ($active_link_count == 0 && $item->id == $gallery_item_id && empty($current_gallery)) ? 'active' : '';
                if (!empty($current_gallery) && $active_link_count == 0 && $current_gallery == str_replace(' ', '-', strtolower($item->name))) {
                    $gallery_item_id = $item->id;
                    $gallery_item_type = $item->type;
                    $active_link = 'active';
                }
                $active_link_count = ($active_link == 'active') ? $active_link_count + 1 : $active_link_count;
                $nav_gallery .= '<li class="nav-item">
        <a class="nav-link ' . $active_link . '" href="' . get_permalink() . $divisions_name . '/' . $talent . '/' .
                    str_replace(' ', '-', strtolower($item->name)) . $offset_gallery . '">' . urldecode($item->name) . '</a>
        </li>';
            }
        }
    }
    $nav_gallery .= '</ul>';
}

$user_api = get_option('modasphere_user_api', '');
$set_domain = get_option('modasphere_domain', '');

if ($user_api != '' && $set_domain != '') {
$api_result = modasphere_api_get_content($set_domain, $user_api, 'contact_gallery', false, 0, 100, 0, $id_talent, $gallery_item_id);
$gallery = $api_result->objects;

$instagram_link = $talent_obj->instagram_link;
$instagram_username = $talent_obj->instagram_username;
$instagram_followers = $talent_obj->instagram_followers;
$twitter_link = $talent_obj->twitter_link;
$twitter_username = $talent_obj->twitter_username;
$twitter_followers = $talent_obj->twitter_followers;
$casting_networks_link = $talent_obj->casting_networks_link;

$upload_folder = wp_get_upload_dir();
$upload_dir = $upload_folder['baseurl'];
$image_save_dir = $upload_folder['basedir'];
?>
<div class="container modasphere talent-gallery">
    <?php
    $new_line = '';
    $talent_info_block_size = 3;
    $talent_gallery_block_size = 9;
    $modasphere_talent_info_position = get_option('modasphere_talent_info_position', '');
    if ($modasphere_talent_info_position == 'left' || $modasphere_talent_info_position == 'right') {
        $talent_info_block_size = 3;
        $talent_gallery_block_size = 9;
        $new_line = 'style="display: block;"';
    } elseif ($modasphere_talent_info_position == 'top') {
        $talent_info_block_size = 12;
        $talent_gallery_block_size = 12;
    }

    $talent_block_info_view = '<div class="col-md-' . $talent_info_block_size . ' talent-info">'; //<h4>Talent info</h4>
    $options_view = get_option('modasphere_talent_info_options_view', '');
    if ($options_view != '') $options_view = json_decode($options_view);

    $opv = (array)$options_view->$id_division;
    if (is_array($opv)) {
        $contact_fields = json_decode(get_transient('contact_fields'));
        $talent_profile = modasphere_get_talent_profile($set_domain, $user_api, $id_talent, $opv, 0, $contact_fields);
        foreach ($talent_profile as $key => $info) {
            if (is_array($info)) {
                $multiField = '';
                foreach ($info as $in) {
                    $multiField .= ($multiField == '') ? $in : ', ' . $in;
                }
                $info = $multiField;
            }
            $talent_block_info_view .= '<div class="talent-info-item" ' . $new_line . '>' . $key . ': <strong>' . $info . '</strong></div>';
        }
    }

    $talent_social = get_option('modasphere_talent_social', '');
    if ($talent_social != '') $talent_social = json_decode($talent_social);
    $talent_s = (array)$talent_social->$id_division;
    if ($talent_s['talent_instagram'] == 'checked') {
        $instagram_link_text = ($talent_s['talent_instagram_username'] == 'checked') ? $instagram_username . ' ' : '';
        $instagram_link_text .= ($talent_s['talent_instagram_f_count'] == 'checked') ? $instagram_followers : '';
        if ($instagram_link) {
            if ($talent_s['talent_social_link_disable'] == 'checked') {
                $talent_block_info_view .= '<div ' . $new_line . '><img class="social-icons"
                                             src="' . plugins_url("modasphere") . '/img/instagram.png"
                                             alt="instagram"><strong>' . $instagram_link_text . '</strong></div>';
            } else {
                $talent_block_info_view .= '<div ' . $new_line . '><a href="' . $instagram_link . '" target="_blank">
                                             <img class="social-icons"
                                             src="' . plugins_url("modasphere") . '/img/instagram.png"
                                             alt="instagram"><strong>' . $instagram_link_text . '</strong></a></div>';
            }
        }
    }
    if ($talent_s['talent_twitter'] == 'checked') {
        $twitter_link_text = ($talent_s['talent_twitter_username'] == 'checked') ? $twitter_username . ' ' : '';
        $twitter_link_text .= ($talent_s['talent_twitter_f_count'] == 'checked') ? $twitter_followers : '';
        if ($twitter_link) {
            if ($talent_s['talent_social_link_disable'] == 'checked') {
                $talent_block_info_view .= '<div ' . $new_line . '><img class="social-icons" src="' . plugins_url("modasphere") . '/img/twitter.png"
                                             alt="twitter"><strong>' . $twitter_link_text . '</strong></div>';
            } else {
                $talent_block_info_view .= '<div ' . $new_line . '><a href="' . $twitter_link . '" target="_blank">
                                            <img class="social-icons" src="' . plugins_url("modasphere") . '/img/twitter.png"
                                             alt="twitter"><strong>' . $twitter_link_text . '</strong></a></div>';
            }
        }
    }
    if ($talent_s['talent_casting_networks'] == 'checked') {
        if ($casting_networks_link) {
            $talent_block_info_view .= '<div ' . $new_line . '><a href="' . $casting_networks_link . '" target="_blank">
                                            <img class="social-icons" src="' . plugins_url("modasphere") . '/img/casting_networks.png"
                                             alt="casting_networks">Resume</a></div>';
        }
    }

    $talent_block_info_view .= $favorite_content;
    $talent_block_info_view .= '<p>' . $parent_link . '</p></div>';

    if ($modasphere_talent_info_position == 'right') {
    ?>
    <div class="row">
        <div class="col-md-<?= $talent_gallery_block_size ?>"><?= $nav_gallery ?></div>
        <div class="col-md-<?= $talent_info_block_size ?>">
            <h4 class="modasphere-talent-name"><?= get_format_name($talent_obj, $name_display) ?></h4>
            <?= $heard ?></div>
    </div>
    <div class="row">
        <?php
        } elseif ($modasphere_talent_info_position == 'left') {
        ?>
        <div class="row">
            <div class="col-md-<?= $talent_info_block_size ?>">
                <h4 class="modasphere-talent-name"><?= get_format_name($talent_obj, $name_display) ?></h4>
                <?= $heard ?></div>
            <div class="col-md-<?= $talent_gallery_block_size ?>"><?= $nav_gallery ?></div>
        </div>
        <div class="row">
            <?php
            echo $talent_block_info_view;
            } elseif ($modasphere_talent_info_position = 'top') { ?>
            <div class="row">
                <div class="col-md-<?= $talent_info_block_size ?>">
                    <h4 class="modasphere-talent-name"><?= get_format_name($talent_obj, $name_display) ?></h4>
                    <?= $heard ?></div>
            </div>
            <div class="row">
                <?php echo $talent_block_info_view; ?>
                <div class="col-md-<?= $talent_gallery_block_size ?>"><?= $nav_gallery ?></div>
                <?php
                }

                if ($gallery && is_array($gallery)) {
                    if ($gallery_item_type == 0) {
                        $talent_block_gallery_view = '<div class="col-md-' . $talent_gallery_block_size . '">';
                        if (get_option('modasphere_talent_type_gallery', '') == 'slider') {
                            $talent_block_gallery_view .= '
                    <div id="talentGallery" class="carousel slide" data-ride="carousel">
                        <ol class="carousel-indicators" style="list-style: none;">';
                            $ii = 0;
                            foreach ($gallery as $gall) {
                                $talent_block_gallery_view .= '<li data-target="#talentGallery" data-slide-to="' . $ii . '"';
                                if ($ii == 0) $talent_block_gallery_view .= ' class="active"></li>';
                                else $talent_block_gallery_view .= '></li>';
                                $ii++;
                            }
                            $talent_block_gallery_view .= '    
                        </ol>
                        <div class="carousel-inner">';
                            $ii = 0;
                            foreach ($gallery as $gall) {
                                $talent_block_gallery_view .= '
                                <div style="height: 550px;" class="carousel-item text-center';
                                if ($ii == 0) $talent_block_gallery_view .= ' active">';
                                else $talent_block_gallery_view .= '">';

                                $img_path = $image_save_dir . '/storage/' . $id_talent . '/' . $gall->photo->id;
                                $img_path_upl = $upload_dir . '/storage/' . $id_talent . '/' . $gall->photo->id;

                                if (file_exists($img_path . '.jpg') || file_exists($img_path . '.png')) {
                                    $img_upl_path = (file_exists($img_path . '.jpg')) ? $img_path_upl . '.jpg' : $img_path_upl . '.png';
                                    $talent_block_gallery_view .= '
                            <img class="d-inline-block h-100" src="' . $img_upl_path . '"
                                 alt="' . $gall->photo->name . '">';
                                } else {
                                    $talent_block_gallery_view .= '<img class="d-inline-block h-100" src="' . $gall->photo->url . '" alt="' . $gall->photo->name . '">';
                                }
                                $talent_block_gallery_view .= '</div>';
                                $ii++;
                            }
                            $talent_block_gallery_view .= '
                        </div>
                        <a class="carousel-control-prev" href="#talentGallery" role="button" data-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next" href="#talentGallery" role="button" data-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </a>
                    </div>';
                        } elseif (get_option('modasphere_talent_type_gallery', '') == 'fotorama') {
                            $fotorama_get_options = get_option('modasphere_fotorama_options', '');
                            if ($fotorama_get_options != '') $fotorama_get_options = (array)json_decode($fotorama_get_options);
                            $fotorama_insert_options = '';
                            if (is_array($fotorama_get_options)) {
                                if ($fotorama_get_options['fullscreen'] == 'checked') $fotorama_insert_options .= ' data-allowfullscreen="true"';
                                if ($fotorama_get_options['thumbnails'] == 'checked') $fotorama_insert_options .= ' data-nav="thumbs"';
                                if ($fotorama_get_options['loop'] == 'checked') $fotorama_insert_options .= ' data-loop="true"';
                                if ($fotorama_get_options['autoplay'] == 'checked') $fotorama_insert_options .= ' data-autoplay="true"';
                                if ($fotorama_get_options['keyboard'] == 'checked') $fotorama_insert_options .= ' data-keyboard="true"';
                                if ($fotorama_get_options['transition'] == 'slide') $fotorama_insert_options .= ' data-transition="slide"';
                                if ($fotorama_get_options['transition'] == 'crossfade') $fotorama_insert_options .= ' data-transition="crossfade"';
                            }

                            if ($modasphere_talent_info_position == 'top') {
                                $talent_block_gallery_view .= '<div class="fotorama" data-width="100%" data-ratio="1110/886" data-maxheight="886" data-max-width="1110" ' . $fotorama_insert_options . '>';
                                $div_style = 'style="width:555px; display: inline-block; overflow: hidden;"';
                                $img_style = 'style="height: 903px;width: auto;max-width: none;"';
                            } else {
                                $talent_block_gallery_view .= '<div class="fotorama" data-width="100%" data-ratio="654/470" data-maxheight="640" data-max-width="824" ' . $fotorama_insert_options . '>';
                                $div_style = 'style="width:412px; display: inline-block; overflow: hidden;"';
                                $img_style = 'style="height: 657px;width: auto;max-width: none;"';
                            }

                            if (!file_exists($image_save_dir . '/storage/' . $id_talent)) {
                                mkdir($image_save_dir . "/storage/" . $id_talent);
                            }
                            if ($fotorama_get_options['two_photo_on_slide'] == 'checked') {
                                $gallery_pairs = modasphere_get_gallery_photo_pairs($gallery, $image_save_dir, $upload_dir, $id_talent);
                                foreach ($gallery_pairs as $gall) {
                                    if (is_array($gall)) {
                                        $talent_block_gallery_view .= '<div class="item">';
                                        foreach ($gall as $gl) {
                                            $img_path = $image_save_dir . '/storage/' . $id_talent . '/' . $gl->photo->id;
                                            $img_path_upl = $upload_dir . '/storage/' . $id_talent . '/' . $gl->photo->id;

                                            if (file_exists($img_path . '.jpg') || file_exists($img_path . '.png')) {
                                                $img_upl_path = (file_exists($img_path . '.jpg')) ? $img_path_upl . '.jpg' : $img_path_upl . '.png';
                                                $talent_block_gallery_view .= '<div ' . $div_style . '>
                                                    <img src="' . $img_upl_path . '" alt="' . $gl->photo->name . '" ' . $img_style . '></div>';
                                            } else {
                                                $talent_block_gallery_view .= '<div ' . $div_style . '><img src="' . $gl->photo->url . '" alt="' . $gl->photo->name . '" ' . $img_style . '></div>';
                                            }
                                        }
                                        $talent_block_gallery_view .= '</div>';
                                    } else {
                                        $img_path = $image_save_dir . '/storage/' . $id_talent . '/' . $gall->photo->id;
                                        $img_path_upl = $upload_dir . '/storage/' . $id_talent . '/' . $gall->photo->id;

                                        if (file_exists($img_path . '.jpg') || file_exists($img_path . '.png')) {
                                            $img_upl_path = (file_exists($img_path . '.jpg')) ? $img_path_upl . '.jpg' : $img_path_upl . '.png';
                                            $talent_block_gallery_view .= '<div class="item"><img src="' . $img_upl_path . '" alt="' . $gall->photo->name . '"></div>';
                                        } else {
                                            $talent_block_gallery_view .= '<div class="item"><img src="' . $gall->photo->url . '" alt="' . $gall->photo->name . '"></div>';
                                        }
                                    }
                                }
                            } else {
                                foreach ($gallery as $gall) {
                                    $img_path = $image_save_dir . '/storage/' . $id_talent . '/' . $gall->photo->id;
                                    $img_path_upl = $upload_dir . '/storage/' . $id_talent . '/' . $gall->photo->id;

                                    if (file_exists($img_path . '.jpg') || file_exists($img_path . '.png')) {
                                        $img_upl_path = (file_exists($img_path . '.jpg')) ? $img_path_upl . '.jpg' : $img_path_upl . '.png';
                                        $talent_block_gallery_view .= '
                            <img src="' . $img_upl_path . '" alt="' . $gall->photo->name . '">';
                                    } else {
                                        $talent_block_gallery_view .= '<img src="' . $gall->photo->url . '" alt="' . $gall->photo->name . '">';
                                    }
                                }
                            }
                            $talent_block_gallery_view .= '</div>';
                        } elseif (get_option('modasphere_talent_type_gallery', '') == 'list') {
                            $talent_block_gallery_view .= '<div class="row gallery-list">';
                            $double_img_block = '';
                            $img_count = 0;

                            foreach ($gallery as $gall) {
                                $img_path = $image_save_dir . '/storage/' . $id_talent . '/' . $gall->photo->id;
                                $img_path_upl = $upload_dir . '/storage/' . $id_talent . '/' . $gall->photo->id;
                                if (file_exists($img_path . '.jpg') || file_exists($img_path . '.png')) {
                                    $image_save_path = (file_exists($img_path . '.jpg')) ? $img_path . '.jpg' : $img_path . '.png';
                                    $image_upload_path = (file_exists($img_path . '.jpg')) ? $img_path_upl . '.jpg' : $img_path_upl . '.png';
                                    $img_info = getimagesize($image_save_path);
                                    if ($img_info[0] < $img_info[1]) {
                                        $double_img_block .= '<div class="col-sm-6 mb-3">
                                            <img class="lazy img-fluid" data-src="' . $image_upload_path . '" alt="' . $gall->photo->name . '" />
                                            </div>';
                                        $img_count++;
                                    } else {
                                        $talent_block_gallery_view .= '<div class="col-sm-12 mb-3">
                                            <img class="lazy img-fluid" data-src="' . $image_upload_path . '" alt="' . $gall->photo->name . '" />
                                        </div>';
                                    }
                                } else {
                                    $img_info = getimagesize($gall->photo->url);
                                    if ($img_info[0] < $img_info[1]) {
                                        $double_img_block .= '<div class="col-sm-6 mb-3"><img class="lazy img-fluid" data-src="' . $gall->photo->url . '" alt="' . $gall->photo->name . '" /></div>';
                                        $img_count++;
                                    } else {
                                        $talent_block_gallery_view .= '<div class="col-sm-12 mb-3"><img class="lazy img-fluid" data-src="' . $gall->photo->url . '" alt="' . $gall->photo->name . '" /></div>';
                                    }
                                }
                                if ($img_count == 2) {
                                    $talent_block_gallery_view .= $double_img_block;
                                    $double_img_block = '';
                                    $img_count = 0;
                                }
                            }
                            if ($img_count > 0) {
                                $talent_block_gallery_view .= $double_img_block;
                            }
                            $talent_block_gallery_view .= '</div>';
                        }
                        $talent_block_gallery_view .= '</div>';
                    }
                    if ($gallery_item_type == 2) {
                        $talent_block_gallery_view = '<div class="col-md-' . $talent_gallery_block_size . '">';
                        foreach ($gallery as $gall) {
                            if ($gall->conversionjob->status == 2) {
                                $conversionjob_output_link = '';
                                $conversionjob_alt_output_link = '';
                                if (!empty($gall->conversionjob->output->url)) {
                                    $conversionjob_output_link = $gall->conversionjob->output->url;
                                }
                                if (!empty($gall->conversionjob->alt_output->url)) {
                                    $conversionjob_alt_output_link = $gall->conversionjob->alt_output->url;
                                }
                                $video_block_gallery_view .= '<div class="item"><video style="" width="100%" height="100%" controls="controls" class="talent-video">
                            <source src="' . $conversionjob_output_link . '" type="video/mp4">
                            <source src="' . $conversionjob_alt_output_link . '" type="video/ogg">
                                Your browser does not support the HTML5 Video.
                            </video></div>';
                            } elseif ($gall->embed) {
                                $embed = $gall->embed;
                                if (strpos($embed, 'vimeo')) {
                                    $embed_url = parse_url($embed, PHP_URL_PATH);
                                    if ($embed_url) {
                                        $embed = '//player.vimeo.com/video' . $embed_url;
                                    }
                                } elseif (strpos($embed, 'youtube')) {
                                    $embed_query = parse_url($embed, PHP_URL_QUERY);
                                    $embed_url = substr($embed_query, strpos($embed_query, '=') + 1);
                                    if ($embed_url) {
                                        $embed = '//www.youtube.com/embed/' . $embed_url;
                                    }
                                }
                                $video_block_gallery_view .= '<div class="item"><div class="video-container">
                                        <iframe class="embedvideo" src="' . $embed . '" width="100%" height=""
                                            frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen>
                                        </iframe>
                                </div></div>';
                            }
                        }
                        $modasphere_video_width = get_option('modasphere_video_width', '100');
                        $modasphere_video_maxheight = get_option('modasphere_video_maxheight', '670');
                        $modasphere_video_maxwidth = get_option('modasphere_video_maxwidth', '1140');
                        $data_width = 'data-width="' . $modasphere_video_width . '%" data-loop="true" data-maxheight="' . $modasphere_video_maxheight . '" data-maxwidth="' . $modasphere_video_maxwidth . '" data-ratio="' . $modasphere_video_maxwidth . '/' . $modasphere_video_maxheight . '" data-arrows="true" data-click="false" data-swipe="false"';
                        $talent_block_gallery_view .= '<div class="fotorama"' . $data_width . '>' . $video_block_gallery_view . '</div></div>';
                    }
                }
                if (get_option('modasphere_talent_info_position', '') == 'right') {
                    echo $talent_block_gallery_view . $talent_block_info_view;
                } else echo $talent_block_gallery_view;
                ?>
            </div>
        </div>
        <?php
        if (!empty($_SESSION['modasphere_search_query'])) {
            $s_q = json_decode($_SESSION['modasphere_search_query']);
            ?>
            <form id="SearchForm" method="post" action="<?= get_permalink() ?>search">
                <?php
                foreach ($s_q as $k_sq => $sq) {
                    if (!is_array($sq)) {
                        ?>
                        <input type="hidden" name="<?= $k_sq ?>" value="<?= $sq ?>">
                        <?php
                    } else {
                        foreach ($sq as $smq_i) {
                            ?>
                            <input type="hidden" name="<?= $k_sq ?>[]" value="<?= $smq_i ?>">
                            <?php
                        }
                    }
                }
                ?>
            </form>
            <?php
            $_SESSION['modasphere_search_query'] = '';
        }
        }
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function () {
                jQuery(function () {
                    jQuery('img.lazy').Lazy({
                        enableThrottle: true,
                        throttle: 250,
                        effect: "fadeIn",
                        effectTime: 2000,
                        // visibleOnly: true,
                    });
                });
                jQuery('#modasphere-actions-favorites').on('click', function () {
                    let talent_id = jQuery(this).data('talent-id');
                    let favorite_content = jQuery(this).text();
                    let favorite_heart = jQuery('#modasphere-favorite-heart');
                    let actions_favorites = jQuery('#modasphere-actions-favorites');

                    jQuery.ajax({
                        url: '<?php echo admin_url("admin-ajax.php") ?>',
                        type: 'POST',
                        data: 'action=modasphere_action_favorite&talent=' + talent_id + '&content=' + favorite_content,
                        dataType: "html",
                        success: function (html) {
                            response = html;
                            if (response) {
                                actions_favorites.empty();
                                if (favorite_content === 'Add to favorites') {
                                    favorite_heart.removeClass('far');
                                    favorite_heart.addClass('fas');
                                    actions_favorites.append('<strong>Remove from favorites</strong>');
                                }
                                if (favorite_content === 'Remove from favorites') {
                                    favorite_heart.removeClass('fas');
                                    favorite_heart.addClass('far');
                                    actions_favorites.append('<strong>Add to favorites</strong>');
                                }
                            }
                        },
                        error: function (html) {
                            alert(html);
                            alert(html.error);
                        }
                    });
                });
                jQuery('.modasphere').on('touchend mousedown', '.fotorama__arr, .fotorama__dot, video', function () {
                    jQuery("iframe").each(function () {
                        let src = jQuery(this).attr('src');
                        jQuery(this).attr('src', src);
                    });
                    jQuery("video").each(function () {
                        jQuery(this).get(0).pause();
                    });
                });
                jQuery('.modasphere').on('touchstart', 'video', function () {
                    if (!jQuery(this).get(0).paused) {
                        jQuery(this).get(0).pause();
                    }
                });
            });
        </script>