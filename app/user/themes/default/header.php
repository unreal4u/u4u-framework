<div id="wrapper"><div id="header">
<?php
printf(__('This is the header. You can create a new theme based on %sthemes/default/'),ROUT);
?></div><div id="page"><?php
include(USER_SPACE.'themes/default/submenu.php');
include(USER_SPACE.'themes/default/leftmenu.php');

echo $app->msgStack->display();
echo $app->bc->c_breadcrump();
