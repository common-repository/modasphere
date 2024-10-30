<?php
include_once 'functions.php';
$user_api = get_option('modasphere_user_api', '');
$set_domain = get_option('modasphere_domain', '');

if ($user_api != '' && $set_domain != '') {
    $search_fields = json_decode(get_option('modasphere_search_field_options_view'));
    $query = (array)sanitize_post($_POST);
    ?>
    <div class="container modasphere search-conditions">
        <form method="post">
            <div class="form-row">
                <?php
                foreach ($search_fields as $sf) {
                    $field_name = '';
                    $field_name_0 = '';
                    $field_name_1 = '';
                    $field_value = '';
                    $field_value_0 = '';
                    $field_value_1 = '';
                    $field_multiple = array();

                    if ($sf->field_info->contact_field->type == 'DateField' || $sf->field_info->contact_field->type == 'AgeField') {
                        $field_name_0 = 'as_' . $sf->field_info->contact_field->name . '_' . $sf->field_info->id . '_0';
                        $field_name_1 = 'as_' . $sf->field_info->contact_field->name . '_' . $sf->field_info->id . '_1';
                    } else {
                        $field_name = 'as_' . $sf->field_info->contact_field->name . '_' . $sf->field_info->id;
                    }
                    if (!empty($field_name) && isset($query[$field_name])) {
                        if (is_string($query[$field_name])) {
                            $field_value = sanitize_text_field($query[$field_name]);
                        } elseif (is_array($query[$field_name])) {
                            $field_multiple = $query[$field_name];
                        }
                    }
                    if (empty($field_name) && (!empty($field_name_0) || !empty($field_name_1))) {
                        $field_value_0 = sanitize_text_field($query[$field_name_0]);
                        $field_value_1 = sanitize_text_field($query[$field_name_1]);
                    }
                    ?>
                    <div class="form-group col-md-2">
                        <label for="field-<?= $sf->field_info->id ?>"><?= $sf->field_info->contact_field->label ?></label><br>
                        <?php
                        switch ($sf->field_info->contact_field->type) {
                            case 'ChoiceField':
                                ?>
                                <select multiple class="form-control selectpicker"
                                        id="field-<?= $sf->field_info->id ?>"
                                        name="<?= 'as_' . $sf->field_info->contact_field->name . '_' . $sf->field_info->id ?>[]">
                                    <option value=""></option>
                                    <?php
                                    foreach ($sf->field_info->contact_field->config as $key => $option) {
                                        ?>
                                        <option <?= (in_array($key, $field_multiple)) ? 'selected' : '' ?>
                                                value="<?= $key ?>"><?= $option ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                                <?php
                                break;
                            case 'MultiChoiceField':
                                ?>
                                <select multiple class="form-control selectpicker"
                                        id="field-<?= $sf->field_info->id ?>"
                                        name="<?= 'as_' . $sf->field_info->contact_field->name . '_' . $sf->field_info->id ?>[]">
                                    <option value=""></option>
                                    <?php
                                    foreach ($sf->field_info->contact_field->config as $key => $option) {
                                        ?>
                                        <option <?= (in_array($key, $field_multiple)) ? 'selected' : '' ?>
                                                value="<?= $key ?>"><?= $option ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                                <?php
                                break;
                            case 'NullBooleanField':
                                ?>
                                <select multiple class="form-control selectpicker"
                                        id="field-<?= $sf->field_info->id ?>"
                                        name="<?= 'as_' . $sf->field_info->contact_field->name . '_' . $sf->field_info->id ?>[]">
                                    <option value></option>
                                    <option <?= (in_array('True', $field_multiple)) ? 'selected' : '' ?> value="True">
                                        Yes
                                    </option>
                                    <option <?= (in_array('False', $field_multiple)) ? 'selected' : '' ?> value="False">
                                        No
                                    </option>
                                </select>
                                <?php
                                break;
                            case 'CharField':
                                echo '<input type="text" class="form-control" id="field-' . $sf->field_info->id . '" name="as_' . $sf->field_info->contact_field->name . '_' . $sf->field_info->id . '" value="' . $field_value . '">';
                                break;
                            case 'EmailField':
                                echo '<input type="email" class="form-control" id="field-' . $sf->field_info->id . '" name="as_' . $sf->field_info->contact_field->name . '_' . $sf->field_info->id . '" value="' . $field_value . '">';
                                break;
                            case 'PhoneField':
                                echo '<input type="tel" class="form-control" id="field-' . $sf->field_info->id . '" name="as_' . $sf->field_info->contact_field->name . '_' . $sf->field_info->id . '" value="' . $field_value . '">';
                                break;
                            case 'StorageField':
                                echo '<input type="file" class="form-control-file" id="field-' . $sf->field_info->id . '" name="as_' . $sf->field_info->contact_field->name . '_' . $sf->field_info->id . '">';
                                break;
                            case 'ImageStorageField':
                                echo '<input type="file" class="form-control-file" id="field-' . $sf->field_info->id . '" name="as_' . $sf->field_info->contact_field->name . '_' . $sf->field_info->id . '">';
                                break;
                            case 'DateField':
                                echo '<input type="text" class="form-control" id="field-' . $sf->field_info->id . '_0" name="as_' . $sf->field_info->contact_field->name . '_' . $sf->field_info->id . '_0" style="width:58px;margin-right:0;display:inline-block" value="' . $field_value_0 . '"> - ';
                                echo '<input type="text" class="form-control" id="field-' . $sf->field_info->id . '_1" name="as_' . $sf->field_info->contact_field->name . '_' . $sf->field_info->id . '_1" style="width:58px;margin-right:0;display:inline-block" value="' . $field_value_1 . '">';
                                break;
                            case 'AgeField':
                                echo '<input type="text" class="form-control" id="field-' . $sf->field_info->id . '_0" name="as_' . $sf->field_info->contact_field->name . '_' . $sf->field_info->id . '_0" style="width:58px;margin-right:0;display:inline-block" value="' . $field_value_0 . '"> - ';
                                echo '<input type="text" class="form-control" id="field-' . $sf->field_info->id . '_1" name="as_' . $sf->field_info->contact_field->name . '_' . $sf->field_info->id . '_1" style="width:58px;margin-right:0;display:inline-block" value="' . $field_value_1 . '">';
                                break;
                            case 'IntegerField':
                                echo '<input type="number" class="form-control" id="field-' . $sf->field_info->id . '" name="as_' . $sf->field_info->contact_field->name . '_' . $sf->field_info->id . '" value="' . $field_value . '">';
                                break;
                            case 'TextField':
                                echo '<textarea class="form-control" id="field-' . $sf->field_info->id . '" rows="3" name="as_' . $sf->field_info->contact_field->name . '_' . $sf->field_info->id . '">' . $field_value . '</textarea>';
                                break;
                            case 'URLField':
                                echo '<input type="text" class="form-control" id="field-' . $sf->field_info->id . '" name="as_' . $sf->field_info->contact_field->name . '_' . $sf->field_info->id . '" value="' . $field_value . '">';
                                break;
                            default:
                                break;
                        }
                        ?>
                    </div>
                    <?php
                }
                ?>
            </div>
            <div class="form-row">
                <div class="form-group col-md-12 text-center">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </div>
        </form>
    </div>
    <?php
    if (!empty($_POST)) {
        $s_f = array();
        foreach ($_POST as $key_p => $fld) {
            if (!empty($fld)) {
                if (strpos($fld, '/')) {
                    $s_f[$key_p] = array($fld);
                } else {
                    $s_f[$key_p] = $fld;
                }
            }
        }
        $s_f = json_encode($s_f);
        $search_api_result = modasphere_api_get_content($set_domain, $user_api, 'search_fields', false, 0, 1000, 0, 0, 0, $s_f, 'post');
        $search_talent = $search_api_result->objects;

        $contact_api_result = modasphere_api_get_content($set_domain, $user_api, 'all_contacts', false, 0, 1000);
        $all_contacts = $contact_api_result->objects;
        $modasphere_visibility_menu = (array)json_decode(get_option('modasphere_visibility_menu_items'));
        $_SESSION['modasphere_search_query'] = $s_f;

        $talent_images_width = get_option('modasphere_talent_list_image_width', '');
        $talent_images_height = get_option('modasphere_talent_list_image_height', '');

        $upload_folder = wp_get_upload_dir();
        $upload_dir = $upload_folder['baseurl'];
        $new_image_save_dir = $upload_folder['basedir'];

        $thumbnail_overlay = get_option('modasphere_talent_list_thumbnail_overlay', '');
        if ($thumbnail_overlay != '') $thumbnail_overlay = json_decode($thumbnail_overlay);

        ?>
        <div class="container modasphere search-result">
            <div class="row">
                <div class="col-12 text-center"><h4 class="head-division">Search result</h4></div>
            </div>
            <?php
            if (is_array($search_talent) && !empty($search_talent)) {
                ?>
                <div class="row">
                    <div class="col-12">
                        <div class="card-deck">
                            <?php
                            foreach ($all_contacts as $item) {
                                $division = $item->division;
                                $talent = $item->contact;

                                $id_division = $division->id;
                                $thumbnail_overlay = $thumbnail_overlay->$id_division;
                                $key_arr = 'm' . $id_division;
                                $division_selected = (array_key_exists($key_arr, $modasphere_visibility_menu)) ? $modasphere_visibility_menu['m' . $id_division] : '';

                                if ($division_selected == 'checked') {

                                    foreach ($search_talent as $kst => $st) {
                                        if ($talent->id == $st->id) {
                                            $cover = $talent->cover;

                                            $name_display = get_option('modasphere_talent_list_name_display', '');
                                            $talent_link_name = str_replace(' ', '', strtolower(get_format_name($talent, $name_display, true))) . '-' . $talent->id;
                                            $talent_obj = (array)json_decode(get_transient($talent_link_name));
                                            $instagram_link = $talent_obj['instagram_link'];
                                            $instagram_followers = $talent_obj['instagram_followers'];
                                            $twitter_link = $talent_obj['twitter_link'];
                                            $twitter_followers = $talent_obj['twitter_followers'];
                                            ?>
                                            <div class="<?= ($thumbnail_overlay != 'disabled') ? 'talent-caption-overlay' : '' ?> col-<?= get_option('modasphere_talent_list_col', '4') ?> m-0 p-0">
                                                <div class="card">
                                                    <div class="caption-item">
                                                        <a href="<?= get_permalink() . $division->name . '-' . $division->id . '/' . $talent_link_name ?>"
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
                                                            ?>
                                                        </a>
                                                    </div>
                                                    <div class="card-body text-center">
                                                        <a href="<?= get_permalink() . $division->name . '-' . $division->id . '/' . $talent_link_name ?>"
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
                                            unset($search_talent[$kst]);
                                        }
                                    }
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
        <?php
    }
}