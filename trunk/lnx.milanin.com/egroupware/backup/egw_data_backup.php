#!/usr/bin/php -q
<?php
/*******************************************************************\
	* eGroupWare - Backup                                             *
	* http://www.egroupware.org                                       *
	*                                                                   *
	* Administration Tool for data backup                               *
	* Written by João Martins joao@wipmail.com.br   2004-01-14          *
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



	$here = getcwd().'/dbset.php';
	include ($here);

	$tbok = mysql_select_db('egroupware',$dbok)
		or die("DB problem: " . mysql_error());

	$qrok = mysql_query('SELECT config_name, config_value FROM phpgw_config where config_app like "backup" ')
		or die("table problem: " . mysql_error());

		while(list($cfn,$cfv) = mysql_fetch_row($qrok))
			{
			switch ($cfn)
				{
				case 'b_intval'		: $bintval = $cfv;break;
				case 'b_type'		: $end = $cfv;break;
				case 'versions'		: $versions = $cfv;break;
				case 'script_path'	: $dir = $cfv;break;
				case 'l_path'		: $basedir = $cfv;break;
				case 'b_sql'		: $bsql = $cfv;break;
				case 'l_save'		: $lsave = $cfv;break;
				case 'home'		: $notemail = $cfv;break;
				case 'l_websave'	: $lwebsave = $cfv;break;
				case 'mysql'		: $database = $cfv;break;
				case 'pgsql'		: $database = $cfv;break;
				case 'php_cgi'		: $php_path = $cfv;break;
				case 'tar'		: $tar_path = $cfv . ' -czf ';break;
				case 'zip'		: $zip_path = $cfv . ' -rq9 ';break;
				case 'bzip2'		: $bz2_path = $cfv . ' -z ';break;
				}
			}

		switch ($end)
			{
			case 'tgz':		$command = $tar_path; $ext = 'tar.gz'; break;
			case 'tar.bz2':		$command = $bz2_path; $ext = 'tar'; break;
			case 'zip':		$command = $zip_path; $ext = 'zip'; break;
			}



		if (is_dir($basedir))
		{
			foreach (glob("$basedir/*.$ext") as $files)
			{
				$archive[] = array
					(
						'file'	=> $files,
						'archdate'	=> filemtime( $files)
					);
			}

		}
		else
		{
			return False;
		}

		$bdate		= time();
		$month		= date('m',$bdate);
		$day		= date('d',$bdate);
		$year		= date('Y',$bdate);

		$bdateout	=  $month . '_' . $day . '_' . $year;

		$dd = date('d');
		$dm = date('m');

		switch($bintval)
		{
			case 'daily':	$dd = $dd - $versions; break;
			case 'weekly':	$dd = $dd - ( 7 * $versions ); break;
			case 'monthly':	$dm = $dm - $versions; break;
		}
		$rdate = mktime(1,0,0,$dm,$dd,date('Y'));

		if ($bsql)
		{
			chdir($database);
			$out	= $basedir . '/' . $bdateout . '_phpGWBackup_mysql.' . $ext;
			$in		= ' egroupware';
			system("$command" . $out . $in);
				if ($bcomp == 'tar.bz2')
				{
					$ext = '.bz2';
					system("$bzip2 -z " . $out . ' 2>&1 > /dev/null');
					$out = $out . $ext;
				}
			$output[]	= $out;
			$input[]	= substr($out,strlen($basedir)+1);
		}

		if ($bldap == 'yes')
		{
			chdir('');
			$out	= $basedir . '/' . $bdateout . '_phpGWBackup_ldap.' . $ext;
			$in		= ' ';
			system("$command" . $out . $in);
			if ($bcomp == 'tar.bz2')
			{
				$ext = '.bz2';
				system("$bzip2 -z " . $out . ' 2>&1 > /dev/null');
				$out = $out . $ext;
			}
			$output[]	= $out;
			$input[]	= substr($out,strlen($basedir)+1);
		}

		if ($bemail == 'yes')
		{
			$out	=  $dir . 'Backup archive created: ' . $bdateout .  '_phpGWBackup_mysql' . '.' . $ext;
			@mail($notemail,"Backup EGW",$out,$dir,$notemail);
		}

		if ($lsave == 'yes')
		{
			if ($lwebsave == 'yes')
			{
				$command = 'cp';
			}
			else
			{
				$command = 'mv';
			}
		if ($basedir != '')
		{
			for ($i=0;$i<count($output);$i++)
			{
				system("$command " . $output[$i] . ' ' . $lpath . '/ 2>&1 > /dev/null');
			}

			while (list($null,$rfiles) = each($archive))
			{
				if ($rfiles['archdate'] <= $rdate)
				{
					unlink($rfiles['file']);
					echo 'removed   ' . $rfiles['file'] . "\n";
				}
			}

		}
		}
		else
		{
			$command = 'rm';
			for ($i=0;$i<count($output);$i++)
			{
				system("$command " . $output[$i] . ' 2>&1 > /dev/null');
			}
		}

?>
