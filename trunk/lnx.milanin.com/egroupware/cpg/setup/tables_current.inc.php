<?php
  /**************************************************************************\
  * eGroupWare - Setup                                                       *
  * http://www.eGroupWare.org                                                *
  * Created by eTemplates DB-Tools written by ralfbecker@outdoor-training.de *
  * --------------------------------------------                             *
  * This program is free software; you can redistribute it and/or modify it  *
  * under the terms of the GNU General Public License as published by the    *
  * Free Software Foundation; either version 2 of the License, or (at your   *
  * option) any later version.                                               *
  \**************************************************************************/

  /* $Id: class.db_tools.inc.php,v 1.27 2004/08/15 20:58:12 ralfbecker Exp $ */


	$phpgw_baseline = array(
		'phpgw_cpg_albums' => array(
			'fd' => array(
				'aid' => array('type' => 'auto','nullable' => False),
				'title' => array('type' => 'varchar','precision' => '255','nullable' => False),
				'description' => array('type' => 'text'),
				'visibility' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'uploads' => array('type' => 'varchar','precision' => '3','nullable' => False,'default' => 'NO'),
				'comments' => array('type' => 'varchar','precision' => '3','nullable' => False,'default' => 'YES'),
				'votes' => array('type' => 'varchar','precision' => '3','nullable' => False,'default' => 'YES'),
				'pos' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'category' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'pic_count' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'thumb' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'last_addition' => array('type' => 'timestamp','nullable' => False,'default' => 'current_timestamp'),
				'stat_uptodate' => array('type' => 'varchar','precision' => '3','nullable' => False,'default' => 'NO'),
				'keyword' => array('type' => 'varchar','precision' => '50')
			),
			'pk' => array('aid'),
			'fk' => array(),
			'ix' => array('category'),
			'uc' => array()
		),
		'phpgw_cpg_banned' => array(
			'fd' => array(
				'ban_id' => array('type' => 'auto','nullable' => False),
				'user_id' => array('type' => 'int','precision' => '4'),
				'ip_addr' => array('type' => 'varchar','precision' => '255'),
				'expiry' => array('type' => 'timestamp')
			),
			'pk' => array('ban_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_cpg_categories' => array(
			'fd' => array(
				'cid' => array('type' => 'auto','nullable' => False),
				'owner_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'name' => array('type' => 'varchar','precision' => '255','nullable' => False),
				'description' => array('type' => 'text'),
				'pos' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'parent' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'thumb' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'subcat_count' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'alb_count' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'pic_count' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'stat_uptodate' => array('type' => 'varchar','precision' => '3','nullable' => False,'default' => 'NO')
			),
			'pk' => array('cid'),
			'fk' => array(),
			'ix' => array('owner_id','pos','parent'),
			'uc' => array()
		),
		'phpgw_cpg_comments' => array(
			'fd' => array(
				'pid' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'msg_id' => array('type' => 'auto','nullable' => False),
				'msg_author' => array('type' => 'varchar','precision' => '25','nullable' => False),
				'msg_body' => array('type' => 'text'),
				'msg_date' => array('type' => 'timestamp','nullable' => False,'default' => 'current_timestamp'),
				'msg_raw_ip' => array('type' => 'varchar','precision' => '255'),
				'msg_hdr_ip' => array('type' => 'varchar','precision' => '255'),
				'author_md5_id' => array('type' => 'varchar','precision' => '32','nullable' => False),
				'author_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0')
			),
			'pk' => array('msg_id'),
			'fk' => array(),
			'ix' => array('pid'),
			'uc' => array()
		),
		'phpgw_cpg_config' => array(
			'fd' => array(
				'name' => array('type' => 'varchar','precision' => '40','nullable' => False),
				'value' => array('type' => 'varchar','precision' => '255','nullable' => False)
			),
			'pk' => array('name'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_cpg_ecards' => array(
			'fd' => array(
				'eid' => array('type' => 'auto','nullable' => False),
				'sender_name' => array('type' => 'varchar','precision' => '50','nullable' => False),
				'sender_email' => array('type' => 'text'),
				'recipient_name' => array('type' => 'varchar','precision' => '50','nullable' => False),
				'recipient_email' => array('type' => 'text'),
				'link' => array('type' => 'text'),
				'date' => array('type' => 'varchar','precision' => '255','nullable' => False),
				'sender_ip' => array('type' => 'varchar','precision' => '255','nullable' => False)
			),
			'pk' => array('eid'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_cpg_exif' => array(
			'fd' => array(
				'filename' => array('type' => 'varchar','precision' => '255','nullable' => False),
				'exifData' => array('type' => 'text')
			),
			'pk' => array('filename'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_cpg_filetypes' => array(
			'fd' => array(
				'extension' => array('type' => 'varchar','precision' => '7','nullable' => False),
				'mime' => array('type' => 'varchar','precision' => '30'),
				'content' => array('type' => 'varchar','precision' => '15')
			),
			'pk' => array('extension'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_cpg_pictures' => array(
			'fd' => array(
				'pid' => array('type' => 'auto','nullable' => False),
				'aid' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'filepath' => array('type' => 'varchar','precision' => '255','nullable' => False),
				'filename' => array('type' => 'varchar','precision' => '255','nullable' => False),
				'filesize' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'total_filesize' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'pwidth' => array('type' => 'int','precision' => '2','nullable' => False,'default' => '0'),
				'pheight' => array('type' => 'int','precision' => '2','nullable' => False,'default' => '0'),
				'hits' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'mtime' => array('type' => 'timestamp'),
				'ctime' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'owner_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'owner_name' => array('type' => 'varchar','precision' => '40','nullable' => False),
				'pic_rating' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'votes' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'title' => array('type' => 'varchar','precision' => '255','nullable' => False),
				'caption' => array('type' => 'text'),
				'keywords' => array('type' => 'varchar','precision' => '255','nullable' => False),
				'approved' => array('type' => 'varchar','precision' => '3','nullable' => False,'default' => 'NO'),
				'user1' => array('type' => 'varchar','precision' => '255','nullable' => False),
				'user2' => array('type' => 'varchar','precision' => '255','nullable' => False),
				'user3' => array('type' => 'varchar','precision' => '255','nullable' => False),
				'user4' => array('type' => 'varchar','precision' => '255','nullable' => False),
				'url_prefix' => array('type' => 'int','precision' => '1','nullable' => False,'default' => '0'),
				'randpos' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'pic_raw_ip' => array('type' => 'varchar','precision' => '255'),
				'pic_hdr_ip' => array('type' => 'varchar','precision' => '255')
			),
			'pk' => array('pid'),
			'fk' => array(),
			'ix' => array('aid','hits','owner_id','pic_rating','randpos',array('aid','approved'),
				array('title','caption','keywords','filename','options' => array('mysql' => 'FULLTEXT'))),
			'uc' => array()
		),
		'phpgw_cpg_temp_data' => array(
			'fd' => array(
				'unique_ID' => array('type' => 'varchar','precision' => '8','nullable' => False),
				'encoded_string' => array('type' => 'blob'),
				'timestamp' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0')
			),
			'pk' => array('unique_ID'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_cpg_usergroups' => array(
			'fd' => array(
				'group_id' => array('type' => 'auto','nullable' => False),
				'group_name' => array('type' => 'varchar','precision' => '255','nullable' => False),
				'group_quota' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'has_admin_access' => array('type' => 'int','precision' => '1','nullable' => False,'default' => '0'),
				'can_rate_pictures' => array('type' => 'int','precision' => '1','nullable' => False,'default' => '0'),
				'can_send_ecards' => array('type' => 'int','precision' => '1','nullable' => False,'default' => '0'),
				'can_post_comments' => array('type' => 'int','precision' => '1','nullable' => False,'default' => '0'),
				'can_upload_pictures' => array('type' => 'int','precision' => '1','nullable' => False,'default' => '0'),
				'can_create_albums' => array('type' => 'int','precision' => '1','nullable' => False,'default' => '0'),
				'pub_upl_need_approval' => array('type' => 'int','precision' => '1','nullable' => False,'default' => '1'),
				'priv_upl_need_approval' => array('type' => 'int','precision' => '1','nullable' => False,'default' => '1'),
				'upload_form_config' => array('type' => 'int','precision' => '1','nullable' => False,'default' => '3'),
				'custom_user_upload' => array('type' => 'int','precision' => '1','nullable' => False,'default' => '0'),
				'num_file_upload' => array('type' => 'int','precision' => '1','nullable' => False,'default' => '5'),
				'num_URI_upload' => array('type' => 'int','precision' => '1','nullable' => False,'default' => '3')
			),
			'pk' => array('group_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_cpg_users' => array(
			'fd' => array(
				'user_id' => array('type' => 'auto','nullable' => False),
				'user_group' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '2'),
				'user_active' => array('type' => 'varchar','precision' => '3','nullable' => False,'default' => 'NO'),
				'user_name' => array('type' => 'varchar','precision' => '25','nullable' => False),
				'user_password' => array('type' => 'varchar','precision' => '25','nullable' => False),
				'user_lastvisit' => array('type' => 'timestamp','nullable' => False,'default' => 'current_timestamp'),
				'user_regdate' => array('type' => 'timestamp','nullable' => False,'default' => 'current_timestamp'),
				'user_group_list' => array('type' => 'varchar','precision' => '255','nullable' => False),
				'user_email' => array('type' => 'varchar','precision' => '255','nullable' => False),
				'user_website' => array('type' => 'varchar','precision' => '255','nullable' => False),
				'user_location' => array('type' => 'varchar','precision' => '255','nullable' => False),
				'user_interests' => array('type' => 'varchar','precision' => '255','nullable' => False),
				'user_occupation' => array('type' => 'varchar','precision' => '255','nullable' => False),
				'user_actkey' => array('type' => 'varchar','precision' => '32','nullable' => False)
			),
			'pk' => array('user_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array('user_name')
		),
		'phpgw_cpg_votes' => array(
			'fd' => array(
				'pic_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'user_md5_id' => array('type' => 'varchar','precision' => '32','nullable' => False),
				'vote_time' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0')
			),
			'pk' => array('pic_id','user_md5_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)
	);
