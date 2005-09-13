<?php
  // NOTE: I would like to get the E-Mail address's to be a link to compose with a subject of the
  //       current app.

  // I will make this look better when I have more time to create a better layout.

	function about_app()
	{
		global $HTTP_USER_AGENT;

		$browser= 'UNKNOWN';

		if (ereg('MSIE',$HTTP_USER_AGENT))
		{
			$browser = 'MSIE';
		}
		elseif (ereg('Mozilla',$HTTP_USER_AGENT))
		{
			$browser = 'NETSCAPE';
		}
		else
		{
			$browser = 'UNKNOWN';
		}

		$tpl = new Template($GLOBALS['phpgw']->common->get_tpl_dir('bookmarks'));

		$tpl->set_file(array(
			'body' => 'about.tpl',
			'msie' => 'faq.msie.quik-mark.tpl',
			'ns'   => 'faq.ns.quik-mark.tpl'
		));
		$tpl->set_var('version','0.0.0');
		if ($browser == 'MSIE')
		{
			$tpl->parse('browser_quik_mark','msie',True);
		}
		else
		{
			$tpl->parse('browser_quik_mark','ns',True);
		}
		$tpl->set_var('user_agent',$HTTP_USER_AGENT);
		$tpl->set_var('create_url',$GLOBALS['phpgw_info']['server']['webserver_url'] . '/login.php');

		return $tpl->parse('out','body');
	}
