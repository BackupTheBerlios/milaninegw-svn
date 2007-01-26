<?php
	/**************************************************************************\
	* eGroupWare SiteMgr - Web Content Management                              *
	* http://www.egroupware.org                                                *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: class.module_hello.inc.php,v 1.3 2004/02/10 14:56:33 ralfbecker Exp $ */

class module_blogs extends Module 
{
	function module_blogs()
	{
		$this->arguments = array(
			'num' => array(
				'type' => 'textfield', 
				'label' => lang('The number of posts to show')
                        ),
                        'base_url' => array(
				'type' => 'textfield', 
				'label' => lang('ELGG base URL')
                        ),
                        'posts_table' => array(
				'type' => 'textfield', 
				'label' => lang('Posts table')
			),
                        'users_table' => array(
				'type' => 'textfield', 
				'label' => lang('Users table')
			),
                        'icons_table' => array(
				'type' => 'textfield', 
				'label' => lang('Users icons table')
			)
		);
		//$this->post = array('name' => array('type' => 'textfield'));
		//$this->session = array('name');
		$this->title = lang('MilanIN Bloggers');
		$this->description = lang('This is a module to show bloggers posts as news');
                $this->db = $GLOBALS['phpgw']->db;
	}

	function get_content(&$arguments,$properties) 
	{
		$this->template = Createobject('phpgwapi.Template',$this->find_template_dir());
		$this->template->set_file('blogs','blogsblock.tpl');
                //Common langs
                $this->template->set_var(
                 array(
                        "blogged_by"=>lang('Blogged by'),
                        "read_more"=>lang('Read more'),
                        "more_blogs"=>lang('Other posts')
                      )
                );
                $this->template->set_block('blogs','BlogsBlock','blogsitem');
                $this->template->set_block('blogs','RssBlock','rsshandle');
                $this->template->set_var('rsslink',$arguments['base_url']."weblog/rss");
		$this->template->parse('rsshandle','RssBlock');
                $this->db->query(
                "select posts.*, users.username , users.name, icons.filename from `".
                $arguments['posts_table']."` posts left join `".
                $arguments['users_table']."` users on posts.owner=users.ident left join `".
                $arguments['icons_table']."` icons on users.icon=icons.ident ".
                "where access = 'PUBLIC' order by posted desc"
                ,_LINE_,_FILE_,0, $arguments['num']);
                
                while($this->db->next_record()){
                  $row = $this->db->row();
                  $this->template->set_var(
                    array(
                         'post_title'=>$row['title'],
                         'post_url'=>$arguments['base_url'].$row['username']."/weblog/".$row['ident'].".html",
                         'author_pic'=>$arguments['base_url']."_icons/data/".$row['filename'],
                         'post_author_url'=>$arguments['base_url'].$row['username'],
                         'post_author'=>$row['name'],
                         'post_date'=>date("F j, Y, g:i a",$row['posted']),
                         'post_content'=>strip_tags($row['body'],"<p></p><a></a>"),
                         'author_blog_url'=>$arguments['base_url'].$row['username']."/weblog"
                      )
                  );
                 $this->template->parse('blogsitem','BlogsBlock',True);
                }
                return $this->template->parse('out','blogs');
	}

}
