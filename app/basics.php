<?php

function debug($a, $print=true) {
    $output = true;

    if (!is_null($a)) {
      if (empty($_SERVER['argv'][0])) $output = '<pre>'.htmlentities(print_r($a,true)).'</pre>';
      else $output = '<pre class="debug">'.print_r($a,TRUE).'</pre>';
    }
    else $output = '<pre class="debug">(null)</pre>';
    if ($print === true) echo $output;
    return $output;
}

