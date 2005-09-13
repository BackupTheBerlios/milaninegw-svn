<?php
	/**************************************************************************\
	* AngleMail - E-Mail Module for eGroupWare					*
	* http://www.anglemail.org									*
	* http://www.egroupware.org									* 
	*/
	/**************************************************************************\
	* AngleMail - E-Mail SpellChecking Header Include file				*
	* This file written by "Angles" Angelo Puglisi <angles@aminvestments.com>	*
	* Class Structures shared between Spell Checking and HTML widgets		*
	* Copyright (C) 2002 Angelo Tony Puglisi (Angles)					*
	* -------------------------------------------------------------------------		*
	* This library is free software; you can redistribute it and/or modify it		*
	* under the terms of the GNU Lesser General Public License as published by	*
	* the Free Software Foundation; either version 2.1 of the License,			*
	* or any later version.											*
	* This library is distributed in the hope that it will be useful, but			*
	* WITHOUT ANY WARRANTY; without even the implied warranty of	*
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	*
	* See the GNU Lesser General Public License for more details.			*
	* You should have received a copy of the GNU Lesser General Public License	*
	* along with this library; if not, write to the Free Software Foundation,		*
	* Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA			*
	\**************************************************************************/
	
	/* $Id: class.spell_struct.inc.php,v 1.4 2004/01/27 16:27:24 reinerj Exp $ */
	/*!
	@class spell_struct
	@abstract A simple C-Style Include .h file, holds public data structure classes for class email spell
	@discussion  Class Email Spell can be used with other classess such as the html widget class, 
	however the html widget class, in this example, must be made aware of any data structures 
	that the spell class may pass to it. Use this file like an include file for such purposes. I 
	suggest require_once.
	*/
	
	/**************************************************************************\
	*	CONSTANTS - custom for our use here
	\**************************************************************************/
	
	/*!
	@constant SP_FEED_
	@abstract does dpell checker want single words or lines of text, SP_FEED_UNKNOWN or SP_FEED_WORDS or SP_FEED_LINES
	@author Angles
	@discussion there should be different ways to spell check depending on what your system has installed. 
	The php builtin pspell extension appears to take one word at a time, the command line version of aspell 
	takes a string, a line of text, at one time. class.spell constructor should determine this and fill $this->sp_feed_type.
	*/
	define('SP_FEED_UNKNOWN',1);
	define('SP_FEED_WORDS',2);
	define('SP_FEED_LINES',4);
	
	/*!
	@class correction_info
	@abstract   coherently combine spelling suggextions with the original text
	@param $orig_word string
	$line_num int
	$word_num int
	$suggestions array of strings
	@discussion  holds information about a misspelled word including where 
	it appears in the original text and up to MAX_SUGGEST suggestions
	*/
	class correction_info
	{
		// coherently combine spelling suggextions with the original text
		var $orig_word;
		var $orig_word_clean;
		var $line_num;
		var $word_num;
		var $suggestions;
		
		function correction_info()
		{
			$this->orig_word='';
			$this->orig_word_clean = '';
			$this->line_num=0;
			$this->word_num=0;
			$this->suggestions=array();
		}
	}
	
?>
