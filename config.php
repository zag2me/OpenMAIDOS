<?php
	
	//MySQL Database Information for OpenMAID v2
	$db_host 		= "localhost";
	$db_user 		= "";
	$db_pass 		= "";
	$db_database	= "slug_OpenMAIDOS";

	//MySQL DB Info for previous version database (used to copy profil_id from old db to new db)
	$old_db_host 			= "localhost";
	$old_db_user 			= "";
	$old_db_pass 			= "";
	$old_db_database		= "slug_OpenMAID";
	$old_db_plugins_table 	= "plugins";

	//Directory where manually uploaded plugins go to be processed. Include trailing slash.
	$ftp_manual_uploads = "ftp/";

	//Directory where plugin repository is.  This is where fetchplugins.php stores all versions of all plugins incase we need to 
	//rebuild the OpenMAID database and plugin directory.  Set automatically...please don't change this line!!!!
	$ftp_repository = $ftp_manual_uploads . "repository/";

	//Allowable plugin_Type and plugin_ModuleType: 'General','Import','Input','Module','Theme','Web','Wizard','Extension','Sub','Hack','Misc','Icon', 'Service', 'Database'
	$types = array("Icon","Theme","General","Input","Module","Import","Extension","Misc","Hack", "Web", "Wizard", "Sub", "Service", "Database");

	//URL should point to the URL where default.php is located	
	$sys_url 		= "http://www.MeediOS.com/OpenMAIDOS/";
	
	//phpBB Forum Root Url
	$sys_forum_url	= "http://www.meedios.com/forum/";
	
	//Specifies how OpenMAID should authenticate values allowed: "virtual" and "forum"
	$sys_auth		= "virtual";
	
	//Specifies all of the OpenMAID administrators bellow
	$sys_admin_list = array(
							"vinny",
							"skypichat",
							"supertoadman",
							"zag2me",
							"binary64",
							"robogeek");
							
	$plugin_home_directory = "plugins";

	//Sets debug mode. 1=TRUE 0=FALSE
	$debug = 0;
	
?>
