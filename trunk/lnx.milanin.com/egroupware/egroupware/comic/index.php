<?php
    /**************************************************************************\
    * eGroupWare - Daily Comics                                                *
    * http://www.egroupware.org                                                *
    * This file written by Sam Wynn <neotexan@wynnsite.com>                    *
    * --------------------------------------------                             *
    *  This program is free software; you can redistribute it and/or modify it *
    *  under the terms of the GNU General Public License as published by the   *
    *  Free Software Foundation; either version 2 of the License, or (at your  *
    *  option) any later version.                                              *
    \**************************************************************************/

    /* $Id: index.php,v 1.12 2004/06/19 22:46:27 wolffc Exp $ */
{
    $GLOBALS['phpgw_info']['flags'] = array("currentapp" => "comic",
                                 "enable_nextmatchs_class" => True,
                                 "enable_network_class" => True);

    include("../header.inc.php");
    
    $GLOBALS['phpgw']->db->query("select * from phpgw_comic "
                      ."WHERE comic_owner='"
                      .$phpgw_info['user']['account_id']."'");

    if ($GLOBALS['phpgw']->db->num_rows() == 0)
    {
        $GLOBALS['phpgw']->db->query("insert into phpgw_comic (comic_owner,comic_list) values ".
                          "('".$phpgw_info['user']['account_id']."','0')");
        $GLOBALS['phpgw']->db->query("select * from phpgw_comic "
                          ."WHERE comic_owner='"
                          .$phpgw_info['user']['account_id']."'");
    }

    $GLOBALS['phpgw']->db->next_record();

    $comic_list     = explode(":", $GLOBALS['phpgw']->db->f("comic_list"));
    $comic_scale    = $GLOBALS['phpgw']->db->f("comic_scale");
    $comic_perpage  = $GLOBALS['phpgw']->db->f("comic_perpage");
    $user_censorlvl = $GLOBALS['phpgw']->db->f("comic_censorlvl");
    
    $template_id    = $GLOBALS['phpgw']->db->f("comic_template");
    
	$page_number = intval(get_var('page_number', array('GET','POST')));
	$start = intval(get_var('start', array('GET','POST')));

    comic_display($comic_list, $comic_scale, $comic_perpage, $user_censorlvl,
                  $start, &$comic_left_c, &$comic_right_c, &$comic_center_c,
                  &$matchs_c);

    /**************************************************************************
     * determine the output template
     *************************************************************************/
    $template_format     = sprintf("format%02d", $template_id);
		  if (!file_exists($GLOBALS['phpgw']->template->root
                      ."/".$template_format.".comic.tpl"))
    {
        $template_format = "format00";
    }
        
    /**************************************************************************
     * pull it all together
     *************************************************************************/
    $body_tpl = $GLOBALS['phpgw']->template;
    $body_tpl->set_unknowns("remove");
    $body_tpl->set_file(body, $template_format.".comic.tpl");
    $body_tpl->set_var(array(title        => lang('eGroupWare Daily Comics'),
                             matchs       => $matchs_c,
                             comic_left   => $comic_left_c,
                             comic_center => $comic_center_c,
                             comic_right  => $comic_right_c));
    $body_tpl->parse(BODY, "body");
    $body_tpl->p("BODY");
        
    $GLOBALS['phpgw']->common->phpgw_footer();
}

?>
