<?php
// Action parser for profiles
global $page_owner, $pVal;
$pVal = new ProfileValidation();
if ($pVal->IsPost())
{
	$pVal->ValidatePostedData(&$data['profile:details']);
	if($pVal->isValid )
	{
		//start send data to DB
		foreach($_POST['profiledetails'] as $field => $value) 
				{
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
				//end send data to db.
				
		if(!$pVal->stayOnThisPage)
		{
			$profile_username = run("users:id_to_name",$page_owner);
			header("Location: ".url.$profile_username); 
			exit;
		}
	}
}

/*****************************************************************************************************/
class ProfileValidation
{
	var $isValid;
	
	function ProfileValidation()
	{
		$this->isValid = true;
		$this->stayOnThisPage = true;
	}
	
	function IsPost()
	{
		return isset($_POST['action']) && isset($_POST['profiledetails']) && $_POST['action'] == "profile:edit" && logged_on && run("permissions:check", "profile");
	}
	
	function ValidatePostedData(&$fields)
	{
		foreach($fields as $key => $field)
		{
			$pValue = $_POST['profiledetails'][$field[1]];
			$fields[$key]["pAccess"] = $_POST['profileaccess'][$field[1]];
			$fields[$key]["pValue"] = $pValue;
			if( isset($field["Valid"]) )
			{
				$rules = &$field["Valid"];
				if($rules["required"] === true && trim($pValue) == $rules["invalid"])
				{
					$fields[$key]["Valid"]["showReq"] = true;
					$this->isValid = false;
				}
				else
				{

				}
			}
		}
	}
}
			

				

?>