<?php
  /**************************************************************************\
  * eGroupWare - Polls                                                       *
  * http://www.egroupware.org                                                *
  * Copyright (c) 1999 Till Gerken (tig@skv.org)                             *
  * Modified by Greg Haygood (shrykedude@bellsouth.net)                      *
  * -----------------------------------------------                          *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

	/* $Id: class.so.inc.php,v 1.3 2004/05/14 23:20:53 alpeb Exp $ */

	class so
	{
		var $debug = False;
		var $db;

		var $total = 0;

		function so($args='')
		{
			$this->db = $GLOBALS['phpgw']->db;
		}

        function load_settings()
        {
            $this->db->query("select * from phpgw_polls_settings");
            while($this->db->next_record())
            {
                $GLOBALS['poll_settings'][$this->db->f('setting_name')] = $this->db->f('setting_value');
            }
			return $GLOBALS['poll_settings'];
        }

		function save_settings($data)
		{
			if(isset($data) && is_array($data))
			{
				$this->db->query('delete from phpgw_polls_settings',__LINE__,__FILE__);
				while(list($name,$value) = each($data))
				{
					$this->db->query("insert into phpgw_polls_settings values ('$name','$value')",__LINE__,__FILE__);
				}
			}
		}

		function get_user_votecount($poll_id)
		{
            return (int)$this->get_value_("select count(*) from phpgw_polls_user where user_id='"
										. (int)($GLOBALS['phpgw_info']['user']['account_id'])
										. "' and poll_id='".(int)$poll_id."'",0);
		}

		function get_poll_title($poll_id)
		{
			return stripslashes($this->get_value_("SELECT poll_title FROM phpgw_polls_desc WHERE poll_id='$poll_id'",0));
		}

		function get_poll_total($poll_id)
		{
			return (int)$this->get_value_("SELECT SUM(option_count) AS sum FROM phpgw_polls_data "
										. "WHERE poll_id='$poll_id'",0);
		}

		function get_poll_data($poll_id,$vote_id = -1)
		{
			$options = array();
            $query = "SELECT * FROM phpgw_polls_data WHERE poll_id='$poll_id'";
			if($vote_id >= 0)
			{
				$query .= " AND vote_id='$vote_id'";
			}
			$query .= ' order by lower(option_text)';
			if($this->debug) { print("QUERY: $query<br>"); }
            $this->db->query($query,__LINE__,__FILE__);
            while ($this->db->next_record())
            {
                $options[] = array(
                    'vote_id' => $this->db->f('vote_id'),
                    'text' => stripslashes($this->db->f('option_text')),
                    'votes' => $this->db->f('option_count')
                );
            }
			return $options;
		}

		function get_latest_poll()
		{
			return $this->get_value_("select max(poll_id) from phpgw_polls_desc", 0);
		}

		function add_answer($poll_id,$answer)
		{
			$vote_id = (int)$this->get_value_("select max(vote_id)+1 from phpgw_polls_data "
											. "where poll_id='$poll_id'",0);
			$answer = addslashes($answer);
			$result = $this->db->query('insert into phpgw_polls_data (poll_id,option_text,option_count,vote_id) '
										. "values ('$poll_id','$answer',0,'$vote_id')",__LINE__,__FILE__);
			if($result)
			{
				return $this->db->get_last_insert_id('phpgw_polls_desc','poll_id');
			}
			return -1;
		}

		function add_question($title)
		{
			$result = $this->db->query("insert into phpgw_polls_desc (poll_title,poll_timestamp) values ('"
										. addslashes($title) . "','" . time() . "')",__LINE__,__FILE__);
			return $result;
			if($result)
			{
				return $this->db->get_last_insert_id('phpgw_polls_desc','poll_id');
			}
			return -1;
		}

		function get_last_added_poll()
		{
			return $this->db->get_last_insert_id('phpgw_polls_desc','poll_id');
		}

		function delete_answer($poll_id,$vote_id)
		{
			$this->db->query("delete from phpgw_polls_data where vote_id='".$vote_id."' AND poll_id=".$poll_id);
		}

		function delete_question($poll_id)
		{
			$this->db->query("delete from phpgw_polls_desc where poll_id='" . $poll_id . "'");
			$this->db->query("delete from phpgw_polls_data where poll_id='" . $poll_id . "'");
			$this->db->query("delete from phpgw_polls_user where poll_id='" . $poll_id . "'");
			if($GLOBALS['currentpoll'] == $poll_id)
			{
				$this->db->query("select MAX(poll_id) as max from phpgw_polls_desc");
				$max = $this->db->f(0);
				$this->db->query("update phpgw_polls_settings set setting_value='$max' "
								." where setting_name='currentpoll'");
			}
		}

		function add_vote($poll_id,$vote_id,$user_id)
		{
			// verify that we're adding a valid vote before update
			$this->db->query("select option_count from phpgw_polls_data"
							." where poll_id='$poll_id' and vote_id='$vote_id'");
			$count = $this->db->f(0);
			if($count >= 0)
			{
				$this->db->query("UPDATE phpgw_polls_data SET option_count=option_count+1 WHERE "
						. "poll_id='" . $poll_id . "' AND vote_id='" . $vote_id . "'",__LINE__,__FILE__);
				$this->db->query("insert into phpgw_polls_user values ('" . $poll_id . "','0','"
						. $GLOBALS['phpgw_info']['user']['account_id'] . "','" . time() . "')",__LINE__,__FILE__);
			}
		}

		function update_answer($poll_id,$vote_id,$answer)
		{
			$this->db->query("update phpgw_polls_data set poll_id='$poll_id',option_text='"
							. addslashes($answer) . "' where vote_id='$vote_id'",__LINE__,__FILE__);
		}

		function update_question($poll_id,$question)
		{
			$this->db->query("update phpgw_polls_desc set poll_title='" . addslashes($question)
							. "' where poll_id='$poll_id'",__LINE__,__FILE__);
		}

		function get_value_($query,$field)
		{
			$this->db->query($query,__LINE__,__FILE__);
            $this->db->next_record();
            return $this->db->f($field);
		}

		function get_data_($query,$key,$args)
		{
			$data = array();
			if(!empty($query) && !empty($key))
			{
				$result = $this->db->query($query,__LINE__,__FILE__);
				$this->total = $this->db->num_rows();

				if($args && is_array($args) && !empty($args['limit']))
				{
					$start = (int)$args['start'];
					$result = $this->db->limit_query($query,$start,__LINE__,__FILE__);
				}

				while ($this->db->next_record())
				{
					$info = array();
					foreach ($this->db->Record as $key => $val)
					{
						$info[$key] = $val;
					}
					$data[] = $info;
				}
			}
			return $data;
		}

		function list_questions($args)
		{
			$query = 'select * from phpgw_polls_desc order by '.$args['order'].' '.$args['sort'];
			if($this->debug) { print("QUERY: $query<br>"); }
			$data = $this->get_data_($query,'poll_id',$args);
			return $data;
		}

		function list_answers($args)
		{
			$query = 'select phpgw_polls_data.*, phpgw_polls_desc.poll_title '
					. 'from phpgw_polls_data,phpgw_polls_desc '
					. 'where phpgw_polls_desc.poll_id = phpgw_polls_data.poll_id '
					. 'order by '.$args['order'].' '.$args['sort'];
			if($this->debug) { print("QUERY: $query<br>"); }
			$data = $this->get_data_($query,'vote_id',$args);
			return $data;
		}

		function somestoragefunc()
		{
			//nothing to be added yet
		}

	}
?>
