<?php
require_once('functions.php');
require_once('authentication.php');
require_once('maintenance.php');
include('header.php');

$u = Authenticate();
global $db_host;
global $db_user;
global $db_pass;
global $db_database;
@mysql_connect($db_host, $db_user, $db_pass) or slowDie("Error connecting to sql " . $db_user );
@mysql_select_db($db_database) or slowDie("Error connecting to db");

//Added way to vote without using ajax (robogeek)
if ($_GET["mode"] == "vote") {

	//confirm profil_id
	if ($u == null || $u !== $_GET['profile_id']) die("Are you trying to hack the vote?");

	//confirm plugin_id and plugin_version
	$getPluginId = $_GET['plugin_id'];
	$getPluginVersion = $_GET['plugin_version'];
	$res = mysql_query("SELECT * FROM plugins WHERE plugin_ID='$getPluginId' AND plugin_Version='$getPluginVersion'");
	$numrows = mysql_numrows($res);
	if ($numrows <> 1) die("Are you scamming an old or non-existant plugin?");

	//confirm vote
	$v = $_GET['vote'];
	if ($v == null || $v == "") die("You forgot to vote?");
	if ($v !== "true" && $v !== "false") die("Invalid vote");
	VoteWorking($_GET['profile_id'], $getPluginId, $getPluginVersion, $v, null);
}
$res = mysql_query("SELECT plugin_ID, plugin_Name, plugin_Author, max(plugin_Date) as maxdate FROM plugins WHERE plugin_ReviewFlag = 'FALSE' GROUP BY plugin_ID ORDER BY maxdate desc LIMIT 10");
while (($enreg=@mysql_fetch_array($res)))
////////////////////////////////////////////////////////////////////////////////
echo "<table border=0 cellpadding=0 cellspacing=0 width=600>";
//Your last 10 downloads
echo "<br><p><h1 id='title'>Your Last 10 Downloads:</b><br><br>";
$where = "WHERE download_profil_id='$u'";
if ($u == "binary64") $where = "";
$res = mysql_query("SELECT * FROM userdownloads $where ORDER BY download_Date DESC LIMIT 0,10");
while (($enreg=@mysql_fetch_array($res)))
{
$plugin_id = $enreg["plugin_ID"];
$plugin_version = $enreg["plugin_Version"];
$plugin_date = $enreg["plugin_Date"];
$profil_id = $enreg["profil_id"];
//is vote still latest version?
$res2 = mysql_query("SELECT * FROM plugins WHERE plugin_ID='$plugin_id' and plugin_Version='$plugin_version'");
$enreg2 = @mysql_fetch_array($res2);
if ($enreg2["plugin_Version"] == $plugin_version)
{
echo "<tr>";
echo "<td id=\"pluginicon\" align='center' valign='center' width=130>\n";
		echo "<a href='detail.php?plugin_id=$plugin_id$breadcrumb'>\n";
		$plugin_directory = "$plugin_home_directory/$plugin_id/$plugin_version/$plugin_state";
		echo "<img src='" . GetPluginIcon($plugin_id,$plugin_version) . "' width='80' height='80'><br>\n"; //width=100 height=100
		echo "</a></td>";
echo "<td id=\"plugininfos\">";
echo "<a href=\"detail.php?plugin_id=" . $enreg2["plugin_ID"] . "\"><b>" . $enreg2["plugin_Name"] . "</b></a> by <a href=\"thelist.php?author=" . urlencode($enreg2["plugin_Author"]) . "\"><i>" . $enreg2["plugin_Author"] . "</i></a> ";
VoteWorking($u, $plugin_id, $plugin_version, "", "userstats.php?");
echo "</td>";
echo "<tr height='10'/>";
}
}
echo "</table>"

?>
