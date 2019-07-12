# vdebug
Simple debug panel. Diplayed the all defined variables from $_GLOBALS &amp;&amp; get_defined_vars()

##Install
<?php
/**
 * View values from $_GLOBALS & thread context (get_defined_vars())
 */
$log=array();

$active = true;
if ($active) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/frontend/classes/utils/VDebug.php';
    VDebug::init(get_defined_vars(), $render_view = true);
}

// page
$log[]='Hello world';

if ($active)
    VDebug::finish(get_defined_vars());




![Alt text](https://github.com/mostali/vdebug/blob/master/screen.jpg?raw=true "PHP Debug Panel")
