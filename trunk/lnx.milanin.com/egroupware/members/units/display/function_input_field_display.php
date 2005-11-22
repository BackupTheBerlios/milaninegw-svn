<?php

	// Displays different HTML depending on input field type

	/*
	
		$parameter(
		
						0 => input name to display (for forms etc)
						1 => data
						2 => type of input field
						3 => reference name (for tag fields and so on)
						4 => ID number (if any)
						5 => Owner
		
					)
	
	*/
	
		if (isset($parameter) && sizeof($parameter) > 2) {
			
			if (!isset($parameter[4])) {
				$parameter[4] = -1;
			}
			
			if (!isset($parameter[5])) {
				$parameter[5] = $_SESSION['userid'];
			}
			
			switch($parameter[2]) {
				
				case "text":
						$run_result .= "<input type=\"text\" name=\"".$parameter[0]."\" value=\"".htmlentities(stripslashes($parameter[1]))."\" style=\"width: 95%\" id=\"".$parameter[0]."\" />";
						break;
				case "password":
						$run_result .= "<input type=\"password\" name=\"".$parameter[0]."\" value=\"".htmlentities(stripslashes($parameter[1]))."\" style=\"width: 95%\" id=\"".$parameter[0]."\" />";
						break;
				
				case "title_selectbox":


    $run_result .= "<select name=\"".$parameter[0]."\" onload=\"getInvitationMsg()\" onchange=\"getInvitationMsg()\"  style=\"width: 95%\" id=\"".$parameter[0]."\" >";
    
    $risultato = mysql_query("SELECT * FROM `template_elements` where `template_id` = ".$parameter[3]." order by name asc");
    //get sql data to js
    $run_result .= <<< END
   <script language="JavaScript" type="text/javascript">
				<!--
				  var template_id = new Array();
          var template_name = new Array();
          var template_content = new Array();
          var template_count = 0
          template_id[0]=0;
				  template_name[0]="";
				  template_content[0]="";
				-->
			</script>
END;

while ($riga = mysql_fetch_object($risultato)) {
   $run_result .= <<< END
   <script language="JavaScript" type="text/javascript">
				<!--
				template_id[template_count]=template_count;
				template_name[template_count]="$riga->name";
				template_content[template_count]="$riga->content";
				template_count++;
				-->
			</script>
END;
 // create options  
   $run_result .= $riga->name;
}

mysql_free_result($risultato);

$run_result .= "</select>";
//js to view title-body msg
$run_result .= <<< END
<script language="JavaScript" type="text/javascript">
				<!--
				function getInvitationMsg()
				{
				  for (var iSelect = 0; iSelect < document.invite_form.invite_title.length; iSelect++) {
				  if (document.invite_form.invite_title[iSelect].selected == true)
				   break;
				  }
				  document.invite_form.invite_text.value=template_content[iSelect];
				  return true;
				}
				-->
			</script>
END;
break;	
						
							
						
				case "mediumtext":
						$run_result .= "<textarea name=\"".$parameter[0]."\" id=\"".$parameter[0]."\" style=\"width: 95%; height: 100px\">".htmlentities(stripslashes($parameter[1]))."</textarea>";
						break;
				case "keywords":
						/*
						$keywords = stripslashes($parameter[1]);
						preg_match_all("/\[\[([A-Za-z0-9 ]+)\]\]/i",$keywords,$keyword_list);
						$keyword_list = $keyword_list[1];
						$keywords = "";
						if (sizeof($keyword_list) > 0) {
							sort($keyword_list);
							foreach($keyword_list as $key => $list_item) {
								$keywords .= $list_item;
								if ($key < sizeof($keyword_list) - 1) {
									$keywords .= ", ";
								}
							}
						}
						$parameter[1] = $keywords;
						*/
						$tags = db_query("select * from tags where tagtype = '".$parameter[3]."' and ref = '".$parameter[4]."' and owner = " . $parameter[5] . " order by tag asc");
						$keywords = "";
						if (sizeof($tags) > 0) {
							foreach($tags as $key => $tag) {
								if ($key > 0) {
									$keywords .= ", ";
								}
								$keywords .= stripslashes($tag->tag);
							}
						}
						$parameter[1] = $keywords;
						// $parameter[1] = var_export($parameter,true);
						$run_result .= "<textarea name=\"".$parameter[0]."\" id=\"".$parameter[0]."\" style=\"width: 95%; height: 100px\">".htmlentities(stripslashes($parameter[1]))."</textarea>";
						break;
				case "longtext":
						$run_result .= "<textarea name=\"".$parameter[0]."\" id=\"".$parameter[0]."\" style=\"width: 95%; height: 200px\">".htmlentities(stripslashes($parameter[1]))."</textarea>";
						break;
                                case "evenlongertext":
						$run_result .= "<textarea name=\"".$parameter[0]."\" id=\"".$parameter[0]."\" style=\"width: 95%; height: 500px\">".htmlentities(stripslashes($parameter[1]))."</textarea>";
						break;
				case "richtext":
						// Rich text editor:
						$run_result .= <<< END
							<script language="JavaScript" type="text/javascript">
							<!--
							function submitForm() {
								//make sure hidden and iframe values are in sync before submitting form
								//to sync only 1 rte, use updateRTE(rte)
								//to sync all rtes, use updateRTEs
								updateRTE('<?=$parameter[0]?>');
								//updateRTEs();
								//alert("rte1 = " + document.elggform.<?=$parameter[0]?>.value);
								
								//change the following line to true to submit form
								return true;
							}
END;
						$content = RTESafe(stripslashes($parameter[1]));
						$run_result .= <<< END
							//Usage: initRTE(imagesPath, includesPath, cssFile)
								initRTE("/units/display/rtfedit/images/", "/units/display/rtfedit/", "/units/display/rtfedit/rte.css");
								</script>
								<noscript><p><b>Javascript must be enabled to use this form.</b></p></noscript>
								<script language="JavaScript" type="text/javascript">
								<!--
								writeRichText('<?=$parameter[0]?>', '<?=$content?>', 220, 200, true, false);
							//-->
							</script>
END;
						break;
				case "blank":
						$run_result .= "<input type=\"hidden\" name=\"".$parameter[0]."\" value=\"blank\" id=\"".$parameter[0]."\" />";
						break;
				case "web":
				case "email":
				case "aim":
				case "msn":
				case "skype":
				case "icq":
						$run_result .= "<input type=\"text\" name=\"".$parameter[0]."\" value=\"".htmlentities(stripslashes($parameter[1]))."\" style=\"width: 95%\" id=\"".$parameter[0]."\" />";
						break;
						
			}
			
		}
	
?>