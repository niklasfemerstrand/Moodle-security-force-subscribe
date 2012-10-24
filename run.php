<?php
$warnmail = "user@domain";
$dbhost   = "localhost";
$dbusr    = "root";
$dbpass   = "root";
$dobedo   = "moodle_security";
if(!$db = mysqli_connect($dbhost, $dbusr, $dbpass))
	die("MySQL connection failure");
mysqli_select_db($db, $dobedo);
$a = file_get_contents("https://moodle.org/mod/forum/view.php?f=996&showall=1");
preg_match("/<tr class=\"discussion r0\"><td class=\"topic starter\"><a href=\"(.*)\">(.*)<\/a><\/td>/", $a, $matches);
if(count($matches) != 3) die();
$checksum = md5($matches[2]);
$q = $db->query("select * from announcements where checksum = '{$checksum}'");
if($q->num_rows == 0)
{
	mail($warnmail, "Moodle security warning", "{$matches[2]} - {$matches[1]}");
	$db->query("insert into announcements (checksum, url, title) values ('{$checksum}', '".$db->real_escape_string($matches[1])."', '".$db->real_escape_string($matches[2])."');");
}
