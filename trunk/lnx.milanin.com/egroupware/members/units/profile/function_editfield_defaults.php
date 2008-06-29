<?php
		$useOnlyOld = false;
		if($useOnlyOld) //&& $GLOBALS["profile_id"] != "654"
		{
				$data['profile:details'][] = array("Who am I?","biography","longtext","A short introduction to you.");
		
				$data['profile:details'][] = array("Postal address","postaladdress","mediumtext");
				$data['profile:details'][] = array("Email address","emailaddress","email");
				$data['profile:details'][] = array("Work telephone","workphone","text");
				$data['profile:details'][] = array("Home telephone","homephone","text");
				$data['profile:details'][] = array("Mobile telephone","mobphone","text");
				$data['profile:details'][] = array("Official website address","workweb","web","The URL to your official website, if you have one.");
				$data['profile:details'][] = array("Personal website address","personalweb","web","The URL to your personal website, if you have one.");

				$data['profile:details'][] = array("ICQ number","icq","icq");
				$data['profile:details'][] = array("MSN chat","msn","msn");
				$data['profile:details'][] = array("AIM screenname","aim","aim");
				$data['profile:details'][] = array("Skype username","skype","skype");
				$data['profile:details'][] = array("Jabber username","jabber","text");
				$data['profile:details'][] = array("Interests","interests","keywords","Separated with commas.");
				$data['profile:details'][] = array("Likes","likes","keywords","Separated with commas.");
				$data['profile:details'][] = array("Dislikes","dislikes","keywords","Separated with commas.");
				$data['profile:details'][] = array("Occupation","occupation","text");
				$data['profile:details'][] = array("Industry","industry","text");
				$data['profile:details'][] = array("Company / Institution","organisation","text");
				$data['profile:details'][] = array("Job Title","jobtitle","text");
				$data['profile:details'][] = array("Job Description","jobdescription","text");
				$data['profile:details'][] = array("Career Goals","careergoals","longtext","Freeform: let colleagues and potential employers know what you'd like to get out of your career.");
				$data['profile:details'][] = array("Level of Education","educationlevel","text");
				$data['profile:details'][] = array("High School","highschool","text");
				$data['profile:details'][] = array("University / College","university","text");
				$data['profile:details'][] = array("Degree","universitydegree","text");
				$data['profile:details'][] = array("Main Skills","skills","keywords","Separated with commas.");
		}
		else
		{
			$indicator = ( isset($GLOBALS["argv"][0]) && substr($GLOBALS["argv"][0], 0, 13) == "profile_name=" ) ? "" : '&nbsp;(<font color="red">*</font>)';
			$requiredMessage = "This field is required.";
		// Initial profile data 	-1 ~ "ReadOnly"=> 'true' or 'false'

		$data['profile:details'][] = array(-1 => false, "Which professional profile better describes you?$indicator", "prof_profile", "GW_dropdown",
											"Valid"=>array("required" => true, "invalid"=>-1, "required_message"=>$requiredMessage) );
		$data['profile:details'][] = array(-1 => true, "LinkedIn profile","linkedin","linkedin","The URL to your LinkedIn Profile page");
		$data['profile:details'][] = array(-1 => false, "Phone Number","phone","text");
		$data['profile:details'][] = array(-1 => true, "Email address","emailaddress","email");
		$data['profile:details'][] = array(-1 => true, "How did you know about this club?","how_did_u","GW_dropdown","");
		$data['profile:details'][] = array(-1 => false, "Sex$indicator","sex","GW_dropdown",
									"Valid"=>array("required" => true, "invalid"=>-1, "required_message"=>$requiredMessage) );
		
		$data['profile:details'][] = array(-1 => false, "Main language$indicator","languages","GW_dropdown",
									"source"=>array("en"=>"English", "it"=>"Italian"), 
									"Valid"=>array("required" => true, "invalid"=>-1, "required_message"=>$requiredMessage) );

		$data['profile:details'][] = array(-1 => false, "Year of birth (YYYY)", "birthDate", "text",
									"Valid"=>array("required" => false, "invalid"=>"", "required_message"=>$requiredMessage,
												   "validFun"=>"IsValidBirthDate", "validFun_message"=>"Birth date is invalid") );

		$data['profile:details'][] = array(-1 => false, "Country of residence$indicator","residence_country","GW_dropdown","", "use_key" =>false, 
									"Valid"=>array("required" => true, "invalid"=>-1, "required_message"=>$requiredMessage));
									
		$data['profile:details'][] = array(-1 => false, "City of residence","residence_city","text","");
		$data['profile:details'][] = array(-1 => false, "Your academic degree$indicator","ac_degree","GW_dropdown",
									"Valid"=>array("required" => true, "invalid"=>-1, "required_message"=>$requiredMessage));
		
		$data['profile:details'][] = array(-1 => false, "Industry$indicator", "industries", "GW_GroupCheckBox", "type"=>"radio",
									"Valid"=>array("required" => true, "invalid"=>"", "required_message"=>$requiredMessage) );
		
		$data['profile:details'][] = array(-1 => false, "Occupation area$indicator","occ_areas","GW_GroupCheckBox", "type"=>"radio",
									"Valid"=>array("required" => true, "invalid"=>"", "required_message"=>$requiredMessage));
		
		$data['profile:details'][] = array(-1 => false, "Base interests","interestsBase", "GW_GroupCheckBox", "type"=>"chk");
		$data['profile:details'][] = array(-1 => false, "Favorite sport","favorite_sport","GW_GroupCheckBox", "type"=>"chk");
		
		$data['profile:details'][] = array("","","HR");
		$data['profile:details'][] = array("Interests","interests","keywords","Separated with commas.");

		$data['profile:details'][] = array("Who am I?","biography","evenlongertext","A short introduction to you.");
		$data['profile:details'][] = array("Postal address","postaladdress","mediumtext");
		
		$data['profile:details'][] = array("Work telephone","workphone","text");
		$data['profile:details'][] = array("Home telephone","homephone","text");
		$data['profile:details'][] = array("Mobile telephone","mobphone","text");
		$data['profile:details'][] = array("Official website address","workweb","web","The URL to your official website, if you have one.");
		$data['profile:details'][] = array("Personal website address","personalweb","web","The URL to your personal website, if you have one.");
		
        $data['profile:details'][] = array("Your ".sitename." Weblog Title","weblog_title","text","The name you give to your weblog");
        $data['profile:details'][] = array("Your ".sitename." Weblog Description","weblog_description","text","How you describe your weblog");
        $data['profile:details'][] = array("Your Google Adsense ID (google_ad_client)","google_ad_client","text","The id to use for showing \"My ads\", \"0\" will disable, empty - will show the default site's ads");
		$data['profile:details'][] = array("ICQ number","icq","icq");
		$data['profile:details'][] = array("MSN chat","msn","msn");
		$data['profile:details'][] = array("AIM screenname","aim","aim");
		$data['profile:details'][] = array("Skype username","skype","skype");
		$data['profile:details'][] = array("Jabber username","jabber","text");
		
		$data['profile:details'][] = array("Likes","likes","keywords","Separated with commas.");
		$data['profile:details'][] = array("Dislikes","dislikes","keywords","Separated with commas.");
		$data['profile:details'][] = array("Occupation","occupation","text");
		$data['profile:details'][] = array("Industry","industry","text");
		$data['profile:details'][] = array("Company / Institution","organisation","text");
		$data['profile:details'][] = array("Job Title","jobtitle","text");
		$data['profile:details'][] = array("Job Description","jobdescription","text");
		$data['profile:details'][] = array("Career Goals","careergoals","evenlongertext","Freeform: let colleagues and potential employers know what you'd like to get out of your career.");
		$data['profile:details'][] = array("Level of Education","educationlevel","text");
		$data['profile:details'][] = array("High School","highschool","text");
		$data['profile:details'][] = array("University / College","university","text");
		$data['profile:details'][] = array("Degree","universitydegree","text");
		$data['profile:details'][] = array("Main Skills","skills","keywords","Separated with commas.");
		}
		
		db_query("update global_config set value='".count($data['profile:details'])."' where name='profile_data_count'");
		
		/*
		$data['profile:details'][] = array(-1 => true, "Name", "select account_firstname value from `phpgw_accounts` where account_id=".$_SESSION["userid"], "GW_label", "Readonly");
		$data['profile:details'][] = array(-1 => true, "Last name","select account_lastname value from `phpgw_accounts` where account_id=".$_SESSION["userid"], "GW_label", "Readonly" );
		$data['profile:details'][] = array(-1 => true, "Reason for requesting Club Membership","requestReason","mediumtext","");
		"select * FROM phpgw_languages where lang_id in ("."'".str_replace(",", "','", $GLOBALS['sitemgr_info']['site_languages'])."'".") ORDER BY lang_name"
		//$data['profile:details'][] = array(-1 => true, "Profession","professions","GW_GroupCheckBox");
		//$data['profile:details'][] = array("Interests","interests","keywords","Separated with commas.");
		*/
?>