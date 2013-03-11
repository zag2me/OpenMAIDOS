<?php
require_once('functions.php');
require_once('db.php');
require_once('authentication.php');
require_once('maintenance.php');
include('header.php');
global $sys_url;
global $plugin_home_directory;
$u = Authenticate();
@mysql_connect($db_host, $db_user, $db_pass) or die("Error");
@mysql_select_db($db_database) or die("Error");

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

//Prints a line of plugin information
function printPluginInfo($preinfo, $infoName, $infoValue, $beginTag, $endTag, $htmlbr)
{
	$infoValue = htmlspecialchars($infoValue);
	$infoValue = nl2br($infoValue);
	$infoValue = replaceLinks($infoValue);
	echo "$preinfo <b> $infoName";
	if ($infoValue) echo ":</b> $beginTag $infoValue $endTag $htmlbr \n";
	else echo "</b><br /> \n";
}
function printPluginVersionReq($infoName, $minValue, $maxValue)
{
	echo "<b>$infoName";
	if (!$minValue) $minValue = "unknown";
	if (!$maxValue) $maxValue = "unknown";
	if ($minValue != "unknown" && $maxValue != "unknown") echo ":</b> Versions $minValue to $maxValue<br />";
	if ($minValue == "unknown" && $maxValue == "unknown") echo ":</b> Not Specified<br />";
	else echo "</b>: Unknown<br />";
}

//used for breadcrumb link
$breadcrumb = ""; $breadcrumblink = "";
if (isset($_GET["ptype"])) $breadcrumb = "&ptype=" . $_GET["ptype"];
if (isset($_GET["start"])) $breadcrumb .= "&start=" . $_GET["start"];
if (isset($_GET["program"])) $breadcrumb .= "&program=" . $_GET["program"];
if (isset($_GET["author"])) $breadcrumb .= "&author=" . urlencode($_GET["author"]);
if (isset($_GET["search"])) $breadcrumb .= "&search=" . urlencode($_GET["search"]);
if (isset($_GET["profile_id"])) $breadcrumb .= "&profile_id=" . urlencode($_GET["profile_id"]);
if (isset($_GET["filter"])) $breadcrumb .= "&filter=" . $_GET["filter"];
if (isset($_GET["ptype"]) || isset($_GET["author"]) || isset($_GET["profile_id"]) || isset($_GET["search"])) $breadcrumblink = "<a href='thelist.php?$breadcrumb'>Back to Plugin List</a>";

//show search bar.  links, breadcrumblink, enable/disable search box.
#SearchBar("<a href=\"" . $sys_url . "extra.php\">Stats & Tools</a>&nbsp;&nbsp;|&nbsp;&nbsp<a href=\"$sys_url\">OpenMAID</a>",$breadcrumblink,TRUE);
echo "<table border=0 width=800 cellspacing=0 cellpading=0>";
echo "<p><a href='detail.php?plugin_id=$plugin_id'>";
if (isset($_GET["plugin_id"])) $plugin_id = formatPluginID($_GET["plugin_id"]);
if (isset($_GET["plugin_version"])) $plugin_version = $_GET["plugin_version"];
if (isset($_GET["plugin_state"])) $plugin_state = $_GET["plugin_state"];
//gets the plugin object
if (!$plugin_version && !$plugin_state && $plugin_id) $plugin = GetPluginObject($plugin_id);
elseif ($plugin_version && $plugin_state && $plugin_id) $plugin = GetPreviousPluginObject($plugin_id, $plugin_version, $plugin_state, TRUE);
else die("Nice hack job...NOT!");
$plugin_directory = "$plugin_home_directory/$plugin_id/$plugin->plugin_Version/$plugin->plugin_State";
//Added another to test for lowercase plugin state directories (robogeek)
$plugin_directory2 = "$plugin_home_directory/$plugin_id/$plugin->plugin_Version/" . strtolower($plugin->plugin_State);
$filename = "plugins/$plugin_id/$plugin->plugin_Version/$plugin->plugin_State/1.png";
$icon1 = "plugins/$plugin_id/$plugin->plugin_Version/$plugin->plugin_State/1.png";
/////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////


#===Show Screenshots===
$res = GetPluginScreenshots($plugin_id, $plugin->plugin_Version);
$nb = mysql_numrows($res);
if ($nb > 0)
{ 
 	echo "<p><b>Icon</b><br />";
	$i = 0;
	while ($i < $nb)
	{
		$image_thumb_path = mysql_result($res, $i, "image_path");
	  	if ($i == 0)
		{
	  		echo "<img src='$image_thumb_path' border=0 width='256'><br />";
		}
		
		else 
		{
			echo "<p><b><center>Screenshots</b><br />";
		
			echo "<a href='images.php?plugin_id=$plugin_id&plugin_version=$plugin->plugin_Version&plugin_state=$plugin->plugin_State&start=$i'>";

	  		echo "<img src='$image_thumb_path' border=0 width='320'><br />";

	 		echo "</a><br />";		
		}
		$i++;
	}
}
echo "</table>";
include('footer.php');
?>
