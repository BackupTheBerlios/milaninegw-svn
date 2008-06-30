<?php
	/*global $fix_page_owner;
	if(!isset($fix_page_owner))
		{
			$X = db_query("select ident from members_users where username = (select account_lid from phpgw_accounts where account_id = $page_owner ) order by ident desc limit 1");
			$fix_page_owner = (int)$X[0]->ident;
		}*/
	global $page_owner;
	if (sizeof($parameter) >= 2 && $parameter[1] != 'linkedin' && $parameter[1] != "activeInactive") 
	{
		$value = db_query("select * from ".tbl_prefix."profile_data where name = '".$parameter[1]."' and owner = '". $page_owner ."'");
		$value = $value[0];

		if( isset($parameter["pValue"]) ) 
		{//veb::we got error on the form.
			$value->value = $parameter["pValue"];
			$value->access = $parameter["pAccess"];
		}
		
		if(!(trim($value->value) == "" && $parameter[-1] == -1))
		{
		#start render field to edit
		$name = <<< END
					<label for="{$parameter[1]}">
						<b>{$parameter[0]}</b>
END;
		
		if (isset($parameter[3]) && $parameter[3] != "") {
			$name .= "<br /><i>" . $parameter[3] . "</i>";
		}
		
		//required message.
		if( isset($parameter["Valid"]) && isset($parameter["Valid"]["showReq"]) && $parameter["Valid"]["showReq"] == true)
			$name .= "<br /><font color='red'>" .$parameter["Valid"]["required_message"] . "</font>";
		//validation message.
		if( isset($parameter["Valid"]) && isset($parameter["Valid"]["showFunValid"]) && $parameter["Valid"]["showFunValid"] == true)
			$name .= "<br /><font color='red'>" .$parameter["Valid"]["validFun_message"] . "</font>";
			
		//$fields[$key]["Valid"]["showReq"] = true;
		//if(required_message
		$name .= <<< END
					</label>
END;
	
		if (sizeof($parameter) < 3) {
			$parameter[2] = "text";
		}
		
		$column1 = run("display:input_field",array("profiledetails[" . $parameter[1] . "]",$value->value,$parameter[2],$parameter[1],$value->ident,$page_owner, "fullParam"=>$parameter));
		
		$column2 = "";
		if($parameter[2] != "HR")
		{
			$column2 = "<label>Access level:";
			$column2 .= run("display:access_level_select",array("profileaccess[".$parameter[1] . "]",$value->access)) . "</label>";
		}
		
		$run_result .= run("templates:draw", array(
							'context' => 'databox',
							'name' => $name,
							'column1' => $column1,
							'column2' => $column2
						)
						);
		#end render field to edit
		}
	}
?>