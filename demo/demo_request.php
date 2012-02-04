<?php
include '../request.php';

$R = new Lib_Request ();
$p=$g=$c=false;

/* remove cookie, ajax requested */
//if (isset ($_SERVER['HTTP_REFERER'])){
if (isset ($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    $c = $R->allRequest ('post');


    if ('del'==$c['act']) {
        if ($R->delCookie($c['name'])) 
            echo '1';
        else
            echo '-1';
    }
    else {
        if ($R->cookie ($c['name'], $c['value']))
            echo '2';
        else
            echo '-2';
    }

    exit(0);
}

/* check post */
if (true===$R->post() && $p=true) 
    $exists = 'exists';
else 
    $exists = 'not exists';
/* end check post */
echo '<b>$_POST ', $exists, '!</b><br/>';

/* check get */
if (true===$R->get() && $g=true)
    $exists = 'exists';
else 
    $exists = 'not exists';

echo '<b>$_GET ', $exists, '!</b><br/>';
/* end check get */


/* check cookie */
if (true===$R->cookie() && $c=true)
    $exists = 'exists';
else 
    $exists = 'not exists';

echo '<b>$_COOKIE ', $exists, '!</b><br/>';
/* end check cookie. */


$R->defaultRequest = 'get';
?>

<select id="method">
   <option value="post">Post</option>
   <option value="get">Get</option>
</select>

<div id="method-name">Method is post</div>
<form method="post" id="frm">
    <input name="r" type="text" value="" />
    <input type="submit" value="Send" />
</form>

<?php
if ($p)
    print_r ($R->allRequest('post'));

if ($g)
    print_r ($R->allRequest('get'));

if ($c) {
    echo '<div id="list-cookie">';
    $R->defaultRequest = 'cookie';
    foreach ($R as $name => $val) {
        echo '<div>';
        echo '<span style="font-weight:bold">', $name ,'</span>';
        echo '<span> --&gt; </span>';
        echo '<span style="font-weight:bold">', $val, '</span>';
        echo '<span style="cursor:pointer" title="Remove cookies"
    onclick=";C.del(this)">  X</span></div>';
    }
    echo '</div>';
}
?>

<div>
   <div>
      <span>Cookie Name: </span>
      <span><input name="cName" type="text" /></span>
   </div>
   <div>
      <span>Cookie Value: </span>
      <span><input name="cValue" type="text" /></span>
   </div>
   <div>
      <span><input onclick="C.set();" type="button" value="Set cookie!" /></span>
   </div>
   <div id="cNotif"></div>
</div>

<script type="text/javascript" >
    function _Id (id) {
        return document.getElementById(id);
    }
function _Name (name) {
    return document.getElementsByName(name)[0];
}

_Id('method').onchange = function(){
    document.forms[0].method = this.value;
    _Id('method-name').innerHTML = 'Method is '+this.value;
};

var C = {
    del: function(obj) {
        var n = obj.parentElement;
        C.notif ('Delete: '+ n.childNodes[0].innerHTML);
        C.send ({
            name:n.childNodes[0].innerHTML,
            act:'del'
        }, n);
    },
    set: function() {
        var n = _Name('cName').value;
        var v = _Name('cValue').value;
        C.notif ('Set: '+ n +' = '+ v);
        C.send ({
            name:n,
            value:v,
            act:'set'
        }, null);
    },

    send: function (data, obj) {
        var xml = new XMLHttpRequest();
        var notifText = _Id('cNotif').innerHTML;

        xml.open ("POST", 'demo_request.php', true);
        // this is post
        xml.setRequestHeader ('Content-Type', 'application/x-www-form-urlencoded');
        xml.setRequestHeader ('X_REQUESTED_TYPE', 'xmlhttprequest');
        xml.onreadystatechange = function() {
            if (xml.readyState==4) {
                if (xml.status>=200 && xml.status<300){
                    resp = parseInt (xml.responseText);
                    if(resp<0)
                        notifText += ' (Gagal)';
                    else
                        notifText += ' (Success)';

                    if ('del'==data.act)
                        obj.parentNode.removeChild(obj);
                    else {
                        var lc = _Id('list-cookie');
                        var newC = lc.childNodes[0].cloneNode(true);
                        newC.childNodes[0].innerHTML = data.name;
                        newC.childNodes[2].innerHTML = data.value;
                        lc.appendChild (newC);
                    }
                        
                    C.notif (notifText);
                }
                else
                    C.notif (notifText+'(Error:'+xml.responseText+')');

                xml = null;
            }
        };
        xml.send (C.serialize(data));
    },

    notif: function (txt) {
        _Id('cNotif').innerHTML = txt;
    },

    serialize: function(data) {
        var arr = [];
        for (var n in data)
            arr.push(n +'='+ encodeURIComponent(data[n]));
        return arr.join('&');
    }
}
</script>
