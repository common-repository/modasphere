<?php
ini_set("memory_limit", -1);
include_once 'functions.php';
$user_api = get_option('modasphere_user_api', '');
$set_domain = get_option('modasphere_domain', '');

if ($user_api != '' && $set_domain != '') {
    $id_division = 0;
    $talent_count_of_page = get_option('modasphere_talent_list_pagination_count', '8');
    $page = get_query_var('pg');
    if(empty($page)) $page = 1;
    $offset = get_offset($talent_count_of_page, $page);
    $ms_search = get_query_var('ms_search');
    $divisions_name = '';

    if ($page > 1) $offs_l = '/' . $page;
    else $offs_l = '';

    if (!empty($ms_search)) {
        $divisions_name = 'Search result!';
        $api_result = modasphere_api_get_content($set_domain, $user_api, 'search_contact', true, 0, $talent_count_of_page, $offset, 0, 0, $ms_search);
        $search_info = get_query_var('ms_search');
        $chunk_link = 'ms-' . $search_info;
    } else {
        $divisions_name_base = get_query_var('division_name');
        $chunks_division_title = explode('-', $divisions_name_base);
        $id_division = array_pop($chunks_division_title);
        $divisions_name = implode(" ", $chunks_division_title);
        $api_result = modasphere_api_get_content($set_domain, $user_api, 'contact_division', true, $id_division, $talent_count_of_page, $offset);
        $chunk_link = $divisions_name_base;
    }
    $divisions = $api_result->objects;
    $total_count = $api_result->total_count;

    $gall_item_id = '';
    $gall_item_name = '';
    $gall_item_type = '';

    $talent_images_width = get_option('modasphere_talent_list_image_width', '');
    $talent_images_height = get_option('modasphere_talent_list_image_height', '');
    $upload_folder = wp_get_upload_dir();
    $upload_dir = $upload_folder['baseurl'];
    $new_image_save_dir = $upload_folder['basedir'];
    $thumbnail_overlay = get_option('modasphere_talent_list_thumbnail_overlay', '');
    if ($thumbnail_overlay != '') $thumbnail_overlay = json_decode($thumbnail_overlay);
    if (!empty($id_division)) {
        $thumbnail_overlay = $thumbnail_overlay->$id_division;
    }
    $modasphere_cover_photo = get_option('modasphere_talent_list_cover_photo','talent');
    ?>
    <div class="container modasphere division">
        <div class="row">
            <div class="col-md-12 text-center"><h4
                        class="head-division"><?= str_replace('%20', ' ', $divisions_name) ?></h4></div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card-deck">
                    <?php
                    $name_display = get_option('modasphere_talent_list_name_display', '');
                    foreach ($divisions as $dev) {
                        $talent = $dev->contact;
                        if($modasphere_cover_photo == 'talent'){
                            $cover = $talent->cover;
                        }else{
                            $cover = $dev->galleries[0]->cover;
                        }

                        $talent_link_name = preg_replace("/[^a-zA-Z0-9\s-]/", "",str_replace(' ', '', strtolower(get_format_name($talent, $name_display, true)))) . '-' . $talent->id;
                        $talent_obj = (array)json_decode(get_transient($talent_link_name));
                        $instagram_link = $talent_obj['instagram_link'];
                        $instagram_followers = $talent_obj['instagram_followers'];
                        $twitter_link = $talent_obj['twitter_link'];
                        $twitter_followers = $talent_obj['twitter_followers'];

                        ?>
                        <div class="<?= ($thumbnail_overlay != 'disabled' && empty($ms_search)) ? 'talent-caption-overlay' : '' ?> col-md-<?= get_option('modasphere_talent_list_col', '4') ?> m-0 p-0">
                            <div class="card">
                                <div class="caption-item">
                                    <a href="<?= get_permalink() . $chunk_link . "/" . $talent_link_name . $offs_l ?>"
                                       class="text-decoration-none">
                                        <?php
                                        if (isset($cover->id) && isset($cover->url)) {
                                            $img_path = $new_image_save_dir . '/modasphere-img/' . $cover->id . '/' . $cover->id . '.png';
                                            $img_path_upl = $upload_dir . '/modasphere-img/' . $cover->id . '/' . $cover->id . '.png';
                                            if (file_exists($img_path)) {
                                                ?>
                                                <img class="card-img-top" src="<?= $img_path_upl ?>" alt="<?= $talent->name ?>">
                                                <?php
                                            }
                                            else {
                                                echo '<div style="height: ' . $talent_images_height . 'px;overflow: hidden;"><img class="card-img-top" style="width: ' . $talent_images_width . 'px;" src="' . $cover->url . '" alt="' . get_format_name($talent, $name_display, true) . '"></div>';
                                            }
                                        }
                                        if ($thumbnail_overlay != 'disabled' && empty($ms_search)) {
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
                                        ?>
                                    </a>
                                </div>
                                <div class="card-body">
                                    <a href="<?= get_permalink() . $chunk_link . "/" . $talent_link_name . $offs_l ?>"
                                       class="text-decoration-none">
                                        <h6 class="card-title">
                                            <?php
                                            echo get_format_name($talent, $name_display)
                                            ?>
                                        </h6>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <?php
                if ($talent_count_of_page != '') {
                    $number_links = ceil($total_count / $talent_count_of_page);
                    if ($number_links > 1){
                    ?>
                    <nav aria-label="Page navigation" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php
                            if ($page != 1) {
                                $previous_page = $page - 1;
                                ?>
                                <li class="page-item">
                                    <?php
                                    if (!empty($ms_search)){
                                    ?>
                                    <a class="page-link"
                                       href="<?= get_permalink() . 'ms_search/' . $ms_search . '/' . $previous_page  ?>"
                                       aria-label="Previous">
                                        <?php
                                        }else{
                                        ?>
                                        <a class="page-link"
                                           href="<?= get_permalink() . $divisions_name_base . '/' . $previous_page  ?>"
                                           aria-label="Previous">
                                            <?php
                                            }
                                            ?>
                                            <span aria-hidden="true">&laquo;</span>
                                        </a>
                                </li>
                                <?php
                            }
                            $start_pagination = $page - 3;
                            $end_pagination = $page + 3;

                            for ($i = 1; $i <= $number_links; $i++) {
                                if ($i == 1 || $i == $number_links || ($i >= $start_pagination && $i <= $end_pagination)) {
                                    if ($i == $page) {
                                        ?>
                                        <li class="page-item active">
                                            <?php if (!empty($ms_search)){ ?>
                                            <a class="page-link"
                                               href="<?= get_permalink() . 'ms_search/' . $ms_search . '/' . $i ?>">
                                                <?php
                                                }else{ ?>
                                                <a class="page-link"
                                                   href="<?= get_permalink() . $divisions_name_base . '/' . $i ?>">
                                                    <?php
                                                    }
                                                    ?>
                                                    <?= $i ?><span class="sr-only">(current)</span>
                                                </a>
                                        </li>
                                        <?php
                                    } else {
                                        ?>
                                        <li class="page-item">
                                            <?php
                                            if (!empty($ms_search)){
                                            ?>
                                            <a class="page-link"
                                               href="<?= get_permalink() . 'ms_search/' . $ms_search . '/' . $i ?>">
                                                <?php
                                                }else{ ?>
                                                <a class="page-link"
                                                   href="<?= get_permalink() . $divisions_name_base . '/' . $i ?>">
                                                    <?php
                                                    }
                                                    ?>
                                                    <?= $i ?>
                                                </a>
                                        </li>
                                        <?php
                                    }
                                } elseif ($i == 2 || $i == ($number_links - 1)) {
                                    ?>
                                    <li class="page-item pl-2 pr-2">
                                        ...
                                    </li>
                                    <?php
                                }
                            }
                            $next_page = $page + 1;
                            if ($next_page < $number_links) {
                                ?>
                                <li class="page-item">
                                    <?php
                                    if (!empty($ms_search)){
                                    ?>
                                    <a class="page-link"
                                       href="<?= get_permalink() . 'ms_search/' . $ms_search . '/' . $next_page ?>"
                                       aria-label="Next">
                                        <?php
                                        } else{ ?>
                                        <a class="page-link"
                                           href="<?= get_permalink() . $divisions_name_base . '/' . $next_page ?>"
                                           aria-label="Next">
                                            <?php
                                            }
                                            ?>
                                            <span aria-hidden="true">&raquo;</span>
                                        </a>
                                </li>
                                <?php
                            }
                            ?>
                        </ul>
                    </nav>
                    <?php
                    }
                }
                ?>
            </div>
        </div>
    </div>
    <?php
}