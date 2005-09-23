<?php
  /**************************************************************************\
  * eGroupWare - E-Mail                                                      *
  * http://www.egroupware.org                                                *
  * Based on Aeromail by Mark Cushman <mark@cushman.net>                     *
  *          http://the.cushman.net/                                         *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id: view_image.php,v 1.18 2004/01/27 15:45:19 reinerj Exp $ */

	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp' => 'email',
		'enable_network_class' => True, 
		'noheader' => True,
		'nonavbar' => True
	);
	include('../header.inc.php');
	/*
	if (isset($GLOBALS['phpgw_info']['flags']['newsmode']) && $GLOBALS['phpgw_info']['flags']['newsmode'])
	{
		$GLOBALS['phpgw']->common->read_preferences('nntp');
	}
	@set_time_limit(0);

	echo 'Mailbox = '.$mailbox.'<br>'."\n";
	echo 'Mailbox = '.$GLOBALS['phpgw']->msg->mailsvr_stream.'<br>'."\n";
	echo 'Msgnum = '.$m.'<br>'."\n";
	echo 'Part Number = '.$p.'<br>'."\n";
	echo 'Subtype = '.$s.'<br>'."\n";
	*/
	//$data = $GLOBALS['phpgw']->dcom->fetchbody($GLOBALS['phpgw']->msg->mailsvr_stream, $m, $p);
	$data = $GLOBALS['phpgw']->msg->phpgw_fetchbody($p);
	//$picture = $GLOBALS['phpgw']->dcom->base64($data);
	$picture = $GLOBALS['phpgw']->msg->de_base64($data);

	//  echo strlen($picture)."<br>\n";
	//  echo $data;

	Header('Content-length: '.strlen($picture));
	Header('Content-type: image/'.$s);
	Header('Content-disposition: attachment; filename="'.urldecode($n).'"');
	echo $picture;
	flush();

	// IS THIS FILE EVER USED ANYMORE?
	if (is_object($GLOBALS['phpgw']->msg))
	{
		$terminate = True;
	}
	else
	{
		$terminate = False;
	}
	
	if ($terminate == True)
	{
		// close down ALL mailserver streams
		$GLOBALS['phpgw']->msg->end_request();
		// destroy the object
		$GLOBALS['phpgw']->msg = '';
		unset($GLOBALS['phpgw']->msg);
	}
	// shut down this transaction
	$GLOBALS['phpgw']->common->phpgw_exit(False);
?>
