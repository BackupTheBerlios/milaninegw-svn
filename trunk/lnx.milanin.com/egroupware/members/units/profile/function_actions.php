<?php

	// Action parser for profiles

		global $page_owner;
	
		if (isset($_POST['action']) && $_POST['action'] == "profile:edit" && logged_on && run("permissions:check", "profile")) {
		
			if (isset($_POST))			
			if (isset($_POST['profiledetails'])) {
				foreach($_POST['profiledetails'] as $field => $value) {
					//veb fix for multi select 
					if(is_array($value))
						$value = implode(",", $value);

					if ($value != "") 
					{
						
						$value = addslashes($value);
						$field = addslashes($field);
						$access = addslashes($_POST['profileaccess'][$field]);
						$owner = (int) $page_owner;
						if ($field=='google_ad_client'){
                                                  $access='user'.$page_owner;
                                                }
						$isReadOnly = false;
						$confItem = "";
						
						foreach($data['profile:details'] as $datatype)
						{
							if ($datatype[1] == $field)
							{
								$confItem = $datatype;
								if($confItem[-1] == true)
									$isReadOnly = true;
							}
						}
						
						db_query("delete from ".tbl_prefix."profile_data where name = '$field' and owner = '".$page_owner."' and name != 'linkedin'");
						db_query("insert into ".tbl_prefix."profile_data set name = '$field', value = '$value', access = '$access', owner = '$owner'");
						$insert_id = (int) db_id();
						
						//foreach($data['profile:details'] as $datatype) {
							if(is_array($confItem))
							{
								$datatype = $confItem;
								if ($datatype[1] == $field && $datatype[2] == "keywords") {
									db_query("delete from ".tbl_prefix."tags where tagtype = '$field' and owner = '$owner'");
									$keywords = "";
									$value = str_replace("\n","",$value);
									$value = str_replace("\r","",$value);
									$keyword_list = explode(",",$value);
									sort($keyword_list);
									if (sizeof($keyword_list) > 0) {
										foreach($keyword_list as $key => $list_item) {
											if ($key > 0) {
												$keywords .= ", ";
											}
											$keywords .= ($list_item);
											$list_item = (trim($list_item));
											db_query("insert into ".tbl_prefix."tags set tagtype = '$field', access = '$access', tag = '$list_item', ref = $insert_id, owner = $owner");
										}
									}
									$value = $keywords;
								}
							}
						//} //end foreach

					}
			
				}
				$messages[] = "Profile updated.";
			}
		exit;
		}

?>