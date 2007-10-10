<?
global $profile_id;
		if (isset($_GET['profile_name'])) {
			$profile_id = (int) run("users:name_to_id", $_GET['profile_name']);
		} else if (isset($_GET['profile_id'])) {
			$profile_id = (int) $_GET['profile_id'];
		} else if (isset($_POST['profile_id'])) {
			$profile_id = (int) $_POST['profileid'];
		} else if (isset($_SESSION['userid'])) {
			$profile_id = (int) $_SESSION['userid'];
		} else {
			$profile_id = -1;
		}

		global $page_owner;
		
// 		$page_owner = $profile_id;
		
		global $page_userid;
		
                
                if (!isset($_SESSION['user_info_cache'][$page_owner])) {
                  $info = db_query("select * from ".tbl_prefix."users where ident = $page_owner");
                  $_SESSION['user_info_cache'][$page_owner] = $info[0];
                  $info = $info[0];
                }
                $info = $_SESSION['user_info_cache'][$page_owner];

$run_result='<tr><td style="background-image: url('.url.'_templates/default/clubincall.png);" id="clubincall_td" class="clubincall_td"><div id="clubincall_wrapper" class="clubincall_wrapper" ><script type="text/javascript"
                    src="'.url.'units/clubincall/clubincall.js">
                  </script>
                  <script type="text/javascript">var url="'.url.'";</script>
<a href="javascript:clubincall('.$page_owner.',\''.url.'\')">Call '.$info->name.'</a>

</div></td></tr>';
// <pre>'.print_r($_SESSION,1).'</pre>
// <pre>'.print_r($_COOKIE,1).'</pre>
?>
