<?php

	// Action parser for file uploads

		global $folder;
		global $page_owner;
	
		if (isset($_REQUEST['action']) && logged_on) {
			
			switch($_REQUEST['action']) {
				
				// Create a new folder
					case "files:createfolder":	
												if (	
														logged_on
														&& isset($_REQUEST['new_folder_name']) 
														&& $_REQUEST['new_folder_name'] != ""
														&& isset($_REQUEST['new_folder_access']) 
														&& isset($folder)
														&& run("permissions:check", "files")
													) {
														$name = addslashes($_REQUEST['new_folder_name']);
														$access = addslashes($_REQUEST['new_folder_access']);
														db_query("	insert into file_folders
																	set 	parent = $folder,
																			name = '$name',
																			access = '$access',
																			files_owner = '$page_owner',
																			owner = ".$_SESSION['userid']);
														$insert_id = db_id();
														if ($_REQUEST['new_folder_keywords'] != "") {
															$value = $_REQUEST['new_folder_keywords'];
															$value = str_replace("\n","",$value);
															$value = str_replace("\r","",$value);
															$keyword_list = explode(",",$value);
															sort($keyword_list);
															if (sizeof($keyword_list) > 0) {
																foreach($keyword_list as $key => $list_item) {
																	$list_item = addslashes(trim($list_item));
																	db_query("insert into tags set tagtype = 'folder', access = '$access', tag = '$list_item', ref = $insert_id, owner = " . $_SESSION['userid']);
																}
															}
														}
														$messages[] = "Your folder was created.";
													} else {
														$messages[] = "Could not create folder. Perhaps the folder name was blank?";
													}
												break;
				
				// Upload a new file
				
					case "files:uploadfile":
												if 	(
														logged_on
														&& isset($_REQUEST['new_file_description'])
														&& isset($_REQUEST['new_file_title'])
														&& isset($_REQUEST['new_file_access'])
														&& isset($_REQUEST['folder'])
														&& isset($_FILES['new_file'])
														&& isset($_REQUEST['copyright'])
														&& run("permissions:check", "files")
													) {
														
														$ul_username = run("users:id_to_name", $page_owner);
														
														if 	(
																$_FILES['new_file']['error'] != 0
															) {
																$messages[] = "There was an error uploading the file. Possibly the file was too large, or the upload was interrupted.";
															} else {
																
																$total_quota = db_query("select sum(size) as sum from files where owner = " . $page_owner);
																$total_quota = $total_quota[0]->sum;
																
																$max_quota = db_query("select file_quota from users where ident = " . $page_owner);
																$max_quota = $max_quota[0]->file_quota;
																
																if ($total_quota + $_FILES['new_file']['size'] > $max_quota) {
																	$messages[] = "You have exceeded the file quota for this account. Some files must be deleted before you can upload this one.";
																} else {
																
																	$access = addslashes($_REQUEST['new_file_access']);
																	$original_filename = addslashes($_FILES['new_file']['name']);
																	$size = $_FILES['new_file']['size'];
																	$folderid = (int) $_REQUEST['folder'];
																	$description = addslashes($_REQUEST['new_file_description']);
																	$title = addslashes($_REQUEST['new_file_title']);
																	
																	$new_filename = time() . "_" . preg_replace("/[^\w.-]/i","_",$original_filename);
																	
																	$upload_folder = substr($ul_username,0,1);
																	
																	if (!file_exists(path . "_files/data/" . $upload_folder)) {
																		mkdir(path . "_files/data/" . $upload_folder);
																	}
																	
																	if (!file_exists(path . "_files/data/" . $upload_folder . "/" . $ul_username)) {
																		mkdir(path . "_files/data/" . $upload_folder . "/" . $ul_username);
																	}
																	
																	$new_filename = path . "_files/data/" . $upload_folder . "/" . $ul_username . "/" . $new_filename;
																	
																	if (move_uploaded_file($_FILES['new_file']['tmp_name'],$new_filename)) {
																	
																		$new_filename = addslashes($new_filename);
																			
																		db_query("insert into files 	set owner = ".$_SESSION['userid'].",
																											files_owner = ".$page_owner.",
																											folder = $folderid,
																											originalname = '$original_filename',
																											title = '$title',
																											description = '$description',
																											location = '$new_filename',
																											access = '$access',
																											size = '$size',
																											time_uploaded = ".time());
																		$file_id = db_id();
																		if ($_REQUEST['new_file_keywords'] != "") {
																			$value = $_REQUEST['new_file_keywords'];
																			$value = str_replace("\n","",$value);
																			$value = str_replace("\r","",$value);
																			$keyword_list = explode(",",$value);
																			sort($keyword_list);
																			if (sizeof($keyword_list) > 0) {
																				foreach($keyword_list as $key => $list_item) {
																					$list_item = addslashes(trim($list_item));
																					db_query("insert into tags set tagtype = 'file', access = '$access', tag = '$list_item', ref = $file_id, owner = " . $page_owner);
																				}
																			}
																		}
																		if (isset($_REQUEST['metadata'])) {
																			$metadata = $_REQUEST['metadata'];
																			if (sizeof($metadata) > 0) {
																				
																				foreach($metadata as $name => $value) {
																					$name = addslashes($name);
																					$value = addslashes($value);
																					db_query("insert into file_metadata
																								set name = '$name',
																								value = '$value',
																								file_id = $file_id");
																				}
																				
																			}
																		}
																		
																		$messages[] = "The file was successfully uploaded.";
																		$redirect_url = url . $ul_username . "/files/";
																		if ($folderid > -1) {
																			$redirect_url .= $folderid;
																		}
																		define('redirect_url', $redirect_url);
																	}
																																		
																}
																
															}
														
													} else {
														$redirect_url = url . $_SESSION['username'] . "/files/";
														if ($folderid > -1) {
															$redirect_url .= $folderid;
														}
														define('redirect_url', $redirect_url);
														$messages[] = "Upload unsuccessful. You must check the copyright box for a file to be uploaded.";
													}
												break;
				// Edit a file
					case "files:editfile":		if (
														logged_on
														&& isset($_REQUEST['file_id'])
														&& isset($_REQUEST['edit_file_title'])
														&& isset($_REQUEST['edit_file_folder'])
														&& isset($_REQUEST['edit_file_description'])
														&& isset($_REQUEST['edit_file_access'])
														&& isset($_REQUEST['edit_file_keywords'])
													) {
														$file_id = (int) $_REQUEST['file_id'];
														$file_title = addslashes($_REQUEST['edit_file_title']);
														$file_folder = (int) ($_REQUEST['edit_file_folder']);
														$file_access = addslashes($_REQUEST['edit_file_access']);
														$file_keywords = addslashes($_REQUEST['edit_file_keywords']);
														$file_description = addslashes($_REQUEST['edit_file_description']);
														$file_info = db_query("select owner, files_owner from files where ident = $file_id");
														$file_info = $file_info[0];
														$files_username = run("users:id_to_name", $file_info->files_owner);
														if ($file_info->owner == $_SESSION['userid'] || $file_info->files_owner == $_SESSION['userid']) {
															db_query("update files set 
																						folder = $file_folder,
																						title = '$file_title',
																						access = '$file_access',
																						description = '$file_description'
																						where ident = $file_id");
															db_query("delete from tags where tagtype = 'file' and ref = $file_id");
															if ($file_keywords != "") {
																$value = $file_keywords;
																$value = str_replace("\n","",$value);
																$value = str_replace("\r","",$value);
																$keyword_list = explode(",",$value);
																sort($keyword_list);
																if (sizeof($keyword_list) > 0) {
																	foreach($keyword_list as $key => $list_item) {
																		$list_item = (trim($list_item));
																		db_query("insert into tags set tagtype = 'file', access = '$file_access', tag = '$list_item', ref = $file_id, owner = " . $_SESSION['userid']);
																	}
																}
															}
															$redirect_url = url . $files_username . "/files/";
															if ($file_folder != -1) {
																$redirect_url .= $file_folder;
															}
															define('redirect_url',$redirect_url);
															$messages[] = "The file was updated.";
														}
													}
												break;
				// Edit a folder
					case "edit_folder":			if (
														logged_on
														&& isset($_REQUEST['edit_folder_id'])
														&& isset($_REQUEST['edit_folder_name'])
														&& isset($_REQUEST['edit_folder_access'])
														&& isset($_REQUEST['edit_folder_keywords'])
														&& isset($_REQUEST['edit_folder_parent'])
													) {
														$edit_folder_id = (int) $_REQUEST['edit_folder_id'];
														$edit_owner = db_query("select owner from file_folders where ident = $edit_folder_id");
														if ($edit_owner[0]->owner == $_SESSION['userid']) {
															$edit_parent_id = (int) $_REQUEST['edit_folder_parent'];
															db_query("update file_folders
																			set name = '".addslashes($_REQUEST['edit_folder_name'])."',
																				access = '".addslashes($_REQUEST['edit_folder_access'])."',
																				parent = ".$edit_parent_id."
																				where ident = $edit_folder_id");
															db_query("delete from tags where tagtype = 'folder' and ref = $edit_folder_id");
															if ($_REQUEST['edit_folder_keywords'] != "") {
																$edit_value = $_REQUEST['edit_folder_keywords'];
																$edit_value = str_replace("\n","",$edit_value);
																$edit_value = str_replace("\r","",$edit_value);
																$edit_keyword_list = explode(",",$edit_value);
																sort($edit_keyword_list);
																if (sizeof($edit_keyword_list) > 0) {
																	foreach($edit_keyword_list as $key => $list_item) {
																		$list_item = addslashes(trim($list_item));
																		db_query("insert into tags set tagtype = 'folder', access = '".addslashes($_REQUEST['edit_folder_access'])."', tag = '$list_item', ref = $edit_folder_id, owner = " . $_SESSION['userid']);
																	}
																}
															}
															$messages[] = "The folder was edited.";
														}
													}
												break;
				// Delete a folder
					case "delete_folder":		if (
														logged_on
														&& isset($_REQUEST['delete_folder_id'])
													) {
														$id = (int) $_REQUEST['delete_folder_id'];
														if ($id > -1) {
															$folder = db_query("select * from file_folders where ident = $id and (owner = " . $_SESSION['userid'] . " or files_owner = " . $_SESSION['userid'] . ")");
															if (sizeof($folder) > 0) {
																$folder = $folder[0];
																$files_username = run("users:id_to_name", $folder->files_owner);
																db_query("update file_folders set parent = " . $folder->parent . " where parent = $id");
																db_query("update files set folder = " . $folder->parent . " where folder = $id");
																db_query("delete from file_folders where ident = $id");
																db_query("delete from tags where tagtype = 'folder' and ref = $id");
																global $redirect_url;
																$redirect_url = url . $files_username . "/files/";
																if ($folder->parent > -1) {
																	$redirect_url .= $folder->parent;
																}
																define('redirect_url', $redirect_url);
																$messages[] = "The folder was deleted.";
															}
														}
													}
													break;
				// Delete a file
					case "delete_file":		if (
														logged_on
														&& isset($_REQUEST['delete_file_id'])
													) {
														$id = (int) $_REQUEST['delete_file_id'];
														if ($id > -1) {
															$file = db_query("select * from files where ident = $id and (owner = " . $_SESSION['userid'] . " or files_owner = " . $_SESSION['userid'] . ")");
															if (sizeof($file) > 0) {
																$file = $file[0];
																$files_username = run("users:id_to_name", $file->files_owner);
																@unlink(stripslashes($file->location));
																db_query("delete from files where ident = $id");
																db_query("delete from tags where tagtype = 'file' and ref = $id");
																$redirect_url = url . $files_username . "/files/";
																if ($file->folder > -1) {
																	$redirect_url .= $file->folder;
																}
																define('redirect_url', $redirect_url);
																$messages[] = "The file was deleted.";
															}
														}
													}
													break;
				
			}
			
		}
	
?>