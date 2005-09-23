<?php
	/**************************************************************************\
	* eGroupWare - Stock Quotes                                                *
	* http://www.egroupware.org                                                *
	* This file is based on PStocks v.0.1                                      *
	* http://www.dansteinman.com/php/pstocks/                                  *
	* Copyright (C) 1999 Dan Steinman (dan@dansteinman.com)                    *
	* --------------------------------------------                             *
	* This program is free software; you can redistribute it and/or modify it  *
	* under the terms of the GNU General Public License as published by the    *
	* Free Software Foundation; either version 2 of the License, or (at your   *
	* option) any later version.                                               *
	\**************************************************************************/
	/* $Id: functions.inc.php,v 1.16 2004/06/19 17:54:56 alpeb Exp $ */

// return content of a url as a string array

	function http_fetch($url,$post,$port,$proxy)
	{
		return $GLOBALS['phpgw']->network->gethttpsocketfile($url);
	}

// Rename this is something better

	function return_html($quotes)
	{
		$return_html = '<table cellspacing="1" cellpadding="0" border="0" bgcolor="black"><tr><td>'
			. '<table cellspacing="1" cellpadding="2" border="0" bgcolor="white">'
			. '<tr><td><b>' . lang('Name') . '</b></td><td><b>' . lang('Symbol') . '</b></td><td align="right"><b>' . lang('Price') . '</b></td><td align="right">'
			. '<b>&nbsp;' . lang('$ change') . '</b></td><td align="right"><b>' . lang('% change') . '</b></td><td align="center"><b>' . lang('Date') . '</b></td><td align="center">'
			. '<b>' . lang('Time') . '</b></td></tr>';

		for ($i=0;$i<count($quotes);$i++)
		{
			$q = $quotes[$i];
			$symbol = $q['symbol'];
			$name = $q['name'];
			$price0 = $q['price0']; // today's price
			$price1 = $q['price1'];
			$price2 = $q['price2'];
			$dollarchange = $q['dchange'];
			$percentchange = $q['pchange'];
			$date = $q['date'];
			$time = $q['time'];
			$volume = $q['volume'];

			if ($dollarchange < 0)
			{
				$color = 'red';
			}
			else
			{
				$color = 'green';
			}

			$return_html .= '<tr><td>' . $name . '</td><td>' . $symbol . '</td><td align="right">' . $price0 . '</td><td align="right"><font color="'
				. $color . '">' . $dollarchange . '</font></td><td align="right"><font color="' . $color . '">' . $percentchange
				. '</font></td><td align="center">' . $date . '</td><td align="center">' . $time . '</td></tr>';
		}

		$return_html .= '</table></td></tr></table>';

		return $return_html;
	}

	function get_quotes($stocklist)
	{
		if (! $stocklist)
		{
			return array();
		}

		while (list($symbol,$name) = each($stocklist))
		{
			$symbollist[] = $symbol;
			$symbollist_no_coding[] = str_replace('^', '\^', urldecode($symbol));
		//	$symbol = rawurlencode($symbol);
			$symbolstr .= $symbol;

			if ($i++<count($stocklist)-1)
			{
				$symbolstr .= '+';
			}
		}

//		$regexp_stocks = '/(' . implode('|',$symbollist) . ')/';
		$regexp_stocks = '/^\"(' . implode('|',$symbollist_no_coding) . ')/';

		$url = 'http://finance.yahoo.com/d/quotes.csv?f=sl1d1t1c1ohgv&e=.csv&s=' . $symbolstr;
		$lines = http_fetch($url,false,80,'');

		$quotes = array();
		$i = 0;

		if ($lines)
		{
			while ($line = each($lines))
			{
				$line = $lines[$i];

				if (preg_match($regexp_stocks,$line))
				{
					$line = str_replace('"','',$line);
					list($symbol,$price0,$date,$time,$dchange,$price1,$price2) = split(',',$line);

					if ($price1>0 && $dchange!=0)
					{
						$pchange = round(10000*($dchange)/$price1)/100;
					}
					else
					{
						$pchange = 0;
					}

					if ($pchange>0)
					{
						$pchange = '+' . $pchange;
					}

					$name = $stocklist[urlencode($symbol)];

					if (! $name)
					{
						$name = $symbol;
					}

					$quotes[] = array(
						'symbol' => $symbol,
						'price0' => $price0,
						'date' => $date,
						'time' => $time,
						'dchange' => $dchange,
						'price1' => $price1,
						'price2' => $price2,
						'pchange' => $pchange,
						'name' => urldecode($name)
					);
				}
				$i++;
			}
			return $quotes;
		}
	}

	function get_savedstocks()
	{
		// If they don't have any stocks in there, give them something to look at
		if (! count($GLOBALS['phpgw_info']['user']['preferences']['stocks']))
		{
			$GLOBALS['phpgw_info']['user']['preferences']['stocks']['LNUX'] = 'VA%20Linux';
			$GLOBALS['phpgw_info']['user']['preferences']['stocks']['RHAT'] = 'RedHat';
		}

		while ($stock = each($GLOBALS['phpgw_info']['user']['preferences']['stocks']))
		{
			if ((rawurldecode($stock[0]) != 'enabled') && (rawurldecode($stock[0]) != 'disabled') && (rawurldecode($stock[0]) != 'homepage_display'))
			{
				$symbol = $stock[0];
				$name = $stock[1];

				if ($symbol)
				{
					if (! $name)
					{
						$name = $symbol;
					}
					$stocklist[$symbol] = $name;
				}
			}
		}
		return $stocklist;
	}

	function return_quotes()
	{
		$stocklist = get_savedstocks();
		$quotes = get_quotes($stocklist);
		return return_html($quotes);
	}
?>
