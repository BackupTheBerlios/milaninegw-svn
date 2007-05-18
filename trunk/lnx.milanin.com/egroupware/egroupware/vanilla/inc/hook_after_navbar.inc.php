<?
	if($GLOBALS['phpgw_info']['flags']['currentapp'] != 'profile' &&
		$GLOBALS['phpgw_info']['flags']['currentapp'] != 'welcome')
	{
        	$GLOBALS['phpgw']->translation->add_app('profile');
		$obj = createobject('profile.soprofile');
                $percentage=$obj->get_relative_percentage();
                        if ($percentage<70 && $percentage >30){
                        	echo '<center><a href="profile/"><span style="color:yellow">'.
                                lang('your profile is').' '.
                                $percentage."% ".
                                lang('complete').'. '.
                                lang('click here')." ".
                                lang('for more info').
                                "</span></a></center>";
                        }elseif ($percentage <30){
                        	echo '<center><a href="profile/"><span style="color:red">'.
                                lang('your profile is').' '.
                                $percentage."% ".
                                lang('complete').'. '.
                                lang('click here')." ".
                                lang('for more info').
                                "</span></a></center>";
                        }
		
	}
?>