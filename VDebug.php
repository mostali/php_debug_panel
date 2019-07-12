<?php
class VUtils {

    static function wo($fn, $obj, $mkdir = false) {
        if ($mkdir)
            @mkdir(dirname($fn), 0755, true);

        $string_data = serialize($obj);
        file_put_contents($fn, $string_data);
    }

    static function ro($fn) {
        $string_data = file_get_contents($fn);
        return unserialize($string_data);
    }

    static function startsWith($haystack, $start) {
        return $start === "" || strrpos($haystack, $start, -strlen($haystack)) !== false;
    }

}
class VDebug {

    public static $path = 'vdebug';

    static function includePanel() {
        include 'debug_panel.php';
    }

    static function saveDefVars($arr, $first) {
        $keys = array_keys($arr);
        $pfx = $first ? '$' : '';
        VUtils::wo("mdb/" . $pfx . "vars.arr", $keys, true);
        foreach ($keys as $key)
            VUtils::wo("mdb/" . $pfx . "$key.arr", $arr[$key], true);
        if ($first) {
            ob_start();
            phpinfo();
            $size = ob_get_contents();
            $info = ob_end_clean();
            VUtils::wo("mdb/" . $pfx . "_PHPINFO.arr", $size, true);
        }
    }

    public static $pathStoreVars = 'mdb';

    static function clearVars() {
        $rmpath = 'mdb/*';
        array_map('unlink', glob($rmpath));
    }

    static function init($get_defined_vars, $include_view = true) {
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
        $path = $_SERVER["REQUEST_URI"];
        $path = substr($path, 1);
        $start = VUtils::startsWith($path, self::$path);
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
                $v = @VUtils::ro($fp);
                if ($v === false)
                    throw new Exception('read object');
                echo json_encode($v);
            } catch (\Exception $e) {
                echo $e->getMessage();
                http_response_code(412);
            }
        }
        exit();
    }

    static function handle_ALL() {
        try {
            $fp = 'mdb/vars.arr';
            $v = @VUtils::ro($fp);
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
