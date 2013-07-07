</div>
<div id="footer"><?php
print($app->he->c_tag('p', sprintf(__('This is the footer. You can create a new theme based on %sthemes/default/'), ABSPATH)));
if (APP_ENVIRONMENT != 'production') {
    print($app->he->c_tag('small', sprintf(__('Page generated in %s seconds.'), $app->he->c_tag('strong', number_format(microtime(true) - $app->timeRequestBegin, 6, '.', ','))), 'center'));
    print('&nbsp;<small class="center">' . __('Typ. Memory: ') . $app->he->c_tag('strong', round(memory_get_usage() / 1024)) . 'KiB');
    if (version_compare(PHP_VERSION, '5.2.0', '>')) {
        print(' / ' . __('Pike: ') . $app->he->c_tag('strong', round(memory_get_peak_usage() / 1024)) . 'KiB');
    }
    print('.</small> ');
    print('<small class="center">' . sprintf(__('Queries: <strong>%d</strong>'), $app->db->executedQueries) . '.</small> ');
    if ($app->loggedIn === true) {
        print('<br /><small class="centrar">' . sprintf(__('Last activity %s second(s) ago. Session renewed for another %s second(s).'), $app->he->c_tag('strong', $app->sessionExpireInformation), $app->he->c_tag('strong', SESION_EXPIRE)) . '</small>');
    }
}
?></div>
</div>