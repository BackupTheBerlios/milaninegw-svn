<?php

	// Initial profile data
		$data['profile:details'][] = array("Name", "select account_firstname value from `phpgw_accounts` where account_id=".$_SESSION["userid"], "GW_label", "Readonly");
		$data['profile:details'][] = array("Last name","select account_lastname value from `phpgw_accounts` where account_id=".$_SESSION["userid"], "GW_label", "Readonly" );
		$data['profile:details'][] = array("Which professional profile better describes you?","prof_profile","GW_dropdown");
		$data['profile:details'][] = array("LinkedIn profile","linkedin","linkedin","The URL to your LinkedIn Profile page");
		$data['profile:details'][] = array("Phone Number","phone","text");
		$data['profile:details'][] = array("Email address","emailaddress","email");
		$data['profile:details'][] = array("Reason for requesting Club Membership","requestReason","mediumtext","");
		$data['profile:details'][] = array("How did you know about this club?","how_did_u","text","");
		$data['profile:details'][] = array("Sex","sex","GW_dropdown");
		//languages
		//birthdate
		//country of residence
		$data['profile:details'][] = array("City of residence","residence_city","text","");
		$data['profile:details'][] = array("Your academic degree","ac_degree","GW_dropdown");
		$data['profile:details'][] = array("Favorite sport","favorite_sport","GW_dropdown");
		$data['profile:details'][] = array("Industry","industries","GW_GroupCheckBox");
		$data['profile:details'][] = array("Profession","professions","GW_GroupCheckBox");
		$data['profile:details'][] = array("occ_area","occ_areas","GW_GroupCheckBox");
		$data['profile:details'][] = array("Interests","interests","GW_GroupCheckBox");
		
		$data['profile:details'][] = array("","","HR");
		/*
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
		$data['profile:details'][] = array("Interests","interests","keywords","Separated with commas.");
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
		*/
?>