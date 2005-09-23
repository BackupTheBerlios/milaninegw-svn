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



		$dbok = mysql_connect( 'localhost' , '**dbadmin**' , '**dbadminpasswd**')
  			or die("SQL problem: " . mysql_error());
?>
