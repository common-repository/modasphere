<?php
$route = get_query_var('route');
$ms_search = get_query_var('ms_search');
if (!empty($route)) {
    if ($route == 'division' || !empty($ms_search)) {
        include_once("talents.php");
    } elseif ($route == 'talent') {
        include_once("talent_gallery.php");
    } elseif ($route == 'favorites'){
        include_once("favorites.php");
    } elseif ($route == 'search'){
        include_once("search.php");
    }else {
        include_once("divisions.php");
    }
} else {
    include_once("divisions.php");
}

