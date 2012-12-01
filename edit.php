<?php
require_once 'auth.php';

if ( ($id = is_logged_in()) === FALSE )
{
	header('Location: /login.php');
	exit;
}
?><!DOCTYPE html>
<html>

<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />

	<title>Initlab user info edit page</title>
	
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	<script>
	$(function(){
		$('#addphone').click(function(){
			$('#phones').append('<tr><td><input type="text" name="phone[]" /> <a class="delphone" href="javascript:void(0);">Изтрий</a></td></tr>');
		});
		
		$('.delphone').live('click', function(){
			$(this).parents('tr').remove();
		});
		
		$('#addmac').click(function(){
			$('#macs').append('<tr><td><input type="text" name="mac[]" /> <a class="delmac" href="javascript:void(0);">Изтрий</a></td></tr>');
		});
		
		$('.delmac').live('click', function(){
			$(this).parents('tr').remove();
		});
	});
	</script>
</head>

<body>
<?php
if ( isset($_POST['name']) )
{
	if ( empty($_POST['name']) )
		echo '<h2>Попълни си името</h2>';
	else
	{
		
		
		echo '<h2>Информацията е записана</h2>';
	}
}

$user = mysql_fetch_assoc(mysql_query('SELECT name, url, twitter FROM users WHERE id = '. $id));
$obj_res = mysql_query('SELECT type, value FROM objects WHERE userid = '. $id);

$phones = array();
$macs = array();
while ( $o = mysql_fetch_assoc($obj_res) )
{
	$obj[$o['type']] = $o;
}
?>
<h2>Редакция на потребителска информация</h2>

Име: <input type="text" name="name" value="<?php echo $user['name'] ?>" /><br />
URL: <input type="text" name="url" value="<?php echo $user['url'] ?>" /><br />
Twitter: <input type="text" name="twitter" value="<?php echo $user['twitter'] ?>" /><br />

Privacy: <select name="privacy">
	<option value="0" <?php $user['privacy'] == 0 ? 'selected="selected"' : '' ?>>Всички</option>
	<option value="1" <?php $user['privacy'] == 1 ? 'selected="selected"' : '' ?>>Само от членове</option>
	<option value="2" <?php $user['privacy'] == 2 ? 'selected="selected"' : '' ?>>Никой</option>
</select><br />

Телефони: <a id="addphone" href="javascript:void(0);">Добави поле</a>
<table id="phones" cellpadding="0" cellspacing="0">
<?php foreach ( $obj['phone'] as $o ): ?>
	<tr><td><input type="text" name="phone[]" value="<?php echo $o ?>" /></td></tr>
<?php endforeach; ?>
</table><br />

MAC адреси: <a id="addmac" href="javascript:void(0);">Добави поле</a>
<table id="macs" cellpadding="0" cellspacing="0">
<?php foreach ( $obj['mac'] as $o ): ?>
	<tr><td><input type="text" name="mac[]" value="<?php echo $o ?>" /></td></tr>
<?php endforeach; ?>
</table><br />

<input type="submit" value="Обнови" />

</body>
</html>
