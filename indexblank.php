<?php

require_once('functions.php');

require_once('authentication.php');

require_once('maintenance.php');

include('header.php');

?>
    <div id="main">
        <div id="content">
        <div id="text">
<p>&nbsp;</p>
<p align="center">Browse the catagories below to find your plugin</p>
<table width="516" border="0" align="center" cellspacing="0" background="images/new/module_trans.gif">
        <tr>
           <td width="4">&nbsp;</td>
           <td width="168" height="22"><strong>Themes</strong></td>
           <td width="3">&nbsp;</td>
           <td width="168" height="22"><strong>Import</strong></td>
           <td width="4">&nbsp;</td>
           <td width="168" height="22"><strong>Module</strong></td>
        </tr>
         <tr>
           <td width="4">&nbsp;</td>
           <td width="168" height="82" valign="top">Graphical Themes that change the appearance of MeediOS.</td>
           <td width="3">&nbsp;</td>
           <td width="168" height="82" valign="top">Plugins that import data from your movies, tv shows and music</td>
           <td width="4">&nbsp;</td>
           <td width="168" height="82" valign="top">Graphical plugins that extend the functions of MeediOS. Examples such as the the MusicIP.</td>
         </tr>
       </table> 
       <table width="516" border="0" align="center" cellspacing="0" background="images/new/module_trans.gif">
         <tr>
           <td width="4">&nbsp;</td>
           <td width="168" height="22"><strong>Icon Sets</strong></td>
           <td width="3">&nbsp;</td>
           <td width="168" height="22"><strong>General</strong></td>
           <td width="4">&nbsp;</td>
           <td width="168" height="22"><strong>Input</strong></td>
         </tr>
         <tr>
           <td width="4">&nbsp;</td>
           <td width="168" height="82" valign="top">Change the default icon sets for themes</td>
           <td width="3">&nbsp;</td>
           <td width="168" height="82" valign="top">Plugins that perform a function inside MeediOS</td>
           <td width="4">&nbsp;</td>
           <td width="168" height="82" valign="top">Input device plugins such as remote controls</td>
         </tr>
       </table>
       <table width="516" border="0" align="center" cellspacing="0" background="images/new/module_trans.gif">
         <tr>
           <td width="4">&nbsp;</td>
           <td width="168" height="22"><strong>Extension</strong></td>
           <td width="3">&nbsp;</td>
           <td width="168" height="22"><strong>Hack</strong></td>
           <td width="4">&nbsp;</td>
           <td width="168" height="22"><strong>Wizard</strong></td>
         </tr>
         <tr>
           <td width="4">&nbsp;</td>
           <td width="168" height="82" valign="top">&nbsp;</td>
           <td width="3">&nbsp;</td>
           <td width="168" height="82" valign="top">&nbsp;</td>
           <td width="4">&nbsp;</td>
           <td width="168" height="82" valign="top">Easy to use wizard interfaces</td>
         </tr>
       </table>
       <table width="516" border="0" align="center" cellspacing="0" background="images/new/module_trans.gif">
         <tr>
           <td width="4">&nbsp;</td>
           <td width="168" height="22"><strong>Misc.</strong></td>
           <td width="3">&nbsp;</td>
           <td width="168" height="22"><strong>Web</strong></td>
           <td width="4">&nbsp;</td>
           <td width="168" height="22"><strong>Service</strong></td>
         </tr>
         <tr>
           <td width="4">&nbsp;</td>
           <td width="168" height="82" valign="top">Misc. plugins for use with MeediOS</td>
           <td width="3">&nbsp;</td>
           <td width="168" height="82" valign="top">&nbsp;</td>
           <td width="4">&nbsp;</td>
           <td width="168" height="82" valign="top">Scraper plugins for use with importers</td>
         </tr>
       </table>
       <p>&nbsp;</p>
       <p>&nbsp;</p>
       <p>&nbsp;</p>
       <p>&nbsp;</p>
        </div>
               <div id="sidebar">
<?php 

echo "<br><p><b>Last 10 Updated Plugins:</b><br><br>";

//Original line was: $res = mysql_query("SELECT * FROM plugins ORDER BY plugin_Date DESC LIMIT 0,10");

//Was showing duplicate plugins in the list if the plugin was updated more than once recently

//The query now shows the last 10 updated plugins without duplicating entries (robogeek)

$res = mysql_query("SELECT plugin_ID, plugin_Name, plugin_Author, max(plugin_Date) as maxdate FROM plugins WHERE plugin_ReviewFlag = 'FALSE' GROUP BY plugin_ID ORDER BY maxdate desc LIMIT 10");

while (($enreg=@mysql_fetch_array($res)))

{

	echo "<a href=\"detail.php?plugin_id=" . $enreg["plugin_ID"] . "\"><b>" . $enreg["plugin_Name"] . "</b></a> by <a href=\"thelist.php?author=" . urlencode($enreg["plugin_Author"]) . "\"><i>" . $enreg["plugin_Author"] . "</i></a><br>";

}

echo "<hr />";
if ($u <> "") echo "<a href='upload.php'>Add plugin</a><br />";

else echo "<span title=\"Login to the forum before uploading\">add/update plugin</span><br />";

if ($u <> "") {

	echo "<p>";

	$usersplugins = findUsersPlugins($u);

	$usersplugins_count = mysql_numrows($usersplugins);

	if ($usersplugins_count > 0) echo "&nbsp;&nbsp;&nbsp;(<a href='extra.php?mode=manageplugins'>Manage My Plugins</a>)";

	if (IsAdmin($u)) echo "&nbsp;&nbsp;&nbsp;(<a href='admin.php'>admin</a>)";

	echo "</p>";

	}

else echo '<p><a href="' . GetLogonURL() . '">Login to upload</a></p><br />';

?>
       <p>&nbsp;</p>
       </div>
       </div>
    </div>
    <!-- end main -->
    <!-- footer -->
    <?php 
include 'footer.php';
?>