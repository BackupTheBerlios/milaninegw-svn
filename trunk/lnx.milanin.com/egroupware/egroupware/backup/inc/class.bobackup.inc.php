<?php
	/*******************************************************************\
	* eGroupWare - Backup                                             *
	* http://www.egroupware.org                                       *
	*                                                                   *
	* Administration Tool for data backup                               *
	* Original Written by Bettina Gille [ceb@phpgroupware.org]          *
	* -----------------------------------------------                   *
	* Written by 2001 Bettina Gille				    *
	* Overworked by João Martins joao@wipmail.com.br   2004-01-14       *
	*                                                                   *
	* This program is free software; you can redistribute it and/or     *
	* modify it under the terms of the GNU General Public License as    *
	* published by the Free Software Foundation; either version 2 of    *
	* the License, or (at your option) any later version.               *
	*                                                                   *
	* This program is distributed in the hope that it will be useful,   *
	* but WITHOUT ANY WARRANTY; without even the implied warranty of    *
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU  *
	* General Public License for more details.                          *
	*                                                                   *
	* You should have received a copy of the GNU General Public License *
	* along with this program; if not, write to the Free Software       *
	* Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.         *
	\*******************************************************************/


	class bobackup
	{
		var $public_functions = array
		(
			'check_values'		=> True,
			'save_items'		=> True,
			'get_config'		=> True,
			'save_config'		=> True,
			'get_archives'		=> True,
			'drop_archive'		=> True
		);

		function bobackup()
		{
			$this->config = CreateObject('phpgwapi.config','backup');
			$this->config->read_repository();
		}

		function get_config()
		{
			if ($this->config->config_data)
			{
				$items = $this->config->config_data;
			}
			return $items;
		}


		function check_values($values)
		{
			if ($values['b_create'])
			{
				$doc_root = get_var('DOCUMENT_ROOT',Array('GLOBAL','SERVER'));

				
				if ($values['versions'])
				{
					if (intval($values['versions']) == 0)
					{
						$error[] = lang('Versions can only be a number !');
					}
				}

				if ($values['l_save'])
				{
					if (! $values['l_path'] && ! $values['l_websave'])
					{
						$error[] = lang('Please enter the path to the backup dir and/or enable showing archives in eGroupWare !');
					}
				}


				$site_co = $this->get_config();
				if (is_array($site_co))
				{
					if (!isset($site_co['php_cgi']) || !isset($site_co['tar']) || !isset($site_co['zip']) || !isset($site_co['bzip2']))
					{
						$error[] = lang('Please enter the path of the needed applications in *Site configuration* !');
					}

					if ($values['b_sql'])
					{
						if ($GLOBALS['phpgw_info']['server']['db_type'] == 'mysql')
						{
							if (!isset($site_co['mysql']))
							{
								$error[] = lang('Please set the path to the MySQL database dir in *Site configuration* !');
							}
						}
						elseif($GLOBALS['phpgw_info']['server']['db_type'] == 'pgsql')
						{
							if (!isset($site_co['pgsql']))
							{
								$error[] = lang('Please set the path to the PostgreSQL database dir in *Site configuration* !');
							}
						}
						else
						{
							$error[] = lang('Your SQL database isnt supported by this application !');
						}
					}

					if ($values['b_ldap'])
					{
						if (!isset($site_co['ldap']) || !isset($site_co['ldap_in']))
						{
							$error[] = lang('Please set the path to the LDAP database dir in *Site configuration* !');
						}
					}

					if ($values['b_email'])
					{
						if (!isset($site_co['maildir']))
						{
							$error[] = lang('Please write down you e-mail for beeing notified');
						}
					}

				}
				else
				{
					$error[] = lang('Please set the values in *Site configuration* !');
				}
			}

			if (is_array($error))
			{
				return $error;
			}
		}

		function save_items($values)
		{
			if ($values['versions'])
			{
				$values['versions'] = intval($values['versions']);
			}
			else
			{
				$values['versions'] = 1;
			}

			if ($values['b_create'])
			{
				$values['b_create'] = 'yes';
			}
			else
			{
				$values['b_create'] = 'no';
			}

			if ($values['b_sql'])
			{
				$values['b_sql'] = $GLOBALS['phpgw_info']['server']['db_type'];
			}

			if ($values['b_ldap'])
			{
				$values['b_ldap'] = 'yes';
			}
			else
			{
				$values['b_ldap'] = 'no';
			}

			if ($values['b_email'])
			{
				$values['b_email'] = 'yes';
			}
			else
			{
				$values['b_email'] = 'no';
			}

			if ($values['r_save'])
			{
				$values['r_save'] = 'yes';
			}
			else
			{
				$values['r_save'] = 'no';
			}

			if ($values['l_save'])
			{
				$values['l_save'] = 'yes';
			}
			else
			{
				$values['l_save'] = 'no';
			}

			if ($values['l_websave'])
			{
				$values['l_websave'] = 'yes';
			}
			else
			{
				$values['l_websave'] = 'no';
			}


			while (list($key,$config) = each($values))
			{
				if ($config)
				{
					$this->config->config_data[$key] = $config;
				}
				else
				{
					unset($config->config_data[$key]);
				}
			}
			$this->config->save_repository(True);

		}



		function get_archives()
		{
			$basedir = PHPGW_SERVER_ROOT . '/backup/archives';
			if (is_dir($basedir))
			{
				$basedir = opendir($basedir);

				while (false !== ($files = readdir($basedir)))
				{
					if (($files != '.') && ($files != '..'))
					{
						$archives[] = $files;
//						_debug_array($archives);
//						exit;
					}
				}
				return $archives;
			}
			else
			{
				return False;
			}
		}

		//in order to work you need chmod the files acording in your httpd.conf that the usr can write here
		function drop_archive($archive)
		{
			$basedir = PHPGW_SERVER_ROOT . '/backup/archives';

			if (is_file($basedir . '/' . $archive))
			{
				unlink($basedir . '/' . $archive);
			}
		}
	}

?>
