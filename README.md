# Simple html view for php debug environment
Simple debug panel. Display the all defined variables from php environment - $GLOBALS &amp;&amp; get_defined_vars()



# Usage
```
<?php
/**
 * View values from $_GLOBALS & thread context (get_defined_vars())
 */
$log=array();

$active = true;
if ($active) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/VDebug.php';
    VDebug::init(get_defined_vars(), $render_view = true);
    //or VDebug::includePanel()
}

// page
$log[]='Hello world';

if ($active)
    VDebug::finish(get_defined_vars());
```


![Alt text](https://github.com/mostali/vdebug/blob/master/screen.jpg?raw=true "PHP Debug Panel")
