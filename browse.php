<?php
require_once('functions.php');
require_once('authentication.php');
require_once('maintenance.php');
include('header.php');
SearchBar("","",TRUE);
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
?>

<table width="" border="0" align="center" >
<p>&nbsp; </p>
<h1 id=title align=center >Browse all Plugins</h1>
<tr>
  <td rowspan="2" id="pluginicon3"><img src="images/new/bigarrow.png"  width="70" height="70"></td>
  <td id="titlebrowse"><strong><a href="thelist.php?program=meedio&amp;ptype=theme">Themes</a></strong></td>
  <td rowspan="12" width="30"></td>
  <td rowspan="2" id="pluginicon3"><img src="images/new/bigarrow.png"  width="70" height="70"></td>
  <td id="titlebrowse"><strong><a href="thelist.php?program=meedio&amp;ptype=extension">Extension</a></strong></td>
</tr>
<tr>
  <td width="220" id="plugininfos3" height="50" valign="top">Graphical Themes that change the appearance of MeediOS.</td>
  <td width="220" id="plugininfos3" height="50" valign="top">Extension Plugins</td>
</tr>
<tr>
  <td rowspan="2" id="pluginicon3"><img src="images/new/bigarrow.png"  width="70" height="70"></td>
  <td id="titlebrowse"><strong><a href="thelist.php?program=meedio&amp;ptype=import">Import</a></strong></td>
  <td rowspan="2" id="pluginicon3"><img src="images/new/bigarrow.png"  width="70" height="70"></td>
  <td id="titlebrowse"><strong><a href="thelist.php?program=meedio&amp;ptype=database">Database</a></strong></td>
</tr>
<tr>
  <td width="220" id="plugininfos3" height="50" valign="top">Plugins that import data from your movies, tv shows and music</td>
  <td width="220" id="plugininfos3" height="50" valign="top">Backend Database Plugins</td>
</tr>
<tr>
  <td rowspan="2" id="pluginicon3"><img src="images/new/bigarrow.png"  width="70" height="70"></td>
  <td id="titlebrowse"><strong><a href="thelist.php?program=meedio&amp;ptype=module">Module</a></strong></td>
  <td rowspan="2" id="pluginicon3"><img src="images/new/bigarrow.png"  width="70" height="70"></td>
  <td id="titlebrowse"><strong><a href="thelist.php?program=meedio&amp;ptype=wizard">Wizard</a></strong></td>
</tr>
<tr>
  <td width="220" id="plugininfos3" height="50" valign="top">Graphical plugins that extend the functions of MeediOS. Examples such as the the MusicIP.</td>
  <td width="220" id="plugininfos3" height="50" valign="top">Easy to use wizard interfaces</td>
</tr>
<tr>
  <td rowspan="2" id="pluginicon3"><img src="images/new/bigarrow.png"  width="70" height="70"></td>
  <td id="titlebrowse"><strong><a href="thelist.php?program=meedio&amp;ptype=icon">Icon Sets</a></strong></td>
  <td rowspan="2" id="pluginicon3"><img src="images/new/bigarrow.png"  width="70" height="70"></td>
  <td id="titlebrowse"><strong><a href="thelist.php?program=meedio&amp;ptype=misc">Misc.</a></strong></td>
</tr>
<tr>
  <td width="220" id="plugininfos3" height="50" valign="top">Change the default icon sets for themes</td>
  <td width="220" id="plugininfos3" height="50" valign="top">Misc. plugins for use with MeediOS</td>
</tr>
<tr>
  <td rowspan="2" id="pluginicon3"><img src="images/new/bigarrow.png"  width="70" height="70"></td>
  <td id="titlebrowse"><strong><a href="thelist.php?program=meedio&amp;ptype=general">General</a></strong></td>
  <td rowspan="2" id="pluginicon3"><img src="images/new/bigarrow.png"  width="70" height="70"></td>
  <td id="titlebrowse"><strong><a href="thelist.php?program=meedio&amp;ptype=web">Web</a></strong></td>
</tr>
<tr>
  <td width="220" id="plugininfos3" height="50" valign="top">Plugins that perform a function inside MeediOS</td>
  <td width="220" id="plugininfos3" height="50" valign="top">&nbsp;</td>
</tr>
<tr>
  <td rowspan="2" id="pluginicon3"><img src="images/new/bigarrow.png"  width="70" height="70"></td>
  <td id="titlebrowse"><strong><a href="thelist.php?program=meedio&amp;ptype=input">Input</a></strong></td>
  <td rowspan="2" id="pluginicon3"><img src="images/new/bigarrow.png"  width="70" height="70"></td>
  <td id="titlebrowse"><strong><a href="thelist.php?program=meedio&amp;ptype=service">Service</a></strong></td>
</tr>
<tr>
  <td width="220" id="plugininfos3" height="50" valign="top">Input device plugins such as remote controls</td>
  <td width="220" id="plugininfos3" height="50" valign="top">Scraper plugins for use with other importers</td>
</tr>
</table>
<table width="800" height="30" border="0">
  <tr>
    <td>
    <?php echo "<a href='rss.php'><img src= images/rss.png width='25' height='25' align='left'></a><br />"; ?>      
    </td>
  </tr>
</table>
<?php
include 'footer.php';
?>