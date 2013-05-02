<?php
require_once 'auth.php';

if (($id = is_logged_in()) === false) {
	header('Location: /login.php');
	exit;
}

if (isset($_POST['logout'])) {
	logout();
	header('Location: /login.php');
	exit;
}

function array_map_recursive($callback, $arr) {
    $ret = array();
    
    foreach ($arr as $key => $val) {
        if (is_array($val)) {
            $ret[$key] = array_map_recursive($callback, $val);
        }
        else {
            $ret[$key] = $callback($val);
        }
    }
    
    return $ret;
}

if (isset($_POST['name'])) {
	if (empty($_POST['name'])) {
		$msg = '<h2>Попълни си името</h2>';
    }
	else {
        $data = array_map_recursive('mysql_real_escape_string', $_POST);
        mysql_query('UPDATE `users` SET `name` = "' . $data['name'] . '", `url` = "' . $data['url'] . '", `twitter` = "' . $data['twitter'] . '" WHERE `id` = ' . $id);
        mysql_query('DELETE FROM `objects` WHERE `userid` = ' . $id);
        
        foreach (array('phone', 'mac') as $type) {
            if (!array_key_exists($type, $data) || !is_array($data[$type])) {
                continue;
            }
            
            mysql_query('INSERT INTO `objects` (`userid`, `type`, `value`) VALUES (' . $id . ', "' . $type . '", "' . implode('"), (' . $id . ', "' . $type . '", "', $data[$type]) . '")');
        }

		$msg = '<h2>Информацията е записана</h2>';
	}
}

$user = mysql_fetch_assoc(mysql_query('SELECT `name`, `url`, `twitter` FROM `users` WHERE `id` = ' . $id));
$obj_res = mysql_query('SELECT `type`, `value` FROM `objects` WHERE `userid` = ' . $id);

$obj = array();

while ($o = mysql_fetch_assoc($obj_res)) {
    if (!array_key_exists($o['type'], $obj)) {
        $obj[$o['type']] = array();
    }
	
    $obj[$o['type']][] = $o['value'];
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Initlab user info edit page</title>
        <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
        <script>
/* <![CDATA[ */
jQuery(function($){
    $(document)
        .on('click', '#addphone,.delphone,#addmac,.delmac', function(event) {
            event
                .preventDefault();
        })
        .on('click', '#addphone', function() {
            $('#phones')
                .append('<tr><td><input type="text" name="phone[]" /> <a class="delphone" href="javascript:void(0);">Изтрий</a></td></tr>');
        })
        .on('click', '.delphone', function() {
        $(this)
            .parents('tr')
            .remove();
        })
        .on('click', '#addmac', function() {
            $('#macs')
                .append('<tr><td><input type="text" name="mac[]" /> <a class="delmac" href="javascript:void(0);">Изтрий</a></td></tr>');
        })
        .on('click', '.delmac', function() {
            $(this)
                .parents('tr')
                .remove();
        });
});
/* ]]> */
        </script>
    </head>
    <body>
        <h2>Редакция на потребителска информация</h2>
<?php
if (isset($msg)) {
    echo $msg;
}
?>
        <form action="" method="post">
            <div>
                <label>Име: <input type="text" name="name" value="<?php echo $user['name'];?>" size="30" /></label>
            </div>
            <div>
                <label>URL: <input type="text" name="url" value="<?php echo $user['url']; ?>" size="30" /></label>
            </div>
            <div>
                <label>Twitter: <input type="text" name="twitter" value="<?php echo $user['twitter']; ?>" size="30" /></label>
            </div>
            <div>
                <label>Privacy: <select name="privacy">
                    <option value="0" <?php (array_key_exists('privacy', $user) && $user['privacy'] == 0) ? 'selected="selected"' : ''; ?>>Всички</option>
                    <option value="1" <?php (array_key_exists('privacy', $user) && $user['privacy'] == 1) ? 'selected="selected"' : ''; ?>>Само от членове</option>
                    <option value="2" <?php (array_key_exists('privacy', $user) && $user['privacy'] == 2) ? 'selected="selected"' : ''; ?>>Никой</option>
                </select></label>
            </div>
            <div>
                Телефони: <a href="#" id="addphone">Добави поле</a>
            </div>
            <table id="phones" cellpadding="0" cellspacing="0">
<?php
if (array_key_exists('phone', $obj) && is_array($obj['phone'])) {
    foreach ($obj['phone'] as $o) {
?>
                <tr>
                    <td>
                        <input type="text" name="phone[]" value="<?php echo $o; ?>" />
                        <a href="#" class="delphone">Изтрий</a>
                    </td>
                </tr>
<?php
    }
}
?>
            </table>
            <div>
                MAC адреси: <a href="#" id="addmac">Добави поле</a>
            </div>
            <table id="macs" cellpadding="0" cellspacing="0">
<?php
if (array_key_exists('mac', $obj) && is_array($obj['mac'])) {
    foreach ($obj['mac'] as $o) {
?>
                <tr>
                    <td>
                        <input type="text" name="mac[]" value="<?php echo $o; ?>" />
                        <a href="#" class="delmac">Изтрий</a>
                    </td>
                </tr>
<?php
    }
}
?>
            </table>
            <div>
                <input type="submit" value="Обнови" />
            </div>
        </form>
        <form action="" method="post">
            <input type="hidden" name="logout" value="вън" />
            <input type="submit" value="Изход" />
        </form>
    </body>
</html>
