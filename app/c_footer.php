<?php

if ($app->loadHeaders !== FALSE) {
    include (USER_SPACE . 'themes/' . $app->options['active_theme'] . '/footer.php');
    echo $app->he->c_closebody() . $app->he->c_closehtml();
}