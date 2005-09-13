<?php
  /**************************************************************************\
  * eGroupWare - iCalendar Parser                                            *
  * http://www.egroupware.org                                                *
  * Written by Mark Peters <skeeter@phpgroupware.org>                        *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

	/* $Id: class.boicalendar.inc.php,v 1.28.4.11 2005/03/31 13:16:18 ralfbecker Exp $ */

	define('FOLD_LENGTH',75);

	define('VEVENT',1);
	define('VTODO',2);

	define('NONE',0);
	define('CHAIR',1);
	define('REQ_PARTICIPANT',2);
	define('OPT_PARTICIPANT',3);
	define('NON_PARTICIPANT',4);

	define('INDIVIDUAL',1);
	define('GROUP',2);
	define('RESOURCE',4);
	define('ROOM',8);
	define('UNKNOWN',16);

	/* event status */
	define('NEEDS_ACTION',0);
	define('ACCEPTED',1);
	define('DECLINED',2);
	define('TENTATIVE',3);
	define('DELEGATED',4);
	define('COMPLETED',5);
	define('IN_PROCESS',6);

	/* class */
	define('PRIVATE',0);
	define('PUBLIC',1);
	define('CONFIDENTIAL',3);

	/* transparency */
	define('TRANSPARENT',0);
	define('OPAQUE',1);

	/* frequency */
	define('SECONDLY',1);
	define('MINUTELY',2);
	define('HOURLY',3);
	define('DAILY',4);
	define('WEEKLY',5);
	define('MONTHLY',6);
	define('YEARLY',7);

	define('FREE',0);
	define('BUSY',1);
	define('BUSY_UNAVAILABLE',2);
	define('BUSY_TENTATIVE',3);

	define('THISANDPRIOR',0);
	define('THISANDFUTURE',1);

	define('START',0);
	define('END',1);

	define('_8BIT',0);
	define('_BASE64',1);

	define('OTHER',99);

	class boicalendar
	{
		var $public_functions = array(
			'import' => True,
			'export' => True,
			'freebusy' => True,
		);

		var $ical;
		var $line = 0;
		var $event = Array();
		var $todo = Array();
		var $journal = Array();
		var $freebusy = Array();
		var $timezone = Array();
		var $property = Array();
		var $parameter = Array();
		var $debug_str = False;
		var $api = True;
		var $chunk_split = True;

		/*
		* Base Functions
		*/

		function boicalendar()
		{
			$this->property = Array(
				'action' => Array(
					'type'		=> 'text',
					'to_text'	=> True,
					'valarm' => Array(
						'state'		=> 'required',
						'multiples'	=> False
					)
				),
				'attach' => Array(
					'type'		=> 'uri',
					'to_text'	=> True,
					'vevent' => Array(
						'state'		=> 'optional',
						'multiples'	=> True
					),
					'vtodo' => Array(
						'state'		=> 'optional',
						'multiples'	=> True
					),
					'vjournal' => Array(
						'state'		=> 'optional',
						'multiples'	=> True
					),
					'valarm' => Array(
						'state'		=> 'optional',
						'multiples'	=> True
					)
				),
				'attendee' => Array(
					'type'		=> 'cal-address',
					'to_text'	=> False,
					'vevent' => Array(
						'state'		=> 'optional',
						'multiples'	=> True
					),
					'vtodo' => Array(
						'state'		=> 'optional',
						'multiples'	=> True
					),
					'vjournal' => Array(
						'state'		=> 'optional',
						'multiples'	=> True
					),
					'valarm' => Array(
						'state'		=> 'optional',
						'multiples'	=> True
					),
					'vfreebusy'		=> Array(
						'state'		=> 'optional',
						'multiples'	=> True
					)
				),
				'categories' => Array(
					'type'		=> 'text',
					'to_text'	=> True,
					'vevent' => Array(
						'state'		=> 'optional',
						'multiples'	=> True
					),
					'vtodo' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					),
					'vjournal' => Array(
						'state'		=> 'optional',
						'multiples'	=> True
					)
				),
				'class' => Array(
					'type'		=> 'text',
					'to_text'	=> True,
					'vevent' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					),
					'vtodo' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					),
					'vjournal' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					)
				),
				'comment' => Array(
					'type'		=> 'text',
					'to_text'	=> True,
					'daylight' => Array(
						'state'		=> 'optional',
						'multiples'	=> True
					),
					'standard' => Array(
						'state'		=> 'optional',
						'multiples'	=> True
					),
					'valarm' => Array(
						'state'		=> 'optional',
						'multiples'	=> True
					),
					'vevent' => Array(
						'state'		=> 'optional',
						'multiples'	=> True
					),
					'vfreebusy' => Array(
						'state'		=> 'optional',
						'multiples'	=> True
					),
					'vjournal' => Array(
						'state'		=> 'optional',
						'multiples'	=> True
					),
					'vtodo' => Array(
						'state'		=> 'optional',
						'multiples'	=> True
					)
				),
				'completed' => Array(
					'type'		=> 'date-time',
					'to_text'	=> False,
					'vtodo' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					)
				),
				'contact' => Array(
					'type'		=> 'text',
					'to_text'	=> True,
					'vevent' => Array(
						'state'		=> 'optional',
						'multiples'	=> True
					),
					'vfreebusy' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					),
					'vjournal' => Array(
						'state'		=> 'optional',
						'multiples'	=> True
					)
				),
				'created' => Array(
					'type'		=> 'date-time',
					'to_text'	=> False,
					'vevent' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					),
					'vtodo' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					),
					'vjournal'		=> Array(
						'state'		=> 'optional',
						'multiples'	=> False
					)
				),
				'description' => Array(
					'type'		=> 'text',
					'to_text'	=> True,
					'vevent' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					),
					'vtodo' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					),
					'vjournal' => Array(
						'state'		=> 'optional',
						'multiples'	=> True
					),
					'valarm' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					)
				),
				'dtend'	 => Array(
					'type'		=> 'date-time',
					'to_text'	=> False,
					'vevent' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					),
					'vfreebusy' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					)
				),
				'dtstamp' => Array(
					'type'		=> 'date-time',
					'to_text'	=> False,
					'vevent' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					),
					'vtodo'	=> Array(
						'state'		=> 'optional',
						'multiples'	=> False
					),
					'vjournal' => Array(
						'state'		=> 'optional',
						'multiples'	=> True
					),
					'vfreebusy' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					)
				),
				'dtstart' => Array(
					'type'		=> 'date-time',
					'to_text'	=> False,
					'daylight' => Array(
						'state'		=> 'required',
						'multiples'	=> False
					),
					'standard' => Array(
						'state'		=> 'required',
						'multiples'	=> False
					),
					'vevent' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					),
					'vfreebusy' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					),
					'vjournal' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					),
					'vtodo' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					)
				),
				'due' => Array(
					'type'		=> 'date-time',
					'to_text'	=> False,
					'vtodo' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					)
				),
				'duration' => Array(
					'type'		=> 'duration',
					'to_text'	=> False,
					'valarm' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					),
					'vevent' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					),
					'vfreebusy' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					),
					'vtodo' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					)
				),
				'exdate' => Array(
					'type'		=> 'date-time',
					'to_text'	=> False,
					'vevent' => Array(
						'state'		=> 'optional',
						'multiples'	=> True
					),
					'vjournal' => Array(
						'state'		=> 'optional',
						'multiples'	=> True
					),
					'vtodo' => Array(
						'state'		=> 'optional',
						'multiples'	=> True
					)
				),
				'exrule' => Array(
					'type'		=> 'recur',
					'to_text'	=> False,
					'vevent' => Array(
						'state'		=> 'optional',
						'multiples'	=> True
					),
					'vjournal' => Array(
						'state'		=> 'optional',
						'multiples'	=> True
					),
					'vtodo' => Array(
						'state'		=> 'optional',
						'multiples'	=> True
					)
				),
				'freebusy' => Array(
					'type'		=> 'text',
					'to_text'	=> False,
					'vfreebusy' => Array(
						'state'		=> 'optional',
						'multiples'	=> True
					)
				),
				'geo' => Array(
					'type'		=> 'float',
					'to_text'	=> True,
					'vevent' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					),
					'vtodo' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					)
				),
				'last_modified' => Array(
					'type'		=> 'date-time',
					'to_text'	=> False,
					'vevent' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					),
					'vtodo' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					),
					'vjournal' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					),
					'vtimezone' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					)
				),
				'location' => Array(
					'type'		=> 'text',
					'to_text'	=> True,
					'vevent' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					),
					'vtodo' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					)
				),
				'method' => Array(
					'type'		=> 'text',
					'to_text'	=> True,
					'ical' => Array(
						'state'		=> 'required',
						'multiples'	=> False
					)
				),
				'organizer' => Array(
					'type'		=> 'cal-address',
					'to_text'	=> False,
					'vevent' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					),
					'vtodo' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					),
					'vjournal' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					),
					'vfreebusy' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					)
				),
				'percent_complete' => Array(
					'type'		=> 'integer',
					'to_text'	=> False,
					'vtodo'	=> Array(
						'state'		=> 'optional',
						'multiples'	=> False
					)
				),
				'priority' => Array(
					'type'		=> 'integer',
					'to_text'	=> True,
					'vevent' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					),
					'vtodo' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					)
				),
				'prodid' => Array(
					'type'		=> 'text',
					'to_text'	=> True,
					'ical'	=> Array(
						'state'		=> 'required',
						'multiples'	=> False
					)
				),
				'rdate' => Array(
					'type'		=> 'date-time',
					'to_text'	=> False,
					'daylight' => Array(
						'state'		=> 'optional',
						'multiples'	=> True
					),
					'standard' => Array(
						'state'		=> 'optional',
						'multiples'	=> True
					),
					'vevent' => Array(
						'state'		=> 'optional',
						'multiples'	=> True
					),
					'vtodo' => Array(
						'state'		=> 'optional',
						'multiples'	=> True
					),
					'vjournal' => Array(
						'state'		=> 'optional',
						'multiples'	=> True
					)
				),
				'recurrence_id' => Array(
					'type'		=> 'date-time',
					'to_text'	=> False,
					'vevent' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					),
					'vjournal' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					),
					'vtodo'	=> Array(
						'state'		=> 'optional',
						'multiples'	=> False
					)
				),
				'related_to' => Array(
					'type'		=> 'text',
					'to_text'	=> False,
					'vevent' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					),
					'vjournal' => Array(
						'state'		=> 'optional',
						'multiples'	=> True
					),
					'vtodo' => Array(
						'state'		=> 'optional',
						'multiples'	=> True
					)
				),
				'request_status' => Array(
					'type'		=> 'text',
					'to_text'	=> True,
					'vevent' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					),
					'vtodo' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					),
					'vjournal' => Array(
						'state'		=> 'optional',
						'multiples'	=> True
					),
					'vfreebusy' => Array(
						'state'		=> 'optional',
						'multiples'	=> True
					)
				),
				'resources' => Array(
					'type'		=> 'text',
					'to_text'	=> False,
					'vevent' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					),
					'vtodo' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					)
				),
				'rrule' => Array(
					'type'		=> 'recur',
					'to_text'	=> False,
					'daylight' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					),
					'standard' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					),
					'vevent' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					),
					'vtodo' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					),
					'vjournal' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					)
				),
				'sequence' => Array(
					'type'		=> 'integer',
					'to_text'	=> True,
					'vevent' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					),
					'vjournal' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					),
					'vtodo' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					)
				),
				'status' => Array(
					'type'		=> 'text',
					'to_text'	=> True,
					'vevent' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					),
					'vjournal' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					),
					'vtodo' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					)
				),
				'summary' => Array(
					'type'		=> 'text',
					'to_text'	=> True,
					'vevent' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					),
					'vtodo' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					),
					'vjournal' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					),
					'valarm' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					)
				),
				'transp' => Array(
					'type'		=> 'text',
					'to_text'	=> True,
					'vevent' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					)
				),
				'trigger' => Array(
					'type'		=> 'text',
					'to_text'	=> True,
					'valarm' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					)
				),
				'tzid' => Array(
					'type'		=> 'text',
					'to_text'	=> True,
					'vtimezone' => Array(
						'state'		=> 'required',
						'multiples'	=> False
					)
				),
				'tzname' => Array(
					'type'		=> 'text',
					'to_text'	=> True,
					'daylight' => Array(
						'state'		=> 'optional',
						'multiples'	=> True
					),
					'standard' => Array(
						'state'		=> 'optional',
						'multiples'	=> True
					)
				),
				'tzoffsetfrom' => Array(
					'type'		=> 'utc-offset',
					'to_text'	=> True,
					'daylight' => Array(
						'state'		=> 'required',
						'multiples'	=> False
					),
					'standard' => Array(
						'state'		=> 'required',
						'multiples'	=> False
					)
				),
				'tzoffsetto' => Array(
					'type'		=> 'utc-offset',
					'to_text'	=> True,
					'daylight' => Array(
						'state'		=> 'required',
						'multiples'	=> False
					),
					'standard' => Array(
						'state'		=> 'required',
						'multiples'	=> False
					)
				),
				'tzurl'	=> Array(
					'type'		=> 'uri',
					'to_text'	=> True,
					'vtimezone' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					)
				),
				'uid' => Array(
					'type'		=> 'text',
					'to_text'	=> True,
					'vevent' => Array(
						'state'		=> 'required',
						'multiples'	=> False
					),
					'vfreebusy' => Array(
						'state'		=> 'required',
						'multiples'	=> False
					),
					'vjournal' => Array(
						'state'		=> 'required',
						'multiples'	=> False
					),
					'vtodo' => Array(
						'state'		=> 'required',
						'multiples'	=> False
					)
				),
				'url' => Array(
					'type'		=> 'text',
					'to_text'	=> True,
					'vevent' => Array(
						'state'		=> 'required',
						'multiples'	=> False
					),
					'vfreebusy' => Array(
						'state'		=> 'required',
						'multiples'	=> False
					),
					'vjournal' => Array(
						'state'		=> 'optional',
						'multiples'	=> False
					),
					'vtodo'	=> Array(
						'state'		=> 'required',
						'multiples'	=> False
					)
				),
				'version' => Array(
					'type'		=> 'text',
					'to_text'	=> True,
					'ical' => Array(
						'state'		=> 'required',
						'multiples'	=> False
					)
				)
			);
			$this->parameter = Array(
				'altrep' => Array(
					'type'		=> 'uri',
					'quoted'	=> True,
					'to_text'	=> True,
					'properties' => Array(
						'comment'	=> True,
						'description'	=> True,
						'location'	=> True,
						'prodid'	=> True,
						'resources'	=> True,
						'summary'	=> True,
						'contact'	=> True
					)
				),
				'byday' => Array(
					'type'		=> 'text',
					'quoted'	=> False,
					'to_text'	=> False,
					'properties' => Array(
						'rrule'	=> True
					)
				),
				'byhour' => Array(
					'type'		=> 'text',
					'quoted'	=> False,
					'to_text'	=> False,
					'properties' => Array(
						'rrule' => True
					)
				),
				'byminute' => Array(
					'type'		=> 'text',
					'quoted'	=> False,
					'to_text'	=> False,
					'properties' => Array(
						'rrule'	=> True
					)
				),
				'bymonth' => Array(
					'type'		=> 'text',
					'quoted'	=> False,
					'to_text'	=> False,
					'properties' => Array(
						'rrule'	=> True
					)
				),
				'bymonthday' => Array(
					'type'		=> 'text',
					'quoted'	=> False,
					'to_text'	=> False,
					'properties' => Array(
						'rrule'	=> True
					)
				),
				'bysecond' => Array(
					'type'		=> 'text',
					'quoted'	=> False,
					'to_text'	=> False,
					'properties' => Array(
						'rrule'	=> True
					)
				),
				'bysetpos' => Array(
					'type'		=> 'text',
					'quoted'	=> False,
					'to_text'	=> False,
					'properties' => Array(
						'rrule' => True
					)
				),
				'byweekno' => Array(
					'type'		=> 'text',
					'quoted'	=> False,
					'to_text'	=> False,
					'properties' => Array(
						'rrule'	=> True
					)
				),
				'byyearday' => Array(
					'type'		=> 'text',
					'quoted'	=> False,
					'to_text'	=> False,
					'properties' => Array(
						'rrule'	=> True
					)
				),
				'class' => Array(
					'type'		=> 'function',
					'function'	=> 'switch_class',
					'quoted'	=> False,
					'to_text'	=> False,
					'properties' => Array(
						'class'	=> True
					)
				),
				'cn' => Array(
					'type'		=> 'text',
					'quoted'	=> True,
					'to_text'	=> False,
					'properties' => Array(
						'attendee'	=> True,
						'organizer'	=> True
					)
				),
				'count' => Array(
					'type'		=> 'integer',
					'quoted'	=> False,
					'to_text'	=> False,
					'properties' => Array(
						'rrule'	=> True
					)
				),
				'cu' => Array(
					'type'		=> 'function',
					'function'	=> 'switch_cu',
					'quoted'	=> False,
					'to_text'	=> False,
					'properties' => Array(
						'attendee'	=> True
					)
				),
				'delegated_from' => Array(
					'type'		=> 'function',
					'function'	=> 'switch_mailto',
					'quoted'	=> True,
					'to_text'	=> False,
					'properties' => Array(
						'attendee'	=> True
					)
				),
				'delegated_to' => Array(
					'type'		=> 'function',
					'function'	=> 'switch_mailto',
					'quoted'	=> True,
					'to_text'	=> False,
					'properties' => Array(
						'attendee'	=> True
					)
				),
				'dir' => Array(
					'type'		=> 'dir',
					'quoted'	=> True,
					'to_text'	=> True,
					'properties' => Array(
						'attendee'	=> True,
						'organizer'	=> True
					)
				),
				'dtend' => Array(
					'type'		=> 'function',
					'function'	=> 'switch_date',
					'quoted'	=> False,
					'to_text'	=> False,
					'properties' => Array(
						'dtend'	=> True
					)
				),
				'dtstamp' => Array(
					'type'		=> 'function',
					'function'	=> 'switch_date',
					'quoted'	=> False,
					'to_text'	=> False,
					'properties' => Array(
						'dtstamp'	=> True
					)
				),
				'dtstart' => Array(
					'type'		=> 'function',
					'function'	=> 'switch_date',
					'quoted'	=> False,
					'to_text'	=> False,
					'properties' => Array(
						'dtstart'	=> True
					)
				),
				'encoding' => Array(
					'type'		=> 'function',
					'function'	=> 'switch_encoding',
					'quoted'	=> False,
					'to_text'	=> False,
					'properties' => Array(
						'attach'	=> True
					)
				),
				'fmttype' => Array(
					'type'		=> 'text',
					'quoted'	=> False,
					'to_text'	=> False,
					'properties' => Array(
						'attach'	=> True
					)
				),
				'fbtype' => Array(
					'type'		=> 'function',
					'function'	=> 'switch_fbtype',
					'quoted'	=> False,
					'to_text'	=> False,
					'properties' => Array(
						'attach'	=> True
					)
				),
				'freebusy' => Array(
					'type'		=> 'text',
					'quoted'	=> False,
					'to_text'	=> False,
					'properties' => Array(
						'freebusy'	=> True
					)
				),
				'freq' => Array(
					'type'		=> 'function',
					'function'	=> 'switch_freq',
					'quoted'	=> False,
					'to_text'	=> False,
					'properties' => Array(
						'rrule'	=> True
					)
				),
				'interval' => Array(
					'type'		=> 'integer',
					'quoted'	=> False,
					'to_text'	=> False,
					'properties' => Array(
						'rrule'		=> True
					)
				),
				'language' => Array(
					'type'	=> 'text',
					'quoted'	=> False,
					'to_text'	=> False,
					'properties' => Array(
						'categories'	=> True,
						'comment'	=> True,
						'description'	=> True,
						'location'	=> True,
						'resources'	=> True,
						'summary'	=> True,
						'tzname'	=> True,
						'attendee'	=> True,
						'contact'	=> True,
						'organizer'	=> True,
						'x-type'	=> True
					)
				),
				'last_modified' => Array(
					'type'		=> 'function',
					'function'	=> 'switch_date',
					'quoted'	=> False,
					'to_text'	=> False,
					'properties' => Array(
						'last_modified'	=> True
					)
				),
				'mailto' => Array(
					'type'		=> 'function',
					'function'	=> 'switch_mailto',
					'quoted'	=> False,
					'to_text'	=> False,
					'properties' => Array(
						'attendee'	=> True,
						'organizer'	=> True
					)
				),
				'member' => Array(
					'type'		=> 'function',
					'function'	=> 'switch_mailto',
					'quoted'	=> True,
					'to_text'	=> False,
					'properties' => Array(
						'attendee'	=> True
					)
				),
				'partstat' => Array(
					'type'		=> 'function',
					'function'	=> 'switch_partstat',
					'quoted'	=> False,
					'to_text'	=> False,
					'properties' => Array(
						'attendee'	=> True,
						'organizer'	=> True
					)
				),
				'range' => Array(
					'type'		=> 'function',
					'function'	=> 'switch_range',
					'quoted'	=> False,
					'to_text'	=> False,
					'properties' => Array(
						'recurrence_id'	=> True
					)
				),
				'related' => Array(
					'type'		=> 'function',
					'function'	=> 'switch_related',
					'quoted'	=> False,
					'to_text'	=> False,
					'properties' => Array(
						'related_to'	=> True
					)
				),
				'role' => Array(
					'type'		=> 'function',
					'function'	=> 'switch_role',
					'quoted'	=> False,
					'to_text'	=> False,
					'properties' => Array(
						'attendee'	=> True,
						'organizer'	=> True
					)
				),
				'rsvp' => Array(
					'type'		=> 'function',
					'function'	=> 'switch_rsvp',
					'quoted'	=> False,
					'to_text'	=> False,
					'properties' => Array(
						'attendee'	=> True
					)
				),
				'sent_by' => Array(
					'type'		=> 'function',
					'function'	=> 'switch_mailto',
					'quoted'	=> True,
					'to_text'	=> False,
					'properties' => Array(
						'attendee'	=> True,
						'organizer'	=> True
					)
				),
				'tzid' => Array(
					'type'		=> 'text',
					'quoted'	=> False,
					'to_text'	=> False,
					'properties' => Array(
						'dtend'	=> True,
						'due'	=> True,
						'dtstart'	=> True,
						'exdate'	=> True,
						'rdate'		=> True,
						'recurrence_id'	=> True
					)
				),
				'until' => Array(
					'type'		=> 'function',
					'function'	=> 'switch_date',
					'quoted'	=> False,
					'to_text'	=> False,
					'properties' => Array(
						'rrule'		=> True
					)
				),
				'value' => Array(
					'type'		=> 'value',
					'quoted'	=> False,
					'to_text'	=> False,
					'properties' => Array(
						'calscale'	=> True,
						'prodid'	=> True,
						'method'	=> True,
						'version'	=> True,
						'attach'	=> True,
						'categories'	=> True,
						'class'		=> True,
						'comment'	=> True,
						'description'	=> True,
						'geo'		=> True,
						'location'	=> True,
						'percent'	=> True,
						'priority'	=> True,
						'resources'	=> True,
						'status'	=> True,
						'summary'	=> True,
						'completed'	=> True,
						'dtend'		=> True,
						'due'		=> True,
						'dtstart'	=> True,
						'duration'	=> True,
						'freebusy'	=> True,
						'transp'	=> True,
						'tzid'		=> True,
						'tzname'	=> True,
						'tzoffsetfrom'	=> True,
						'tzoffsetto'	=> True,
						'tzurl'		=> True,
						'attendee'	=> True,
						'contact'	=> True,
						'organizer'	=> True,
						'recurrence_id'	=> True,
						'url'		=> True,
						'uid'		=> True,
						'exdate'	=> True,
						'exrule'	=> True,
						'rdate'		=> True,
						'rrule'		=> True,
						'action'	=> True,
						'repeat'	=> True,
						'trigger'	=> True,
						'created'	=> True,
						'dtstamp'	=> True,
						'last_modified'	=> True,
						'sequence'	=> True,
						'x_type'	=> True,
						'request_status'=> True
					)
				),
				'wkst' => Array(
					'type'		=> 'string',
					'quoted'		=> False,
					'to_text'	=> False,
					'properties' => Array(
						'rrule'		=> True
					)
				),
				'x_type' => Array(
					'type'		=> 'x_type',
					'quoted'	=> False,
					'to_text'	=> False,
					'properties' => Array(
						'calscale'	=> True,
						'method'	=> True,
						'prodid'	=> True,
						'version'	=> True,
						'attach'	=> True,
						'categories'	=> True,
						'class'		=> True,
						'comment'	=> True,
						'description'	=> True,
						'geo'		=> True,
						'location'	=> True,
						'percent'	=> True,
						'priority'	=> True,
						'resources'	=> True,
						'status'	=> True,
						'summary'	=> True,
						'completed'	=> True,
						'dtend'		=> True,
						'due'		=> True,
						'dtstart'	=> True,
						'duration'	=> True,
						'freebusy'	=> True,
						'transp'	=> True,
						'tzid'		=> True,
						'tzname'	=> True,
						'tzoffsetfrom'	=> True,
						'tzoffsetto'	=> True,
						'tzurl'		=> True,
						'attendee'	=> True,
						'contact'	=> True,
						'organizer'	=> True,
						'recurrence_id'	=> True,
						'url'		=> True,
						'uid'		=> True,
						'exdate'	=> True,
						'exrule'	=> True,
						'rdate'		=> True,
						'rrule'		=> True,
						'action'	=> True,
						'repeat'	=> True,
						'trigger'	=> True,
						'created'	=> True,
						'dtstamp'	=> True,
						'last_modified'	=> True,
						'sequence'	=> True,
						'x_type'	=> True,
						'request_status'=> True
					)
				)
			);
			if(!is_object($GLOBALS['phpgw']->datetime))
			{
				$GLOBALS['phpgw']->datetime = createobject('phpgwapi.datetime');
			}
		}

		function set_var(&$event,$type,$value)
		{
			$type = strtolower(str_replace('-','_',$type));
			$event[$type] = $value;
			if(is_string($value))
			{
				$this->debug("Setting ".$type." = ".$value);
			}
			else
			{
				$this->debug("Setting ".$type." = "._debug_array($value,False));
			}
		}

		function read_line_unfold($ical_text)
		{
			if($this->line < count($ical_text))
			{
				$str = str_replace("\r\n",'',$ical_text[$this->line]);
				$this->line = $this->line + 1;
				while(ereg("^[[:space:]]",$ical_text[$this->line]))
				{
					$str .= substr(str_replace("\r\n",'',$ical_text[$this->line]),1);
					$this->line = $this->line + 1;
				}
				$this->debug("LINE : ".$str);
				return $str;
			}
			else
			{
				return False;
			}
		}

		function fold($str)
		{
			return $this->chunk_split==True ? chunk_split($str,FOLD_LENGTH,"\r\n") : $str."\r\n";
		}

		function strip_quotes($str)
		{
			return str_replace('"','',$str);
		}

		function from_text($str)
		{
			$str = str_replace("\\,",",",$str);
			$str = str_replace("\\;",";",$str);
			$str = str_replace("\\N","\n",$str);
			$str = str_replace("\\n","\n",$str);
			$str = str_replace("\\\\","\\",$str);
			return "$str";
		}

		function to_text($str)
		{
			$str = str_replace("\\","\\\\",$str);
			$str = str_replace(",","\\,",$str);
			$str = str_replace(";","\\;",$str);
			$str = str_replace("\n","\\n",$str);
			return "$str";
		}

		function from_dir($str)
		{
			return str_replace('=3D','=',str_replace('%20',' ',$str));
		}

		function to_dir($str)
		{
			return str_replace('=','=3D',str_replace(' ','%20',$str));
		}

		/**
		 * Searches all parameters allowed for a certain property
		 *
		 * @param string $property, every key from the $this->property
		 * @return array with allowed parameters (keys from $this->parameters which list $property under 'properties'
		 */
		function find_parameters($property)
		{
			static  $cached_returns;

			if(isset($cached_returns[$property]))
			{
				reset($cached_returns[$property]);
				return $cached_returns[$property];
			}

			reset($this->parameter);
			while(list($key,$param_array) = each($this->parameter))
			{
				if($param_array['properties'][$property])
				{
					$param[] = $key;
					$this->debug('Property : '.$property.' = Parameter : '.$key);
				}
			}
			reset($param);
			$cached_returns[$property] = $param;
			return $param;
		}

		function find_properties($ical_type)
		{
			static  $cached_returns;

			if(isset($cached_returns[$ical_type]))
			{
				reset($cached_returns[$ical_type]);
				return $cached_returns[$ical_type];
			}

			reset($this->property);
			while(list($key,$param_array) = each($this->property))
			{
				if($param_array[$ical_type])
				{
					$prop[] = $key;
				}
			}
			reset($prop);
			$cached_returns[$ical_type] = $prop;
			return $prop;
		}

		function new_ical()
		{
			return Array();
		}

		/*
		* Parse Functions
		*/

		function parse_geo(&$event,$value)
		{
			if(count($return_value) == 2)
			{
				$event['lat'] = $return_value[0];
				$event['lon'] = $return_value[1];
			}
		}

		function parse_xtype(&$event,$majortype,$value)
		{
			$temp_x_type['name'] = strtoupper(substr($majortype,2));
			$temp_x_type['value'] = $value;
			$event['x_type'][] = $temp_x_type;
		}

		function parse_parameters(&$event,$majortype,$value)
		{
			if(!ereg('[\=\;]',$value) || $majortype == 'url')
			{
				$return_value[] = Array(
					'param'	=> $majortype,
					'value'	=> $value
				);
				$value = '';
			}
			elseif(ereg('(.*(\:\\\\)?.*):(.*)',$value,$temp))
			{
				$this->debug('Value : '._debug_array($temp,False));
				$this->debug('Param '.$majortype.' Value : '.$temp[3]);
				if($temp[3])
				{
					$return_value[] = Array(
						'param'	=> $majortype,
						'value'	=> $temp[3]
					);
					$value = str_replace(':MAILTO','',$temp[1]);
				}
				while(ereg('(([A-Z\-]*)[=]([[:alnum:] \_\)\(\/\$\.\,\:\\\|\*\&\^\%\#\!\~\"\?\&\@\<\>\-]*))([\;]?)(.*)',$value,$temp))
				{
					$this->debug('Value : '._debug_array($temp,False));
					$this->debug('Param '.$temp[2].' Value : '.$temp[3]);
					$return_value[] = Array(
						'param'	=> $temp[2],
						'value'	=> $temp[3]
					);
					$value = chop($temp[5]);
					$this->debug('Value would be = '.$value);
				}
			}
			else
			{
				while(ereg('(([A-Z\-]*)[=]([[:alnum:] \_\)\(\/\$\.\,\:\\\|\*\&\^\%\#\!\~\"\?\&\@\<\>\-]*))([\;]?)(.*)',$value,$temp))
				{
					$this->debug('Value : '._debug_array($temp,False));
					$this->debug('Param '.$temp[2].' Value : '.$temp[3]);
					$return_value[] = Array(
						'param'	=> $temp[2],
						'value'	=> $temp[3]
					);
					$value = chop($temp[5]);
					$this->debug('Value would be = '.$value);
				}
			}

			for($i=0;$i<count($return_value);$i++)
			{
				$name = strtolower($return_value[$i]['param']);
				$value = $this->strip_quotes($return_value[$i]['value']);
				if(substr($name,0,2) == 'x-')
				{
					$param = 'x_type';
					$name = str_replace('-','_',$return_value[$i]['param']);
				}
				else
				{
					$param = str_replace('-','_',strtolower($name));
					if(!isset($this->parameter[$param]) || $majortype == 'tzid')
					{
						if($majortype == 'attendee' || $majortype == 'organizer')
						{
							$param = 'mailto';
							$name = $param;
						}
						else
						{
							$param = 'value';
						}
					}
				}
				// hack to write freebusy as value (freebusy:<value>) and not as param (freebusy:freebusy=<value>)
				if ($name == 'freebusy') $name = 'value';

				$this->debug('name : '.$name.' : Param = '.$param);
				if(@$this->parameter[$param]['properties'][$majortype])
				{
					
					switch(@$this->parameter[$param]['type'])
					{
						case 'dir':
							$this->set_var($event,$name,$this->from_dir($value));
							break;
						case 'text':
							$this->set_var($event,$name,$value);
							break;
						case 'x_type':
							$this->parse_xtype($event,$name,$value);
							break;
						case 'function':
							$function = $this->parameter[$param]['function'];
							$this->set_var($event,$name,$this->$function($value));
							break;
						case 'uri':
							if(@$this->parameter[$param]['to_text'])
							{
								$value = $this->to_text($value);
							}
							$this->set_var($event,$name,$value);
							break;
						case 'integer':
							$this->set_var($event,$name,(int)$value);
							break;
						case 'value':
							if(@$this->property[$majortype]['type'] == 'date-time')
							{
								$this->set_var($event,$param,$this->switch_date($name));
							}
							elseif($majortype == 'url')
							{
								$this->set_var($event,$param,$value);
							}
							elseif($value <> "\\n" && $value)
							{
								$this->set_var($event[$majortype],$param,$value);
							}
							$this->debug('Event : '._debug_array($event,False));
							break;
					}
				}
			}
		}

		function parse_value(&$event,$majortype,$value,$mode)
		{
			$var = Array();
			$this->debug('Mode : '.$mode.' Majortype : '.$majortype);
			$this->parse_parameters($var,$majortype,$value);
			if($this->property[$majortype][$mode]['multiples'])
			{
				$this->debug(_debug_array($var,False));
				$event[$majortype][] = $var;
			}
			else
			{
				$this->debug('Majortype : '.$majortype);
				$this->debug('Property : '.$this->property[$majortype]['type']);
				if($this->property[$majortype]['type'] == 'date-time')
				{
					$this->debug('Got a DATE-TIME type!');
					$t_var = $var[$majortype];
					unset($var[$majortype]);
					@reset($t_var);
					while(list($key,$val) = @each($t_var))
					{
						$var[$key] = $val;
					}
					$this->debug($majortype.' : '._debug_array($var,False));
				}
				$this->set_var($event,$majortype,$var);
			}
		}

		/*
		 * Build-Card Functions
		 */

		function build_xtype($x_type,$seperator='=')
		{
			$quote = '';
			if($seperator == '=')
			{
				$quote = '"';
			}

			$return_value = $this->fold('X-'.$x_type['name'].$seperator.$quote.$x_type['value'].$quote);
			if($seperator == '=')
			{
				return str_replace("\r\n",'',$return_value);
			}
			else
			{
				return $return_value;
			}
		}

		function build_parameters($event,$property)
		{
			$str = '';
			$include_mailto = False;
			$include_datetime = False;
			$param = $this->find_parameters($property);
			if($property == 'exdate')
			{
				while(list($key,$value) = each($event))
				{
					$exdates[] = $this->switch_date($value);
				}
				return ':'.implode($exdates,',');
			}
			else
			{
				while(list($dumb_key,$key) = each($param))
				{
					if($key == 'value')
					{
						continue;
					}
					if($key == 'mailto')
					{
						$include_mailto = True;
						continue;
					}
					$param_array = @$this->parameter[$key];
					$type = @$this->parameter[$key]['type'];
					if($type == 'date-time')
					{
						$include_datetime = True;
						continue;
					}
					$quote = (@$this->parameter[$key]['quoted']?'"':'');
					if(isset($event[$key]) && @$this->parameter[$key]['properties'][$property])
					{
						$change_text = @$this->parameter[$key]['to_text'];
						$value = $event[$key];
						if($change_text && $type == 'text')
						{
							$value = $this->to_text($value);
						}
						switch($type)
						{
							case 'dir':
								$str .= ';'.str_replace('_','-',strtoupper($key)).'='.$quote.$this->to_dir($value).$quote;
								break;
							case 'function':
								$str .= ';'.str_replace('_','-',strtoupper($key)).'=';
								$function = $this->parameter[$key]['function'];
								$this->debug($key.' Function Param : '.$value);
								$str .= $quote.$this->$function($value).$quote;
								break;
							case 'text':
							case 'string':
								$str .= ';'.strtoupper($key).'='.$quote.$value.$quote;
								break;
							case 'date-time':
								$str .= ($key=='until'?':':';UNTIL=').date('Ymd\THis',mktime($event['hour'],$event['min'],$event['sec'],$event['month'],$event['mday'],$event['year'])).(!@isset($event['tzid'])?'Z':'');
								break;
						}
						unset($value);
					}
				}

				if(!empty($event['x_type']))
				{
					$c_x_type = count($event['x_type']);
					for($j=0;$j<$c_x_type;$j++)
					{
						$str .= ';'.$this->build_xtype($event['x_type'][$j],'=');
					}
				}
				if(!empty($event['value']))
				{
					if($property == 'trigger')
					{
						$seperator = ';';
					}
					else
					{
						$seperator = ':';
					}
					$str .= $seperator.($this->parameter['value']['to_text']?$this->to_text($event['value']):$event['value']);
				}
				if($include_mailto == True)
				{
					$key = 'mailto';
					$function = $this->parameter[$key]['function'];
					$ret_value = $this->$function($event[$key]);
					$str .= ($ret_value?':'.$ret_value:'');
				}
				if($include_datetime == True || @$this->property[$property]['type'] == 'date-time')
				{
					$str .= ':'.date('Ymd\THis',mktime($event['hour'],$event['min'],$event['sec'],$event['month'],$event['mday'],$event['year'])).(!@isset($event['tzid'])?'Z':'');
				}
				return ($property=='rrule'?':'.substr($str,1):$str);
			}
		}

		function build_text($event,$property)
		{
			$str = '';
			$param = $this->find_parameters($property);
			while(list($dumb_key,$key) = each($param))
			{
				if(!empty($event[$key]) && $key != 'value')
				{
					$type = @$this->parameter[$key]['type'];
					$quote = @$this->parameter[$key]['quote'];
					if(@$this->parameter[$key]['to_text'] == True)
					{
						$value = $this->to_text($event[$key]);
					}
					else
					{
						$value = $event[$key];
					}
					switch($type)
					{
						case 'text':
						$str .= ';'.strtoupper($key).'='.$quote.$value.$quote;
						break;
					}
				}
			}
			if(!empty($event['x_type']))
			{
				$c_x_type = count($event['x_type']);
				for($j=0;$j<$c_x_type;$j++)
				{
					$str .= ';'.$this->build_xtype($event['x_type'][$j],'=');
				}
			}
			if(!empty($event['value']))
			{
				$str .= ':'.($this->parameter['value']['to_text']?$this->to_text($event['value']):$event['value']);
			}
			return $str;
		}

		function build_card_internals($ical_item,$event)
		{
			foreach($this->find_properties($ical_item) as $value)
			{
				$varray = $this->property[$value];

				$type   = $varray['type'];
				$to_text = $varray['to_text'];
				$state  = @$varray[$ical_item]['state'];
				$multiples  = @$varray[$ical_item]['multiples'];
				switch($type)
				{
					case 'date-time':
						if(!empty($event[$value]))
						{
							if($multiples && $value != 'exdate')
							{
								for($i=0;$i<count($event[$value]);$i++)
								{
									$str .= $this->fold(strtoupper(str_replace('_','-',$value)).$this->build_parameters($event[$value][$i],$value));
								}
							}
							else
							{
								$str .= $this->fold(strtoupper(str_replace('_','-',$value)).$this->build_parameters($event[$value],$value));
							}
						}
						elseif($value == 'dtstamp' || $value == 'created')
						{
							$str .= $this->fold(strtoupper(str_replace('_','-',$value)).':'.gmdate('Ymd\THis\Z'));
						}
						break;
					case 'uri':
						if(!empty($event[$value]))
						{
							for($i=0;$i<count($event[$value]);$i++)
							{
								$str .= $this->fold(strtoupper(str_replace('_','-',$value)).$this->build_parameters($event[$value][$i],$to_text));
							}
						}
						break;
					case 'recur':
						if(!empty($event[$value]))
						{
							if($multiples)
							{
								for($i=0;$i<count($event[$value]);$i++)
								{
									$str .= $this->fold(strtoupper(str_replace('_','-',$value)).$this->build_parameters($event[$value][$i],$value));
								}
							}
							else
							{
								$str .= $this->fold(strtoupper(str_replace('_','-',$value)).$this->build_parameters($event[$value],$value));
							}
						}
						break;
					case 'integer':
						if(!empty($event[$value]))
						{
							$str .= $this->fold(strtoupper(str_replace('_','-',$value)).':'.$event[$value]);
						}
						elseif($value == 'sequence' || $value == 'percent_complete')
						{
							$str .= $this->fold(strtoupper(str_replace('_','-',$value)).':0');
						}
						break;
					case 'function':
						$str .= ';'.str_replace('_','-',strtoupper($value)).'=';
						$function = @$this->parameter[$key]['function'];
						$str .= (@$this->parameter[$key]['quoted']?'"':'').$this->$function($event[$key]).(@$this->parameter[$key]['quoted']?'"':'');
						break;
					case 'float':
						if(!empty($event[$value]))
						{
							$str .= $this->fold(strtoupper(str_replace('_','-',$value)).':'.$event[$value]['lat'].';'.$event[$value]['lon']);
						}
						break;
					case 'text':
						if(isset($event[$value]))
						{
							if(@$this->parameter[$key]['type'] != 'function')
							{
								if($multiples && count($event[$value]) > 1)
								{
									for($i=0;$i<count($event[$value]);$i++)
									{
										$str .= $this->fold(strtoupper(str_replace('_','-',$value)).$this->build_parameters($event[$value][$i],$value));
									}
								}
								else
								{
									$str .= $this->fold(strtoupper(str_replace('_','-',$value)).$this->build_parameters($event[$value],$value));
								}
							}
							else
							{
								$function = $this->parameter[$value]['function'];
								if($multiples)
								{
									for($i=0;$i<count($event[$value]);$i++)
									{
										$str .= $this->fold(strtoupper(str_replace('_','-',$value)).':'.$this->$function($event[$value][$i]));
									}
								}
								else
								{
									$str .= $this->fold(strtoupper(str_replace('_','-',$value)).':'.$this->$function($event[$value]));
								}
							}
						}
						break;
					case 'cal-address':
						if(is_array($event[$value][0]))
						{
							for($j=0;$j<count($event[$value]);$j++)
							{
								$temp_output = $this->build_parameters($event[$value][$j],$value);
								if($temp_output)
								{
									$str .= $this->fold(strtoupper(str_replace('_','-',$value)).$temp_output);
								}
							}
						}
						else
						{
							$temp_output = $this->build_parameters($event[$value],$value);
							if($temp_output)
							{
								$str .= $this->fold(strtoupper(str_replace('_','-',$value)).$temp_output);
							}
						}
						break;
				}
			}
			if(!empty($event['x_type']))
			{
				for($i=0;$i<count($event['x_type']);$i++)
				{
					$str .= $this->build_xtype($event['x_type'][$i],':');
				}
			}

			if($ical_item == 'vtimezone')
			{
				if($event['tzdata'])
				{
					for($k=0;$k<count($event['tzdata']);$k++)
					{
						$str .= 'BEGIN:'.strtoupper($event['tzdata'][$k]['type'])."\r\n";
						$str .= $this->build_card_internals(strtolower($event['tzdata'][$k]['type']),$event['tzdata'][$k]);
						$str .= 'END:'.strtoupper($event['tzdata'][$k]['type'])."\r\n";
					}
				}
			}
			elseif($event['alarm'])
			{
				for($k=0;$k<count($event['alarm']);$k++)
				{
					$str .= 'BEGIN:VALARM'."\r\n";
					$str .= $this->build_card_internals('valarm',$event['alarm'][$k]);
					$str .= 'END:VALARM'."\r\n";
				}
			}
			return $str;
		}

		/*
		* Switching Functions
		*/

		function switch_class($var)
		{
			if(is_string($var))
			{
				switch($var)
				{
					case 'PRIVATE':
						return PRIVATE;
						break;
					case 'PUBLIC':
						return PUBLIC;
						break;
					case 'CONFIDENTIAL':
						return CONFIDENTIAL;
						break;
				}
			}
			elseif(is_int($var))
			{
				switch((int)$var)
				{
					case PRIVATE:
						return 'PRIVATE';
						break;
					case PUBLIC:
						return 'PUBLIC';
						break;
					case CONFIDENTIAL:
						return 'CONFIDENTIAL';
						break;
				}
			}
			else
			{
				return $var;
			}
		}

		function switch_cu($var)
		{
			if(gettype($var) == 'string')
			{
				switch($var)
				{
					case 'INDIVIDUAL':
						return INDIVIDUAL;
						break;
					case 'GROUP':
						return GROUP;
						break;
					case 'RESOURCE':
						return RESOURCE;
						break;
					case 'ROOM':
						return ROOM;
						break;
					case 'UNKNOWN':
						return UNKNOWN;
						break;
					default:
						return OTHER;
						break;
				}
			}
			elseif(gettype($var) == 'integer')
			{
				switch($var)
				{
					case INDIVIDUAL:
						return 'INDIVIDUAL';
						break;
					case GROUP:
						return 'GROUP';
						break;
					case RESOURCE:
						return 'RESOURCE';
						break;
					case ROOM:
						return 'ROOM';
						break;
					case UNKNOWN:
						return 'UNKNOWN';
						break;
					default:
						return 'X-OTHER';
						break;
				}
			}
			else
			{
				return $var;
			}
		}

		function switch_date($var)
		{
		$this->debug('SWITCH_DATE: gettype = '.gettype($var));
			if(is_string($var))
			{
				$dtime = Array();
				if(strpos($var,':'))
				{
					$pos = explode(':',$var);
					$var = $pos[1];
				}
				$this->set_var($dtime,'year',(int)(substr($var,0,4)));
				$this->set_var($dtime,'month',(int)(substr($var,4,2)));
				$this->set_var($dtime,'mday',(int)(substr($var,6,2)));
				if(substr($var,8,1) == 'T')
				{
					$this->set_var($dtime,'hour',(int)(substr($var,9,2)));
					$this->set_var($dtime,'min',(int)(substr($var,11,2)));
					$this->set_var($dtime,'sec',(int)(substr($var,13,2)));
					if(strlen($var) > 14)
					{
						if(substr($var,14,1) != 'Z')
						{
							if($this->api)
							{
								$dtime['hour'] -= $GLOBALS['phpgw_info']['users']['common']['tz_offset'];
								if($dtime['hour'] < 0)
								{
									$dtime['mday'] -= 1;
									$dtime['hour'] = 24 - $dtime['hour'];
								}
								elseif($dtime['hour'] >= 24)
								{
									$dtime['mday'] += 1;
									$dtime['hour'] = $dtime['hour'] - 24;
								}
							}
						}
					}
				}
				else
				{
					$this->set_var($dtime,'hour',0);
					$this->set_var($dtime,'min',0);
					$this->set_var($dtime,'sec',0);
					if($this->api)
					{
						$dtime['hour'] -= $GLOBALS['phpgw_info']['users']['common']['tz_offset'];
						if($dtime['hour'] < 0)
						{
							$dtime['mday'] -= 1;
							$dtime['hour'] = 24 - $dtime['hour'];
						}
						elseif($dtime['hour'] >= 24)
						{
							$dtime['mday'] += 1;
							$dtime['hour'] = $dtime['hour'] - 24;
						}
					}
				}
				$this->debug('DATETIME : '._debug_array($dtime,False));
				return $dtime;
			}
			elseif(is_array($var))
			{
				return date('Ymd\THis\Z',mktime($var['hour'],$var['min'],$var['sec'],$var['month'],$var['mday'],$var['year']));
			}
			else
			{
				return $var;
			}
		}

		function switch_encoding($var)
		{
			if(is_string($var))
			{
				switch($var)
				{
					case '8BIT':
						return _8BIT;
						break;
					case 'BASE64':
						return _BASE64;
						break;
					default:
						return OTHER;
						break;
				}
			}
			elseif(is_int($var))
			{
				switch($var)
				{
					case _8BIT:
						return '8BIT';
						break;
					case _BASE64:
						return 'BASE64';
						break;
					case OTHER:
						return 'OTHER';
						break;
				}
			}
			else
			{
				return $var;
			}
		}

		function switch_fbtype($var)
		{
			if(is_string($var))
			{
				switch($var)
				{
					case 'FREE':
						return FREE;
						break;
					case 'BUSY':
						return BUSY;
						break;
					case 'BUSY-UNAVAILABLE':
						return BUSY_UNAVAILABLE;
						break;
					case 'BUSY-TENTATIVE':
						return BUSY_TENTATIVE;
						break;
					default:
						return OTHER;
						break;
				}
			}
			elseif(is_int($var))
			{
				switch($var)
				{
					case FREE:
						return 'FREE';
						break;
					case BUSY:
						return 'BUSY';
						break;
					case BUSY_UNAVAILABLE:
						return 'BUSY-UNAVAILABLE';
						break;
					case BUSY_TENTATIVE:
						return 'BUSY-TENTATIVE';
						break;
					default:
						return 'OTHER';
						break;
				}
			}
			else
			{
				return $var;
			}
		}

		function switch_freq($var)
		{
			if(is_string($var))
			{
				switch($var)
				{
					case 'SECONDLY':
						return SECONDLY;
						break;
					case 'MINUTELY':
						return MINUTELY;
						break;
					case 'HOURLY':
						return HOURLY;
						break;
					case 'DAILY':
						return DAILY;
						break;
					case 'WEEKLY':
						return WEEKLY;
						break;
					case 'MONTHLY':
						return MONTHLY;
						break;
					case 'YEARLY':
						return YEARLY;
						break;
				}
			}
			elseif(is_int($var))
			{
				switch($var)
				{
					case SECONDLY:
						return 'SECONDLY';
						break;
					case MINUTELY:
						return 'MINUTELY';
						break;
					case HOURLY:
						return 'HOURLY';
						break;
					case DAILY:
						return 'DAILY';
						break;
					case WEEKLY:
						return 'WEEKLY';
						break;
					case MONTHLY:
						return 'MONTHLY';
						break;
					case YEARLY:
						return 'YEARLY';
						break;
				}
			}
			else
			{
				return $var;
			}
		}

		function switch_mailto($var)
		{
			if(is_string($var))
			{
				if(strpos(' '.$var,':'))
				{
					$parts = explode(':',$var);
					$var = $parts[1];
				}

				$parts = explode('@',$var);
				$this->debug("Count of mailto parts : ".count($parts));
				if(count($parts) == 2)
				{
					$this->debug("Splitting ".$parts[0]." @ ".$parts[1]);
					$temp_address = Array();
					$temp_address['user'] = $parts[0];
					$temp_address['host'] = $parts[1];
					return $temp_address;
				}
				else
				{
					return False;
				}
			}
			elseif(is_array($var))
			{
				return $var['user'].'@'.$var['host'];
			}
		}

		function switch_partstat($var)
		{
			$this->debug('PARTSTAT = '.$var);
			if(is_string($var))
			{
				switch($var)
				{
					case 'NEEDS-ACTION':
						return NEEDS_ACTION;
						break;
					case 'ACCEPTED':
						return ACCEPTED;
						break;
					case 'DECLINED':
						return DECLINED;
						break;
					case 'TENTATIVE':
						return TENTATIVE;
						break;
					case 'DELEGATED':
						return DELEGATED;
						break;
					case 'COMPLETED':
						return COMPLETED;
						break;
					case 'IN-PROCESS':
						return IN_PROCESS;
						break;
					default:
						return OTHER;
						break;
				}
			}
			elseif(is_int($var))
			{
				switch((int)$var)
				{
					case NEEDS_ACTION:
						return 'NEEDS-ACTION';
						break;
					case ACCEPTED:
						return 'ACCEPTED';
						break;
					case DECLINED:
						return 'DECLINED';
						break;
					case TENTATIVE:
						return 'TENTATIVE';
						break;
					case DELEGATED:
						return 'DELEGATED';
						break;
					case COMPLETED:
						return 'COMPLETED';
						break;
					case IN_PROCESS:
						return 'IN-PROCESS';
						break;
					default:
						return 'X-OTHER';
						break;
				}
			}
			else
			{
				return $var;
			}
		}

		function switch_range($var)
		{
			if(is_string($var))
			{
				switch($var)
				{
					case 'THISANDPRIOR':
						return THISANDPRIOR;
						break;
					case 'THISANDFUTURE':
						return THISANDFUTURE;
						break;
				}
			}
			elseif(is_int($var))
			{
				switch($var)
				{
					case THISANDPRIOR:
						return 'THISANDPRIOR';
						break;
					case THISANDFUTURE:
						return 'THISANDFUTURE';
						break;
				}
			}
			else
			{
				return $var;
			}
		}

		function switch_related($var)
		{
			if(is_string($var))
			{
				switch($var)
				{
					case 'START':
						return START;
						break;
					case 'END':
						return END;
						break;
				}
			}
			elseif(is_int($var))
			{
				switch($var)
				{
					case START:
						return 'START';
						break;
					case END:
						return 'END';
						break;
				}
			}
			else
			{
				return $var;
			}
		}

		function switch_reltype($var)
		{
			if(is_string($var))
			{
				switch($var)
				{
					case 'PARENT':
						return PARENT;
						break;
					case 'CHILD':
						return CHILD;
						break;
					case 'SIBLING':
						return SIBLING;
						break;
				}
			}
			elseif(is_int($var))
			{
				switch($var)
				{
					case PARENT:
						return 'PARENT';
						break;
					case CHILD:
						return 'CHILD';
						break;
					case SIBLING:
						return 'SIBLING';
						break;
				}
			}
			else
			{
				return $var;
			}
		}

		function switch_role($var)
		{
			if(is_string($var))
			{
				switch($var)
				{
					case 'NONE':
						return NONE;
						break;
					case 'CHAIR':
						return CHAIR;
						break;
					case 'REQ-PARTICIPANT':
						return REQ_PARTICIPANT;
						break;
					case 'OPT-PARTICIPANT':
						return OPT_PARTICIPANT;
						break;
					case 'NON-PARTICIPANT':
						return NON_PARTICIPANT;
						break;
				}
			}
			elseif(is_int($var))
			{
				switch($var)
				{
					case NONE:
						return 'NONE';
						break;
					case CHAIR:
						return 'CHAIR';
						break;
					case REQ_PARTICIPANT:
						return 'REQ-PARTICIPANT';
						break;
					case OPT_PARTICIPANT:
						return 'OPT-PARTICIPANT';
						break;
					case NON_PARTICIPANT:
						return 'NON-PARTICIPANT';
						break;
				}
			}
			else
			{
				return $var;
			}
		}

		function switch_rsvp($var)
		{
			if(is_string($var))
			{
				if($var == 'TRUE')
				{
					return 1;
				}
				elseif($var == 'FALSE')
				{
					return 0;
				}
			}
			elseif(is_int($var) || $var == False)
			{
				if($var == 1)
				{
					return 'TRUE';
				}
				elseif($var == 0)
				{
					return 'FALSE';
				}
			}
			else
			{
				return $var;
			}
		}

		function switch_transp($var)
		{
			if(is_string($var))
			{
				switch($var)
				{
					case 'TRANSPARENT':
						return TRANSPARENT;
						break;
					case 'OPAQUE':
						return OPAQUE;
						break;
				}
			}
			elseif(is_int($var))
			{
				switch($var)
				{
					case TRANSPARENT:
						return 'TRANSPARENT';
						break;
					case OPAQUE:
						return 'OPAQUE';
						break;
				}
			}
			else
			{
				return $var;
			}
		}

		/*
		 * The brunt of the class
		 */

		function parse($ical_text)
		{
			$begin_regexp = '^';
			$semi_colon_regexp = '[\;\:]';
			$colon_regexp = '[\:]';
			$catch_all_regexp = '(.*)';
			$end_regexp = '$';
			$property_regexp = $begin_regexp.'([A-Z\-]*)'.$semi_colon_regexp.$catch_all_regexp.$end_regexp;
			$param_regexp = $begin_regexp.$catch_all_regexp.':'.$catch_all_regexp.$end_regexp;

			$mode = 'none';
			$text = $this->read_line_unfold($ical_text);
			while($text)
			{
				ereg($property_regexp,$text,$temp);
				$majortype = str_replace('-','_',strtolower($temp[1]));
				$value = chop($temp[2]);

				if($mode != 'none' && ($majortype != 'begin' && $majortype != 'end'))
				{
					$this->debug('PARSE:MAJORTYPE : '.$majortype);
					if(isset($this->property[$majortype]))
					{
						$state = @$this->property[$majortype]["$mode"]['state'];
						$type = @$this->property[$majortype]['type'];
						$multiples = @$this->property[$majortype]["$mode"]['multiples'];
						$do_to_text = @$this->property[$majortype]['to_text'];
					}
					elseif(substr($majortype,0,2) == 'x_')
					{
						$state = 'optional';
						$type = 'xtype';
						$multiples = True;
						$do_to_test = True;
					}
					else
					{
						$state = '';
					}
				}
				else
				{
					$state = 'required';
				}

				if($majortype == 'duration')
				{
					unset($dur);

					// Split �DURATION�
					list($_f_['day_raw'], $_f_['time_raw']) = split('T', substr($value, 1, strlen($value)-1));

					/* Datecode */
					if(isset($_f_['day_raw']) OR $_f_['day_raw'])
					{
						/* Days */
						if(strstr($_f_['day_raw'],'D'))
						{
							$dur['days'] = eregi_replace("([0-9]+)D(.*)", "\\1", $_f_['day_raw']);
						}

						/* Weeks */
						if(strstr($_f_['day_raw'],'W'))
						{
							$dur['weeks'] = eregi_replace("([^|.*]+D)?([0-9]+)W", "\\2", $_f_['day_raw']);
						}
					}

					/* Timecode */
					if(isset($_f_['time_raw']) OR $_f_['time_raw'])
					{
						/* Hours */
						if(strstr($_f_['time_raw'],'H'))
						{
							$dur['hours'] = eregi_replace("([0-9]+)H(.*)", "\\1", $_f_['time_raw']);
						}

						/* Minutes */
						if(strstr($_f_['time_raw'],'M'))
						{
							$dur['minutes'] = eregi_replace("([^|.*]+H)?([0-9]+)M(.*)", "\\2", $_f_['time_raw']);
						}

						/* Seconds */
						if(strstr($_f_['time_raw'],'S'))
						{
							$dur['seconds'] = eregi_replace("([^|.*]+M)?([0-9]+)S(.*)", "\\2", $_f_['time_raw']);
						}
					}

					$dur['raw'] = Array(
						'timecode' => $_f_['time_raw'],
						'datecode' => $_f_['day_raw'],
						'all'      => $value
					);
					/* Add new parameters in Event */
					$this->set_var($event, $majortype, $dur);
				}

				if($majortype == 'begin')
				{
					$tmode = $mode;
					$mode = strtolower($value);
					switch(strtolower($value))
					{
						case 'daylight':
						case 'standard':
							$t_event = Array();
							$t_event = $event;
							$event = Array();
							break;
						case 'valarm':
							if($tmode == 'vevent' || $tmode == 'vtodo')
							{
								$t_event = $event;
								unset($event);
								$event = Array();
							}
							else
							{
								$mode = $tmode;
							}
							break;
						case 'vcalendar':
							$ical = $this->new_ical();
							break;
						case 'vevent':
						case 'vfreebusy':
						case 'vjournal':
						case 'vtimezone':
						case 'vtodo':
							$event = Array();
							break;
					}
					$event['type'] = strtolower($value);
				}
				elseif($majortype == 'end')
				{
					$mode = 'none';
					switch(strtolower($value))
					{
						case 'daylight':
						case 'standard':
							$tzdata[] = $event;
							unset($event);
							$event = $t_event;
							unset($t_event);
							$mode = 'vtimezone';
							break;
						case 'valarm':
							$alarm[] = $event;
							unset($event);
							$event = $t_event;
							unset($t_event);
							$mode = $tmode;
							break;
						case 'vevent':
							if(!empty($alarm))
							{
								$event['alarm'] = $alarm;
								unset($alarm);
							}
							$this->event[] = $event;
							unset($event);
							break;
						case 'vfreebusy':
							$this->freebusy[] = $event;
							unset($event);
							break;
						case 'vjournal':
							$this->journal[] = $event;
							unset($event);
							break;
						case 'vtimezone':
							if(!empty($tzdata))
							{
								$event['tzdata'] = $tzdata;
								unset($tzdata);
							}
							$this->timezone[] = $event;
							unset($event);
							break;
						case 'vtodo':
							if(!empty($alarm))
							{
								$event['alarm'] = $alarm;
								unset($alarm);
							}
							$this->todo[] = $event['alarm'];
							unset($event);
							break;
						case 'vcalendar':
							$this->ical = $ical;
							$this->ical['event'] = $this->event;
							$this->ical['freebusy'] = $this->freebusy;
							$this->ical['journal'] = $this->journal;
							$this->ical['timezone'] = $this->timezone;
							$this->ical['todo'] = $this->todo;
							break 2;
					}
				}
				elseif($majortype == 'prodid' || $majortype == 'version' || $majortype == 'method' || $majortype == 'calscale')
				{
					$this->parse_parameters($ical,$majortype,$this->from_text($value));
				}
				elseif($state == 'optional' || $state == 'required')
				{
					$this->debug('Mode : '.$mode.' Majortype : '.$majortype);
					if($do_to_text)
					{
						$value = $this->from_text($value);
					}
					switch($type)
					{
						case 'text':
							$this->parse_parameters($event,$majortype,$value);
							break;
						case 'recur':
						case 'date-time':
						case 'cal-address':
							$this->parse_value($event,$majortype,$value,$mode);
							break;
						case 'integer':
							if($multiples)
							{
								$event[$majortype][] = (int)$value;
							}
							else
							{
								$this->set_var($event,$majortype,(int)$value);
							}
							break;
						case 'float':
							$event->$majortype = new class_geo;
							$this->parse_geo($event->$majortype,$value);
							break;
						case 'utc-offset':
							$this->set_var($event,$majortype,(int)$value);
							break;
						case 'uri':
							$new_var = Array();
							$this->parse_parameters($new_var,$majortype,$value);
							if($multiples)
							{
								switch($mode)
								{
									case 'valarm':
										$alarm['attach'][] = $new_var;
										break;
									default:
										$event[$majortype][] = $new_var;
										break;
								}
							}
							else
							{
								$event[$majortype] = $new_var;
							}
							unset($new_var);
							break;
						case 'xtype':
							$this->parse_xtype($event,$majortype,$value);
							break;
					}
				}
				$text = $this->read_line_unfold($ical_text);
			}
			return $this->ical;
		}

		function build_ical($ical)
		{
			$var = Array(
				'timezone',
				'event',
				'todo',
				'journal',
				'freebusy'
			);

			$str = 'BEGIN:VCALENDAR'."\r\n";
			$str .= $this->fold('PRODID'.$this->build_text($ical['prodid'],'prodid'));
			$str .= $this->fold('VERSION'.$this->build_text($ical['version'],'version'));
			$str .= $this->fold('METHOD'.$this->build_text($ical['method'],'method'));
			foreach($var as $vtype)
			{
				if($ical[$vtype])
				{
					for($i=0;$i<count($ical[$vtype]);$i++)
					{
						$str .= 'BEGIN:V'.strtoupper($vtype)."\r\n";
						$str .= $this->build_card_internals('v'.$vtype,$ical[$vtype][$i]);
						$str .= 'END:V'.strtoupper($vtype)."\r\n";
					}
				}
			}
			$str .= 'END:VCALENDAR'."\r\n";

			return $str;
		}

		function switch_to_phpgw_status($partstat)
		{
			switch($partstat)
			{
				case 0:
					return 'U';
					break;
				case 1:
					return 'A';
					break;
				case 2:
					return 'R';
					break;
				case 3:
					return 'T';
					break;
				default:
					return 'U';
					break;
			}
		}

		function switch_phpgw_status($status)
		{
			switch($status)
			{
				case 'U':
					return 0;
					break;
				case 'A':
					return 1;
					break;
				case 'R':
					return 2;
					break;
				case 'T':
					return 3;
					break;
			}
		}

		function is_owner($part_record)
		{
			if(($part_record['mailto']['user'].'@'.$part_record['mailto']['host'] == $GLOBALS['phpgw_info']['user']['preferences']['email']['address']) ||
				($part_record['cn'] == $GLOBALS['phpgw_info']['user']['account_lid']))
			{
				return True;
			}
			else
			{
				return False;
			}
		}

		function check_owner(&$event,$ical,$so_event)
		{
			if(!isset($event['participant'][$GLOBALS['phpgw_info']['user']['account_id']]))
			{
				if(isset($ical['organizer']))
				{
					if($this->is_owner($ical['organizer']))
					{
						$so_event->add_attribute('owner',$GLOBALS['phpgw_info']['user']['account_id']);
						$so_event->add_attribute('participants',$this->switch_to_phpgw_status($ical['organizer']['partstat']),$GLOBALS['phpgw_info']['user']['account_id']);
					}
				}
				elseif(isset($ical['attendee']))
				{
					$attendee_count = count($ical['attendee']);

					for($j=0;$j<$attendee_count;$j++)
					{
						if($this->is_owner($ical['attendee'][$j]))
						{
							$so_event->add_attribute('participants',$this->switch_to_phpgw_status($ical['attendee'][$j]['partstat']),(int)$GLOBALS['phpgw_info']['user']['account_id']);
						}
					}
				}
				else
				{
					$so_event->add_attribute('owner',$GLOBALS['phpgw_info']['user']['account_id']);
					$so_event->add_attribute('participants','A',$GLOBALS['phpgw_info']['user']['account_id']);
				}
			}
		}

		function import_file()
		{
			if($_FILES['uploadedfile']['tmp_name'] == 'none' || $_FILES['uploadedfile']['tmp_name'] == '')
			{
				Header('Location: ' . $GLOBALS['phpgw']->link('/index.php',
						Array(
							'menuaction'	=> 'calendar.uiicalendar.import',
							'action'	=> 'GetFile'
						)
					)
				);
				$GLOBALS['phpwg']->common->phpgw_exit();
			}
			$uploaddir = $GLOBALS['phpgw_info']['server']['temp_dir'] . SEP;

			srand((double)microtime()*1000000);
			$random_number = rand(100000000,999999999);
			$newfilename = md5($_FILES['uploadedfile']['tmp_name'].", ".$uploadedfile_name.", "
				. time() . getenv("REMOTE_ADDR") . $random_number );

			$filename = $uploaddir . $newfilename;

			move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $filename);
			return $filename;
		}

		function import($mime_msg='')
		{
			if($_FILES['uploadedfile']['tmp_name'] != 'none' && $_FILES['uploadedfile']['tmp_name'] != '')
			{
				$filename = $this->import_file();
				$fp=fopen($filename,'rt');
				$mime_msg = fread($fp, filesize($filename));
				fclose($fp);
				unlink($filename);

				/* explode can fail easily, so pre-chew the ical file */
				$mime_msg = preg_replace("/(\r\n|\r)/", "\n", $mime_msg);	/* dos2unix */
				$mime_msg = preg_replace("/\n\n+/", "\n", $mime_msg);	/* strip duplicate newlines */
				$mime_msg = explode("\n",$mime_msg); 	/* explode the sanitized message into itself */
			}
			elseif(!$mime_msg)
			{
				Header('Location: ' . $GLOBALS['phpgw']->link('/index.php',
						Array(
							'menuaction'	=> 'calendar.uiicalendar.import',
							'action'	=> 'GetFile'
						)
					)
				);
				$GLOBALS['phpwg']->common->phpgw_exit();
			}

			if(!is_object($GLOBALS['uicalendar']))
			{
				$so_event = createobject('calendar.socalendar',
					Array(
						'owner'	=> 0,
						'filter'	=> '',
						'category'	=> ''
					)
				);
			}
			else
			{
				$so_event = $GLOBALS['uicalendar']->bo->so;
			}

			$datetime_vars = Array(
				'start'		=> 'dtstart',
				'end'		=> 'dtend',
				'modtime'	=> 'dtstamp',
				'modtime'	=> 'last_modified'
			);

			$date_array = Array(
				'Y'	=> 'year',
				'm'	=> 'month',
				'd'	=> 'mday',
				'H'	=> 'hour',
				'i'	=> 'min',
				's'	=> 'sec'
			);

			/* time limit should be controlled elsewhere */
			@set_time_limit(0);

			$GLOBALS['phpgw_info']['user']['preferences'] = $GLOBALS['phpgw']->preferences->create_email_preferences();
			$users_email = $GLOBALS['phpgw_info']['user']['preferences']['email']['address'];
			$cats = CreateObject('phpgwapi.categories');
			$ical = $this->parse($mime_msg);
			switch($ical['version']['value'])
			{
				case '1.0':
					$cat_sep = ';';
					break;
				case '2.0':
				default:
					$cat_sep = ',';
					break;
			}
			$c_events = count($ical['event']);
			for($i=0;$i<$c_events;$i++)
			{
				if($ical['event'][$i]['uid']['value'])
				{
					$uid_exists = $so_event->find_uid($ical['event'][$i]['uid']['value']);
				}
				else
				{
					$uid_exists = False;
				}
				if($uid_exists)
				{
					$event = $so_event->read_entry($uid_exists);
					$this->check_owner($event,$ical['event'][$i],$so_event);
					$event = $so_event->get_cached_event();
					$so_event->add_entry($event);
				}
				else
				{
					$so_event->event_init();
					$so_event->add_attribute('id',0);
					$so_event->add_attribute('reference',0);
					if($ical['event'][$i]['uid']['value'])
					{
						$so_event->add_attribute('uid',$ical['event'][$i]['uid']['value']);
					}
					if($ical['event'][$i]['summary']['value'])
					{
						$so_event->set_title($ical['event'][$i]['summary']['value']);
					}
					if($ical['event'][$i]['description']['value'])
					{
						$so_event->set_description($ical['event'][$i]['description']['value']);
					}
					if($ical['event'][$i]['location']['value'])
					{
						$so_event->add_attribute('location',$ical['event'][$i]['location']['value']);
					}
					if(isset($ical['event'][$i]['priority']))
					{
						$so_event->add_attribute('priority',$ical['event'][$i]['priority']);
					}
					else
					{
						$so_event->add_attribute('priority',2);
					}
					if(!isset($ical['event'][$i]['class']))
					{
						$ical['event'][$i]['class'] = 1;
					}
					$so_event->set_class($ical['event'][$i]['class']);

					@reset($datetime_vars);
					while(list($e_datevar,$i_datevar) = each($datetime_vars))
					{
						if(isset($ical['event'][$i][$i_datevar]))
						{
							$temp_time = $so_event->maketime($ical['event'][$i][$i_datevar]) + $GLOBALS['phpgw']->datetime->tz_offset;
							@reset($date_array);
							while(list($key,$var) = each($date_array))
							{
								$event[$e_datevar][$var] = (int)(date($key,$temp_time));
							}
							$so_event->set_date($e_datevar,$event[$e_datevar]['year'],$event[$e_datevar]['month'],$event[$e_datevar]['mday'],$event[$e_datevar]['hour'],$event[$e_datevar]['min'],$event[$e_datevar]['sec']);
						}
					}
					if(!isset($ical['event'][$i]['categories']['value']) || !$ical['event'][$i]['categories']['value'])
					{
						$so_event->set_category(0);
					}
					else
					{
						$ical_cats = Array();
						if(strpos($ical['event'][$i]['categories']['value'],$cat_sep))
						{
							$ical_cats = explode($cat_sep,$ical['event'][$i]['categories']['value']);
						}
						else
						{
							$ical_cats[] = $ical['event'][$i]['categories']['value'];
						}

						@reset($ical_cats);
						$cat_id_nums = Array();
						while(list($key,$cat) = each($ical_cats))
						{
							if(!$cats->exists('appandmains',$cat))
							{
								$cats->add(
									Array(
										'name'	=> $cat,
										'descr'	=> $cat,
										'parent'	=> '',
										'access'	=> 'private',
										'data'	=> ''
									)
								);
							}
							$cat_id_nums[] = $cats->name2id($cat);
						}
						@reset($cat_id_nums);
						if(count($cat_id_nums) > 1)
						{
							$so_event->set_category(implode($cat_id_nums,','));
						}
						else
						{
							$so_event->set_category($cat_id_nums[0]);
						}
					}

					/* rrule */
					if(isset($ical['event'][$i]['rrule']) || isset($ical['event'][$i]['duration']))
					{
						/* recur_enddate */
						if(isset($ical['event'][$i]['rrule']['until']))
						{
							$recur_enddate['year']  = (int)($ical['event'][$i]['rrule']['until']['year']);
							$recur_enddate['month'] = (int)($ical['event'][$i]['rrule']['until']['month']);
							$recur_enddate['mday']  = (int)($ical['event'][$i]['rrule']['until']['mday']);
							$recur_enddate['hour']  = (int)($ical['event'][$i]['rrule']['until']['hour']);
							$recur_enddate['min']  = (int)($ical['event'][$i]['rrule']['until']['min']);
							$recur_enddate['sec']  = (int)($ical['event'][$i]['rrule']['until']['sec']);
						}
						elseif( isset($ical['event'][$i]['duration']) )
						{
							/* Create timecode for strtotime */
							$ptimer = mktime($ical['event'][$i]['dtstart']['hour'],
								$ical['event'][$i]['dtstart']['min'],
								$ical['event'][$i]['dtstart']['sec'],
								$ical['event'][$i]['dtstart']['month'],
								$ical['event'][$i]['dtstart']['mday'],
								$ical['event'][$i]['dtstart']['year']);

							/* 
							 * Subtract 1 second so we don't overlap with the beginning of
							 * another event.
							 */

							/* handle events with duration in weeks */
							if ($ical['event'][$i]['duration']['weeks']		&&
								($ical['event'][$i]['duration']['hours'] == 0)	&&
								($ical['event'][$i]['duration']['minutes'] == 0))
							{
								$ical['event'][$i]['duration']['days'] =
									($ical['event'][$i]['duration']['days'] +
									($ical['event'][$i]['duration']['weeks']*7)) - 1;

								unset($ical['event'][$i]['duration']['weeks']);

								$ical['event'][$i]['duration']['hours'] = "23";
								$ical['event'][$i]['duration']['minutes'] = "59";
								$ical['event'][$i]['duration']['seconds'] = "59";
							}
							/* handle events with duration  in days */
							if(($ical['event'][$i]['duration']['days'] && $ical['event'][$i]['duration']['hours'] == 0) &&
							   ($ical['event'][$i]['duration']['minutes'] == 0))
							{
								$ical['event'][$i]['duration']['days']--;
								$ical['event'][$i]['duration']['hours'] = "23";
								$ical['event'][$i]['duration']['minutes'] = "59";
								$ical['event'][$i]['duration']['seconds'] = "59";
							}

							/* Create string contains datetime for strtotime */
							$pdate = "+";
							if(isset($ical['event'][$i]['duration']['weeks']))
								$pdate .= $ical['event'][$i]['duration']['weeks'] . " weeks ";

							if(isset($ical['event'][$i]['duration']['days']))
								$pdate .= $ical['event'][$i]['duration']['days'] . " days ";

							if(isset($ical['event'][$i]['duration']['hours']))
								$pdate .= $ical['event'][$i]['duration']['hours'] . " hours ";

							if(isset($ical['event'][$i]['duration']['minutes']))
								$pdate .= $ical['event'][$i]['duration']['minutes'] . " minutes ";

							if(isset($ical['event'][$i]['duration']['seconds']))
								$pdate .= $ical['event'][$i]['duration']['seconds'] . " seconds ";

							/* What is datetime in 2192? */
							$enddate = strtotime($pdate, $ptimer);
							list(
								$recur_enddate['year'],
								$recur_enddate['month'],
								$recur_enddate['mday'],
								$recur_enddate['hour'],
								$recur_enddate['min'],
								$recur_enddate['sec']
							) = split(":", date("Y:m:d:H:i:s", $enddate));

							/* Set End of event */
							$so_event->set_end(
								$recur_enddate['year'],
								$recur_enddate['month'],
								$recur_enddate['mday'],
								$recur_enddate['hour'],
								$recur_enddate['min'],
								$recur_enddate['sec']
							);
						}
						else
						{
							$recur_enddate['year'] = 0;
							$recur_enddate['month'] = 0;
							$recur_enddate['mday'] = 0;
						}

						/* recur_data */
						$recur_data = 0;
						if(isset($ical['event'][$i]['rrule']['byday']))
						{
							$week_days = Array(
								MCAL_M_SUNDAY		=> 'SU',
								MCAL_M_MONDAY		=> 'MO',
								MCAL_M_TUESDAY		=> 'TU',
								MCAL_M_WEDNESDAY	=> 'WE',
								MCAL_M_THURSDAY		=> 'TH',
								MCAL_M_FRIDAY		=> 'FR',
								MCAL_M_SATURDAY		=> 'SA'
							);
							@reset($week_days);
							while(list($key,$val) = each($week_days))
							{
								if(strpos(' '.$ical['event'][$i]['rrule']['byday'],$val))
								{
									$recur_data += $key;
								}
							}
						}

						/* interval */
						if(!isset($ical['event'][$i]['rrule']['interval']))
						{
							$interval = 1;
						}
						else
						{
							$interval = (int)$ical['event'][$i]['rrule']['interval'];
						}
						/* recur_type */
						switch($ical['event'][$i]['rrule']['freq'])
						{
							case DAILY:
								$so_event->set_recur_daily($recur_enddate['year'],$recur_enddate['month'],$recur_enddate['mday'],$interval);
								break;
							case WEEKLY:
								$so_event->set_recur_weekly($recur_enddate['year'],$recur_enddate['month'],$recur_enddate['mday'],$interval,$recur_data);
								break;
							case MONTHLY:
							/* FIXME: need to handle by month by day or by week.
							 * AssUMe by month by day for now. */
								$so_event->set_recur_monthly_mday($recur_enddate['year'],$recur_enddate['month'],$recur_enddate['mday'],$interval);
								break;
							case YEARLY:
								$so_event->set_recur_yearly($recur_enddate['year'],$recur_enddate['month'],$recur_enddate['mday'],$interval);
								break;
						}
					}
					else
					{
						$so_event->set_recur_none();
					}

					/* the organizer must be the current user doing the importing */
					if(!isset($ical['event'][$i]['organizer']) || !$this->is_owner($ical['event'][$i]['organizer']))
					{
						$so_event->add_attribute('owner',$GLOBALS['phpgw_info']['user']['account_id']);
						$so_event->add_attribute('participants','A',$GLOBALS['phpgw_info']['user']['account_id']);
					}

					/* if the original organizer is an egroupware user, add the original 
					 * user as an event participant.
					 * NB: ['mailto'] has two parts, ['user'], containing the username, 
					 * and ['host'], containing the domain of the user's email address.
					 */
					if (isset($ical['event'][$i]['organizer']['mailto']['user']) && $GLOBALS['phpgw']->accounts->exists($ical['event'][$i]['organizer']['mailto']['user']) == True)
					{
						$so_event->add_attribute('participants','A',(int)$GLOBALS['phpgw']->accounts->name2id($ical['event'][$i]['organizer']['mailto']['user']));
					}

					$event = $so_event->get_cached_event();
					$so_event->add_entry($event);
				}
			}
			Header('Location: '.$GLOBALS['phpgw']->link('/index.php',
					Array(
						'menuaction'	=> 'calendar.uicalendar.view',
						'cal_id'	=> $event['id']
					)
				)
			);
			$GLOBALS['phpgw']->common->phpgw_exit();
		}

		function export($params)
		{
			$event_id = $params['l_event_id'] ? $params['l_event_id'] : $_GET['cal_id'];
			$this->chunk_split = $params['chunk_split'];
			$method = $params['method'] ? $params['method'] : 'publish';

			$string_array = Array(
				'summary'		=> 'title',
				'location'		=> 'location',
				'description'	=> 'description',
				'uid'			=> 'uid'
			);

			$cats = CreateObject('phpgwapi.categories');

			if(!is_array($event_id))
			{
				$ids[] = $event_id;
			}
			else
			{
				$ids = $event_id;
			}

			$ical = $this->new_ical();

			$this->set_var($ical['prodid'],'value','-//eGroupWare//eGroupWare '.$GLOBALS['phpgw_info']['apps']['calendar']['version'].' MIMEDIR//'.strtoupper($GLOBALS['phpgw_info']['user']['preferences']['common']['lang']));
			$this->set_var($ical['version'],'value','1.0');
			$this->set_var($ical['method'],'value',strtoupper($method));

			if(!$GLOBALS['phpgw_info']['flags']['included_classes']['uicalendar'])
			{
				if(!$GLOBALS['phpgw_info']['flags']['included_classes']['bocalendar'])
				{
					$so_event = createobject('calendar.socalendar',
						Array(
							'owner'	=> 0,
							'filter'	=> '',
							'category'	=> ''
						)
					);
				}
				else
				{
					$so_event = $GLOBALS['bocalendar']->so;
				}
			}
			else
			{
				$so_event = $GLOBALS['uicalendar']->bo->so;
			}

			foreach($ids as $event)
			{
				$ical_event = Array();
				if (!is_array($event))
				{
					$event = $so_event->read_entry($event);
				}
				if($event['alarm'])
				{
					foreach($event['alarm'] as $alarm)
					{
						$ical_temp = Array();
						$ical_temp['action']['value'] = 'DISPLAY';
						$ical_temp['description']['value'] = $alarm['text'];
						$this->set_var($ical_temp['trigger'],'value','VALUE=DATE-TIME:'.date('Ymd\THis\Z',$alarm['time']),'valarm');
						$ical_event['alarm'][] = $ical_temp;
					}
				}

				// $event has times in user's time zone, so have to adjust them to GMT, which is used by ical
				// To do that one must substract the users time zone difference with the server and then substract the server's time zone difference with GMT
				$gmt_offset = date('O');  // server's offset to GMT
				$offset = ((int)(substr($gmt_offset, 0, 3)) + $GLOBALS['phpgw_info']['user']['preferences']['common']['tz_offset']) * 60 + (int)(substr($gmt_offset, 3, 2));
				$event['start']['min']   -= $offset;
				$event['end']['min']     -= $offset;
				$event['modtime']['min'] -= $offset;

				$ical_event['priority'] = $event['priority'];
				$ical_event['class'] = (int)$event['public'];
				$dtstart_mktime = $so_event->maketime($event['start']);
				$this->parse_value($ical_event,'dtstart',date('Ymd\THis\Z',$dtstart_mktime),'vevent');
				$dtend_mktime = $so_event->maketime($event['end']);
				$this->parse_value($ical_event,'dtend',date('Ymd\THis\Z',$dtend_mktime),'vevent');
				$mod_mktime = $so_event->maketime($event['modtime']);
				$this->parse_value($ical_event,'last_modified',date('Ymd\THis\Z',$mod_mktime),'vevent');
				foreach($string_array as $ical_value => $event_value)
				{
					if($event[$event_value])
					{
						$this->set_var($ical_event[$ical_value],'value',$event[$event_value]);
					}
				}

				if ($event['category'])
				{
					$cats->categories(0,'calendar');
					foreach(explode(',',$event['category']) as $cat)
					{
						$_cat = $cats->return_single($cat);
						$cat_string[] = $_cat[0]['name'];
					}
					@reset($cat_string);
					$this->set_var($ical_event['categories'],'value',implode($cat_string,','));
				}

				if(count($event['participants']) > 1)
				{
					if(!is_object($db))
					{
						$db = $GLOBALS['phpgw']->db;
					}
					foreach($event['participants'] as $part => $status)
					{
						$GLOBALS['phpgw']->accounts->get_account_name($part,$lid,$fname,$lname);
						$name = $fname.' '.$lname;

						$owner_status = $this->switch_partstat((int)$this->switch_phpgw_status($event['participants'][$part]));

						$mailto = $GLOBALS['phpgw']->accounts->id2name($part,'account_email');

						$str = 'CN="'.$name.'";PARTSTAT='.$owner_status.':'.$mailto;
						if($part == $event['owner'])
						{
							$str = 'ROLE=CHAIR;'.$str;
						}
						else
						{
							$str = 'ROLE=REQ-PARTICIPANT;'.$str;
						}
						if ($method != 'reply' || $part == $GLOBALS['phpgw_info']['user']['account_id'])
						{
							$this->parse_value($ical_event,'attendee',$str,'vevent');
						}
						if($part == $event['owner'])
						{
							$this->parse_value($ical_event,'organizer',$str,'vevent');
						}
					}
				}
				if($event['recur_type'])
				{
					$str = '';
					switch($event['recur_type'])
					{
						case MCAL_RECUR_DAILY:
							$str .= 'FREQ=DAILY';
							break;
						case MCAL_RECUR_WEEKLY:
							$str .= 'FREQ=WEEKLY';
							if($event['recur_data'])
							{
								$str .= ';BYDAY=';
								for($i=1;$i<MCAL_M_ALLDAYS;$i=$i*2)
								{
									if($i & $event['recur_data'])
									{
										switch($i)
										{
											case MCAL_M_SUNDAY:
												$day[] = 'SU';
												break;
											case MCAL_M_MONDAY:
												$day[] = 'MO';
												break;
											CASE MCAL_M_TUESDAY:
												$day[] = 'TU';
												break;
											case MCAL_M_WEDNESDAY:
												$day[] = 'WE';
												break;
											case MCAL_M_THURSDAY:
												$day[] = 'TH';
												break;
											case MCAL_M_FRIDAY:
												$day[] = 'FR';
												break;
											case MCAL_M_SATURDAY:
												$day[] = 'SA';
												break;
										}
									}
								}
								$str .= implode(',',$day);
							}
							break;
						case MCAL_RECUR_MONTHLY_MDAY:
							break;
						case MCAL_RECUR_MONTHLY_WDAY:
							break;
						case MCAL_RECUR_YEARLY:
							$str .= 'FREQ=YEARLY';
							break;
					}
					if($event['recur_interval'])
					{
						$str .= ';INTERVAL='.$event['recur_interval'];
					}
					if($event['recur_enddate']['month'] != 0 && $event['recur_enddate']['mday'] != 0 && $event['recur_enddate']['year'] != 0)
					{
						$recur_mktime = $so_event->maketime($event['recur_enddate']) - $GLOBALS['phpgw']->datetime->tz_offset;
						$str .= ';UNTIL='.date('Ymd\THis\Z',$recur_mktime);
					}
					$this->parse_value($ical_event,'rrule',$str,'vevent');

					$exceptions = $event['recur_exception'];
					if(is_array($exceptions))
					{
						foreach($exceptions as $except_datetime)
						{
							$ical_event['exdate'][] = $this->switch_date(date('Ymd\THis\Z',$except_datetime));
						}
					}
				}
				$ical_events[] = $ical_event;
			}

			$ical['event'] = $ical_events;

			// iCals are by default utf-8
			return $GLOBALS['phpgw']->translation->convert($this->build_ical($ical),$GLOBALS['phpgw']->translation->charset(),'utf-8');
		}

		function freebusy($params=False)
		{
			if (!$params) $params = $_GET;
			$this->chunk_split = $params['chunk_split'];
			$method = $params['method'] ? $params['method'] : 'publish';
			$user  = is_numeric($params['user']) ? (int) $params['user'] : $GLOBALS['phpgw']->accounts->name2id($params['user']);
			$start = isset($params['start']) ? $params['start'] : date('Ymd');
			$end   = isset($params['end']) ? $params['end'] : (date('Y')+1).date('md');

			$this->bo = CreateObject('calendar.bocalendar');
			$events_per_day = $this->bo->store_to_cache(array(
				'owner'  => $user,
				'syear'  => (int) substr($start,0,4),
				'smonth' => (int) substr($start,4,2),
				'sday'   => (int) substr($start,6,2),
				'eyear'  => (int) substr($end,0,4),
				'emonth' => (int) substr($end,4,2),
				'eday'   => (int) substr($end,6,2),
				'no_doubles' => True,	// report events only on the startday
			));
			if (!is_array($events_per_day)) $events_per_day = array();
			
			$browser = CreateObject('phpgwapi.browser');
			$browser->content_header($GLOBALS['phpgw']->accounts->id2name($user).'.ifb','text/calendar');

			$ical = $this->new_ical();
			$this->set_var($ical['prodid'],'value','-//eGroupWare//eGroupWare '.$GLOBALS['phpgw_info']['apps']['calendar']['version'].' MIMEDIR//'.strtoupper($GLOBALS['phpgw_info']['user']['preferences']['common']['lang']));
			$this->set_var($ical['version'],'value','1.0');
			$this->set_var($ical['method'],'value',strtoupper($method));

			$ical_freebusy = array();
			
			$mailto = $GLOBALS['phpgw']->accounts->id2name($user,'account_email');
			$name = $GLOBALS['phpgw']->common->grab_owner_name($user);

			$str = 'CN="'.$name.'";MAILTO='.$mailto;
			$this->parse_value($ical_freebusy,'organizer',$str,'vfreebusy');
			
			$freebusy_url = $GLOBALS['phpgw_info']['server']['webserver_url'].'/calendar/freebusy.php?user='.$GLOBALS['phpgw_info']['user']['account_lid']/* not sure if this should be in the file .'&password='.$GLOBALS['phpgw_info']['user']['preferences']['calendar']['freebusy_pw']*/;
			if ($freebusy_url[0] == '/')
			{
				$freebusy_url = ($_SERVER['HTTPS'] ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].$freebusy_url;
			}
			$this->parse_value($ical_freebusy,'url',$freebusy_url,'vfreebusy');

			$this->parse_value($ical_freebusy,'dtstart',$start.'T000000Z','vfreebusy');
			$this->parse_value($ical_freebusy,'dtend',$end.'T000000Z','vfreebusy');

			// $event has times in user's time zone, so have to adjust them to GMT, which is used by ical
			// To do that one must substract the users time zone difference with the server and then substract the server's time zone difference with GMT
			$gmt_offset = date('O');  // server's offset to GMT
			$offset = ((int)(substr($gmt_offset, 0, 3)) + $GLOBALS['phpgw_info']['user']['preferences']['common']['tz_offset']) * 60 + (int)(substr($gmt_offset, 3, 2));

			foreach($events_per_day as $day => $events)
			{
				foreach($events as $event)
				{
					$event['start']['min']   -= $offset;
					$event['end']['min']     -= $offset;
			
					$dtstart_mktime = $this->bo->so->maketime($event['start']);
					$dtend_mktime = $this->bo->so->maketime($event['end']);
					$this->parse_value($ical_freebusy,'freebusy',date('Ymd\THis\Z',$dtstart_mktime).'/'.date('Ymd\THis\Z',$dtend_mktime),'vfreebusy');
				}
			}

			$ical['freebusy'][0] =& $ical_freebusy;
			//_debug_array($ical);

			// iCals are by default utf-8
			echo $GLOBALS['phpgw']->translation->convert($this->build_ical($ical),$GLOBALS['phpgw']->translation->charset(),'utf-8');
		}

		function debug($str='')
		{
			if($this->debug_str)
			{
				echo $str."<br>\n";
			}
		}
	}
?>
