<?php

	// Download script
	// Usage: http://URL/{username}/files/{folder_id}/{file_id}/{filename}
	
	// Run includes
		require("../includes.php");
		
	// Initialise functions for user details, icon management and profile management
		run("userdetails:init");
		run("profile:init");
		run("files:init");
		
	// If an ID number for the file has been specified ...
	
		if (isset($_REQUEST['id'])) {
			$id = (int) $_REQUEST['id'];
			
	// ... and the file exists ...
			
			$file = db_query("select location, access, originalname from files where ident = $id");
			if (sizeof($file) > 0) {
				
				$file = $file[0];
								
					if (run("users:access_level_check",$file->access) == true) {

	// Send 304s where possible, rather than spitting out the file each time
						$if_modified_since = preg_replace('/;.*$/', '', $_SERVER['HTTP_IF_MODIFIED_SINCE']);
						
						$tstamp = filemtime($file->location);
						$lm = gmdate("D, d M Y H:i:s", $tstamp) . " GMT";
						
						if ($if_modified_since == $lm) {
							header("{$_SERVER['SERVER_PROTOCOL']} 304 Not Modified");
							exit;
						}

	// Send last-modified header to enable if-modified-since requests
						if ($tstamp < time()) {
							header("Last-Modified: " . $lm);
						}
						
	// Then output some appropriate headers and send the file data!
	
						$mimetype = run("files:mimetype:determine",$file->originalname);
						if ($mimetype == false) {
							$mimetype = "application/octet-stream";
						}
						if ($mimetype == "image/jpeg" || $mimetype == "image/png") {
							$icon = url . "units/phpthumb/phpThumb.php?w=90&src=" . urlencode($file->location);
							$mimetype = "image/jpeg";
						} else {
							$mimetype = "image/png";
							$icon = path . "_files/file.png";
						}

	// "Cache-Control: private" to allow a user's browser to cache the file, but not a shared proxy
	// Also to override PHP's default "DON'T EVER CACHE THIS EVER" header
						header("Cache-Control: private");
						
						header("Content-type: $mimetype");
						readfile($icon);
						
					}
					
				}
			}

?>