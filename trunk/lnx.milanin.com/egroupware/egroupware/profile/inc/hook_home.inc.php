<?php
		echo '<!-- start profile hook -->';
		$obj = createobject('profile.uiprofile');
                if ($obj->percentage<30){
                	$GLOBALS['phpgw']->translation->add_app('profile');
			$obj->info(True);
                }
                echo '<!-- end profile hook -->';
?>
