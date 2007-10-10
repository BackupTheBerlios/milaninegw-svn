<?php

	// Database library functions

	// Generalised function to query the database
	// (returns an array of result rows)
		function db_query($sql_query) {
			global $querynum;
			global $querycache;
			
			/*if (isset($querycache[$sql_query])) {
				return $querycache[$sql_query];
			}*/
			
			if (!isset($querynum)) {
				$querynum = 1;
			} else {
				$querynum++;
			}
			global $run_context;
			// echo "<b>" . $run_context . "</b>&nbsp;&nbsp;" . $sql_query . "<br />";
			if ($sql_query != "") {
//				 echo "<!-- $sql_query -->\n\r";
                                //preg_match_all('/(?:from|into|join|update)\s+(\w+?)\s/i',$sql_query,$tables);
                                //echo "<pre>".$sql_query."\n".print_r($tables,1)."\n";
                                //foreach ($tables[1] as $tbl){
                                  //echo "replacing ".$tbl." with ".tbl_prefix.$tbl."\n";
                                  //$sql_query=preg_replace("/$tbl/",tbl_prefix.$tbl,$sql_query);
                                //}
                                //echo "launching: ".$sql_query."\n</pre>";
				if ($result = @mysql_query($sql_query)) {
					$data = array();
					if (!is_bool($result)) {
						while ($row = @mysql_fetch_object($result)){
							$data[] = $row;
						}
					}
					$querycache[$sql_query] = $data;
					return $data;
				} else {
					// echo '<!--'.$sql_query . " :: " . @mysql_error() . " -->\n";
					$querycache[$sql_query] = FALSE;
					return FALSE;
				}
			} else {
				return FALSE;
			}
		}
	
	// Rows affected by the last MySQL transaction
		function db_affected_rows() {
			return @mysql_affected_rows();
		}
		
	// Returns the ID of the last MySQL transaction
		function db_id() {
			return @mysql_insert_id();
		}

?>
