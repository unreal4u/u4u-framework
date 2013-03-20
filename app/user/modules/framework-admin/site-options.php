<?php
/**
 * This module allows to edit the site options, such as active theme and sitename
 *
 * @package General
 * @version $Rev$
 * @copyright $Date$
 * @author $Author$
 */

$app->view->pageTitle = __('Edit Site Options');
$app->bc->add($app->myHome, __('Site options'));

echo $app->misc->c_title($app->view->pageTitle, __('Choose the site option and edit the value'));
if (!empty($_POST['option_name'])) {
    $_SESSION['custom']['option_name'] = $_POST['option_name'];
    $app->db->query('UPDATE sist_options SET v = ? WHERE name = ? AND id_option = ?', $_POST['option_value'], $_POST['option_name'], 'sop');
    $app->msgStack->add(3, __('Option successfully updated'));
    $app->cache->purgeIdentifierCache('siteOptions');
    $app->misc->redir($app->myHome);
}
$aOptions = $app->db->query('SELECT name AS option_name FROM sist_options WHERE id_option = ?', 'sop');
if ($app->db->num_rows > 0) {
    if (empty($_SESSION['custom']['option_name'])) {
        $_SESSION['custom']['option_name'] = $aOptions[0]['option_name'];
    }
    $r['print'] = '<form action="' . $_SERVER['REQUEST_URI'] . '" method="post" id="ppal" class="center"><select name="option_name" id="option_name">';
    foreach ($aOptions as $a) {
        $selected = '';
        if ($_SESSION['custom']['option_name'] == $a['option_name'])
            $selected = ' selected="selected"';
        $r['print'] .= '<option value="' . $a['option_name'] . '"' . $selected . '>' . $a['option_name'] . '</option>';
    }
    $r['print'] .= '</select><input type="text" style="width:300px" id="option_value" name="option_value" value="" /><input type="submit" value="Modify value" /></form>';
    $app->javascriptCode[] = 'function update_value(){$.ajax({url:"' . HOME . 'framework-admin/ajax/admin-site-options-1/",type:"POST",data:"option="+$("#option_name").val(),success:function(d){$("#option_value").val(d).select().focus();}});};';
    $r['print'] .= $app->he->c_javascript('$("#option_name").change(function(){update_value();});');
    $r['print'] .= $app->he->c_javascript('$(document).ready(function(){update_value();});');
}
echo $r['print'];