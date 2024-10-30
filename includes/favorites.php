<?php
ini_set("memory_limit", -1);
include_once 'functions.php';
$user_api = get_option('modasphere_user_api', '');
$set_domain = get_option('modasphere_domain', '');

if (!empty($_POST)) {
    if(is_array($_SESSION['modasphere_favorites']) && !empty($_SESSION['modasphere_favorites'])) {
        $favorite_links = $_SESSION['modasphere_favorites_link'];
        $build_email = modasphere_favorite_build_email($favorite_links);
        $headers[] = 'Content-type: text/html; charset=utf-8';
        if (isset($_POST['send_agency'])) {
            $agency_email = get_option('modasphere_agency_email');
            if ($agency_email) {
                wp_mail($agency_email, 'Website Favorites Notify', $build_email, $headers);
            }
        } elseif (isset($_POST['send_me'])) {
            $your_email = sanitize_text_field($_POST['your_email']);
            if (!empty($your_email) and is_email($your_email)) {
                wp_mail($your_email, 'Website Favorites Notify', $build_email, $headers);
            }
        }
    }
}
$_SESSION['modasphere_favorites_link'] = array();

if ($user_api != '' && $set_domain != '') {
    $api_result = modasphere_api_get_content($set_domain, $user_api, 'all_contacts', false, 0, 1000, 0);
    $contacts = $api_result->objects;
    $chunk_link = 'ms-favorite';

    $talent_images_width = get_option('modasphere_talent_list_image_width', '');
    $talent_images_height = get_option('modasphere_talent_list_image_height', '');

    $upload_folder = wp_get_upload_dir();
    $upload_dir = $upload_folder['baseurl'];
    $new_image_save_dir = $upload_folder['basedir'];
    $favorites = $_SESSION['modasphere_favorites'];

    $thumbnail_overlay = get_option('modasphere_talent_list_thumbnail_overlay', '');
    if ($thumbnail_overlay != '') $thumbnail_overlay = json_decode($thumbnail_overlay);
    $id_division = 9999999;
    $thumbnail_overlay = $thumbnail_overlay->$id_division;
    ?>
    <div class="container modasphere favorites">
        <div class="row">
            <div class="col-12 text-center"><h4 class="head-division">Favorites</h4></div>
        </div>
        <?php
        if (is_array($favorites) && !empty($favorites)) {
            ?>
            <div class="row">
                <div class="col-12">
                    <div class="card-deck">
                        <?php
                        foreach ($contacts as $item) {
                            $talent = $item->contact;
                            $talent_in_favorite = in_array($talent->id, $favorites);

                            if ($talent_in_favorite) {
                                $talent = $item->contact;
                                $cover = $talent->cover;

                                $name_display = get_option('modasphere_talent_list_name_display', '');
                                $talent_link_name = str_replace(' ', '', strtolower(get_format_name($talent, $name_display, true))) . '-' . $talent->id;
                                $talent_obj = (array)json_decode(get_transient($talent_link_name));
                                $instagram_link = $talent_obj['instagram_link'];
                                $instagram_followers = $talent_obj['instagram_followers'];
                                $twitter_link = $talent_obj['twitter_link'];
                                $twitter_followers = $talent_obj['twitter_followers'];
                                ?>
                                <div class="<?= ($thumbnail_overlay != 'disabled' && empty($ms_search)) ? 'talent-caption-overlay' : '' ?> col-<?= get_option('modasphere_talent_list_col', '4') ?> m-0 p-0">
                                    <div class="card">
                                        <div class="caption-item">
                                            <a href="<?= get_permalink() . $chunk_link . "/" . $talent_link_name ?>"
                                               class="text-decoration-none">
                                                <?php
                                                if (isset($cover->id) && isset($cover->url)) {
                                                    if (file_exists($new_image_save_dir . '/modasphere-img/' . $cover->id . '.png')) {
                                                        ?>
                                                        <img class="card-img-top"
                                                             src="<?= $upload_dir . '/modasphere-img/' . $cover->id . '.png' ?>"
                                                             alt="<?= $talent->name ?>">
                                                        <?php
                                                    } else {
                                                        echo '<div style="height: ' . $talent_images_height . 'px;overflow: hidden;"><img class="card-img-top" style="width: ' . $talent_images_width . 'px;" src="' . $cover->url . '" alt="' . get_format_name($talent, $name_display, true) . '"></div>';
                                                    }
                                                }
                                                if ($thumbnail_overlay != 'disabled') {
                                                    if ($thumbnail_overlay == 'dimensions') {
                                                        ?>
                                                        <div class="caption">
                                                            <div class="blur"></div>
                                                            <div class="caption-text">
                                                                <?php
                                                                $talent_dimensions = '';
                                                                $t_dimensions = $talent_obj['dimensions'];
                                                                foreach ($t_dimensions as $key => $info) {
                                                                    if (!is_object($info)) {
                                                                        $talent_dimensions .= '<p>' . $key . ' <strong>' . $info . '</strong></p>';
                                                                    }
                                                                }
                                                                echo $talent_dimensions;
                                                                ?>
                                                            </div>
                                                        </div>
                                                        <?php
                                                    } elseif ($thumbnail_overlay == 'social') {
                                                        ?>
                                                        <div class="caption">
                                                            <div class="blur"></div>
                                                            <div class="caption-text">
                                                                <?php
                                                                if ($instagram_link) {
                                                                    echo '<p class="social-pr"><i class="fa fa-2x fa-instagram"></i><span class="social-followers"> ' . $instagram_followers . '</span></p>';
                                                                }
                                                                if ($twitter_link) {
                                                                    echo '<p class="social-pr"><i class="fa fa-2x fa-twitter"></i><span class="social-followers"> ' . $twitter_followers . '</span></p>';
                                                                }
                                                                ?>
                                                                <div class="overlay-name">
                                                                    <?php
                                                                    echo get_format_name($talent, $name_display, false, true);
                                                                    ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php
                                                    }
                                                }
                                                $_SESSION['modasphere_favorites_link'][] = array('talent_name' => get_format_name($talent, $name_display),
                                                    'talent_link' => get_permalink() . $chunk_link . "/" . $talent_link_name);
                                                ?>
                                            </a>
                                        </div>
                                        <div class="card-body text-center">
                                            <a href="<?= get_permalink() . $chunk_link . "/" . $talent_link_name ?>"
                                               class="text-decoration-none">
                                                <h6 class="card-title">
                                                    <?php
                                                    echo get_format_name($talent, $name_display)
                                                    ?>
                                                </h6>
                                            </a>
                                            <span class="card-link text-secondary ms-remove-favorites"
                                                  data-talent-id="<?= $talent->id ?>">REMOVE FROM FAVORITES</span>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                foreach ($favorites as $key => $fav) {
                                    if ($fav == $talent->id) {
                                        unset($favorites[$key]);
                                    }
                                }
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 text-center mt-5">
                    <form method="post">
                        <div class="row">
                            <div class="col-4 text-right">
                                <button type="submit" class="btn btn-primary mb-2" name="send_agency">Send to Agency
                                </button>
                            </div>
                            <div class="col-4">
                                <input type="email" class="form-control" id="your_email" name="your_email"
                                       placeholder="Your Email">
                            </div>
                            <div class="col-4 text-left">
                                <button type="submit" class="btn btn-primary mb-2" name="send_me">Send to Me</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <?php
        }
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function () {
                jQuery('.ms-remove-favorites').on('click', function () {
                    let talent_id = jQuery(this).data('talent-id');
                    let favorite_content = 'Remove from favorites';
                    let item_for_delete = jQuery(this).parent().parent().parent();
                    jQuery.ajax({
                        url: '<?php echo admin_url("admin-ajax.php") ?>',
                        type: 'POST',
                        data: 'action=modasphere_action_favorite&talent=' + talent_id + '&content=' + favorite_content,
                        dataType: "html",
                        success: function (html) {
                            response = html;
                            if (response) {
                                if(response === '0'){
                                    item_for_delete.parent().parent().parent().next().remove();
                                }
                                item_for_delete.remove();
                            }
                        },
                        error: function (html) {
                            alert(html.error);
                        }
                    });
                });
            });
        </script>
    </div>
    <?php
}