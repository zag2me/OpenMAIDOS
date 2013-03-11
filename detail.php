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
$filename2 = "plugins/$plugin_id/$plugin->plugin_Version/$plugin->plugin_State/2.png";
$icon1 = "plugins/$plugin_id/$plugin->plugin_Version/$plugin->plugin_State/1.png";
$icon2 = "images/missing.gif";
/////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////
echo "<table border=0 width=800 cellspacing=0 cellpading=0>  
<tr> <td id=\"pluginicon2\" width=300 align='center'>";
// plugin Icon
if (file_exists($filename)) {
    echo "<img src='$icon1' width='230' height='230'>";
} else {
    echo "<img src='$icon2' width='230' height='230'>";
}
$currentUser = Authenticate();
if ($plugin->plugin_Author == $currentUser | IsAdmin($currentUser) )
{
	echo "<p><center><a href=edit.php?plugin_id=$plugin_id&plugin_version=$plugin->plugin_Version&plugin_state=$plugin->plugin_State><b>Edit</b></a></center></p>";
}
if ($plugin->plugin_VersionValue < $newest_versionvalue) {
	echo "<p><a href=\"dl.php?plugin_id=$plugin_id&plugin_version=$plugin->plugin_Version&plugin_state=$plugin->plugin_State\"><b>Download Latest $plugin->plugin_State</b> (" . formatFileSize($plugin->plugin_DownloadSize) . ")</a><br />";
	echo "<p><a href=\"dl.php?plugin_id=$newest_id&plugin_version=$newest_version&plugin_state=$newest_state\"><b>Download Newer $newest_state</b> (" . formatFileSize($newest_downloadsize) . ")</a><br />";
} else {
echo "<p><center><a href='dl.php?plugin_id=" . $plugin_id . "'><b>Download</b> (" . formatFileSize($plugin->plugin_DownloadSize) . ")</a></p></center>";
}
if (file_exists($filename2)) {
  echo "<p><a href='screenshots.php?plugin_id=$plugin_id$breadcrumb'><b>Show the screenshots.</b></a></p></center>";
} else {
  echo "<b>No screenshot available</b></center>";
}
echo "</td> <td height='350' id=\"plugininfos2\">";
//Plugin info
////////////////////////////////////////////////////////////////
//nom du plugin

echo "<font size=+2>$plugin->plugin_Name</font> v$plugin->plugin_Version ($plugin->plugin_State)<br>";
if ($plugin->plugin_Author) echo "By <a href='thelist.php?author=" . urlencode($plugin->plugin_Author) . "'>$plugin->plugin_Author</a>";
if ($plugin->plugin_Author && $plugin->profil_id) echo " (Updated by <a href='thelist.php?profile_id=" . urlencode($plugin->profil_id) . "'>$plugin->profil_id</a>)";
elseif (!$plugin->plugin_Author && $plugin->profil_id) echo "By <a href='thelist.php?profile_id=" . urlencode($plugin->profil_id) . "'>$plugin->profil_id</a>";
//Get plugin history now so we know if there is more than one version.  We'll use the info now to choose
//how to display the download count lines. (robogeek)
$res = GetPluginHistory($plugin_id);
$nb = mysql_numrows($res);
//$popularity = GetPopularity($plugin_id);
#$popularity = "(Temporarily disabled)";
#if (!$popularity) $popularity = "No Popularity Data Available for this Plugin";
echo "<hr />";

///////en dessous icone
echo "<p align='center'><a href=\"dl.php?plugin_id=$plugin_id&plugin_version=$plugin->plugin_Version&plugin_state=$plugin->plugin_State\"><img src='images/download.png' width='64' height='64' border='0' /></a><br>";
echo "<table align='center'>";
if ($nb > 1) {
	printPluginInfo("<tr valign=top><td align='right' NOWRAP>","Total Downloads (All Versions)","Today: ". GetDownloadsToday($plugin_id) . " - Month: " . GetDownloadsMonth($plugin_id) . " - Total: " . GetDownloadsTotal($plugin_id),"</td><td align='left'>","</td></tr>","");
	printPluginInfo("<tr valign=top><td align='right' NOWRAP>","Total Downloads (This Version)","Today: ". GetDownloadsTodayThisVersion($plugin_id,$plugin->plugin_Version) . " - Month: " . GetDownloadsMonthThisVersion($plugin_id,$plugin->plugin_Version) . " - Total: " . GetDownloadsTotalThisVersion($plugin_id,$plugin->plugin_Version),"</td><td align='left'>","</td></tr>","");
	}
else printPluginInfo("<tr valign=top><td align='right' NOWRAP>","Downloads","Today: ". GetDownloadsToday($plugin_id) . " - Month: " . GetDownloadsMonth($plugin_id) . " - Total: " . GetDownloadsTotal($plugin_id),"</td><td align='left'>","</td></tr>","");
#printPluginInfo("<tr valign=top><td align='right' NOWRAP>","Popularity",$popularity,"</td><td>","</td></tr>","");
printPluginInfo("<tr valign=top><td align='right' NOWRAP>","Type",$plugin->plugin_Type,"</td><td align='left'>","</td></tr>","");
if ($plugin->plugin_Licence) printPluginInfo("<tr valign=top><td align='right' NOWRAP>","License",$plugin->plugin_Licence,"</td><td align='left'>","</td></tr>","");
if ($plugin->plugin_Date) printPluginInfo("<tr valign=top><td align='right' NOWRAP>","Last Updated Date",$plugin->plugin_Date,"</td><td align='left'>","</td></tr>","");
if ($plugin->profil_id) printPluginInfo("<tr valign=top><td align='right' NOWRAP>","Forum Profile", $plugin->profil_id, "</td><td align='left'><a href='http://www.meedios.com/forum/memberlist.php?mode=viewprofile&un=" . urlencode($plugin->profil_id) . "'>","</a></td></tr>","");
if ($plugin->plugin_Copyright) printPluginInfo("<tr valign=top><td align='right' NOWRAP>","Copyright",$plugin->plugin_Copyright,"</td><td align='left'>","</td></tr>","");
//Added another if clause to check for file in lowercase state directory (robogeek)
if (is_file("$plugin_directory2/$plugin->plugin_DocumentationFile")) {
	$doc_begintag = "<a target='_blank' href='$plugin_directory2/$plugin->plugin_DocumentationFile'>";
	$documentation = $plugin->plugin_DocumentationFile;
	$doc_endtag = "</a>";
	}
elseif (is_file("$plugin_directory/$plugin->plugin_DocumentationFile")) {
	$doc_begintag = "<a target='_blank' href='$plugin_directory/$plugin->plugin_DocumentationFile'>";
	$documentation = $plugin->plugin_DocumentationFile;
	$doc_endtag = "</a>";
	}
else
	{$doc_begintag = ""; $documentation = "(none)"; $doc_endtag = "";}
if ($documentation != "(none)") printPluginInfo("<tr valign=top><td align='right' NOWRAP>","Documentation", $documentation,"</td><td>" . $doc_begintag,$doc_endtag . "</td align='left'></tr>","");
if ($plugin->plugin_SourceLink) printPluginInfo("<tr valign=top><td align='right' NOWRAP>","Source Link", $plugin->plugin_SourceLink,"</td><td align='left'>","</td></tr>","");
if ($plugin->plugin_SupportLink) printPluginInfo("<tr valign=top><td align='right' NOWRAP>","Plugin Forum Link", $plugin->plugin_SupportLink,"</td><td align='left'>","</td></tr>","");
if ($plugin->plugin_DonationLink) printPluginInfo("<tr valign=top><td align='right' NOWRAP>","Donation Link", $plugin->plugin_DonationLink,"</td><td align='left'>","</td></tr>","");
if ($plugin->plugin_Site) printPluginInfo("<tr valign=top><td align='right' NOWRAP>","Author WebSite", strip_tags($plugin->plugin_Site),"</td><td align='left'>","</td></tr>","");
//Generate Wiki Page link
printPluginInfo("<tr valign=top><td align='right' NOWRAP>","Plugin Wiki", "http://www.meedios.com/wiki/index.php?title=Plugin_" . trim($plugin_id,"{}") ,"</td><td align='left'>","</td></tr>","");
echo "</table>";
#echo "<hr />";
//status du plugin
if ($plugin->plugin_DonationLink) printPluginVersionReq("Version Requirements", $plugin->plugin_MinRequiredVersion, $plugin->plugin_MaxRequiredVersion);
# if ($plugin->plugin_IsDotNET) printPluginInfo("","This is a .NET plugin","","","","<br />");
# if ($plugin->plugin_IsDotNETSimple) printPluginInfo("","This is a Simple .NET plugin","","","","<br />");
$newest_plugin = GetMostRecentPluginObject($plugin_id);
$newest_id = $newest_plugin->plugin_ID;
$newest_versionvalue = $newest_plugin->plugin_VersionValue;
$newest_version = $newest_plugin->plugin_Version;
$newest_state = $newest_plugin->plugin_State;
$newest_downloadsize = $newest_plugin->plugin_DownloadSize;
if ($plugin->plugin_VersionValue < $newest_versionvalue) {
	echo "<b>A newer $newest_state version is available:<br />";
	echo "[ <a href=\"dl.php?plugin_id=$newest_id&plugin_version=$newest_version&plugin_state=$newest_state\">Download Latest $newest_state</a> ] - [ <a href='detail.php?plugin_id=$newest_id&plugin_version=$newest_version&plugin_state=$newest_state'>Full Details for Latest $newest_state</a> ]</b><br />";
}
echo "<b>";
VoteWorking($u, $plugin_id, $plugin->plugin_Version, null, "detail.php?plugin_state=$plugin->plugin_State$breadcrumb"); 
echo "</b>";
echo "</td></tr>   
<tr> <td id=\"plugindescription\" width=300 halign='center' valign='top'>";
//plugin short description
echo "<hr />";
printPluginInfo("","Description","$plugin->plugin_ShortDescription","<p>","</p>","<br />");
echo "<hr />";
if ($plugin->plugin_DocumentText != "")
//plugin overview
{
	printPluginInfo("","Document Text","$plugin->plugin_DocumentText","<p>","</p>","<br />");
}
echo "</td> <td id=\"pluginlistdownload\" height='200' halign='center' valign='top'>";
//plugin old version list
if ($nb > 0)
{
  echo "<b>Plugin History</b>";
	echo "<table id=pluginboard width=470 cellspacing=0>";
	echo "<tr><th>Name</th><th>Version</th><th>State</th><th>Date</th><th>License</th><th>Download</th></tr>";
	$history_count = 0;
	while ( $row=mysql_fetch_array($res,MYSQL_NUM))
	{
		//if we want full history, don't count the number of plugins to display
		if ($_GET["mode"] != "fullhistory") {
			if ($history_count == 8) {$history_count = 6; break;}
			$history_count++;
			}
		echo "<tr>";
		$field_num = 0;
		foreach($row as $field)
		{
			if ($field_num == 0) echo "<td><a href='detail.php?plugin_id=" . $plugin_id . "&plugin_version=" . $row[1] . "&plugin_state=" . $row[2] . "'>$field</a></td>";
			elseif ($field_num == 3) {
				$theDate = explode(" ",$field);
				echo "<td align='center' nowrap>$theDate[0]</td>";
			}
			elseif ($field_num == 5) echo "\n";
			else echo "<td align='center' nowrap>$field</td>";
			$field_num++;
		}
		//$row[1] = version   $row[2] = state
		//Added plugin_version and plugin_state to dl.php parameters for retrieving previous versions (robogeek)
		echo "<td align='center'><a href='dl.php?plugin_id=" . $plugin_id . "&plugin_version=" . $row[1] . "&plugin_state=" . $row[2] . "'><b>Download</b> (" . formatFileSize($row[5]) . ")</a></td>";
		echo "</tr>";
	}
	echo "<tr><td colspan=6 align='center'>(Click plugin name in Name column to see plugin details for that version)";
	if ($history_count > 5) echo "<br>Additional versions are available.  Click <a href='detail.php?plugin_id=" . $plugin_id . "&plugin_version=" . $row[1] . "&plugin_state=" . $row[2] . "&mode=fullhistory'>HERE</a> to see full history.";
	echo "</td></tr>";
	echo "</table>";
}
echo "</font></td></tr>  
</table>";
include('footer.php');  
mysql_close();
?>



