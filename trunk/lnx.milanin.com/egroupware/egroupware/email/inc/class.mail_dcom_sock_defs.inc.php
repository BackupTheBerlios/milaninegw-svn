<?php
  /**************************************************************************\
  * eGroupWare API - MAIL Defines and Class Includes for Sockets IMAP		*
  * This file written by Angelo "Angles" Puglisi <angles@aminvestments.com>	*
  * Handles general functionality for mail/mail structures					*
  * Copyright (C) 2003,2004 Angelo "Angles" Puglisi						*
  * -------------------------------------------------------------------------			*
  * This library is part of the eGroupWare API                             *
  * http://www.egroupware.org/api                                          * 
  * ------------------------------------------------------------------------ *
  * This library is free software; you can redistribute it and/or modify it  *
  * under the terms of the GNU Lesser General Public License as published by *
  * the Free Software Foundation; either version 2.1 of the License,         *
  * or any later version.                                                    *
  * This library is distributed in the hope that it will be useful, but      *
  * WITHOUT ANY WARRANTY; without even the implied warranty of               *
  * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                     *
  * See the GNU Lesser General Public License for more details.              *
  * You should have received a copy of the GNU Lesser General Public License *
  * along with this library; if not, write to the Free Software Foundation,  *
  * Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA            *
  \**************************************************************************/

	define('SA_MESSAGES',1);
	define('SA_RECENT',2);
	define('SA_UNSEEN',4);
	define('SA_UIDNEXT',8);
	define('SA_UIDVALIDITY',16);
	define('SA_ALL',31);
	
	define('SORTDATE',0);
	define('SORTARRIVAL',1);
	define('SORTFROM',2);
	define('SORTSUBJECT',3);
	define('SORTTO',4);
	define('SORTCC',5);
	define('SORTSIZE',6);
	
	define ('TYPETEXT',0);
	define ('TYPEMULTIPART',1);
	define ('TYPEMESSAGE',2);
	define ('TYPEAPPLICATION',3);
	define ('TYPEAUDIO',4);
	define ('TYPEIMAGE',5);
	define ('TYPEVIDEO',6);
	// what is defined as 7 ? , not typemodel
	define ('TYPEOTHER',8);
	//  define ('TYPEMODEL',
	define ('ENC7BIT',0);
	define ('ENC8BIT',1);
	define ('ENCBINARY',2);
	define ('ENCBASE64',3);
	define ('ENCQUOTEDPRINTABLE',4);
	define ('ENCOTHER',5);
	//  ENCUU not defined in php 4, but we may use it
	define ('ENCUU',6);
	
	define ('FT_UID',1);	// the msgnum is a UID
	define ('FT_PEEK',2);	// do not set the \Seen flag if not already set
	define ('FT_NOT',4);	// do not fetch header lines (with IMAP_BODY)
	define ('FT_INTERNAL',8); // server will not attempt to standardize CRLFs
	define ('FT_PREFETCHTEXT',16); // grab the header AND its associated RFC822.TEXT
  
	define ('SE_UID',1); // used with IMAP_SORT, IMAP_SEARCH,
	define ('SE_FREE',2); // Return the search program to free storage after finishing NOT used by PHP
	define ('SE_NOPREFETCH',4); // used with IMAP_SORT , don't really understand it though
		//SE_UID	Return UIDs instead of sequence numbers
		//SE_NOPREFETCH	Don't prefetch searched messages.
	
	// This may need to be a reference to the different months in native tongue....
	$GLOBALS['month_array'] = Array(
		'jan' => 1,
		'feb' => 2,
		'mar' => 3,
		'apr' => 4,
		'may' => 5,
		'jun' => 6,
		'jul' => 7,
		'aug' => 8,
		'sep' => 9,
		'oct' => 10,
		'nov' => 11,
		'dec' => 12
	);

	/*!
	@class mailbox_status (sockets)
	@abstract part of mail Data Communications class
	@discussion see PHP function: IMAP_STATUS --  This function returns status information on a mailbox other than the current one
	SA_MESSAGES - set status->messages to the number of messages in the mailbox
	SA_RECENT - set status->recent to the number of recent messages in the mailbox
	SA_UNSEEN - set status->unseen to the number of unseen (new) messages in the mailbox
	SA_UIDNEXT - set status->uidnext to the next uid to be used in the mailbox
	SA_UIDVALIDITY - set status->uidvalidity to a constant that changes when uids for the mailbox may no longer be valid
	SA_ALL - set all of the above
	php-imap puts the int value used for the "flags" member.
	*/
	class mailbox_status
	{
		var $flags = '';
		var $messages = '';
		var $recent = '';
		var $unseen = '';
		var $uidnext = '';
		var $uidvalidity = '';
		// quota and quota_all not in php builtin
		var $quota = '';
		var $quota_all = '';
	}
	
	/*!
	@class mailbox_msg_info (sockets)
	@abstract part of mail Data Communications class
	@discussion see PHP function: IMAP_MAILBOXMSGINFO -- Get information about the current mailbox
	@syntax structure returns this data
	Date		date of last change
	Driver		driver
	Mailbox	name of the mailbox
	Nmsgs	number of messages
	Recent		number of recent messages
	Unread	number of unread messages
	Deleted	number of deleted messages
	Size		mailbox size
	*/
	class mailbox_msg_info
	{
		var $Date = '';
		var $Driver ='';
		var $Mailbox = '';
		var $Nmsgs = '';
		var $Recent = '';
		var $Unread = '';
		var $Size = '';
	}
	
	/*!
	@class mailbox_status (sockets) discussion VS. class mailbox_msg_info (sockets) 
	@abstract compare these two similar classes and their functions
	@discussion class mailbox_status is used by function IMAP_STATUS
	class mailbox_msg_info is used by function IMAP_MAILBOXMSGINFO
	These two functions / classes are similar,  some notes on their usage is the example below
	@example Note 1)
	IMAP_MAILBOXMSGINFO is only used for the folder that the client is currently logged into,
	for pop3 this is always "INBOX", for imap this is the currently selected (opened) folder.
	Therefor, with imap the target folder must already be selected (via IMAP_OPEN or IMAP_REOPEN)
	Note 2)
	IMAP_STATUS is can be used to obtain data on a folder that is NOT currently selected (opened)
	by the client. For pop3 this difference means nothing, for imap this means the client
	need NOT select (i.e. open) the target folder before requesting status data.
	Still, IMAP_STATUS can be used on any folder wheter it is currently selected (opened) or not.
	Note 3)
	The main functional difference is that one function returns size data, and the other does not.
	imap_mailboxmsginfo returns size data, imap_status does NOT.
	This size data adds all the sizes of the messages in that folder together to get the total folder size.
	Some IMAP servers can take alot of time and CPU cycles to get this total,
	particularly with MAILDIR type imap servers such as Courier-imap, while other imap servers
	seem to return this size data with little difficulty.
	*/

	/*!
	@class msg_structure (sockets)
	@abstract part of mail Data Communications class
	@discussion see PHP function: imap_fetchstructure --  Read the structure of a particular message
	@syntax structure of return data is this
	type			Primary body type
	encoding		Body transfer encoding
	ifsubtype		TRUE if there is a subtype string
	subtype		MIME subtype
	ifdescription		TRUE if there is a description string
	description		Content description string
	ifid			TRUE if there is an identification string
	id			Identification string
	lines			Number of lines
	bytes			Number of bytes
	ifdisposition		TRUE if there is a disposition string
	disposition		Disposition string
	ifdparameters		TRUE if the dparameters array exists
	dparameters		Disposition parameter array
	ifparameters		TRUE if the parameters array exists
	parameters		MIME parameters array
	parts			Array of objects describing each message part
	*/
	class msg_structure
	{
		var $type = '';
		var $encoding = '';
		var $ifsubtype = False;
		var $subtype = '';
		var $ifdescription = False;
		var $description = '';
		var $ifid = False;
		var $id = '';
		var $lines = '';
		var $bytes = '';
		var $ifdisposition = False;
		var $disposition = '';
		var $ifdparameters = False;
		var $dparameters = array();
		var $ifparameters = False;
		var $parameters = array();
		// custom phpgw data to aid in building this structure
		var $custom = array();
		var $parts = array();
	}
	
	// gonna have to decide on one of the next two
	class msg_params
	{
		var $attribute;
		var $value;
		
		function msg_params($attrib,$val)
		{
			$this->attribute = $attrib;
			$this->value     = $val;
		}
	}
	class att_parameter
	{
		var $attribute;
		var $value;
	}
	
	class address
	{
		var $personal;
		var $mailbox;
		var $host;
		var $adl;
		// constructor
		function address()
		{
			$this->personal = '';
			$this->mailbox = '';
			$this->host = '';
			$this->adl = '';
		}
	}
	
	/*!
	@class msg_overview (sockets)
	@abstract part of mail Data Communications class
	@discussion see PHP function:  imap_fetch_overview -- Read an overview of the information in the 
	headers of the given message. NOT CURRENTY IMPLEMENTED
	*/
	class msg_overview
	{
		var $subject;	// the messages subject
		var $from;	// who sent it
		var $date;	// when was it sent
		var $message_id;	// Message-ID
		var $references;	// is a reference to this message id
		var $size;		// size in bytes
		var $uid;		// UID the message has in the mailbox
		var $msgno;	// message sequence number in the maibox
		var $recent;	// this message is flagged as recent
		var $flagged;	// this message is flagged
		var $answered;	// this message is flagged as answered
		var $deleted;	// this message is flagged for deletion
		var $seen;	// this message is flagged as already read
		var $draft;	// this message is flagged as being a draft
	}

	/*!
	@class hdr_info_envelope (sockets)
	@abstract part of mail Data Communications class
	@discussion see PHP function:  imap_headerinfo -- Read the header of the message
	see PHP function:   imap_header  which is simply an alias to imap_headerinfo
	*/
	class hdr_info_envelope
	{
		// I do not know wtf "remail" this is for
		var $remail = ''; // // unset for imap (is this for Newsgroups?)
		// --- Basic Header Data ---
		var $date = '';  // message Date header, should be like this: Tue, 16 Sep 2003 15:28:37 -0400 
		var $Date = '';
		var $subject = '';
		var $Subject = '';
		var $in_reply_to = ''; // unset if not available in imap ENVELOPE
		var $message_id = ''; // ALWAYS present even if not filled, available in imap ENVELOPE
		var $newsgroups = ''; // unset for imap
		var $followup_to = ''; // unset if not filled - NOT available in imap ENVELOPE data
		var $references = ''; // unset if not filled - NOT available in imap ENVELOPE data
		// note "references" and "followup_to" are provided by php-imap but is NOT available in imap ENVELOPE data
		// to obtain them php-imap extension includes an extra request in its standard FETCH query, like this
		// (snip) BODY.PEEK[HEADER.FIELDS (Path Message-ID Newsgroups Followup-To References)] 
		
		// --- To, From, etc... Data ---
		// source is imap ENVELOPE data
		// ALL OF THESE follow these rules
		// 1. $to, $from, etc. arrays are numbered arrays of object O->personal  O->mailbox  O->host
		// 2. ??address string is either A. personal if available, or B. mailbox@host
		var $toaddress = '';
		var $to;	
		var $fromaddress = '';
		var $from;
		var $ccaddress = '';
		var $cc;
		var $bccaddress = '';
		var $bcc;
		var $reply_toaddress = '';
		var $reply_to;
		var $senderaddress = '';
		var $sender;
		
		// these next two never seen in the wild with imap usage
		var $return_pathaddress = '';  // unset for imap
		var $return_path; // unset for imap
		
		// --- Message Flags --- 
		// all of these can be deternimed by looking at imap FLAGS data, which contains any of these:
		// 	\Seen  \Answered  \Flagged  \Deleted  \Draft  \Recent
		var $Recent = '';		//  'R' if recent and seen, 'N' if recent and not seen, ' ' if not recent
		var $Unseen = '';		//  'U' if not seen AND not recent, ' ' if seen OR not seen and recent
		var $Flagged = '';		//  'F' if flagged, ' ' if not flagged
		var $Answered = '';	//  'A' if answered, ' ' if unanswered
		var $Deleted = '';		//  'D' if deleted, ' ' if not deleted
		var $Draft = '';		//  'X' if draft, ' ' if not draft
		
		// --- Additional Stuff  ---		
		// "Msgno" message sequence number, NOT the UID
		// in the example below actual tcpdump shows how Msgno 95 was obtained
		// c->s 0000000d UID FETCH 129 UID
		// s->c * 95 FETCH (UID 129)
		// s->c 0000000d OK Completed
		// HOWEVER in this sockets mode we ONLY use UID and it is wasteful that php-imap does the above simply for this data
		var $Msgno = ''; // we do not use this OR we will put uid here?
		
		var $MailDate = ''; // imap INTERNALDATE it looks like this: 17-Sep-2003 01:41:43 -0400
		var $Size = ''; // imap RFC822.SIZE
		var $udate = '';	// mail message date in unix time, example 1076645800
		var $fetchfrom = '';	// ALWAYS present even if not filled, and not filled for imap
		var $fetchsubject = '';	// ALWAYS present even if not filled, and not filled for imap
		var $lines = ''; // unset for imap
	}

	/*!
	class parts_parent_stub
	@abstract stub for base of bodystructure making
	@author Angles
	@discussion makes things easier during recursive 
	bodystructure struct building so we can always refer 
	to "parent->parts" even if parent is this stub.
	Note we will always return parts_stub[0] as the 
	return struct that php would return
	*/
	class parts_parent_stub
	{
		var $parts;
		
		// initialize
		function parts_parent_stub()
		{
			$this->parts = array();
		}
	}
?>
