<?php
require_once('feedcreator.class.php'); 
require_once('functions.php');
require_once('db.php');

if (file_exists("feeds.php")) unlink("feeds.php");
if (isset($_GET["feed_type"])) $feed_type = $_GET["feed_type"];
else $feed_type = "rss2";
if (isset($_GET["feed_name"])) $feed_name = $_GET["feed_name"];
else $feed_name = "lastupdated";
if (isset($_GET["count"])) $count = $_GET["count"];
else $count = 10;
//mkdir("feeds");
//mkdir("feeds/rss2");
//mkdir("feeds/rss1");
//mkdir("feeds/rss091");
switch ($feed_name)
{
	case "lastupdated":
		lastupdated($feed_type, $count);
		break;
	case "mostdownloaded":
		mostdownloaded($feed_type, $count);
		break;
}

function lastupdated($feed_type, $count){
	//connect to db
	ConnectOnce();
	$filename = "last_" . $count . "_updated_" . $feed_type . ".xml";

	$rss = new UniversalFeedCreator(); 
	$rss->useCached(); // use cached version if age<1 hour
	$rss->title = "MeediOS OpenMAID Last $count Updated Plugins"; 
	$rss->description = "A list of the last $count plugins uploaded to OpenMAID."; 

	//optional
	$rss->descriptionTruncSize = 500;
	$rss->descriptionHtmlSyndicated = true;

	$rss->link = "http://www.meedios.com/OpenMAIDOS/" . $filename; 
	$rss->syndicationURL = "http://www.meedios.com".$_SERVER["PHP_SELF"]; 

	$image = new FeedImage(); 
	$image->title = "meedios logo"; 
	$image->url = "http://www.meedios.com/OpenMAIDOS/images/missing.gif"; 
	$image->link = "http://www.meedios.com"; 
	$image->description = "Feed provided by meedios.com. Click to visit."; 

	//optional
	$image->descriptionTruncSize = 500;
	$image->descriptionHtmlSyndicated = true;

	$rss->image = $image; 

	// get news items from somewhere, e.g. your database: 
	$res = mysql_query("SELECT plugin_ID, plugin_Name, plugin_Author, plugin_AuthorEmail, plugin_ShortDescription, plugin_Date, max(plugin_Date) as maxdate FROM plugins WHERE plugin_Current = true AND plugin_ReviewFlag = 'FALSE' GROUP BY plugin_ID ORDER BY maxdate desc LIMIT $count");
	$res = mysql_query("SELECT *, max(plugin_Date) as maxdate FROM plugins WHERE plugin_Current = true AND plugin_ReviewFlag = 'FALSE' GROUP BY plugin_ID ORDER BY maxdate desc LIMIT $count");
	while ($data = mysql_fetch_object($res)) { 
	    $item = new FeedItem(); 
	    $item->title = $data->plugin_Name; 
	    $item->link = "http://www.meedios.com/OpenMAIDOS/detail.php?plugin_id=" . $data->plugin_ID; 
	    $item->description = $data->plugin_ShortDescription; 

	    //optional
	    $item->descriptionTruncSize = 500;
	    $item->descriptionHtmlSyndicated = true;

	    $item->date = strtotime($data->plugin_Date); 
	    $item->source = "http://www.meedios.com"; 
	    $item->author = $data->plugin_Author; 
	    $item->category = $data->plugin_Type;
    	$item->guid = $data->plugin_ID;
    	$additionalElements = array("version"=>$data->plugin_Version, 
											"state"=>$data->plugin_State, 
											"license"=>$data->plugin_Licence, 
											"copyright"=>$data->plugin_Copyright, 
											"download_link"=>"http://www.meedios.com/OpenMAIDOS/" . $data->plugin_DownloadLink, 
											"download_size"=>$data->plugin_DownloadSize, 
											"support_link"=>$data->plugin_SupportLink, 
											"donation_link"=>$data->plugin_DonationLink, 
											"downloads_this_version"=>$data->plugin_DownloadCount, 
											"downloads_total"=>$data->plugin_DownloadCountTotal, 
											"popularity_this_version"=>$data->plugin_PopularityCount, 
											"popularity_total"=>$data->plugin_PopularityCountTotal, 
											"profile_id"=>$data->profil_id);

		//Get screenshot thumbnails and images and add them to array
		$ss = GetPluginScreenshots($data->plugin_ID, $data->plugin_Version);
		$sstmp = array();
		$screenshots = array("screenshot_thumb_0"=>"http://www.meedios.com/OpenMAIDOS/images/missing.gif","screenshot_0"=>"http://www.meedios.com/OpenMAIDOS/images/missing.gif");
		$nb = mysql_numrows($ss);
		if ($nb > 0)
		{
		 	//echo "<b>Screenshots</b><br />";
			$i = 0;
			while ($i < $nb)
			{
				$image_thumb_path = "http://www.meedios.com/OpenMAIDOS/" . mysql_result($ss, $i, "image_thumb_path");
				$image_path = "http://www.meedios.com/OpenMAIDOS/" . mysql_result($ss, $i, "image_path");
				$sstmp = array("screenshot_thumb_$i"=>$image_thumb_path, "screenshot_$i"=>$image_path);
				if ($i > 0) $screenshots = associative_push($screenshots, $sstmp);	
				else $screenshots = $sstmp;
				$i++;
			}
		}
		if (is_array($screenshots)) $item->additionalElements = array_merge($additionalElements,$screenshots);
		else $item->additionalElements = $additionalElements;
		$rss->addItem($item); 
	} 

	// valid format strings are: RSS0.91, RSS1.0, RSS2.0, PIE0.1 (deprecated),
	// MBOX, OPML, ATOM, ATOM0.3, HTML, JS
	//if ($feed_type == "rss1") $rss->saveFeed("RSS1.0", $filename);
	//if ($feed_type == "rss2") $rss->saveFeed("RSS2.0", $filename);
	//if ($feed_type == "rss091") $rss->saveFeed("RSS0.91", $filename);
	if ($feed_type == "rss1") $rss->outputFeed("RSS1.0", $filename);
	if ($feed_type == "rss2") $rss->outputFeed("RSS2.0", $filename);
	if ($feed_type == "rss091") $rss->outputFeed("RSS0.91", $filename);
}

// append associative array elements
function associative_push($arr, $tmp) {
  if (is_array($tmp)) {
    foreach ($tmp as $key => $value) { 
      $arr[$key] = $value;
    }
    return $arr;
  }
  return false;
}
?>