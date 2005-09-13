<?php
	/**************************************************************************\
	* AngleMail - E-Mail Module for eGroupWare					*
	* http://www.anglemail.org									*
	* http://www.egroupware.org									* 
	*/
	/**************************************************************************\
	* AngleMail - Email SpellChecking Backend Service Class				*
	* This file written by "Angles" Angelo Puglisi <angles@aminvestments.com>	*
	* Email SpellChecking Backend Service Class - Dummy Class			*
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
	
	/* $Id: class.spell_svc_none.inc.php,v 1.4 2004/01/27 16:27:24 reinerj Exp $ */
	
	/*!
	@class spell_svc_none
	@abstract  dummy placeholder for spell-less installations
	@param can_spell (boolean) PRIVATE - flag if this is a working module or a dummy one
	Only the calling spell class should access this, from there it is made public. 
	@param $sp_feed_type (defined int) PRIVATE , if tis services takes single words or strings, 
	values are defined in spell class, which gets the value from here and makes it public.
	@author Angles
	@discussion  If PHP psspell support is not compiled in,  this  dummy module 
	spell_ svc_none is loaded so there are no errors related to undefined pspell functions.
	@access public
	*/
	class spell_svc_none
	{
		/**************************************************************************\
		*	VARS
		\**************************************************************************/
		var $can_spell = False;
		var $sp_feed_type;
		
		/**************************************************************************\
		*	CONSTRUCTOR
		\**************************************************************************/
		function spell_svc_none()
		{
			// this is a dummy module for installations with no spell capability
			$this->can_spell = False;
			// SP_FEED_UNKNOWN is defined in the spell class.
			$this->sp_feed_type = SP_FEED_UNKNOWN;
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
		maked that information public. This is a dummy placeholder module so it returns False.
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
		@abstract  dummy placeholder for spell-less installations
		@param string language, string [spelling], string [jargon], string [encoding], int [mode]
		@discussion returns False so ignorant calling code will sense something is wrong with 
		spelling code.
		@access public
		*/
		function pgw_pspell_new($language, $spelling, $jargon, $encoding, $mode)
		{
			return False;
		}
		
		/*!
		@function pgw_pspell_check
		@abstract  dummy placeholder for spell-less installations
		@param int dictionary_link, string word
		@discussion Returns True to imitate a word is spelled correctly, then ignorant 
		calling code will not ask for suggestions, hopefully.
		@access public
		*/
		function pgw_pspell_check($dictionary_link, $word)
		{
			return True;
		}
		
		/*!
		@function pgw_pspell_suggest
		@abstract  dummy placeholder for spell-less installations
		@param int dictionary_link, string word
		@discussion Returns empty array to imitate pspell hafving no suggestions, 
		since this is a dummy module there are indeed no suggestions, and ignorant calling 
		code will not act on any suggestions if it gets an empty array back.
		@access public
		*/
		function pgw_pspell_suggest($dictionary_link, $word)
		{
			return array();
		}
	}
?>
