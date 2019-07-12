<?php

class VDebug {

    public static $path = 'vdebug';

    static function includePanel() {
        include 'debug_panel.php';
    }

    static function saveDefVars($arr, $first) {
        $keys = array_keys($arr);
        $pfx = $first ? '$' : '';
        UFS::wo("mdb/" . $pfx . "vars.arr", $keys, true);
        foreach ($keys as $key)
            UFS::wo("mdb/" . $pfx . "$key.arr", $arr[$key], true);
        if ($first) {
            ob_start();
            phpinfo();
            $size = ob_get_contents();
            $info = ob_end_clean();
            UFS::wo("mdb/" . $pfx . "_PHPINFO.arr", $size, true);
        }
    }

    public static $pathStoreVars = 'mdb';

    static function clearVars() {
        $rmpath = 'mdb/*';
        array_map('unlink', glob($rmpath));
    }

    static function init($get_defined_vars, $include_view = true) {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/frontend/classes/utils/U.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/frontend/classes/utils/ULog.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/frontend/classes/utils/US.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/frontend/classes/utils/UFS.php';
        VDebug::handle(); //api
        VDebug::clearVars();
        VDebug::saveDefVars($get_defined_vars, true);
        if ($include_view)
            VDebug::includePanel();
    }

    static function finish($get_defined_vars) {
        VDebug::saveDefVars($get_defined_vars, false);
    }

    static function handle() {
//        if (!self::$isEnbale)
//            return;
        $path = $_SERVER["REQUEST_URI"];
        $path = substr($path, 1);
        $start = US::startsWith($path, self::$path);
        if (!$start)
            return;

        $all = isset($_GET['all']) ? $_GET['all'] : null;
        if ($all)
            self::handle_ALL();

        $name = isset($_GET['name']) ? $_GET['name'] : null;
        if ($name == null)
            echo "set name variable";
        else {
            try {
                $fp = "mdb/$name.arr";
//                include($fp);
                $v = @UFS::ro($fp);
                if ($v === false)
                    throw new Exception('read object');
                echo json_encode($v);
            } catch (\Exception $e) {
                echo $e->getMessage();
                http_response_code(412);
//                echo "var not found[$name]";
            }
        }
        exit();
    }

    static function handle_ALL() {
        try {
            $fp = 'mdb/vars.arr';
//                include($fp);
            $v = @UFS::ro($fp);
            if ($v === false)
                throw new Exception('read object');
            echo json_encode($v);
        } catch (\Exception $e) {
            echo $e->getMessage();
            http_response_code(412);
        }

        exit();
    }

}
