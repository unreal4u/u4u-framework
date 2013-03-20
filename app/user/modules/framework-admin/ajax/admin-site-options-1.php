<?php

$aValor = $app->db->query('SELECT v FROM sist_options WHERE id_option = ? AND name = ?', 'sop', $_POST['option']);
if ($app->db->num_rows > 0) {
    $a = $aValor[0]['v'];
    echo $a;
} else {
    echo '-- NO VALUE --';
}
