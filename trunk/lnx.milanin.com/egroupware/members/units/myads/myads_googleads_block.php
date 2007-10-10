<?php

		global $page_owner;
		$profile_id = $page_owner;
		

		$url=url;
                $google_ad_client=db_query("select value from ".tbl_prefix."profile_data where name='google_ad_client' and owner='".$profile_id."'");
                $messages[]="<pre>".print_r($google_ad_client,1)."</pre>";
                $messages[]="select value from ".tbl_prefix."profile_data where name='google_ad_client' and owner='".$profile_id."'";
                if (count($google_ad_client)>0){
                  $google_ad_client=$google_ad_client[0]->value;
                  $title = "My ads";
                }else{
                  $google_ad_client=google_ad_client;
                  $title = sitename." ads";
		}
                
                if ($google_ad_client!="0"){
                  $messages[]="google_ad_client = ".google_ad_client.",".$google_ad_client;
                  $body = <<< END
                  <script type="text/javascript"><!--
                  google_ad_client = "{$google_ad_client}";
                  google_ad_width = 160;
                  google_ad_height = 600;
                  google_ad_format = "160x600_as";
                  google_ad_type = "text_image";
                  google_ad_channel = "";
                  //--></script>
                  
                  <script type="text/javascript"
                    src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
                  </script>
END;

                  $run_result .= "<div>";
                  $run_result .= run("templates:draw", array(
						'context' => 'infobox',
						'name' => $title,
						'contents' => $body
						)
					);
                  $run_result .= "</div>";
                 }
                 else
                 {
                  $run_result.="";
                 }
