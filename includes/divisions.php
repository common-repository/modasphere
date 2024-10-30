<?php
include_once 'functions.php';
$user_api = get_option('modasphere_user_api', '');
$set_domain = get_option('modasphere_domain', '');

if ($user_api != '' && $set_domain != '') {
    $api_result = modasphere_api_get_content($set_domain, $user_api, 'division');

    if ($api_result != false) {
        $divisions = $api_result->objects;
        if ($divisions == NULL) {
            echo $api_result->detail;
        } else {
            ?>
            <div class="container modasphere">
                <div class="row d-flex justify-content-center">
                    <div class="col-<?= get_option('modasphere_division_col', 6) ?>">
                        <div class="card divisions"
                             style="background-color: <?= get_option('modasphere_division_background', '#fff') ?>;
                                     border-color: <?= get_option('modasphere_division_border_color', '') ?>;
                                     ">
                            <div class="card-header"
                                 style="color: <?= get_option('modasphere_division_text_color', '') ?>;
                                         background-color: <?= get_option('modasphere_division_header_color', '') ?>;
                                         text-transform: <?= get_option('modasphere_division_header_text_transform', '') ?>;
                                         font-size: <?= get_option('modasphere_division_header_font_size', '') ?>px;
                                         ">
                                Divisions
                            </div>
                            <ul class="list-group list-group-flush">
                                <?php
                                foreach ($divisions as $dev) {
                                    ?>
                                    <li class="list-group-item">
                                        <a class="stretched-link text-decoration-none"
                                           href=" <?= get_permalink() . "&division=" . $dev->id . "&division_name=" . $dev->name ?>">
                                            <?= $dev->name ?>
                                        </a>
                                    </li>
                                    <?php
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
    }
}