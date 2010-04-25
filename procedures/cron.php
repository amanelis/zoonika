<?PHP
define('POST_URL', 'http://z2.zootzy.com/ztbot/ztbot.php');

$ch = curl_init(POST_URL);
curl_setopt($ch, CURL_POST, 1);
curl_setopt($ch, CURL_HEADER, 0);
curl_setopt($ch, CURL_RETURNTRANSFER, 1);
$return_data = curl_exec($ch);

ob_start();
ob_get_clean();
curl_close($ch);
?>