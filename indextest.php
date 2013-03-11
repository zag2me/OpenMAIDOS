<?php
require_once('functions.php');
require_once('authentication.php');
require_once('maintenance.php');
include('header.php');
SearchBar("","",FALSE);
$u = Authenticate();
global $db_host;
global $db_user;
global $db_pass;
global $db_database;
@mysql_connect($db_host, $db_user, $db_pass) or slowDie("Error connecting to sql " . $db_user );
@mysql_select_db($db_database) or slowDie("Error connecting to db");
function printPara($para)
{
echo "<p>$para</p>\n";
}
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
<table id="index2" width="750" border="0" align="center">
  <tr>
    <td id="" valign="top" align="center">
      <h1 id=title>Welcome</h1>
      <p>OpenMAID is a central repository for MeediOS plugins.</p>
      <h1 id=title>Featured plugins</h1>
      <table width="" border="0" align="center">
       <tr>
         <td><strong><a href="thelist.php?search=Moviesdata&Search=Search">MoviesDataSuite</a></strong></td>
          <td><strong><a href="detail.php?plugin_id=16EE4ED1-0936-459C-A404-B710E87CF88B&search=Homemodule">HomeModule</a></strong></td>
       </tr>
       <tr>
         <td><img src="images/new/moviesdatasuite.jpg" width="200" height="112" /></td>
         <td><img src="images/new/homemodule.jpg" width="200" height="112" /></td>
       </tr>
        <tr>
         <td width="210">A suite of plugins to retrieve information about your movies.</td>
          <td width="210">Extends the functions of the first group module.</td>
       </tr>
       <tr>
         <td><strong><a href="thelist.php?search=tvshowsdata&Search=Search">TVShowsDataSuite</a></strong></td>
          <td><strong><a href="thelist.php?search=musicdata&Search=Search">MusicDataSuite</a></strong></td>
       </tr>
       <tr>
         <td><img src="images/new/tvshowsdatasuite.jpg" width="200" height="112" /></td>
         <td><img src="images/new/musicdatasuite.jpg" width="200" height="112" /></td>
        </tr>
        <tr>
         <td width="210">A suite of plugins to retrieve information about your TV shows.</td>
          <td width="210">A suite of plugins to retrieve information about your Music.</td>
        </tr>
      </table>
     </td>
     <td id="indexmenu" />
       <?php
          echo "<br><p><b id='title2'>Last 20 Updated Plugins:</b><br><br>";
          //Original line was: $res = mysql_query("SELECT * FROM plugins ORDER BY plugin_Date DESC LIMIT 0,10");
          //Was showing duplicate plugins in the list if the plugin was updated more than once recently
          //The query now shows the last 10 updated plugins without duplicating entries (robogeek)
          $res = mysql_query("SELECT plugin_ID, plugin_Version, plugin_State, plugin_Name, plugin_Author, max(plugin_Date) as maxdate FROM plugins WHERE plugin_ReviewFlag = 'FALSE' GROUP BY plugin_ID ORDER BY maxdate desc LIMIT 20");
          while (($enreg=@mysql_fetch_array($res)))
          {
          #echo "<img src= plugins/" . $enreg["plugin_ID"] . "/" . $enreg["plugin_Version"] . "/" . $enreg["plugin_State"] . "/1.png width='20' height='20'>";
          echo "<a href=\"detail.php?plugin_id=" . $enreg["plugin_ID"] . "\"><b>" . "<img src= images/info.png width='12' height='12'> " . $enreg["plugin_Name"] . "</b></a></i></a><br>";
          }
          echo "<hr />";
          //echo "<br><p><b>10 Most Downloaded:</b><br><br>";
          //Original line was: $res = mysql_query("SELECT * FROM plugins ORDER BY plugin_DownloadCount DESC LIMIT 0,10");
          //Now we use the userdownloads table and we need to account for multiple versions of a plugin
          //$res = mysql_query("SELECT plugins.plugin_ID, plugins.plugin_Name, plugins.plugin_Author, count(*) AS dl_num FROM userdownloads, plugins WHERE plugins.plugin_ID = userdownloads.plugin_ID AND plugins.plugin_Version = userdownloads.plugin_Version AND userdownloads.is_dupe = 'false' GROUP BY plugins.plugin_ID ORDER BY dl_num DESC LIMIT 10");
          //while (($enreg=@mysql_fetch_array($res)))
          //{
          //echo "<a href=\"detail.php?plugin_id=" . $enreg["plugin_ID"] . "\"><b>" . "<img src= images/info.png width='12' height='12'> " .$enreg["plugin_Name"] . "</b></a><br>";
          //}
          //echo "<hr />";
          echo "<p><b>";
          if ($u != "") printPara ("Welcome, $u");
          echo "</b>";
          echo "<p><b id='title2'>User menu:</b><br><br>";
          $usersplugins = findUsersPlugins($u);
          $usersplugins_count = mysql_numrows($usersplugins);
          if (($usersplugins_count > 0) && ($u != "")){
          echo '<a href="extra.php?mode=home"><b><img src=images/add.png width=12 height=12> Stats & Tools Home</b></a><br />';
        	//if user has plugins, show Check Plugin Metadata menu item
        	echo '<a href="extra.php?mode=check"><b><img src=images/add.png width=12 height=12> Check Plugin Metadata</a><br />';
        	echo '<a href="userstats.php"><b><img src=images/add.png width=12 height=12> Your Last 10 Downloads</a><br />';
        	}
          if ($u <> "") echo "<a href='upload.php'><b><img src=images/add.png width=12 height=12> Add plugin</a><br />";
          else echo "<span title=\"Login to the forum before uploading\">add/update plugin</span><br />";
          if ($u <> "") {
          if ($usersplugins_count > 0) echo "<a href='extra.php?mode=manageplugins'><b><img src=images/add.png width=12 height=12> Manage My Plugins</a><br />";
          if (IsAdmin($u)) echo "<a href='admin.php'><b><img src=images/add.png width=12 height=12> admin</a><br />";
          echo "<p>";
          if ($usersplugins_count > 0) echo "<a href='virtual_auth.php'><b><img src=images/Delete.png width=12 height=12> Logout</a>";
          echo "</p>";
          }
          else echo '<p><a href="' . GetLogonURL() . '"><b><img src=images/info.png width=12 height=12> Login to upload</a></p><br />';
        ?>
     </td>   
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
include 'footer.php'; ?>