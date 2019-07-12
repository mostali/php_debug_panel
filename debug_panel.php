<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.jQuery) {
            var tag = document.createElement("script");
            tag.src = "https://cdnjs.cloudflare.com/ajax/libs/jquery.colorbox/1.6.4/jquery.colorbox.js";
            document.getElementsByTagName("head")[0].appendChild(tag);
        }
    });

    if (!window.jQuery)
        LoadScripts();

    function LoadScripts(async)
    {
        if (async === undefined) {
            async = false;
        }
        var scripts = [];
        var _scripts = ['https://code.jquery.com/jquery-3.3.1.min.js', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.colorbox/1.6.4/jquery.colorbox.js'];

        if (async) {
            LoadScriptsAsync(_scripts, scripts)
        } else {
            LoadScriptsSync(_scripts, scripts)
        }
    }

    function LoadScriptsSync(_scripts, scripts) {

        var x = 0;
        var loopArray = function (_scripts, scripts) {
            // call itself
            loadScript(_scripts[x], scripts[x], function () {
                // set x to next item
                x++;
                // any more items in array?
                if (x < _scripts.length) {
                    loopArray(_scripts, scripts);
                }
            });
        }
        loopArray(_scripts, scripts);
    }

    function LoadScriptsAsync(_scripts, scripts) {
        for (var i = 0; i < _scripts.length; i++) {
            loadScript(_scripts[i], scripts[i], function () {});
        }
    }

    function loadScript(src, script, callback) {

        script = document.createElement('script');
        script.onerror = function () {
            // handling error when loading script
            alert('Error to handle')
        }
        script.onload = function () {
            console.log(src + ' loaded ')
            callback();
        }
        script.src = src;
        document.getElementsByTagName('head')[0].appendChild(script);
    }
</script>


<div id="mdebug-panel" style="position: fixed;z-index:9999">
    <div>
        <button onclick="$('#mdebug-panel').toggle();">X</button>
        <button onclick="$.colorbox.close()">X</button>
        <button onclick="show($(this).html(), $_SERVER)">$_SERVER</button>
        <button onclick="show($(this).html(), $_GET)">$_GET</button>
        <button onclick="show($(this).html(), $_POST)">$_POST</button>
        <button onclick="show($(this).html(), $_COOKIE)">$_COOKIE</button>
        <button onclick="show($(this).html(), $_FILES)">$_FILES</button>
        <button onclick="show($(this).html(), $_PHPINFO)">$_PHPINFO</button>

        <button onclick="$('#mdebug-panel').css('position', 'relative')">not-fixed</button>
        <button onclick="callAllVD();$('#alldefinedvars').toggle();">all</button>
    </div>
    <div id="alldefinedvars" style="display:none">
    </div>
</div>
<div style="display:none;"><pre id="dbg_var" style="background-color:lightblue" ></pre></div>
<script>

    var $_SERVER =<?php echo json_encode(VUtils::ro('mdb/$_SERVER.arr')); ?>;
    var $_GET =<?php echo json_encode(VUtils::ro('mdb/$_GET.arr')); ?>;
    var $_POST =<?php echo json_encode(VUtils::ro('mdb/$_POST.arr')); ?>;
    var $_COOKIE =<?php echo json_encode(VUtils::ro('mdb/$_COOKIE.arr')); ?>;
    var $_FILES =<?php echo json_encode(VUtils::ro('mdb/$_FILES.arr')); ?>;
    var $_PHPINFO =<?php echo json_encode(VUtils::ro('mdb/$_PHPINFO.arr')); ?>;

    function show(name, data) {
        var emptyData = data.length === 0;
        var html = name + ' is empty';
        if (!emptyData)
            html = JSON.stringify(data, undefined, 2);
        var id = 'dbg_var';
        document.getElementById(id).innerHTML = html;
        $.colorbox({inline: true, height: name == '$_PHPINFO' ? "90%" : "60%", width: "93%", escKey: true, overlayClose: true, href: '#' + id});
    }
    function callNameVD(name) {
        var call2 = '/vdebug?name=' + name;
        fetch(call2)
                .then(function (response) {
                    return response.text();
                })
                .then(function (data) {
                    appendJsVar(name, data);
                })
                .catch(alert);
    }

    var isLoadedVD = false;
    function callAllVD() {

        if (isLoadedVD)
            return;
        else
            isLoadedVD = true;

        fetch('/vdebug?all=1')
                .then(function (response) {
                    return response.text();
                })
                .then(function (data) {
                    var arr = eval(data);
                    arr.sort().map((v) => {
                        callNameVD(v);
                        var but = "<button onclick='show($(this).html(), " + v + ")'>" + v + "</button>";
                        setMarkup2element("#alldefinedvars", but, true);
                    });

                })
                .catch(alert);
    }


    function setMarkup2element(append2selector, markup, appendOrReplace) {
        if (appendOrReplace)
            document.querySelector(append2selector).innerHTML += markup;
        else
            document.querySelector(append2selector).innerHTML = markup;
    }
    function appendJsVar(varName, varData) {
        var scr = null;
        if (varData)
            scr = 'var ' + varName + ' = ' + varData + ';';
        else
            scr = 'var ' + varName + ' = \'\';';
        appendJs(scr);
    }
    function appendJs(jsCode) {
        var tag = document.createElement("script");
        var node = document.createTextNode(jsCode);
        tag.appendChild(node);
        document.getElementsByTagName("head")[0].appendChild(tag);
    }

</script>
