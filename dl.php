<?php
require_once('functions.php');
require_once('authentication.php');
require_once('db.php');
require_once('maintenance.php');

//set_time_limit(1800);

$u = Authenticate();

$plugin_id = "";
if (isset($_GET["plugin_id"])) $plugin_id = formatPluginID($_GET["plugin_id"]);
else { if (isset($_GET["plugin"])) $plugin_id = formatPluginID($_GET["plugin"]); }

//check plugin_id for curly braces.  if none, add them.

//Added for downloading previous versions (robogeek)
$plugin_version = "";
$plugin_state = "";
$previous_verison_check = 0;
if (isset($_GET["plugin_version"])) {$plugin_version = $_GET["plugin_version"]; $previous_version_check++;}
if (isset($_GET["plugin_state"])) {$plugin_state = $_GET["plugin_state"]; $previous_version_check++;}
if ($previous_version_check == 1) die ("Error: You can't do that! If you think you should be able to do that, please report this error.");
if ($previous_version_check != 2) {
	$previous_version_check = 0;
	$plugin_version = "";
	$plugin_state = "";
	}

if ($plugin_id =="")
{
	//TODO Try to get a Key!	
}
else
{
//	if (!eregi("\{[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}\}", $plugin_id) || !eregi("[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}", $plugin_id)) die("nice hack job, not :P");
}

//Changed to use GetPreviousPluginObject to make it compatible with fetching previous version data (robogeek)
$plugin = GetPreviousPluginObject($plugin_id, $plugin_version, $plugin_state, $previous_version_check);
$thismpp = getPluginFileName($plugin->plugin_Name, $plugin->plugin_Version, $plugin->plugin_State);
$filename = $plugin_home_directory . "/" . $plugin->plugin_ID . "/" . $plugin->plugin_Version . "/" . $plugin->plugin_State . "/this.mpp";

//For plugins with lowercase state directory. (robogeek)
if (!is_file($filename)) {
	$filename = $plugin_home_directory . "/" . $plugin->plugin_ID . "/" . $plugin->plugin_Version . "/" . strtolower($plugin->plugin_State) . "/this.mpp";
	}
if (!is_file($filename)) {
	$filename = $plugin_home_directory . "/" . $plugin->plugin_ID . "/" . $plugin->plugin_Version . "/" . $plugin->plugin_State . "/" . $thismpp;
	}
if (!is_file($filename)) {
	$filename = $plugin_home_directory . "/" . $plugin->plugin_ID . "/" . $plugin->plugin_Version . "/" . strtolower($plugin->plugin_State) . "/" . $thismpp;
	}
if (!is_file($filename)) die ("Error: Can't find plugin file ($filename)!");


$dl_filename = $plugin->plugin_Name . "_" . $plugin->plugin_Version . "_" . $plugin->plugin_State ;

//Clean Up the file name for any bad characters
$dl_filename = str_replace(" ", "_", $dl_filename);
$dl_size = filesize($filename);

//Workaround for MSIE download bug where [] are added to downloads with mutliple periods in them
if (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE"))
	//$dl_filename = preg_replace('/\./', '%2e', $dl_filename, substr_count($dl_filename, '.'));
	$dl_filename = str_replace('.', '%2e', $dl_filename);

//log the download to the db and increment download counters.
//if user is hammering the server for the same plugin, we'll exit without sending the plugin.
$do_exit = IncrementDownloadCount($plugin->plugin_ID,$plugin->plugin_Version,$u);
if ($do_exit == 255) exit;

//do a redirect to the mopp file instead of sending it via php.
#header("Location: " . $sys_url . $filename);
#header("Content-Disposition: attachment; filename=plugin.mopp");


header("Cache-Control: public, must-revalidate");
header("Content-Type: application/octet-stream");
header("Content-Length: ".$dl_size);
//header("Content-Disposition: attachment; filename=".$dl_filename.".mpp;");
header('Content-Disposition: attachment; filename="' . $dl_filename . '.mopp"');
header("Content-Transfer-Encoding: binary");
session_write_close();
ob_flush();flush();

//getting error calling virtual() (robogeek)
//virtual("$filename");



if (!$fp = @fopen($filename, 'rb')){
	die("Cannot Open File!  Please report this error to the OpenMAID admin!<br>\n");
	} 
else {
	sleep(1);
	$mem_limit = return_bytes(ini_get('memory_limit'));
	$dl_size_limit = $mem_limit/2.5;
	if ($dl_size > $dl_size_limit) {
		while(!feof($fp)) {
			$buffer = fread($fp, 32 * 1024);
			//sleep(1);  //uncomment and change # of seconds to wait to throttle the download speed.  only works in this loop, not for fpassthru
			print $buffer;
			ob_flush(); // flush();
			} 
		}
	else {
		fpassthru($fp);
		}
	@fclose($fp);
	}




?>