<?php
	/**************************************************************************\
	* AngleMail - E-Mail Module for eGroupWare					*
	* http://www.anglemail.org									*
	* http://www.egroupware.org									* 
	*/
	/**************************************************************************\
	* AngleMail - Email SpellChecking Backend Service Class				*
	* This file written by "Angles" Angelo Puglisi <angles@aminvestments.com>	*
	* Email SpellChecking Backend Service Class - for PHP pspell Extension	*
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
	
	/* $Id: class.spell_svc_php.inc.php,v 1.4 2004/01/27 16:27:24 reinerj Exp $ */
	
	/*!
	@class spell_svc_php
	@abstract Wraps calls to the spell checking backend psspell buildin tp PHP
	@param can_spell (boolean) PRIVATE - flag if this is a working module or a dummy one
	Only the calling spell class should access this, from there it is made public.
	@param $sp_feed_type (defined int) PRIVATE , if tis services takes single words or strings, 
	values are defined in spell class, which gets the value from here and makes it public.
	@author  Angles
	@discussion  This is loaded if PHP has psspell support compiled in. If it is not 
	compiled in, a dummy module spell_ svc_none is loaded so there are no errors 
	related to undefined pspell functions.
	*/
	class spell_svc_php
	{
		/**************************************************************************\
		*	VARS
		\**************************************************************************/
		var $can_spell = True;
		var $sp_feed_type;
		
		/**************************************************************************\
		*	CONSTRUCTOR
		\**************************************************************************/
		function spell_svc_php()
		{
			$this->can_spell = True;
			// SP_FEED_WORDS is defined in the spell class.
			$this->sp_feed_type = SP_FEED_WORDS;
			return;
		}
		
		/**************************************************************************\
		*	OO ACCESS METHODS
		\**************************************************************************/
		/*!
		@function get_can_spell
		@abstract Read Only, report if this spell service is capable of spell check or not. 
		@author Angles
		@discussion The calling spell class will ask if this spell service is capable of spell check or not. 
		This function is exposed to the calling spell class for this purpose. The calling spell class then 
		maked that information public.
		@access private
		*/
		function get_can_spell()
		{
			return $this->can_spell;
		}
		
		/*!
		@function get_sp_feed_type
		@abstract Read Only, report if this spell service takes single words or strings. 
		@author Angles
		@discussion The calling spell class will ask if this spell service takes single words or strings. 
		This function is exposed to the calling spell class for this purpose. The calling spell class then 
		maked that information public.
		@access private
		*/
		function get_sp_feed_type()
		{
			return $this->sp_feed_type;
		}
		
		/**************************************************************************\
		*	CODE
		\**************************************************************************/
		/*!
		@function pgw_pspell_new
		@abstract wraps calls to "pspell_new"
		@param string language, string [spelling], string [jargon], string [encoding], int [mode]
		@discussion Php manual shows params to be: 
		pspell_new  (string language, string [spelling], string [jargon], string [encoding], int [mode])
		@access public
		*/
		function pgw_pspell_new($language, $spelling, $jargon, $encoding, $mode)
		{
			// open connection to dictionary backend
			// see: http://rock.earthlink.net/manual/mod/mod_php4/function.pspell-new.html
			return pspell_new($language, $spelling, $jargon, $encoding, $mode);
		}
		
		
		/*!
		@function pgw_pspell_check
		@abstract wraps calls to "pspell_check"
		@param int dictionary_link, string word
		@discussion Php manual shows params to be: 
		pspell_check  (int dictionary_link, string word)
		@access public
		*/
		function pgw_pspell_check($dictionary_link, $word)
		{
			return pspell_check($dictionary_link, $word);
		}

		/*!
		@function pgw_pspell_suggest
		@abstract wraps calls to "pspell_suggest"
		@param int dictionary_link, string word
		@discussion Php manual shows params to be:  
		pspell_suggest (int dictionary_link, string word)
		@access public
		*/
		function pgw_pspell_suggest($dictionary_link, $word)
		{
			// http://rock.earthlink.net/manual/mod/mod_php4/function.pspell-suggest.html
			return pspell_suggest($dictionary_link, $word);
		}
	}
?>
