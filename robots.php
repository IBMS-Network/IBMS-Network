<?
include("config/config.php");


$file = SYS_DOMAIN_PATH."confsystem/robots.txt";
if (!file_exists($file))
{
	header("HTTP/1.0 404 Not Found");
	exit;
}
header("Content-type: text/plain");
echo file_get_contents($file);
?>