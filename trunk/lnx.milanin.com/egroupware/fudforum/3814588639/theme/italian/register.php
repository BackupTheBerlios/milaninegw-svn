<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: register.php.t,v 1.6 2003/12/18 18:22:09 iliaa Exp $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

if (_uid === '_uid') {
		exit('sorry, you can not access this page');
	}function tmpl_draw_select_opt($values, $names, $selected, $normal_tmpl, $selected_tmpl)
{
	$vls = explode("\n", $values);
	$nms = explode("\n", $names);

	if (($a = count($vls)) != count($nms)) {
		exit("FATAL ERROR: inconsistent number of values inside a select<br>\n");
	}

	$options = '';
	for ($i = 0; $i < $a; $i++) {
		$options .= $vls[$i] != $selected ? '<option value="'.$vls[$i].'" '.$normal_tmpl.'>'.$nms[$i].'</option>' : '<option value="'.$vls[$i].'" selected '.$selected_tmpl.'>'.$nms[$i].'</option>';
	}

	return $options;
}function tmpl_draw_radio_opt($name, $values, $names, $selected, $normal_tmpl, $selected_tmpl, $sep)
{
	$vls = explode("\n", $values);
	$nms = explode("\n", $names);

	if (($a = count($vls)) != count($nms)) {
		exit("FATAL ERROR: inconsistent number of values<br>\n");
	}

	$checkboxes = '';
	for ($i = 0; $i < $a; $i++) {
		$checkboxes .= $vls[$i] != $selected ? '<input type="radio" name="'.$name.'" value="'.$vls[$i].'" '.$normal_tmpl.'>'.$nms[$i].$sep : '<input type="radio" name="'.$name.'" value="'.$vls[$i].'" checked '.$selected_tmpl.'>'.$nms[$i].$sep;
	}

	return $checkboxes;
}if (@getenv('OSTYPE') == 'AIX' || @strpos(php_uname(), 'AIX')) {
	$GLOBALS['tz_names'] = "Coordinated Universal Time\nUnited Kingdom\nAzores, Cape Verde\nFalkland Islands\nGreenland, East Brazil\nCentral Brazil\nEastern United States, Colombia\nCentral United States, Honduras\nMountain United States\nPacific United States, Yukon\nAlaska\nHawaii, Aleutian Islands\nBering Strait\nNew Zealand\nSolomon Islands\nEastern Australia\nJapan\nKorea\nWestern Australia\nTaiwan\nThailand\nCentral Asia\nPakistan\nGorki, Central Asia, Oman\nTurkey\nSaudi Arabia\nFinland\nSouth Africa\nNorway";
	$GLOBALS['tz_values'] = "CUT0GDT\nGMT0BST\nAZOREST1AZOREDT\nFALKST2FALKDT\nGRNLNDST3GRNLNDDT\nAST4ADT\nEST5EDT\nCST6CDT\nMST7MDT\nPST8PDT\nAST9ADT\nHST10HDT\nBST11BDT\nNZST-12NZDT\nMET-11METDT\nEET-10EETDT\nJST-9JSTDT\nKORST-9KORDT\nWAUST-8WAUDT\nTAIST-8TAIDT\nTHAIST-7THAIDT\nTASHST-6TASHDT\nPAKST-5PAKDT\nWST-4WDT\nMEST-3MEDT\nSAUST-3SAUDT\nWET-2WET\nUSAST-2USADT\nNFT-1DFT";
} else {
	$GLOBALS['tz_names'] = "\nAfghanistan/Kabul\nAlbania/Tirane\nAlgeria/Algiers\nAndorra/Andorra\nAngola/Luanda\nAnguilla/Anguilla\nAntarctica/Casey Casey Station, Bailey Peninsula\nAntarctica/Davis Davis Station, Vestfold Hills\nAntarctica/DumontDUrville Dumont-d'Urville Base, Terre Adelie\nAntarctica/Mawson Mawson Station, Holme Bay\nAntarctica/McMurdo McMurdo Station, Ross Island\nAntarctica/Palmer Palmer Station, Anvers Island\nAntarctica/South Pole Amundsen-Scott Station, South Pole\nAntarctica/Syowa Syowa Station, E Ongul I\nAntigua & Barbuda/Antigua\nArgentina/Buenos Aires E Argentina (BA, DF, SC, TF)\nArgentina/Catamarca Catamarca (CT)\nArgentina/Cordoba W Argentina (CB, SA, TM, LR, SJ, SL, NQ, RN)\nArgentina/Jujuy Jujuy (JY)\nArgentina/Mendoza Mendoza (MZ)\nArgentina/Rosario NE Argentina (SF, ER, CN, MN, CC, FM, LP, CH)\nArmenia/Yerevan\nAruba/Aruba\nAustralia/Adelaide South Australia\nAustralia/Brisbane Queensland - most locations\nAustralia/Broken Hill New South Wales - Broken Hill\nAustralia/Darwin Northern Territory\nAustralia/Hobart Tasmania\nAustralia/Lindeman Queensland - Holiday Islands\nAustralia/Lord Howe Lord Howe Island\nAustralia/Melbourne Victoria\nAustralia/Perth Western Australia\nAustralia/Sydney New South Wales - most locations\nAustria/Vienna\nAzerbaijan/Baku\nBahamas/Nassau\nBahrain/Bahrain\nBangladesh/Dhaka\nBarbados/Barbados\nBelarus/Minsk\nBelgium/Brussels\nBelize/Belize\nBenin/Porto-Novo\nBermuda/Bermuda\nBhutan/Thimphu\nBolivia/La Paz\nBosnia & Herzegovina/Sarajevo\nBotswana/Gaborone\nBrazil/Araguaina Tocantins\nBrazil/Belem Amapa, E Para\nBrazil/Boa Vista Roraima\nBrazil/Cuiaba Mato Grosso, Mato Grosso do Sul\nBrazil/Eirunepe W Amazonas\nBrazil/Fortaleza NE Brazil (MA, PI, CE, RN, PR)\nBrazil/Maceio Alagoas, Sergipe\nBrazil/Manaus E Amazonas\nBrazil/Noronha Atlantic islands\nBrazil/Porto Acre Acre\nBrazil/Porto Velho W Para, Rondonia\nBrazil/Recife Pernambuco\nBrazil/Sao Paulo S & SE Brazil (BA, GO, DF, MG, ES, RJ, SP, PR, SC, RS)\nBritain (UK)/Belfast Northern Ireland\nBritain (UK)/London Great Britain\nBritish Indian Ocean Territory/Chagos\nBrunei/Brunei\nBulgaria/Sofia\nBurkina Faso/Ouagadougou\nBurundi/Bujumbura\nCambodia/Phnom Penh\nCameroon/Douala\nCanada/Cambridge Bay Central Time - west Nunavut\nCanada/Dawson Pacific Time - north Yukon\nCanada/Dawson Creek Mountain Standard Time - Dawson Creek & Fort Saint John, British Columbia\nCanada/Edmonton Mountain Time - Alberta, east British Columbia & west Saskatchewan\nCanada/Glace Bay Atlantic Time - Nova Scotia - places that did not observe DST 1966-1971\nCanada/Goose Bay Atlantic Time - E Labrador\nCanada/Halifax Atlantic Time - Nova Scotia (most places), NB, W Labrador, E Quebec & PEI\nCanada/Inuvik Mountain Time - west Northwest Territories\nCanada/Iqaluit Eastern Standard Time - east Nunavut\nCanada/Montreal Eastern Time - Ontario & Quebec - most locations\nCanada/Nipigon Eastern Time - Ontario & Quebec - places that did not observe DST 1967-1973\nCanada/Pangnirtung Eastern Standard Time - Pangnirtung, Nunavut\nCanada/Rainy River Central Time - Rainy River & Fort Frances, Ontario\nCanada/Rankin Inlet Eastern Standard Time - central Nunavut\nCanada/Regina Central Standard Time - Saskatchewan - most locations\nCanada/St Johns Newfoundland Island\nCanada/Swift Current Central Standard Time - Saskatchewan - midwest\nCanada/Thunder Bay Eastern Time - Thunder Bay, Ontario\nCanada/Vancouver Pacific Time - west British Columbia\nCanada/Whitehorse Pacific Time - south Yukon\nCanada/Winnipeg Central Time - Manitoba & west Ontario\nCanada/Yellowknife Mountain Time - central Northwest Territories\nCape Verde/Cape Verde\nCayman Islands/Cayman\nCentral African Rep./Bangui\nChad/Ndjamena\nChile/Easter Easter Island\nChile/Santiago mainland\nChina/Chungking China mountains\nChina/Harbin north Manchuria\nChina/Kashgar Eastern Turkestan\nChina/Shanghai China coast\nChina/Urumqi Tibet & Xinjiang\nChristmas Island/Christmas\nCocos (Keeling) Islands/Cocos\nColombia/Bogota\nComoros/Comoro\nCongo (Dem. Rep.)/Kinshasa west Dem. Rep. of Congo\nCongo (Dem. Rep.)/Lubumbashi east Dem. Rep. of Congo\nCongo (Rep.)/Brazzaville\nCook Islands/Rarotonga\nCosta Rica/Costa Rica\nCote d'Ivoire/Abidjan\nCroatia/Zagreb\nCuba/Havana\nCyprus/Nicosia\nCzech Republic/Prague\nDenmark/Copenhagen\nDjibouti/Djibouti\nDominica/Dominica\nDominican Republic/Santo Domingo\nEast Timor/Dili\nEcuador/Galapagos Galapagos Islands\nEcuador/Guayaquil mainland\nEgypt/Cairo\nEl Salvador/El Salvador\nEquatorial Guinea/Malabo\nEritrea/Asmera\nEstonia/Tallinn\nEthiopia/Addis Ababa\nFaeroe Islands/Faeroe\nFalkland Islands/Stanley\nFiji/Fiji\nFinland/Helsinki\nFrance/Paris\nFrench Guiana/Cayenne\nFrench Polynesia/Gambier Gambier Islands\nFrench Polynesia/Marquesas Marquesas Islands\nFrench Polynesia/Tahiti Society Islands\nFrench Southern & Antarctic Lands/Kerguelen\nGabon/Libreville\nGambia/Banjul\nGeorgia/Tbilisi\nGermany/Berlin\nGhana/Accra\nGibraltar/Gibraltar\nGreece/Athens\nGreenland/Godthab southwest Greenland\nGreenland/Scoresbysund east Greenland\nGreenland/Thule northwest Greenland\nGrenada/Grenada\nGuadeloupe/Guadeloupe\nGuam/Guam\nGuatemala/Guatemala\nGuinea/Conakry\nGuinea-Bissau/Bissau\nGuyana/Guyana\nHaiti/Port-au-Prince\nHonduras/Tegucigalpa\nHong Kong/Hong Kong\nHungary/Budapest\nIceland/Reykjavik\nIndia/Calcutta\nIndonesia/Jakarta Java & Sumatra\nIndonesia/Jayapura Irian Jaya & the Moluccas\nIndonesia/Ujung Pandang Borneo & Celebes\nIran/Tehran\nIraq/Baghdad\nIreland/Dublin\nIsrael/Jerusalem\nItaly/Rome\nJamaica/Jamaica\nJapan/Tokyo\nJordan/Amman\nKazakhstan/Almaty east Kazakhstan\nKazakhstan/Aqtau west Kazakhstan\nKazakhstan/Aqtobe central Kazakhstan\nKenya/Nairobi\nKiribati/Enderbury Phoenix Islands\nKiribati/Kiritimati Line Islands\nKiribati/Tarawa Gilbert Islands\nKorea (North)/Pyongyang\nKorea (South)/Seoul\nKuwait/Kuwait\nKyrgyzstan/Bishkek\nLaos/Vientiane\nLatvia/Riga\nLebanon/Beirut\nLesotho/Maseru\nLiberia/Monrovia\nLibya/Tripoli\nLiechtenstein/Vaduz\nLithuania/Vilnius\nLuxembourg/Luxembourg\nMacao/Macao\nMacedonia/Skopje\nMadagascar/Antananarivo\nMalawi/Blantyre\nMalaysia/Kuala Lumpur peninsular Malaysia\nMalaysia/Kuching Sabah & Sarawak\nMaldives/Maldives\nMali/Bamako southwest Mali\nMali/Timbuktu northeast Mali\nMalta/Malta\nMarshall Islands/Kwajalein Kwajalein\nMarshall Islands/Majuro most locations\nMartinique/Martinique\nMauritania/Nouakchott\nMauritius/Mauritius\nMayotte/Mayotte\nMexico/Cancun Central Time - Quintana Roo\nMexico/Chihuahua Mountain Time - Chihuahua\nMexico/Hermosillo Mountain Standard Time - Sonora\nMexico/Mazatlan Mountain Time - S Baja, Nayarit, Sinaloa\nMexico/Merida Central Time - Campeche, Yucatan\nMexico/Mexico City Central Time - most locations\nMexico/Monterrey Central Time - Coahuila, Durango, Nuevo Leon, Tamaulipas\nMexico/Tijuana Pacific Time\nMicronesia/Kosrae Kosrae\nMicronesia/Ponape Ponape (Pohnpei)\nMicronesia/Truk Truk (Chuuk)\nMicronesia/Yap Yap\nMoldova/Chisinau most locations\nMoldova/Tiraspol Transdniestria\nMonaco/Monaco\nMongolia/Hovd Bayan-Olgiy, Hovd, Uvs\nMongolia/Ulaanbaatar most locations\nMontserrat/Montserrat\nMorocco/Casablanca\nMozambique/Maputo\nMyanmar (Burma)/Rangoon\nNamibia/Windhoek\nNauru/Nauru\nNepal/Katmandu\nNetherlands/Amsterdam\nNetherlands Antilles/Curacao\nNew Caledonia/Noumea\nNew Zealand/Auckland most locations\nNew Zealand/Chatham Chatham Islands\nNicaragua/Managua\nNiger/Niamey\nNigeria/Lagos\nNiue/Niue\nNorfolk Island/Norfolk\nNorthern Mariana Islands/Saipan\nNorway/Oslo\nOman/Muscat\nPakistan/Karachi\nPalau/Palau\nPalestine/Gaza\nPanama/Panama\nPapua New Guinea/Port Moresby\nParaguay/Asuncion\nPeru/Lima\nPhilippines/Manila\nPitcairn/Pitcairn\nPoland/Warsaw\nPortugal/Azores Azores\nPortugal/Lisbon mainland\nPortugal/Madeira Madeira Islands\nPuerto Rico/Puerto Rico\nQatar/Qatar\nReunion/Reunion\nRomania/Bucharest\nRussia/Anadyr Moscow+10 - Bering Sea\nRussia/Irkutsk Moscow+05 - Lake Baikal\nRussia/Kaliningrad Moscow-01 - Kaliningrad\nRussia/Kamchatka Moscow+09 - Kamchatka\nRussia/Krasnoyarsk Moscow+04 - Yenisei River\nRussia/Magadan Moscow+08 - Magadan & Sakhalin\nRussia/Moscow Moscow+00 - west Russia\nRussia/Novosibirsk Moscow+03 - Novosibirsk\nRussia/Omsk Moscow+03 - west Siberia\nRussia/Samara Moscow+01 - Caspian Sea\nRussia/Vladivostok Moscow+07 - Amur River\nRussia/Yakutsk Moscow+06 - Lena River\nRussia/Yekaterinburg Moscow+02 - Urals\nRwanda/Kigali\nSamoa (American)/Pago Pago\nSamoa (Western)/Apia\nSan Marino/San Marino\nSao Tome & Principe/Sao Tome\nSaudi Arabia/Riyadh\nSenegal/Dakar\nSeychelles/Mahe\nSierra Leone/Freetown\nSingapore/Singapore\nSlovakia/Bratislava\nSlovenia/Ljubljana\nSolomon Islands/Guadalcanal\nSomalia/Mogadishu\nSouth Africa/Johannesburg\nSouth Georgia & the South Sandwich Islands/South Georgia\nSpain/Canary Canary Islands\nSpain/Ceuta Ceuta & Melilla\nSpain/Madrid mainland\nSri Lanka/Colombo\nSt Helena/St Helena\nSt Kitts & Nevis/St Kitts\nSt Lucia/St Lucia\nSt Pierre & Miquelon/Miquelon\nSt Vincent/St Vincent\nSudan/Khartoum\nSuriname/Paramaribo\nSvalbard & Jan Mayen/Jan Mayen Jan Mayen\nSvalbard & Jan Mayen/Longyearbyen Svalbard\nSwaziland/Mbabane\nSweden/Stockholm\nSwitzerland/Zurich\nSyria/Damascus\nTaiwan/Taipei\nTajikistan/Dushanbe\nTanzania/Dar es Salaam\nThailand/Bangkok\nTogo/Lome\nTokelau/Fakaofo\nTonga/Tongatapu\nTrinidad & Tobago/Port of Spain\nTunisia/Tunis\nTurkey/Istanbul\nTurkmenistan/Ashgabat\nTurks & Caicos Is/Grand Turk\nTuvalu/Funafuti\nUS minor outlying islands/Johnston Johnston Atoll\nUS minor outlying islands/Midway Midway Islands\nUS minor outlying islands/Wake Wake Island\nUganda/Kampala\nUkraine/Kiev most locations\nUkraine/Simferopol central Crimea\nUkraine/Uzhgorod Ruthenia\nUkraine/Zaporozhye Zaporozh'ye, E Lugansk\nUnited Arab Emirates/Dubai\nUnited States/Adak Aleutian Islands\nUnited States/Anchorage Alaska Time\nUnited States/Boise Mountain Time - south Idaho & east Oregon\nUnited States/Chicago Central Time\nUnited States/Denver Mountain Time\nUnited States/Detroit Eastern Time - Michigan - most locations\nUnited States/Honolulu Hawaii\nUnited States/Indiana Eastern Standard Time - Indiana - Crawford County\nUnited States/Indiana Eastern Standard Time - Indiana - Starke County\nUnited States/Indiana Eastern Standard Time - Indiana - Switzerland County\nUnited States/Indianapolis Eastern Standard Time - Indiana - most locations\nUnited States/Juneau Alaska Time - Alaska panhandle\nUnited States/Kentucky Eastern Time - Kentucky - Wayne County\nUnited States/Los Angeles Pacific Time\nUnited States/Louisville Eastern Time - Kentucky - Louisville area\nUnited States/Menominee Central Time - Michigan - Wisconsin border\nUnited States/New York Eastern Time\nUnited States/Nome Alaska Time - west Alaska\nUnited States/Phoenix Mountain Standard Time - Arizona\nUnited States/Shiprock Mountain Time - Navajo\nUnited States/Yakutat Alaska Time - Alaska panhandle neck\nUruguay/Montevideo\nUzbekistan/Samarkand west Uzbekistan\nUzbekistan/Tashkent east Uzbekistan\nVanuatu/Efate\nVatican City/Vatican\nVenezuela/Caracas\nVietnam/Saigon\nVirgin Islands (UK)/Tortola\nVirgin Islands (US)/St Thomas\nWallis & Futuna/Wallis\nWestern Sahara/El Aaiun\nYemen/Aden\nYugoslavia/Belgrade\nZambia/Lusaka\nZimbabwe/Harare";
	$GLOBALS['tz_values'] = "\nAsia/Kabul\nEurope/Tirane\nAfrica/Algiers\nEurope/Andorra\nAfrica/Luanda\nAmerica/Anguilla\nAntarctica/Casey\nAntarctica/Davis\nAntarctica/DumontDUrville\nAntarctica/Mawson\nAntarctica/McMurdo\nAntarctica/Palmer\nAntarctica/South_Pole\nAntarctica/Syowa\nAmerica/Antigua\nAmerica/Buenos_Aires\nAmerica/Catamarca\nAmerica/Cordoba\nAmerica/Jujuy\nAmerica/Mendoza\nAmerica/Rosario\nAsia/Yerevan\nAmerica/Aruba\nAustralia/Adelaide\nAustralia/Brisbane\nAustralia/Broken_Hill\nAustralia/Darwin\nAustralia/Hobart\nAustralia/Lindeman\nAustralia/Lord_Howe\nAustralia/Melbourne\nAustralia/Perth\nAustralia/Sydney\nEurope/Vienna\nAsia/Baku\nAmerica/Nassau\nAsia/Bahrain\nAsia/Dhaka\nAmerica/Barbados\nEurope/Minsk\nEurope/Brussels\nAmerica/Belize\nAfrica/Porto-Novo\nAtlantic/Bermuda\nAsia/Thimphu\nAmerica/La_Paz\nEurope/Sarajevo\nAfrica/Gaborone\nAmerica/Araguaina\nAmerica/Belem\nAmerica/Boa_Vista\nAmerica/Cuiaba\nAmerica/Eirunepe\nAmerica/Fortaleza\nAmerica/Maceio\nAmerica/Manaus\nAmerica/Noronha\nAmerica/Porto_Acre\nAmerica/Porto_Velho\nAmerica/Recife\nAmerica/Sao_Paulo\nEurope/Belfast\nEurope/London\nIndian/Chagos\nAsia/Brunei\nEurope/Sofia\nAfrica/Ouagadougou\nAfrica/Bujumbura\nAsia/Phnom_Penh\nAfrica/Douala\nAmerica/Cambridge_Bay\nAmerica/Dawson\nAmerica/Dawson_Creek\nAmerica/Edmonton\nAmerica/Glace_Bay\nAmerica/Goose_Bay\nAmerica/Halifax\nAmerica/Inuvik\nAmerica/Iqaluit\nAmerica/Montreal\nAmerica/Nipigon\nAmerica/Pangnirtung\nAmerica/Rainy_River\nAmerica/Rankin_Inlet\nAmerica/Regina\nAmerica/St_Johns\nAmerica/Swift_Current\nAmerica/Thunder_Bay\nAmerica/Vancouver\nAmerica/Whitehorse\nAmerica/Winnipeg\nAmerica/Yellowknife\nAtlantic/Cape_Verde\nAmerica/Cayman\nAfrica/Bangui\nAfrica/Ndjamena\nPacific/Easter\nAmerica/Santiago\nAsia/Chungking\nAsia/Harbin\nAsia/Kashgar\nAsia/Shanghai\nAsia/Urumqi\nIndian/Christmas\nIndian/Cocos\nAmerica/Bogota\nIndian/Comoro\nAfrica/Kinshasa\nAfrica/Lubumbashi\nAfrica/Brazzaville\nPacific/Rarotonga\nAmerica/Costa_Rica\nAfrica/Abidjan\nEurope/Zagreb\nAmerica/Havana\nAsia/Nicosia\nEurope/Prague\nEurope/Copenhagen\nAfrica/Djibouti\nAmerica/Dominica\nAmerica/Santo_Domingo\nAsia/Dili\nPacific/Galapagos\nAmerica/Guayaquil\nAfrica/Cairo\nAmerica/El_Salvador\nAfrica/Malabo\nAfrica/Asmera\nEurope/Tallinn\nAfrica/Addis_Ababa\nAtlantic/Faeroe\nAtlantic/Stanley\nPacific/Fiji\nEurope/Helsinki\nEurope/Paris\nAmerica/Cayenne\nPacific/Gambier\nPacific/Marquesas\nPacific/Tahiti\nIndian/Kerguelen\nAfrica/Libreville\nAfrica/Banjul\nAsia/Tbilisi\nEurope/Berlin\nAfrica/Accra\nEurope/Gibraltar\nEurope/Athens\nAmerica/Godthab\nAmerica/Scoresbysund\nAmerica/Thule\nAmerica/Grenada\nAmerica/Guadeloupe\nPacific/Guam\nAmerica/Guatemala\nAfrica/Conakry\nAfrica/Bissau\nAmerica/Guyana\nAmerica/Port-au-Prince\nAmerica/Tegucigalpa\nAsia/Hong_Kong\nEurope/Budapest\nAtlantic/Reykjavik\nAsia/Calcutta\nAsia/Jakarta\nAsia/Jayapura\nAsia/Ujung_Pandang\nAsia/Tehran\nAsia/Baghdad\nEurope/Dublin\nAsia/Jerusalem\nEurope/Rome\nAmerica/Jamaica\nAsia/Tokyo\nAsia/Amman\nAsia/Almaty\nAsia/Aqtau\nAsia/Aqtobe\nAfrica/Nairobi\nPacific/Enderbury\nPacific/Kiritimati\nPacific/Tarawa\nAsia/Pyongyang\nAsia/Seoul\nAsia/Kuwait\nAsia/Bishkek\nAsia/Vientiane\nEurope/Riga\nAsia/Beirut\nAfrica/Maseru\nAfrica/Monrovia\nAfrica/Tripoli\nEurope/Vaduz\nEurope/Vilnius\nEurope/Luxembourg\nAsia/Macao\nEurope/Skopje\nIndian/Antananarivo\nAfrica/Blantyre\nAsia/Kuala_Lumpur\nAsia/Kuching\nIndian/Maldives\nAfrica/Bamako\nAfrica/Timbuktu\nEurope/Malta\nPacific/Kwajalein\nPacific/Majuro\nAmerica/Martinique\nAfrica/Nouakchott\nIndian/Mauritius\nIndian/Mayotte\nAmerica/Cancun\nAmerica/Chihuahua\nAmerica/Hermosillo\nAmerica/Mazatlan\nAmerica/Merida\nAmerica/Mexico_City\nAmerica/Monterrey\nAmerica/Tijuana\nPacific/Kosrae\nPacific/Ponape\nPacific/Truk\nPacific/Yap\nEurope/Chisinau\nEurope/Tiraspol\nEurope/Monaco\nAsia/Hovd\nAsia/Ulaanbaatar\nAmerica/Montserrat\nAfrica/Casablanca\nAfrica/Maputo\nAsia/Rangoon\nAfrica/Windhoek\nPacific/Nauru\nAsia/Katmandu\nEurope/Amsterdam\nAmerica/Curacao\nPacific/Noumea\nPacific/Auckland\nPacific/Chatham\nAmerica/Managua\nAfrica/Niamey\nAfrica/Lagos\nPacific/Niue\nPacific/Norfolk\nPacific/Saipan\nEurope/Oslo\nAsia/Muscat\nAsia/Karachi\nPacific/Palau\nAsia/Gaza\nAmerica/Panama\nPacific/Port_Moresby\nAmerica/Asuncion\nAmerica/Lima\nAsia/Manila\nPacific/Pitcairn\nEurope/Warsaw\nAtlantic/Azores\nEurope/Lisbon\nAtlantic/Madeira\nAmerica/Puerto_Rico\nAsia/Qatar\nIndian/Reunion\nEurope/Bucharest\nAsia/Anadyr\nAsia/Irkutsk\nEurope/Kaliningrad\nAsia/Kamchatka\nAsia/Krasnoyarsk\nAsia/Magadan\nEurope/Moscow\nAsia/Novosibirsk\nAsia/Omsk\nEurope/Samara\nAsia/Vladivostok\nAsia/Yakutsk\nAsia/Yekaterinburg\nAfrica/Kigali\nPacific/Pago_Pago\nPacific/Apia\nEurope/San_Marino\nAfrica/Sao_Tome\nAsia/Riyadh\nAfrica/Dakar\nIndian/Mahe\nAfrica/Freetown\nAsia/Singapore\nEurope/Bratislava\nEurope/Ljubljana\nPacific/Guadalcanal\nAfrica/Mogadishu\nAfrica/Johannesburg\nAtlantic/South_Georgia\nAtlantic/Canary\nAfrica/Ceuta\nEurope/Madrid\nAsia/Colombo\nAtlantic/St_Helena\nAmerica/St_Kitts\nAmerica/St_Lucia\nAmerica/Miquelon\nAmerica/St_Vincent\nAfrica/Khartoum\nAmerica/Paramaribo\nAtlantic/Jan_Mayen\nArctic/Longyearbyen\nAfrica/Mbabane\nEurope/Stockholm\nEurope/Zurich\nAsia/Damascus\nAsia/Taipei\nAsia/Dushanbe\nAfrica/Dar_es_Salaam\nAsia/Bangkok\nAfrica/Lome\nPacific/Fakaofo\nPacific/Tongatapu\nAmerica/Port_of_Spain\nAfrica/Tunis\nEurope/Istanbul\nAsia/Ashgabat\nAmerica/Grand_Turk\nPacific/Funafuti\nPacific/Johnston\nPacific/Midway\nPacific/Wake\nAfrica/Kampala\nEurope/Kiev\nEurope/Simferopol\nEurope/Uzhgorod\nEurope/Zaporozhye\nAsia/Dubai\nAmerica/Adak\nAmerica/Anchorage\nAmerica/Boise\nAmerica/Chicago\nAmerica/Denver\nAmerica/Detroit\nPacific/Honolulu\nAmerica/Indiana/Marengo\nAmerica/Indiana/Knox\nAmerica/Indiana/Vevay\nAmerica/Indianapolis\nAmerica/Juneau\nAmerica/Kentucky/Monticello\nAmerica/Los_Angeles\nAmerica/Louisville\nAmerica/Menominee\nAmerica/New_York\nAmerica/Nome\nAmerica/Phoenix\nAmerica/Shiprock\nAmerica/Yakutat\nAmerica/Montevideo\nAsia/Samarkand\nAsia/Tashkent\nPacific/Efate\nEurope/Vatican\nAmerica/Caracas\nAsia/Saigon\nAmerica/Tortola\nAmerica/St_Thomas\nPacific/Wallis\nAfrica/El_Aaiun\nAsia/Aden\nEurope/Belgrade\nAfrica/Lusaka\nAfrica/Harare";
}function tmpl_post_options($arg, $perms=0)
{
	$post_opt_html		= 'L&#39;<b>HTML</b> è <b>OFF</b>';
	$post_opt_fud		= '<b>FUDcode</b> è <b>OFF</b>';
	$post_opt_images 	= 'Le <b>immagini</b> sono <b>OFF</b>';
	$post_opt_smilies	= 'Gli <b>smiley</b> sono <b>OFF</b>';
	$edit_time_limit	= '';

	if (is_int($arg)) {
		if ($arg & 16) {
			$post_opt_fud = '<b><a href="/egroupware/fudforum/3814588639/index.php?t=help_index&sect=readingposting&'._rsid.'#style" target=_new>FUDcode</b> è <b>ON</a></b>';
		} else if (!($arg & 8)) {
			$post_opt_html = 'L&#39;<b>HTML</b> è <b>ON</b>';
		}
		if ($perms & 16384) {
			$post_opt_smilies = '<a href="/egroupware/fudforum/3814588639/index.php?t=help_index&sect=readingposting&'._rsid.'#sml" target=_new>Gli <b>smiley</b> sono <b>ON</b></a>';
		}
		if ($perms & 32768) {
			$post_opt_images = 'Le <b>immagini</b> sono <b>ON</b>';
		}
		$edit_time_limit = $GLOBALS['EDIT_TIME_LIMIT'] ? '<br><b>Tempo limite per la modifica</b>: <b>'.$GLOBALS['EDIT_TIME_LIMIT'].'</b> minuti' : '<br><b>Tempo limite per la modifica</b>: <b>illimitato</b>';
	} else if ($arg == 'private') {
		$o =& $GLOBALS['FUD_OPT_1'];

		if ($o & 4096) {
			$post_opt_fud = '<b><a href="/egroupware/fudforum/3814588639/index.php?t=help_index&sect=readingposting&'._rsid.'#style" target=_new>FUDcode</b> è <b>ON</a></b>';
		} else if (!($o & 2048)) {
			$post_opt_html = 'L&#39;<b>HTML</b> è <b>ON</b>';
		}
		if ($o & 16384) {
			$post_opt_images = 'Le <b>immagini</b> sono <b>ON</b>';
		}
		if ($o & 8192) {
			$post_opt_smilies = '<a href="/egroupware/fudforum/3814588639/index.php?t=help_index&sect=readingposting&'._rsid.'#sml" target=_new>Gli <b>smiley</b> sono <b>ON</b></a>';
		}
	} else if ($arg == 'sig') {
		$o =& $GLOBALS['FUD_OPT_1'];

		if ($o & 131072) {
			$post_opt_fud = '<b><a href="/egroupware/fudforum/3814588639/index.php?t=help_index&sect=readingposting&'._rsid.'#style" target=_new>FUDcode</b> è <b>ON</a></b>';
		} else if (!($o & 65536)) {
			$post_opt_html = 'L&#39;<b>HTML</b> è <b>ON</b>';
		}
		if ($o & 524288) {
			$post_opt_images = 'Le <b>immagini</b> sono <b>ON</b>';
		}
		if ($o & 262144) {
			$post_opt_smilies = '<a href="/egroupware/fudforum/3814588639/index.php?t=help_index&sect=readingposting&'._rsid.'#sml" target=_new>Gli <b>smiley</b> sono <b>ON</b></a>';
		}
	}

	return '<font class="SmallText"><b>Opzioni del forum</b><br />
'.$post_opt_html.'<br />
'.$post_opt_fud.'<br />
'.$post_opt_images.'<br />
'.$post_opt_smilies.$edit_time_limit.'</font><br />';
}class fud_user
{
	var $id, $login, $alias, $passwd, $plaintext_passwd, $name, $email, $location, $occupation, $interests,
	    $icq, $aim, $yahoo, $msnm, $jabber, $affero, $avatar, $avatar_loc, $posts_ppg, $time_zone, $bday, $home_page,
	    $sig, $bio, $posted_msg_count, $last_visit, $last_event, $conf_key, $user_image, $join_date, $theme, $last_read,
	    $mod_list, $mod_cur, $level_id, $u_last_post_id, $users_opt, $cat_collapse_status, $ignore_list, $buddy_list;
}

function make_alias($text)
{
	if (strlen($text) > $GLOBALS['MAX_LOGIN_SHOW']) {
		$text = substr($text, 0, $GLOBALS['MAX_LOGIN_SHOW']);
	}
	return htmlspecialchars($text);
}

class fud_user_reg extends fud_user
{
	function sync_user()
	{
		$rb_mod_list = (!($this->users_opt & 524288) && ($is_mod = q_singleval("SELECT id FROM phpgw_fud_mod WHERE user_id={$this->id}")) && (q_singleval("SELECT alias FROM phpgw_fud_users WHERE id={$this->id}") == $this->alias));

		q("UPDATE phpgw_fud_users SET ".$passwd."
			icq=".in($this->icq).",
			aim=".ssn(urlencode($this->aim)).",
			yahoo=".ssn(urlencode($this->yahoo)).",
			msnm=".ssn(urlencode($this->msnm)).",
			jabber=".ssn(htmlspecialchars($this->jabber)).",
			affero=".ssn(urlencode($this->affero)).",
			posts_ppg='".iz($this->posts_ppg)."',
			time_zone='".addslashes($this->time_zone)."',
			bday=".iz($this->bday).",
			user_image=".ssn(htmlspecialchars($this->user_image)).",
			location=".ssn(htmlspecialchars($this->location)).",
			occupation=".ssn(htmlspecialchars($this->occupation)).",
			interests=".ssn(htmlspecialchars($this->interests)).",
			avatar=".iz($this->avatar).",
			theme=".iz($this->theme).",
			avatar_loc=".ssn($this->avatar_loc).",
			sig=".ssn($this->sig).",
			home_page=".ssn(htmlspecialchars($this->home_page)).",
			bio=".ssn($this->bio).",
			users_opt=".$this->users_opt."
		WHERE id=".$this->id);

		if ($rb_mod_list) {
			rebuildmodlist();
		}
	}
}

function get_id_by_email($email)
{
	return q_singleval("SELECT id FROM phpgw_fud_users WHERE email='".addslashes($email)."'");
}

function get_id_by_login($login)
{
	return q_singleval("SELECT id FROM phpgw_fud_users WHERE login='".addslashes($login)."'");
}

function &usr_reg_get_full($id)
{
	if (($r = db_sab('SELECT * FROM phpgw_fud_users WHERE id='.$id))) {
		if (!function_exists('aggregate_methods')) {
			$o = new fud_user_reg;
			foreach ($r as $k => $v) {
				$o->{$k} = $v;
			}
			$r = $o;
		} else {
			aggregate_methods($r, 'fud_user_reg');
		}
	}
	return $r;
}

function rebuildmodlist()
{
	$tbl =& $GLOBALS['DBHOST_TBL_PREFIX'];
	$lmt =& $GLOBALS['SHOW_N_MODS'];
	$c = uq('SELECT u.id, u.alias, f.id FROM '.$tbl.'mod mm INNER JOIN '.$tbl.'users u ON mm.user_id=u.id INNER JOIN '.$tbl.'forum f ON f.id=mm.forum_id ORDER BY f.id,u.alias');
	while ($r = db_rowarr($c)) {
		$u[] = $r[0];
		if (isset($ar[$r[2]]) && count($ar[$r[2]]) >= $lmt) {
			continue;
		}
		$ar[$r[2]][$r[0]] = $r[1];
	}

	q('UPDATE '.$tbl.'forum SET moderators=NULL');
	if (isset($ar)) {
		foreach ($ar as $k => $v) {
			q('UPDATE '.$tbl.'forum SET moderators='.strnull(addslashes(@serialize($v))).' WHERE id='.$k);
		}
	}
	q('UPDATE '.$tbl.'users SET users_opt=users_opt & ~ 524288 WHERE users_opt>=524288 AND (users_opt & 524288) > 0');
	if (isset($u)) {
		q('UPDATE '.$tbl.'users SET users_opt=users_opt|524288 WHERE id IN('.implode(',', $u).') AND (users_opt & 1048576)=0');
	}
}$GLOBALS['seps'] = array(' '=>' ', "\n"=>"\n", "\r"=>"\r", "'"=>"'", '"'=>'"', '['=>'[', ']'=>']', '('=>'(', ';'=>';', ')'=>')', "\t"=>"\t", '='=>'=', '>'=>'>', '<'=>'<');

function fud_substr_replace($str, $newstr, $pos, $len)
{
        return substr($str, 0, $pos).$newstr.substr($str, $pos+$len);
}

function char_fix(&$str)
{
	$str = str_replace(
		array('&amp;#0', '&amp;#1', '&amp;#2', '&amp;#3', '&amp;#4', '&amp;#5', '&amp;#6', '&amp;#7','&amp;#8','&amp;#9'),
		array('&#0', '&#1', '&#2', '&#3', '&#4', '&#5', '&#6', '&#7', '&#8', '&#9'),
		$str);
}

function tags_to_html($str, $allow_img=1, $no_char=0)
{
	if (!$no_char) {
		$str = htmlspecialchars($str);
	}

	$str = nl2br($str);

	$ostr = '';
	$pos = $old_pos = 0;

	while (($pos = strpos($str, '[', $pos)) !== false) {
		if (isset($GLOBALS['seps'][$str[$pos + 1]])) {
			++$pos;
			continue;
		}

		if (($epos = strpos($str, ']', $pos)) === false) {
			break;
		}
		if (!($epos-$pos-1)) {
			$pos = $epos + 1;
			continue;
		}
		$tag = substr($str, $pos+1, $epos-$pos-1);
		if (($pparms = strpos($tag, '=')) !== false) {
			$parms = substr($tag, $pparms+1);
			if (!$pparms) { /*[= exception */
				$pos = $epos+1;
				continue;
			}
			$tag = substr($tag, 0, $pparms);
		} else {
			$parms = '';
		}

		$tag = strtolower($tag);

		switch ($tag) {
			case 'quote title':
				$tag = 'quote';
				break;
			case 'list type':
				$tag = 'list';
				break;
		}

		if ($tag[0] == '/') {
			if (isset($end_tag[$pos])) {
				if( ($pos-$old_pos) ) $ostr .= substr($str, $old_pos, $pos-$old_pos);
				$ostr .= $end_tag[$pos];
				$pos = $old_pos = $epos+1;
			} else {
				$pos = $epos+1;
			}

			continue;
		}

		$cpos = $epos;
		$ctag = '[/'.$tag.']';
		$ctag_l = strlen($ctag);
		$otag = '['.$tag;
		$otag_l = strlen($otag);
		$rf = 1;
		while (($cpos = strpos($str, '[', $cpos)) !== false) {
			if (isset($end_tag[$cpos]) || isset($GLOBALS['seps'][$str[$cpos + 1]])) {
				++$cpos;
				continue;
			}

			if (($cepos = strpos($str, ']', $cpos)) === false) {
				break 2;
			}

			if (strcasecmp(substr($str, $cpos, $ctag_l), $ctag) == 0) {
				--$rf;
			} else if (strcasecmp(substr($str, $cpos, $otag_l), $otag) == 0) {
				++$rf;
			} else {
				++$cpos;
				continue;
			}

			if (!$rf) {
				break;
			}
			$cpos = $cepos;
		}

		if (!$cpos || ($rf && $str[$cpos] == '<')) { /* left over [ handler */
			++$pos;
			continue;
		}

		if ($cpos !== false) {
			if (($pos-$old_pos)) {
				$ostr .= substr($str, $old_pos, $pos-$old_pos);
			}
			switch ($tag) {
				case 'notag':
					$ostr .= '<span name="notag">'.substr($str, $epos+1, $cpos-1-$epos).'</span>';
					$epos = $cepos;
					break;
				case 'url':
					if (!$parms) {
						$url = substr($str, $epos+1, ($cpos-$epos)-1);
					} else {
						$url = $parms;
					}

					if (!strncasecmp($url, 'www.', 4)) {
						$url = 'http&#58;&#47;&#47;'. $url;
					} else if (strpos(strtolower($url), 'javascript:') !== false) {
						$ostr .= substr($str, $pos, $cepos - $pos + 1);
						$epos = $cepos;
						$str[$cpos] = '<';
						break;
					} else {
						$url = str_replace('://', '&#58;&#47;&#47;', $url);
					}

					$end_tag[$cpos] = '</a>';
					$ostr .= '<a href="'.$url.'" target="_blank">';
					break;
				case 'i':
				case 'u':
				case 'b':
				case 's':
				case 'sub':
				case 'sup':
					$end_tag[$cpos] = '</'.$tag.'>';
					$ostr .= '<'.$tag.'>';
					break;
				case 'email':
					if (!$parms) {
						$parms = str_replace('@', '&#64;', substr($str, $epos+1, ($cpos-$epos)-1));
						$ostr .= '<a href="mailto:'.$parms.'" target="_blank">'.$parms.'</a>';
						$epos = $cepos;
						$str[$cpos] = '<';
					} else {
						$end_tag[$cpos] = '</a>';
						$ostr .= '<a href="mailto:'.str_replace('@', '&#64;', $parms).'" target="_blank">';
					}
					break;
				case 'color':
				case 'size':
				case 'font':
					if ($tag == 'font') {
						$tag = 'face';
					}
					$end_tag[$cpos] = '</font>';
					$ostr .= '<font '.$tag.'="'.$parms.'">';
					break;
				case 'code':
					$param = substr($str, $epos+1, ($cpos-$epos)-1);
					reverse_nl2br($param);

					$ostr .= '<div class="pre"><pre>'.$param.'</pre></div>';
					$epos = $cepos;
					$str[$cpos] = '<';
					break;
				case 'pre':
					$param = substr($str, $epos+1, ($cpos-$epos)-1);
					reverse_nl2br($param);

					$ostr .= '<pre>'.$param.'</pre>';
					$epos = $cepos;
					$str[$cpos] = '<';
					break;
				case 'php':
					$param = substr($str, $epos+1, ($cpos-$epos)-1);
					reverse_nl2br($param);
					reverse_fmt($param);
					$param = trim($param);

					if (strncmp($param, '<?php', 5)) {
						if (strncmp($param, '<?', 2)) {
							$param = "<?php\n" . $param;
						} else {
							$param = "<?php\n" . substr($param, 3);
						}
					}
					if (substr($param, -2) != '?>') {
						$param .= "\n?>";
					}

					$ostr .= '<span name="php">'.trim(@highlight_string($param, true)).'</span>';
					$epos = $cepos;
					$str[$cpos] = '<';
					break;
				case 'img':
					if (!$allow_img) {
						$ostr .= substr($str, $pos, ($cepos-$pos)+1);
					} else {
						if (!$parms) {
							$parms = substr($str, $epos+1, ($cpos-$epos)-1);
							if (strpos(strtolower($parms), 'javascript:') === false) {
								$ostr .= '<img src="'.$parms.'" border=0 alt="'.$parms.'">';
							} else {
								$ostr .= substr($str, $pos, ($cepos-$pos)+1);
							}
						} else {
							if (strpos(strtolower($parms), 'javascript:') === false) {
								$ostr .= '<img src="'.$parms.'" border=0 alt="'.substr($str, $epos+1, ($cpos-$epos)-1).'">';
							} else {
								$ostr .= substr($str, $pos, ($cepos-$pos)+1);
							}
						}
					}
					$epos = $cepos;
					$str[$cpos] = '<';
					break;
				case 'quote':
					if (!$parms) {
						$parms = 'Quote:';
					}
					$ostr .= '<table border="0" align="center" width="90%" cellpadding="3" cellspacing="1"><tr><td class="SmallText"><b>'.$parms.'</b></td></tr><tr><td class="quote"><br>';
					$end_tag[$cpos] = '<br></td></tr></table>';
					break;
				case 'align':
					$end_tag[$cpos] = '</div>';
					$ostr .= '<div align="'.$parms.'">';
					break;
				case 'list':
					$tmp = substr($str, $epos, ($cpos-$epos));
					$tmp_l = strlen($tmp);
					$tmp2 = str_replace(array('[*]', '<br />'), array('<li>', ''), $tmp);
					$tmp2_l = strlen($tmp2);
					$str = str_replace($tmp, $tmp2, $str);

					$diff = $tmp2_l - $tmp_l;
					$cpos += $diff;

					if (isset($end_tag)) {
						foreach($end_tag as $key => $val) {
							if ($key < $epos) {
								continue;
							}

							$end_tag[$key+$diff] = $val;
						}
					}

					switch (strtolower($parms)) {
						case '1':
						case 'a':
							$end_tag[$cpos] = '</ol>';
							$ostr .= '<ol type="'.$parms.'">';
							break;
						case 'square':
						case 'circle':
						case 'disc':
							$end_tag[$cpos] = '</ul>';
							$ostr .= '<ul type="'.$parms.'">';
							break;
						default:
							$end_tag[$cpos] = '</ul>';
							$ostr .= '<ul>';
					}
					break;
				case 'spoiler':
					$rnd = get_random_value(64);
					$end_tag[$cpos] = '</div></div>';
					$ostr .= '<div class="dashed" style="padding: 3px;" align="center" width="100%"><a href="javascript://" OnClick="javascript: layerVis(\''.$rnd.'\', 1);">Mostra lo spoiler</a><div align="left" id="'.$rnd.'" style="visibility: hidden;">';
					break;
			}

			$str[$pos] = '<';
			$pos = $old_pos = $epos+1;
		} else {
			$pos = $epos+1;
		}
	}
	$ostr .= substr($str, $old_pos, strlen($str)-$old_pos);

	/* url paser */
	$pos = 0;
	$ppos = 0;
	while (($pos = @strpos($ostr, '://', $pos)) !== false) {
		if ($pos < $ppos) {
			break;
		}
		// check if it's inside any tag;
		$i = $pos;
		while (--$i && $i > $ppos) {
			if ($ostr[$i] == '>' || $ostr[$i] == '<') {
				break;
			}
		}
		if ($ostr[$i]=='<') {
			$pos+=3;
			continue;
		}

		// check if it's inside the a tag
		if (($ts = strpos($ostr, '<a ', $pos)) === false) {
			$ts = strlen($ostr);
		}
		if (($te = strpos($ostr, '</a>', $pos)) == false) {
			$te = strlen($ostr);
		}
		if ($te < $ts) {
			$ppos = $pos += 3;
			continue;
		}

		// check if it's inside the pre tag
		if (($ts = strpos($ostr, '<pre>', $pos)) === false) {
			$ts = strlen($ostr);
		}
		if (($te = strpos($ostr, '</pre>', $pos)) == false) {
			$te = strlen($ostr);
		}
		if ($te < $ts) {
			$ppos = $pos += 3;
			continue;
		}

		// check if it's inside the span tag
		if (($ts = strpos($ostr, '<span>', $pos)) === false) {
			$ts = strlen($ostr);
		}
		if (($te = strpos($ostr, '</span>', $pos)) == false) {
			$te = strlen($ostr);
		}
		if ($te < $ts) {
			$ppos = $pos += 3;
			continue;
		}

		$us = $pos;
		$l = strlen($ostr);
		while (1) {
			--$us;
			if ($ppos > $us || $us >= $l || isset($GLOBALS['seps'][$ostr[$us]])) {
				break;
			}
		}

		unset($GLOBALS['seps']['=']);
		$ue = $pos;
		while (1) {
			++$ue;
			if ($ue >= $l || isset($GLOBALS['seps'][$ostr[$ue]])) {
				break;
			}

			if ($ostr[$ue] == '&') {
				if ($ostr[$ue+4] == ';') {
					$ue += 4;
					continue;
				}
				if ($ostr[$ue+3] == ';' || $ostr[$ue+5] == ';') {
					break;
				}
			}

			if ($ue >= $l || isset($GLOBALS['seps'][$ostr[$ue]])) {
				break;
			}
		}
		$GLOBALS['seps']['='] = '=';

		$url = substr($ostr, $us+1, $ue-$us-1);
		if (!strncasecmp($url, 'javascript', strlen('javascript'))) {
			$pos = $ue;
			continue;
		}
		$html_url = '<a href="'.$url.'" target="_blank">'.$url.'</a>';
		$html_url_l = strlen($html_url);
		$ostr = fud_substr_replace($ostr, $html_url, $us+1, $ue-$us-1);
		$ppos = $pos;
		$pos = $us+$html_url_l;
	}

	/* email parser */
	$pos = 0;
	$ppos = 0;
	while (($pos = @strpos($ostr, '@', $pos)) !== false) {
		if ($pos < $ppos) {
			break;
		}

		// check if it's inside any tag;
		$i = $pos;
		while (--$i && $i>$ppos) {
			if ( $ostr[$i] == '>' || $ostr[$i] == '<') {
				break;
			}
		}
		if ($ostr[$i]=='<') {
			++$pos;
			continue;
		}


		// check if it's inside the a tag
		if (($ts = strpos($ostr, '<a ', $pos)) === false) {
			$ts = strlen($ostr);
		}
		if (($te = strpos($ostr, '</a>', $pos)) == false) {
			$te = strlen($ostr);
		}
		if ($te < $ts) {
			$ppos = $pos += 1;
			continue;
		}

		// check if it's inside the pre tag
		if (($ts = strpos($ostr, '<div class="pre"><pre>', $pos)) === false) {
			$ts = strlen($ostr);
		}
		if (($te = strpos($ostr, '</pre></div>', $pos)) == false) {
			$te = strlen($ostr);
		}
		if ($te < $ts) {
			$ppos = $pos += 1;
			continue;
		}

		for ($es = ($pos - 1); $es > ($ppos - 1); $es--) {
			if (
				( ord($ostr[$es]) >= ord('A') && ord($ostr[$es]) <= ord('z') ) ||
				( ord($ostr[$es]) >= ord(0) && ord($ostr[$es]) <= ord(9) ) ||
				( $ostr[$es] == '.' || $ostr[$es] == '-' || $ostr[$es] == '\'')
			) { continue; }
			++$es;
			break;
		}
		if ($es == $pos) {
			$ppos = $pos += 1;
			continue;
		}
		if ($es < 0) {
			$es = 0;
		}

		for ($ee = ($pos + 1); @isset($ostr[$ee]); $ee++) {
			if (
				( ord($ostr[$ee]) >= ord('A') && ord($ostr[$ee]) <= ord('z') ) ||
				( ord($ostr[$ee]) >= ord(0) && ord($ostr[$ee]) <= ord(9) ) ||
				( $ostr[$ee] == '.' || $ostr[$ee] == '-' )
			) { continue; }
			break;
		}
		if ($ee == ($pos+1)) {
			$ppos = $pos += 1;
			continue;
		}

		$email = str_replace('@', '&#64;', substr($ostr, $es, $ee-$es));
		$email_url = '<a href="mailto:'.$email.'" target="_blank">'.$email.'</a>';
		$email_url_l = strlen($email_url);
		$ostr = fud_substr_replace($ostr, $email_url, $es, $ee-$es);
		$ppos =	$es+$email_url_l;
		$pos = $ppos;
	}

	return $ostr;
}

if (!function_exists('html_entity_decode')) {
	function html_entity_decode($s)
	{
		return strtr($s, array_flip(get_html_translation_table(HTML_ENTITIES)));
	}
}

function html_to_tags($fudml)
{
	while (preg_match('!<span name="php">(.*?)</span>!is', $fudml, $res)) {
		$tmp = trim(html_entity_decode(strip_tags(str_replace('<br />', "\n", $res[1]))));
		$m = md5($tmp);
		$php[$m] = $tmp;
		$fudml = str_replace($res[0], "[php]\n".$m."\n[/php]", $fudml);
	}

	if (strpos($fudml, '<table border="0" align="center" width="90%" cellpadding="3" cellspacing="1"><tr><td class="SmallText"><b>')  !== false) {
		$fudml = str_replace(array('<table border="0" align="center" width="90%" cellpadding="3" cellspacing="1"><tr><td class="SmallText"><b>','</b></td></tr><tr><td class="quote"><br>','<br></td></tr></table>'), array('[quote title=', ']', '[/quote]'), $fudml);
	}

	if (preg_match('!<div class="dashed" style="padding: 3px;" align="center" width="100%"><a href="javascript://" OnClick="javascript: layerVis\(\'.*?\', 1\);">Mostra lo spoiler</a><div align="left" id=".*?" style="visibility: hidden;">!is', $fudml)) {
		$fudml = preg_replace('!\<div class\="dashed" style\="padding: 3px;" align\="center" width\="100%"\>\<a href\="javascript://" OnClick\="javascript: layerVis\(\'.*?\', 1\);">Mostra lo spoiler\</a\>\<div align\="left" id\=".*?" style\="visibility: hidden;"\>!is', '[spoiler]', $fudml);
		$fudml = str_replace('</div></div>', '[/spoiler]', $fudml);
	}

	while (preg_match('!<font (color|face|size)=".+?">.*?</font>!is', $fudml)) {
		$fudml = preg_replace('!<font (color|face|size)="(.+?)">(.*?)</font>!is', '[\1=\2]\3[/\1]', $fudml);
	}
	while (preg_match('!<(o|u)l type=".+?">.*?</\\1l>!is', $fudml)) {
		$fudml = preg_replace('!<(o|u)l type="(.+?)">(.*?)</\\1l>!is', '[list type=\2]\3[/list]', $fudml);
	}

	$fudml = str_replace(
	array(
		'<b>', '</b>', '<i>', '</i>', '<u>', '</u>', '<s>', '</s>', '<sub>', '</sub>', '<sup>', '</sup>',
		'<div class="pre"><pre>', '</pre></div>', '<div align="center">', '<div align="left">', '<div align="right">', '</div>',
		'<ul>', '</ul>', '<span name="notag">', '</span>', '<li>', '&#64;', '&#58;&#47;&#47;', '<br />', '<pre>', '</pre>'
	),
	array(
		'[b]', '[/b]', '[i]', '[/i]', '[/u]', '[/u]', '[s]', '[/s]', '[sub]', '[/sub]', '[sup]', '[/sup]', 
		'[code]', '[/code]', '[align=center]', '[align=left]', '[align=right]', '[/align]', '[list]', '[/list]',
		'[notag]', '[/notag]', '[*]', '@', '://', '', '[pre]', '[/pre]'
	), 
	$fudml);

	while (preg_match('!<img src="(.*?)" border=0 alt="\\1">!is', $fudml)) {
		$fudml = preg_replace('!<img src="(.*?)" border=0 alt="\\1">!is', '[img]\1[/img]', $fudml);
	}
	while (preg_match('!<a href="mailto:(.+?)" target="_blank">\\1</a>!is', $fudml)) {
		$fudml = preg_replace('!<a href="mailto:(.+?)" target="_blank">\\1</a>!is', '[email]\1[/email]', $fudml);
	}
	while (preg_match('!<a href="(.+?)" target="_blank">\\1</a>!is', $fudml)) {
		$fudml = preg_replace('!<a href="(.+?)" target="_blank">\\1</a>!is', '[url]\1[/url]', $fudml);
	}

	if (strpos($fudml, '<img src="') !== false) {
		$fudml = preg_replace('!<img src="(.*?)" border=0 alt="(.*?)">!is', '[img=\1]\2[/img]', $fudml);
	}
	if (strpos($fudml, '<a href="mailto:') !== false) {
		$fudml = preg_replace('!<a href="mailto:(.+?)" target="_blank">(.+?)</a>!is', '[email=\1]\2[/email]', $fudml);
	}
	if (strpos($fudml, '<a href="') !== false) { 
		$fudml = preg_replace('!<a href="(.+?)" target="_blank">(.+?)</a>!is', '[url=\1]\2[/url]', $fudml);
	}

	if (isset($php)) {
		$fudml = str_replace(array_keys($php), array_values($php), $fudml);
	}

	/* unhtmlspecialchars */
	reverse_fmt($fudml);

	return $fudml;
}


function filter_ext($file_name)
{
	include $GLOBALS['FORUM_SETTINGS_PATH'] . 'file_filter_regexp';
	if (!count($GLOBALS['__FUD_EXT_FILER__'])) {
		return;
	}
	if (($p = strrpos($file_name, '.')) === false) {
		return 1;
	}
	return !in_array(strtolower(substr($file_name, ($p + 1))), $GLOBALS['__FUD_EXT_FILER__']);
}

function safe_tmp_copy($source, $del_source=0, $prefx='')
{
	if (!$prefx) {
		 $prefx = getmypid();
	}

	$umask = umask(($GLOBALS['FUD_OPT_2'] & 8388608 ? 0177 : 0111));
	if (!move_uploaded_file($source, ($name = tempnam($GLOBALS['TMP'], $prefx.'_')))) {
		return;
	}
	umask($umask);
	if ($del_source) {
		@unlink($source);
	}
	umask($umask);

	return basename($name);
}

function reverse_nl2br(&$data)
{
	$data = str_replace('<br />', '', $data);
}function reverse_fmt(&$data)
{
	$data = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array('&', '"', '<', '>'), $data);
}function fud_wrap_tok($data)
{
	$wa = array();
	$len = strlen($data);

	$i=$j=$p=0;
	$str = '';
	while ($i < $len) {
		switch ($data{$i}) {
			case ' ':
			case "\n":
			case "\t":
				if ($j) {
					$wa[] = array('word'=>$str, 'len'=>($j+1));
					$j=0;
					$str ='';
				}

				$wa[] = array('word'=>$data[$i]);

				break;
			case '<':
				if (($p = strpos($data, '>', $i)) !== false) {
					if ($j) {
						$wa[] = array('word'=>$str, 'len'=>($j+1));
						$j=0;
						$str ='';
					}
					$s = substr($data, $i, ($p - $i) + 1);
					if ($s == '<pre>') {
						$s = substr($data, $i, ($p = (strpos($data, '</pre>', $p) + 6)) - $i);
						--$p;
					} else if ($s == '<span name="php">') {
						$s = substr($data, $i, ($p = (strpos($data, '</span>', $p) + 7)) - $i);
						--$p;
					}

					$wa[] = array('word' => $s);

					$i = $p;
					$j = 0;
				} else {
					$str .= $data[$i];
					$j++;
				}
				break;

			case '&':
				if (($e = strpos($data, ';', $i))) {
					$st = substr($data, $i, ($e - $i + 1));
					if (($st{1} == '#' && is_numeric(substr($st, 3, -1))) || !strcmp($st, '&nbsp;') || !strcmp($st, '&gt;') || !strcmp($st, '&lt;') || !strcmp($st, '&quot;')) {
						if ($j) {
							$wa[] = array('word'=>$str, 'len'=>($j+1));
							$j=0;
							$str ='';
						}

						$wa[] = array('word' => $st, 'sp' => 1);
						$i=$e;
						$j=0;
						break;
					}
				} /* fall through */
			default:
				$str .= $data[$i];
				$j++;
		}
		$i++;
	}

	if ($j) {
		$wa[] = array('word'=>$str, 'len'=>($j+1));
	}

	return $wa;
}

function fud_wordwrap(&$data)
{
	if (!$GLOBALS['WORD_WRAP'] || $GLOBALS['WORD_WRAP'] >= strlen($data)) {
		return;
	}

	$wa = fud_wrap_tok($data);
	$m = (int) $GLOBALS['WORD_WRAP'];
	$l = 0;
	$data = null;
	foreach($wa as $v) {
		if (isset($v['len']) && $v['len'] > $m) {
			if ($v['len'] + $l > $m) {
				$l = 0;
				$data .= ' ';
			}
			$data .= wordwrap($v['word'], $m, ' ', 1);
			$l += $v['len'];
		} else {
			if (isset($v['sp'])) {
				if ($l > $m) {
					$data .= ' ';
					$l = 0;
				}
				++$l;
			} else if (!isset($v['len'])) {
				$l = 0;
			} else {
				$l += $v['len'];
			}
			$data .= $v['word'];
		}
	}
}$GLOBALS['__SML_CHR_CHK__'] = array("\n"=>1, "\r"=>1, "\t"=>1, " "=>1, "]"=>1, "["=>1, "<"=>1, ">"=>1, "'"=>1, '"'=>1, "("=>1, ")"=>1, "."=>1, ","=>1, "!"=>1, "?"=>1);

function smiley_to_post($text)
{
	$text_l = strtolower($text);

        $c = uq('SELECT code, '.__FUD_SQL_CONCAT__.'(\'images/smiley_icons/\', img), descr FROM phpgw_fud_smiley');
        while ($r = db_rowarr($c)) {
        	$codes = (strpos($r[0], '~') !== false) ? explode('~', strtolower($r[0])) : array(strtolower($r[0]));

		foreach ($codes as $v) {
			$a = 0;
			$len = strlen($v);
			while (($a = strpos($text_l, $v, $a)) !== false) {
				if ((!$a || isset($GLOBALS['__SML_CHR_CHK__'][$text_l[$a - 1]])) && ((@$ch = $text_l[$a + $len]) == "" || isset($GLOBALS['__SML_CHR_CHK__'][$ch]))) {
					$rep = '<img src="'.$r[1].'" border=0 alt="'.$r[2].'">';
					$text = substr_replace($text, $rep, $a, $len);
					$text_l = substr_replace($text_l, $rep, $a, $len);
					$a += strlen($rep);
				} else {
					$a += $len;
				}
			}
		}
	}

	return $text;
}

function post_to_smiley($text)
{
	$c = uq('SELECT code, '.__FUD_SQL_CONCAT__.'(\'images/smiley_icons/\', img), descr FROM phpgw_fud_smiley');
	while ($r = db_rowarr($c)) {
		$im = '<img src="'.$r[1].'" border=0 alt="'.$r[2].'">';
		$re[$im] = (($p = strpos($r[0], '~')) !== false) ? substr($r[0], 0, $p) : $r[0];
	}

	return (isset($re) ? strtr($text, $re) : $text);
}function apply_custom_replace($text)
{
	if (!defined('__fud_replace_init')) {
		make_replace_array();
	}
	if (!isset($GLOBALS['__FUD_REPL__'])) {
		return $text;
	}

	return preg_replace($GLOBALS['__FUD_REPL__']['pattern'], $GLOBALS['__FUD_REPL__']['replace'], $text);
}

function make_replace_array()
{
	$c = uq('SELECT with_str, replace_str FROM phpgw_fud_replace WHERE replace_str IS NOT NULL AND with_str IS NOT NULL AND LENGTH(replace_str)>0');
	while ($r = db_rowarr($c)) {
		$GLOBALS['__FUD_REPL__']['pattern'][] = $r[1];
		$GLOBALS['__FUD_REPL__']['replace'][] = $r[0];
	}

	define('__fud_replace_init', 1);
}

function make_reverse_replace_array()
{
	$c = uq('SELECT replace_opt, with_str, replace_str, from_post, to_msg FROM phpgw_fud_replace');

	while ($r = db_rowarr($c)) {
		if (!$r[0]) {
			$GLOBALS['__FUD_REPLR__']['pattern'][] = $r[3];
			$GLOBALS['__FUD_REPLR__']['replace'][] = $r[4];
		} else if ($r[0] && strlen($r[1]) && strlen($r[2])) {
			$GLOBALS['__FUD_REPLR__']['pattern'][] = '/'.str_replace('/', '\\/', preg_quote(stripslashes($r[1]))).'/';
			preg_match('/\/(.+)\/(.*)/', $r[2], $regs);
			$GLOBALS['__FUD_REPLR__']['replace'][] = str_replace('\\/', '/', $regs[1]);
		}
	}

	define('__fud_replacer_init', 1);
}

function apply_reverse_replace($text)
{
	if (!defined('__fud_replacer_init')) {
		make_reverse_replace_array();
	}
	if (!isset($GLOBALS['__FUD_REPLR__'])) {
		return $text;
	}
	return preg_replace($GLOBALS['__FUD_REPLR__']['pattern'], $GLOBALS['__FUD_REPLR__']['replace'], $text);
}function check_return($returnto)
{
	if (!$returnto || !strncmp($returnto, 't=error', 7)) {
		header('Location: /egroupware/fudforum/3814588639/index.php?t=index&'._rsidl);
	} else {
		if (strpos($returnto, 'S=') === false && $GLOBALS['FUD_OPT_1'] & 128) {
			header('Location: /egroupware/fudforum/3814588639/index.php?'.$returnto.'&S='.s);
		} else {
			header('Location: /egroupware/fudforum/3814588639/index.php?'.$returnto);
		}
	}
	exit;
}function validate_email($email)
{
        return !preg_match('!([-_A-Za-z0-9\.]+)\@([-_A-Za-z0-9\.]+)\.([A-Za-z0-9]{2,4})$!', $email);
}

function send_email($from, $to, $subj, $body, $header='')
{
	if (empty($to) || !count($to)) {
		return;
	}
	$body = str_replace('\n', "\n", $body);

	if ($GLOBALS['FUD_OPT_1'] & 512) {
		if (!class_exists('fud_smtp')) {
			fud_use('smtp.inc');
		}
		$smtp = new fud_smtp;
		$smtp->msg = str_replace("\n.", "\n..", $body);
		$smtp->subject = $subj;
		$smtp->to = $to;
		$smtp->from = $from;
		$smtp->headers = $header;
		$smtp->send_smtp_email();
	} else {
		$bcc = '';

		if (is_array($to)) {
			$to = $to[0];
			if (count($to) > 1) {
				unset($to[0]);
				$bcc = 'Bcc: ' . implode(', ', $to);
			}
		}
		if ($header) {
			$header = "\n" . str_replace("\r", "", $header);
		} else if ($bcc) {
			$bcc = "\n" . $bcc;
		}

		if (version_compare("4.3.3RC2", phpversion(), ">")) {
			$body = str_replace("\n.", "\n..", $body);
		}

		mail($to, $subj, str_replace("\r", "", $body), "From: ".$from."\nErrors-To: ".$from."\nReturn-Path: ".$from."\nX-Mailer: FUDforum v".$GLOBALS['FORUM_VERSION'].$header.$bcc);
	}
}include $GLOBALS['FORUM_SETTINGS_PATH'] . 'ip_filter_cache';
	include $GLOBALS['FORUM_SETTINGS_PATH'] . 'login_filter_cache';
	include $GLOBALS['FORUM_SETTINGS_PATH'] . 'email_filter_cache';

function is_ip_blocked($ip)
{
	if (!count($GLOBALS['__FUD_IP_FILTER__'])) {
		return;
	}
	$block =& $GLOBALS['__FUD_IP_FILTER__'];
	list($a,$b,$c,$d) = explode('.', $ip);

	if (!isset($block[$a])) {
		return;
	}
	if (isset($block[$a][$b][$c][$d])) {
		return 1;
	}

	if (isset($block[$a][256])) {
		$t = $block[$a][256];
	} else if (isset($block[$a][$b])) {
		$t = $block[$a][$b];
	} else {
		return;
	}

	if (isset($t[$c])) {
		$t = $t[$c];
	} else if (isset($t[256])) {
		$t = $t[256];
	} else {
		return;
	}

	return (isset($t[$d]) || isset($t[256])) ? 1 : null;
}

function is_login_blocked($l)
{
	foreach ($GLOBALS['__FUD_LGN_FILTER__'] as $v) {
		if (preg_match($v, $l)) {
			return 1;
		}
	}
	return;
}

function is_email_blocked($addr)
{
	if (!count($GLOBALS['__FUD_EMAIL_FILTER__'])) {
		return;
	}
	$addr = strtolower($addr);
	foreach ($GLOBALS['__FUD_EMAIL_FILTER__'] as $k => $v) {
		if (($v && (strpos($addr, $k) !== false)) || (!$v && preg_match($k, $addr))) {
			return 1;
		}
	}
	return;
}

function is_allowed_user(&$usr)
{
	if ($GLOBALS['FUD_OPT_2'] & 1024 && $usr->users_opt & 2097152) {
		error_dialog('Unverified Account', 'The administrator had chosen to review all accounts manually prior to activation. Until your account is validated by the administrator you will not be able to utilize the full capabilities of your account.');
	}

	if ($usr->users_opt & 65536 || is_email_blocked($usr->email) || is_login_blocked($usr->login) || is_ip_blocked(get_ip())) {
		error_dialog('ERRORE: non sei autorizzato a postare messaggi', 'A questo account è stata impedita la possibilità di scrivere messaggi');
	}
}function register_fp($id)
{
	if (!isset($GLOBALS['__MSG_FP__'][$id])) {
		$GLOBALS['__MSG_FP__'][$id] = fopen($GLOBALS['MSG_STORE_DIR'].'msg_'.$id, 'rb');
	}

	return $GLOBALS['__MSG_FP__'][$id];
}

function un_register_fps()
{
	if (!isset($GLOBALS['__MSG_FP__'])) {
		return;
	}
	unset($GLOBALS['__MSG_FP__']);
}

function read_msg_body($off, $len, $file_id)
{
	$fp = register_fp($file_id);
	fseek($fp, $off);
	return fread($fp, $len);
}class fud_smtp
{
	var $fs, $last_ret, $msg, $subject, $to, $from, $headers;

	function get_return_code($cmp_code='250')
	{
		if (!($this->last_ret = fgets($this->fs, 1024))) {
			return;
		}
		if (substr($this->last_ret, 0, 3) == $cmp_code) {
			return 1;
		}

		return;
	}

	function wts($string)
	{
		fwrite($this->fs, $string . "\r\n");
	}

	function open_smtp_connex()
	{
		if( !($this->fs = fsockopen($GLOBALS['FUD_SMTP_SERVER'], 25, $errno, $errstr, $GLOBALS['FUD_SMTP_TIMEOUT'])) ) {
			exit("ERROR: stmp server at ".$GLOBALS['FUD_SMTP_SERVER']." is not avaliable<br>\nAdditional Problem Info: $errno -> $errstr <br>\n");
		}
		if (!$this->get_return_code(220)) {
			return;
		}
		$this->wts("HELO ".$GLOBALS['FUD_SMTP_SERVER']);
		if (!$this->get_return_code()) {
			return;
		}

		/* Do SMTP Auth if needed */
		if ($GLOBALS['FUD_SMTP_LOGIN']) {
			$this->wts('AUTH LOGIN');
			if (!$this->get_return_code(334)) {
				return;
			}
			$this->wts(base64_encode($GLOBALS['FUD_SMTP_LOGIN']));
			if (!$this->get_return_code(334)) {
				return;
			}
			$this->wts(base64_encode($GLOBALS['FUD_SMTP_PASS']));
			if (!$this->get_return_code(235)) {
				return;
			}
		}

		return 1;
	}

	function send_from_hdr()
	{
		$this->wts('MAIL FROM: <'.$GLOBALS['NOTIFY_FROM'].'>');
		return $this->get_return_code();
	}

	function send_to_hdr()
	{
		if (!@is_array($this->to)) {
			$this->to = array($this->to);
		}

		foreach ($this->to as $to_addr) {
			$this->wts('RCPT TO: <'.$to_addr.'>');
			if (!$this->get_return_code()) {
				return;
			}
		}
		return 1;
	}

	function send_data()
	{
		$this->wts('DATA');
		if (!$this->get_return_code(354)) {
			return;
		}

		/* This is done to ensure what we comply with RFC requiring each line to end with \r\n */
		$this->msg = preg_replace("!(\r)?\n!si", "\r\n", $this->msg);

		if( empty($this->from) ) $this->from = $GLOBALS['NOTIFY_FROM'];

		$this->wts('Subject: '.$this->subject);
		$this->wts('Date: '.date("r"));
		$this->wts('To: '.(count($this->to) == 1 ? $this->to[0] : $GLOBALS['NOTIFY_FROM']));
		$this->wts('From: '.$this->from);
		$this->wts('X-Mailer: FUDforum v'.$GLOBALS['FORUM_VERSION']);
		$this->wts($this->headers."\r\n");
		$this->wts($this->msg);
		$this->wts('.');

		return $this->get_return_code();
	}

	function close_connex()
	{
		$this->wts('quit');
		fclose($this->fs);
	}

	function send_smtp_email()
	{
		if (!$this->open_smtp_connex()) {
			exit("Invalid STMP return code: ".$this->last_ret."<br>\n");
		}
		if (!$this->send_from_hdr()) {
			$this->close_connex();
			exit("Invalid STMP return code: ".$this->last_ret."<br>\n");
		}
		if (!$this->send_to_hdr()) {
			$this->close_connex();
			exit("Invalid STMP return code: ".$this->last_ret."<br>\n");
		}
		if (!$this->send_data()) {
			$this->close_connex();
			exit("Invalid STMP return code: ".$this->last_ret."<br>\n");
		}

		$this->close_connex();
	}
}

/* Create a list of avaliable themes */
function create_theme_select($name, $def=null)
{
	$theme_select_values = '';
	$r = uq("SELECT id, name FROM phpgw_fud_themes WHERE theme_opt>=1 AND (theme_opt & 1) > 0 ORDER BY ((theme_opt & 2) > 0) DESC");
	while ($t = db_rowarr($r)) {
		$selected = $t[0] == $def ? ' selected' : '';
		$theme_select_values .= '<option value="'.$t[0].'"'.$selected.'>'.$t[1].'</option>';
	}

	return '<select name="'.$name.'">
'.$theme_select_values.'
</select>';
}

if (!function_exists('array_fill')) {
function array_fill($s, $e, $t)
{
	++$e;
	do {
		$arr[$s] = $t;
	} while (++$s < $e);

	return $arr;
}
}

function fetch_img($url, $user_id)
{
	$ext = array(1=>'gif', 2=>'jpg', 3=>'png', 4=>'swf');
	list($max_w, $max_y) = explode('x', $GLOBALS['CUSTOM_AVATAR_MAX_DIM']);
	if (!($img_info = @getimagesize($url)) || $img_info[0] > $max_w || $img_info[1] > $max_y || $img_info[2] > ($GLOBALS['FUD_OPT_1'] & 64 ? 4 : 3)) {
		return;
	}
	if (!($img_data = file_get_contents($url))) {
		return;
	}
	$name = $user_id . '.' . $ext[$img_info[2]]. '_';

	while (($fp = fopen(($path = tempnam($GLOBALS['TMP'], $name)), 'ab'))) {
		if (!ftell($fp)) { /* make sure that the temporary file picked, did not exist before, yes, this is paranoid. */
			break;
		}
	}
	fwrite($fp, $img_data);
	fclose($fp);

	return $path;
}
	/* intialize error status */
	$GLOBALS['error'] = 0;

function check_passwd($id, $passwd)
{
	return q_singleval("SELECT login FROM phpgw_fud_users WHERE id=".$id." AND passwd='".md5($passwd)."'");
}

function sanitize_url($url)
{
	if (!$url) {
		return '';
	}

	if (strncasecmp($url, 'http://', strlen('http://')) && strncasecmp($url, 'https://', strlen('https://')) && strncasecmp($url, 'ftp://', strlen('ftp://'))) {
		if (stristr($url, 'javascript:')) {
			return '';
		} else {
			return 'http://' . $url;
		}
	}
	return $url;
}

function sanitize_login($login)
{
	for ($i = 0; $i < 32; $i++) $list[] = chr($i);
	for ($i = 127; $i < 160; $i++) $list[] = chr($i);

	return str_replace($list, array_fill(0, count($list), ''), $login);
}

function register_form_check($user_id)
{
	$_POST['reg_home_page'] = sanitize_url(trim($_POST['reg_home_page']));
	$_POST['reg_user_image'] = !empty($_POST['reg_user_image']) ? sanitize_url(trim($_POST['reg_user_image'])) : '';

	if (!empty($_POST['reg_icq']) && !(int)$_POST['reg_icq']) { /* ICQ # can only be an integer */
		$_POST['reg_icq'] = '';
	}

	/* Image count check */
	if ($GLOBALS['FORUM_IMG_CNT_SIG'] && $GLOBALS['FORUM_IMG_CNT_SIG'] < substr_count(strtolower($_POST['reg_sig']), '[img]') ) {
		set_err('reg_sig', 'Stai cercando di utilizzare nella tua signature più immagini delle '.$GLOBALS['FORUM_IMG_CNT_SIG'].' consentite.');
	}

	/* Url Avatar check */
	if (!empty($_POST['reg_avatar_loc']) && !($GLOBALS['reg_avatar_loc_file'] = fetch_img($_POST['reg_avatar_loc'], $user_id))) {
		set_err('avatar', 'L&#39;URL che hai inserito non contiene un&#39;immagine valida');
	}

	/* Alias Check */
	if ($GLOBALS['FUD_OPT_2'] & 128 && isset($_POST['reg_alias'])) {
		if (($_POST['reg_alias'] = trim(sanitize_login($_POST['reg_alias'])))) {
			if (strlen($_POST['reg_alias']) > $GLOBALS['MAX_LOGIN_SHOW']) {
				$_POST['reg_alias'] = substr($_POST['reg_alias'], 0, $GLOBALS['MAX_LOGIN_SHOW']);
			}
			if (q_singleval("SELECT id FROM phpgw_fud_users WHERE alias='".addslashes(htmlspecialchars($_POST['reg_alias']))."' AND id!=".$user_id)) {
				set_err('reg_alias', 'Username già utilizzato');
			}
		}
	}

	if ($GLOBALS['FORUM_SIG_ML'] && strlen($_POST['reg_sig']) > $GLOBALS['FORUM_SIG_ML']) {
		set_err('reg_sig', 'Your signature exceeds the maximum allowed length of '.$GLOBALS['FORUM_SIG_ML'].' characters characters.');
	}

	return $GLOBALS['error'];
}

function fmt_year($val)
{
	if (!($val = (int)$val)) {
		return;
	}
	if ($val > 1000) {
		return $val;
	} else if ($val < 100 && $val > 10) {
		return (1900 + $val);
	} else if ($val < 10) {
		return (2000 + $val);
	}
}

function set_err($err_name, $err_msg)
{
	$GLOBALS['error'] = 1;
	if (isset($GLOBALS['err_msg'])) {
		array_push($GLOBALS['err_msg'], array($err_name => $err_msg));
	} else {
		$GLOBALS['err_msg'] = array($err_name => $err_msg);
	}
}

function draw_err($err_name)
{
	if (!isset($GLOBALS['err_msg'][$err_name])) {
		return;
	}
	return '<br /><font class="ErrorText">'.$GLOBALS['err_msg'][$err_name].'</font>';
}

function make_avatar_loc($path, $disk, $web)
{
	$img_info = @getimagesize($disk . $path);

	if ($img_info[2] < 4 && $img_info[2] > 0) {
		return '<img src="'.$web . $path.'" '.$img_info[3].' />';
	} else if ($img_info[2] == 4) {
		return '<embed src="'.$web . $path.'" '.$img_info[3].' />';
	} else {
		return '';
	}
}

function remove_old_avatar($avatar_str)
{
	if (preg_match('!images/custom_avatars/(([0-9]+)\.([A-Za-z]+))" width=!', $avatar_str, $tmp)) {
		@unlink($GLOBALS['WWW_ROOT_DISK'] . 'images/custom_avatars/' . basename($tmp[1]));
	}
}

function decode_uent(&$uent)
{
	reverse_fmt($uent->home_page);
	reverse_fmt($uent->bio);
	reverse_fmt($uent->interests);
	reverse_fmt($uent->occupation);
	reverse_fmt($uent->location);
	reverse_fmt($uent->user_image);
	$uent->aim = urldecode($uent->aim);
	$uent->yahoo = urldecode($uent->yahoo);
	$uent->msnm = urldecode($uent->msnm);
	$uent->jabber = urldecode($uent->jabber);
	$uent->affero = urldecode($uent->affero);
}

	if (isset($_GET['mod_id'])) {
		$mod_id = (int)$_GET['mod_id'];
	} else if (isset($_POST['mod_id'])) {
		$mod_id = (int)$_POST['mod_id'];
	} else {
		$mod_id = '';
	}

	/* allow the root to modify settings other lusers */
	if (_uid && $usr->users_opt & 1048576 && $mod_id) {
		if (!($uent =& usr_reg_get_full($mod_id))) {
			exit('Invalid User Id');
		}
		decode_uent($uent);
	} else {
		$uent =& usr_reg_get_full($usr->id);
		decode_uent($uent);
	}

	/* this is a hack, it essentially disables uploading of avatars when file_uploads are off */
	if (ini_get("file_uploads") != 1 || !($FUD_OPT_1 & 8)) {
		$register_enctype = '';
		$FUD_OPT_1 = $FUD_OPT_1 &~ 8;
	} else {
		$register_enctype = 'enctype="multipart/form-data"';
	}

	$avatar_tmp = $avatar_arr = null;
	/* deal with avatars, only done for regged users */
	if (!empty($_POST['avatar_tmp'])) {
		list($avatar_arr['file'], $avatar_arr['del'], $avatar_arr['leave']) = explode("\n", base64_decode($_POST['avatar_tmp']));
	}
	if (isset($_POST['btn_detach']) && isset($avatar_arr)) {
		$avatar_arr['del'] = 1;
	}
	if (!($FUD_OPT_1 & 8) && (!@file_exists($avatar_arr['file']) || empty($avatar_arr['leave']))) {
		/* hack attempt for URL avatar */
		$avatar_arr = null;
	} else if (($FUD_OPT_1 & 8) && isset($_FILES['avatar_upload']) && $_FILES['avatar_upload']['size'] > 0) { /* new upload */
		if ($_FILES['avatar_upload']['size'] >= $CUSTOM_AVATAR_MAX_SIZE) {
			set_err('avatar', 'Il file che stai cercando di spedite è troppo grande, oltre il limite di '.$GLOBALS['CUSTOM_AVATAR_MAX_SIZE'].' byte');
		} else {
			/* [user_id].[file_extension]_'random data' */
			$file_name = $uent->id . strrchr($_FILES['avatar_upload']['name'], '.') . '_';
			$tmp_name = safe_tmp_copy($_FILES['avatar_upload']['tmp_name'], 0, $file_name);

			if (!($img_info = @getimagesize($TMP . $tmp_name))) {
				set_err('avatar', 'L&#39;URL che hai inserito non contiene un&#39;immagine valida');
				unlink($TMP . $tmp_name);
			}

			list($max_w, $max_y) = explode('x', $CUSTOM_AVATAR_MAX_DIM);
			if ($img_info[2] > ($FUD_OPT_1 & 64 ? 4 : 3)) {
				set_err('avatar', 'L&#39;avatar che stai cercando di spedire non è ammesso, controlla i tipi di file supportati.');
				unlink($TMP . $tmp_name);
			} else if ($img_info[0] >$max_w || $img_info[1] >$max_y) {
				set_err('avatar', 'Avatar dimensions of <b>('.$img_info[0].'x'.$img_info[1].')</b> exceed the allowed dimensions of <b>('.$GLOBALS['CUSTOM_AVATAR_MAX_DIM'].')</b>.');
				unlink($TMP . $tmp_name);
			} else {
				/* remove old uploaded file, if one exists & is not in DB */
				if (empty($avatar_arr['leave']) && @file_exists($avatar_arr['file'])) {
					@unlink($TMP . $avatar_arr['file']);
				}

				$avatar_arr['file'] = $tmp_name;
				$avatar_arr['del'] = 0;
				$avatar_arr['leave'] = 0;
			}
		}
	}

	if (count($_POST)) {
		$new_users_opt = 0;
		foreach (array('display_email', 'notify', 'notify_method', 'ignore_admin', 'email_messages', 'pm_messages', 'pm_notify', 'default_view', 'gender', 'append_sig', 'show_sigs', 'show_avatars', 'show_im', 'invisible_mode') as $v) {
			if (!empty($_POST['reg_'.$v])) {
				$new_users_opt |= (int) $_POST['reg_'.$v];
			}
		}
		/* security check, prevent haxors from passing values that shouldn't */
		if (!($new_users_opt & (131072|65536|262144|524288|1048576|2097152|4194304|8388608|16777216))) {
			$uent->users_opt = ($uent->users_opt & (131072|65536|262144|524288|1048576|2097152|4194304|8388608|16777216)) | $new_users_opt;
		}
	}

	/* SUBMITTION CODE */
	if (isset($_POST['fud_submit']) && !isset($_POST['btn_detach']) && !isset($_POST['btn_upload']) && !register_form_check($uent->id)) {
		$old_email = $uent->email;
		$old_avatar_loc = $uent->avatar_loc;
		$old_avatar = $uent->avatar;

		/* import data from _POST into $uent object */
		$vars = array_keys(get_object_vars($uent));
		foreach ($vars as $v) {
			if (isset($_POST['reg_'.$v])) {
				$uent->{$v} = $_POST['reg_'.$v];
			}
		}

		$uent->bday = fmt_year($_POST['b_year']) . str_pad((int)$_POST['b_month'], 2, '0', STR_PAD_LEFT) . str_pad((int)$_POST['b_day'], 2, '0', STR_PAD_LEFT);
		$uent->sig = apply_custom_replace($uent->sig);
		if ($FUD_OPT_1 & 131072) {
			$uent->sig = tags_to_html($uent->sig, $FUD_OPT_1 & 524288);
		} else if ($FUD_OPT_1 & 65536) {
			$uent->sig = nl2br(htmlspecialchars($uent->sig));
		}

		if ($FUD_OPT_1 & 196608) {
			char_fix($uent->sig);
		}

		if ($FUD_OPT_1 & 262144) {
			$uent->sig = smiley_to_post($uent->sig);
		}
		fud_wordwrap($uent->sig);

		if ($uent->bio) {
			$uent->bio = htmlspecialchars($uent->bio);
			char_fix($uent->bio);
		}

		if (!$uent->icq && !($uent->users_opt & 4)) {
			$uent->users_opt |= 4;
		}

		/* Restore avatar values to their previous values */
		$uent->avatar = $old_avatar;
		$uent->avatar_loc = $old_avatar_loc;
		$old_opt = $uent->users_opt & (4194304|16777216|8388608);
		$uent->users_opt |= 4194304|16777216|8388608;

		/* prevent non-confirmed users from playing with avatars, yes we are that cruel */
		if ($FUD_OPT_1 & 28 && _uid) {
			if ($_POST['avatar_type'] == 'b') { /* built-in avatar */
				if (!$old_avatar && $old_avatar_loc) {
					remove_old_avatar($old_avatar_loc);
					$uent->avatar_loc = '';
				} else if (isset($avatar_arr['file'])) {
					@unlink($TMP . basename($avatar_arr['file']));
				}
				if ($_POST['reg_avatar'] == '0') {
					$uent->avatar_loc = '';
					$uent->avatar = 0;
				} else if ($uent->avatar != $_POST['reg_avatar'] && ($img = q_singleval('SELECT img FROM phpgw_fud_avatar WHERE id='.(int)$_POST['reg_avatar']))) {
					/* verify that the avatar exists and it is different from the one in DB */
					$uent->avatar_loc = make_avatar_loc('images/avatars/' . $img, $WWW_ROOT_DISK, $WWW_ROOT);
					$uent->avatar = $_POST['reg_avatar'];
				}
				if ($uent->avatar && $uent->avatar_loc) {
					$uent->users_opt ^= 4194304|16777216;
				}
			} else {
				if ($_POST['avatar_type'] == 'c' && isset($reg_avatar_loc_file)) { /* New URL avatar */
					$common_av_name = $reg_avatar_loc_file;

					if (!empty($avatar_arr['file'])) {
						$avatar_arr['del'] = 1;
					}
				} else if ($_POST['avatar_type'] == 'u' && empty($avatar_arr['del']) && empty($avatar_arr['leave'])) { /* uploaded file */
					$common_av_name = $avatar_arr['file'];
				}

				/* remove old avatar if need be */
				if (!empty($avatar_arr['del'])) {
					if (empty($avatar_arr['leave'])) {
						@unlink($TMP . basename($avatar_arr['file']));
					} else {
						remove_old_avatar($old_avatar_loc);
					}
				}

				/* add new avatar if needed */
				if (isset($common_av_name)) {
					$common_av_name = basename($common_av_name);
					$av_path = 'images/custom_avatars/' . substr($common_av_name, 0, strpos($common_av_name, '_'));
					copy($TMP . basename($common_av_name), $WWW_ROOT_DISK . $av_path);
					@unlink($TMP . basename($common_av_name));
					if (($uent->avatar_loc = make_avatar_loc($av_path, $WWW_ROOT_DISK, $WWW_ROOT))) {
						if (!($FUD_OPT_1 & 32) || $uent->users_opt & 1048576) {
							$uent->users_opt ^= 16777216|4194304;
						} else {
							$uent->users_opt ^= 8388608|4194304;
						}
					}
				} else if (empty($avatar_arr['leave']) || !empty($avatar_arr['del'])) {
					$uent->avatar_loc = '';
				} else if (!empty($avatar_arr['leave'])) {
					$uent->users_opt ^= (8388608|16777216|4194304) ^ $old_opt;
				}
				$uent->avatar = 0;
			}
			if (empty($uent->avatar_loc)) {
				$uent->users_opt ^= 8388608|16777216;
			}
		} else {
			$uent->users_opt ^= (8388608|16777216|4194304) ^ $old_opt;
		}

		$uent->sync_user();

		if (!$mod_id) {
			check_return($usr->returnto);
		} else {
			header('Location: adm/admuser.php?usr_id='.$uent->id.'&'._rsidl.'&act=nada');
			exit;
		}
	}

	/* populate form variables based on user's profile */
	if (!isset($_POST['prev_loaded'])) {
		foreach ($uent as $k => $v) {
			${'reg_'.$k} = htmlspecialchars($v);
		}
		reverse_fmt($reg_sig);
		$reg_sig = apply_reverse_replace($reg_sig);

		if ($FUD_OPT_1 & 262144) {
			$reg_sig = post_to_smiley($reg_sig);
		}

		if ($FUD_OPT_1 & 131072) {
			$reg_sig = html_to_tags($reg_sig);
		} else if ($FUD_OPT_1 & 65536) {
			reverse_nl2br($reg_sig);
		}

		if ($FUD_OPT_1 & 196608) {
			char_fix($reg_sig);
		}
		char_fix($reg_bio);

		if ($uent->bday) {
			$b_year = substr($uent->bday, 0, 4);
			$b_month = substr($uent->bday, 4, 2);
			$b_day = substr($uent->bday, 6, 8);
		} else {
			$b_year = $b_month = $b_day = '';
		}
		if (!$reg_avatar && $reg_avatar_loc) { /* custom avatar */
			reverse_fmt($reg_avatar_loc);
			if (preg_match('!src="([^"]+)" width="!', $reg_avatar_loc, $tmp)) {
				$avatar_arr['file'] = $tmp[1];
				$avatar_arr['del'] = 0;
				$avatar_arr['leave'] = 1;
				$avatar_type = 'u';
			}
		}
		reverse_fmt($reg_alias);
	} else if (isset($_POST['prev_loaded'])) { /* import data from POST data */
		foreach ($_POST as $k => $v) {
			if (!strncmp($k, 'reg_', 4)) {
				${$k} = htmlspecialchars($v);
			}
		}
		char_fix($reg_bio);
		char_fix($reg_sig);

		$b_year = $_POST['b_year'];
		$b_month = $_POST['b_month'];
		$b_day = $_POST['b_day'];
		if (isset($_POST['avatar_type'])) {
			$avatar_type = $_POST['avatar_type'];
		}
	}

	if (empty($reg_time_zone)) {
		$reg_time_zone = $SERVER_TZ;
	}

	if (!$mod_id) {
		ses_update_status($usr->sid, 'Guarda il suo profilo', 0, 0);
	}

	$TITLE_EXTRA = ': Pagina di registrazione';

$tabs = '';
if (_uid) {
	$tablist = array(
'Impostazioni'=>'register',
'Iscrizioni'=>'subscribed',
'Referrals'=>'referals',
'Buddy List'=>'buddy_list',
'Ignore List'=>'ignore_list'
);
	if (isset($_POST['mod_id'])) {
		$mod_id_chk = $_POST['mod_id'];
	} else if (isset($_GET['mod_id'])) {
		$mod_id_chk = $_GET['mod_id'];
	} else {
		$mod_id_chk = null;
	}

	if (!$mod_id_chk) {
		if ($FUD_OPT_1 & 1024) {
			$tablist['Messaggi privati'] = 'pmsg';
		}
		$pg = ($_GET['t'] == 'pmsg_view' || $_GET['t'] == 'ppost') ? 'pmsg' : $_GET['t'];

		foreach($tablist as $tab_name => $tab) {
			$tab_url = '/egroupware/fudforum/3814588639/index.php?t='.$tab.'&amp;'._rsid;
			if ($tab == 'referals') {
				if (!($FUD_OPT_2 & 8192)) {
					continue;
				}
				$tab_url .= '&amp;id='._uid;
			}
			$tabs .= $pg == $tab ? '<td class="tabA"><div class="tabT"><a href="'.$tab_url.'">'.$tab_name.'</a></div></td>' : '<td class="tabI"><div class="tabT"><a href="'.$tab_url.'">'.$tab_name.'</a></div></td>';
		}

		$tabs = '<table border=0 cellspacing=1 cellpadding=0 class="tab">
<tr class="tab">'.$tabs.'</tr>
</table>';
	}
}

	$reg_sig_err	= draw_err('reg_sig');

	if ($FUD_OPT_2 & 2048) {
		$affero_domain = parse_url($WWW_ROOT);
		$register_affero = '<tr><td class="RowStyleA">Affero Username:<br /><font class="SmallText">If you have an <a href="http://www.affero.com/ca/'.urlencode($GLOBALS['FORUM_TITLE']).'" target="_blank">Affero</a> username enter it here.</font></td><td class="RowStyleA"><input type="text" name="reg_affero" value="'.$reg_affero.'" maxLength=32 size=25></td></tr>';
	} else {
		$register_affero = '';
	}

	/* Initialize avatar options */
	$avatar = $avatar_type_sel = '';

	$reg_time_limit_err = '';

	$avatar_err = draw_err('avatar');

	$submit_button = '<input type="submit" class="button" name="fud_submit" value="Aggiorna">';

	if ($FUD_OPT_1 & 28 && _uid) {
		if ($FUD_OPT_1 == 28) {
			/* if there are no built-in avatars, don't show them */
			if (q_singleval('SELECT count(*) FROM phpgw_fud_avatar')) {
				$sel_opt = "Built-in\nSpecifica URL\nCarica avatar";
				$a_type='b';
				$sel_val = "b\nc\nu";
			} else {
				$sel_opt = "Specifica URL\nCarica avatar";
				$a_type='u';
				$sel_val = "c\nu";
			}
		} else {
			$sel_opt = $sel_val = '';

			if (q_singleval('SELECT count(*) FROM phpgw_fud_avatar') && $FUD_OPT_1 & 16) {
				$sel_opt .= "Built-in\n";
				$a_type = 'b';
				$sel_val .= "b\n";
			}
			if ($FUD_OPT_1 & 8) {
				$sel_opt .= "Carica avatar\n";
				if (!isset($a_type)) {
					$a_type = 'u';
				}
				$sel_val .= "u\n";
			}
			if ($FUD_OPT_1 & 4) {
				$sel_opt .= "Specifica URL\n";
				if (!isset($a_type)) {
					$a_type = 'c';
				}
				$sel_val .= "c\n";
			}
			$sel_opt = trim($sel_opt);
			$sel_val = trim($sel_val);
		}
		if (isset($a_type)) { /* rare condition, no built-in avatars & no other avatars are allowed */
			if (!isset($avatar_type)) {
				$avatar_type = $a_type;
			}
			$avatar_type_sel_options = tmpl_draw_select_opt($sel_val, $sel_opt, $avatar_type, '', '');
			$avatar_type_sel = '<tr valign="top"><td class="RowStyleA">Avatar:</td><td class="RowStyleA"><select name="avatar_type" onChange="javascript: document.fud_register.submit();">'.$avatar_type_sel_options.'</select></td></tr>';

			/* preview image */
			if (isset($_POST['prev_loaded'])) {
				if ((!empty($_POST['reg_avatar']) && $_POST['reg_avatar'] == $uent->avatar) || (!empty($avatar_arr['file']) && empty($avatar_arr['del']) && $avatar_arr['leave'])) {
					$custom_avatar_preview = $uent->avatar_loc;
				} else if (!empty($_POST['reg_avatar']) && ($im = q_singleval('SELECT img FROM phpgw_fud_avatar WHERE id='.(int)$_POST['reg_avatar']))) {
					$custom_avatar_preview = make_avatar_loc('images/avatars/' . $im, $WWW_ROOT_DISK, $WWW_ROOT);
				} else {
					if (isset($reg_avatar_loc_file)) {
						$common_name = $reg_avatar_loc_file;
					} else if (!empty($avatar_arr['file']) && empty($avatar_arr['del'])) {
						$common_name = $avatar_arr['file'];
					}
					if (isset($common_name)) {
						$custom_avatar_preview = make_avatar_loc(basename($common_name), $TMP, '/egroupware/fudforum/3814588639/index.php?t=tmp_view&img=');
					}
				}
			} else if ($uent->avatar_loc) {
				$custom_avatar_preview = $uent->avatar_loc;
			}

			if (!isset($custom_avatar_preview)) {
				$custom_avatar_preview = '<img src="blank.gif" />';
			}

			/* determine the avatar specification field to show */
			if ($avatar_type == 'b') {
				if (empty($reg_avatar)) {
					$reg_avatar = '0';
					$reg_avatar_img = 'blank.gif';
				} else if (!empty($reg_avatar_loc)) {
					reverse_fmt($reg_avatar_loc);
					preg_match('!images/avatars/([^"]+)"!', $reg_avatar_loc, $tmp);
					$reg_avatar_img = 'images/avatars/' . $tmp[1];
				} else {
					$reg_avatar_img = 'images/avatars/' . q_singleval('SELECT img FROM phpgw_fud_avatar WHERE id='.(int)$reg_avatar);
				}
				$del_built_in_avatar = $reg_avatar ? '[<a href="javascript: return false;" onClick="document.reg_avatar_img.src=\'blank.gif\'; document.fud_register.reg_avatar.value=\'0\';" class="GenLink">Cancella</a>]' : '';
				$avatar = '<tr valign="top"><td class="RowStyleA">Avatar:</td><td class="RowStyleA"><img src="'.$reg_avatar_img.'" name="reg_avatar_img" alt="" />
<input type="hidden" name="reg_avatar" value="'.$reg_avatar.'">[<a class="GenLink" href="javascript: window_open(\'/egroupware/fudforum/3814588639/index.php?t=avatarsel&amp;'._rsid.'\', \'avtsel\', 400, 300);">Seleziona avatar</a>]
'.$del_built_in_avatar.'<br /></td></tr>';
			} else if ($avatar_type == 'c') {
				if (!isset($reg_avatar_loc)) {
					$reg_avatar_loc = '';
				}
				$avatar = '<tr valign="top"><td class="RowStyleC" colspan=2>L&#39;avatar personalizzato non apparirà fino a quando non sarà approvato dall&#39;amministratore del forum.<br><font class="SmallText">L&#39;immagine dell&#39;avatar non dovrebbe essere più grande di <b>'.$GLOBALS['CUSTOM_AVATAR_MAX_DIM'].' pixel</b> e deve essere in formato <b>jpg</b>, <b>gif</b> o <b>png</b>.</font></td></tr>
<tr valign="top"><td class="RowStyleA">URL dell&#39;avatar personalizzato: '.$avatar_err.'</td><td class="RowStyleA"><input type="text" value="'.$reg_avatar_loc.'" name="reg_avatar_loc"></td></tr>';
			} else if ($avatar_type == 'u') {
				$avatar_tmp = $avatar_arr ? base64_encode($avatar_arr['file'] . "\n" . $avatar_arr['del'] . "\n" . $avatar_arr['leave']) : '';
				$buttons = (!empty($avatar_arr['file']) && empty($avatar_arr['del'])) ? '&nbsp;<input type="submit" class="button" name="btn_detach" value="Cancella">' : '<input type="file" name="avatar_upload"> <input type="submit" class="button" name="btn_upload" value="Anteprima">';
				$avatar = '<tr valign="top"><td class="RowStyleC" colspan=2>L&#39;avatar personalizzato non apparirà fino a quando non sarà approvato dall&#39;amministratore del forum.<br><font class="SmallText">L&#39;immagine dell&#39;avatar non dovrebbe essere più grande di <b>'.$GLOBALS['CUSTOM_AVATAR_MAX_DIM'].' pixel</b> e deve essere in formato <b>jpg</b>, <b>gif</b> o <b>png</b>.</font></td></tr>
<tr valign="top"><td class="RowStyleA">File dell&#39;avatar personalizzato: '.$avatar_err.'</td><td class="RowStyleA"><table border=0 cellspacing=0 cellpadding=0><tr><td>'.$custom_avatar_preview.'</td><td>'.$buttons.'</td></tr></table></td></tr> 
<input type="hidden" name="avatar_tmp" value="'.$avatar_tmp.'">';
			}
		}
	}

	$post_options = tmpl_post_options('sig');

	$theme_select = create_theme_select('reg_theme', $reg_theme);

	$day_select		= tmpl_draw_select_opt("\n1\n2\n3\n4\n5\n6\n7\n8\n9\n10\n11\n12\n13\n14\n15\n16\n17\n18\n19\n20\n21\n22\n23\n24\n25\n26\n27\n28\n29\n30\n31", "\n1\n2\n3\n4\n5\n6\n7\n8\n9\n10\n11\n12\n13\n14\n15\n16\n17\n18\n19\n20\n21\n22\n23\n24\n25\n26\n27\n28\n29\n30\n31", $b_day, '', '');
	$month_select		= tmpl_draw_select_opt("\n1\n2\n3\n4\n5\n6\n7\n8\n9\n10\n11\n12", "\nGennaio\nFebbraio\nMarzo\nAprile\nMaggio\nGiugno\nLuglio\nAgosto\nSettembre\nOttobre\nNovembre\nDicembre", $b_month, '', '');
	$gender_select		= tmpl_draw_select_opt("512\n1024\n0","NON SPECIFICATO\nMaschio\nFemmina", ($uent->users_opt & 512 ? 512 : ($uent->users_opt & 1024)), '', '');
	$mppg_select		= tmpl_draw_select_opt("0\n5\n10\n20\n30\n40", "Usa le impostazioni standard del forum\n5\n10\n20\n30\n40", $reg_posts_ppg, '', '');
	$view_select		= tmpl_draw_select_opt("384\n128".($FUD_OPT_2 & 512 ?"\n256\n0":''), "Vista piatta\nFlat thread listing/Tree message listing".($FUD_OPT_2 & 512 ? "\nTree thread listing/Flat message listing\nVista ad albero":''), ($uent->users_opt & (128|256)), '', '');
	$timezone_select	= tmpl_draw_select_opt($tz_values, $tz_names, $reg_time_zone, '', '');
	$notification_select	= tmpl_draw_select_opt("4\n0", "Email\nICQ", ($uent->users_opt & 4), '', '');

	$ignore_admin_radio	= tmpl_draw_radio_opt('reg_ignore_admin', "8\n0", "Sì\nNo", ($uent->users_opt & 8), '', '', '&nbsp;&nbsp;');
	$invisible_mode_radio	= tmpl_draw_radio_opt('reg_invisible_mode', "32768\n0", "Sì\nNo", ($uent->users_opt & 32768), '', '', '&nbsp;&nbsp;');
	$show_email_radio	= tmpl_draw_radio_opt('reg_display_email', "1\n0", "Sì\nNo", ($uent->users_opt & 1), '', '', '&nbsp;&nbsp;');
	$notify_default_radio	= tmpl_draw_radio_opt('reg_notify', "2\n0", "Sì\nNo", ($uent->users_opt & 2), '', '', '&nbsp;&nbsp;');
	$pm_notify_default_radio= tmpl_draw_radio_opt('reg_pm_notify', "64\n0", "Sì\nNo", ($uent->users_opt & 64), '', '', '&nbsp;&nbsp;');
	$accept_user_email	= tmpl_draw_radio_opt('reg_email_messages', "16\n0", "Sì\nNo", ($uent->users_opt & 16), '', '', '&nbsp;&nbsp;');
	$accept_pm		= tmpl_draw_radio_opt('reg_pm_messages', "32\n0", "Sì\nNo", ($uent->users_opt & 32), '', '', '&nbsp;&nbsp;');
	$show_sig_radio		= tmpl_draw_radio_opt('reg_show_sigs', "4096\n0", "Sì\nNo", ($uent->users_opt & 4096), '', '', '&nbsp;&nbsp;');
	$show_avatar_radio	= tmpl_draw_radio_opt('reg_show_avatars', "8192\n0", "Sì\nNo", ($uent->users_opt & 8192), '', '', '&nbsp;&nbsp;');
	$show_im_radio		= tmpl_draw_radio_opt('reg_show_im', "16384\n0", "Sì\nNo", ($uent->users_opt & 16384), '', '', '&nbsp;&nbsp;');
	$append_sig_radio	= tmpl_draw_radio_opt('reg_append_sig', "2048\n0", "Sì\nNo", ($uent->users_opt & 2048), '', '', '&nbsp;&nbsp;');

	$reg_user_image_field = $FUD_OPT_2 & 65536 ? '<tr><td class="RowStyleA">Immagine:</td><td class="RowStyleA"><input type="text" name="reg_user_image" value="'.$reg_user_image.'"maxlength=255 size=30></td></tr>' : '';
	$sig_len_limit = $FORUM_SIG_ML ? '<b>Maximum Length: </b>'.$GLOBALS['FORUM_SIG_ML'].' characters <a href="javascript: alert(\'Your Signature is \'+document.fud_register.reg_sig.value.length+\' characters long. The maximum allowed signature length is '.$GLOBALS['FORUM_SIG_ML'].'\');" class="SmallText">Check Signature Length</a>' : '';

if ($FUD_OPT_2 & 2) {
	$page_gen_end = gettimeofday();
	$page_gen_time = sprintf('%.5f', ($page_gen_end['sec'] - $PAGE_TIME['sec'] + (($page_gen_end['usec'] - $PAGE_TIME['usec'])/1000000)));
	$page_stats = '<br /><div align="left" class="SmallText">Tempo totale richiesto per generare la pagina: '.$page_gen_time.' secondi</div>';
} else {
	$page_stats = '';
}
?>
<?php echo $GLOBALS['fud_egw_hdr']; ?>
<table width="100%" border="0" cellspacing="3" cellpadding="5"><tr><td class="ForumBackground">
<?php echo $tabs; ?>
<form method="post" action="/egroupware/fudforum/3814588639/index.php?t=register" name="fud_register" <?php echo $register_enctype; ?>>
<table border="0" cellspacing="1" cellpadding="2" class="ContentTable">
<tr><td class="RowStyleA">Località:</td><td class="RowStyleA"><input type="text" name="reg_location" value="<?php echo $reg_location; ?>"maxlength=255 size=30></td></tr>
<tr><td class="RowStyleA">Occupazione:</td><td class="RowStyleA"><input type="text" name="reg_occupation" value="<?php echo $reg_occupation; ?>"maxlength=255 size=30></td></tr>
<tr><td class="RowStyleA">Interessi:</td><td class="RowStyleA"><input type="text" name="reg_interests" value="<?php echo $reg_interests; ?>"maxlength=255 size=30></td></tr>
<?php echo $reg_user_image_field; ?>
<tr><td class="RowStyleA">ICQ</td><td class="RowStyleA"><input type="text" name="reg_icq" value="<?php echo $reg_icq; ?>" maxLength=32 size=25></td></tr>
<tr><td class="RowStyleA">AIM Handle:</td><td class="RowStyleA"><input type="text" name="reg_aim" value="<?php echo $reg_aim; ?>" maxLength=32 size=25></td></tr>
<tr><td class="RowStyleA">Yahoo Messenger:</td><td class="RowStyleA"><input type="text" name="reg_yahoo" value="<?php echo $reg_yahoo; ?>" maxLength=32 size=25></td></tr>
<tr><td class="RowStyleA">MSN Messenger:</td><td class="RowStyleA"><input type="text" name="reg_msnm" value="<?php echo $reg_msnm; ?>" maxLength=32 size=25></td></tr>
<tr><td class="RowStyleA">Jabber Handle:</td><td class="RowStyleA"><input type="text" name="reg_jabber" value="<?php echo $reg_jabber; ?>" maxLength=32 size=25></td></tr>
<?php echo $register_affero; ?>
<tr><td class="RowStyleA">Homepage:</td><td class="RowStyleA"><input type="text" name="reg_home_page" value="<?php echo $reg_home_page; ?>" maxLength=255></td></tr>
<?php echo $avatar_type_sel; ?>
<?php echo $avatar; ?>
<tr valign="top"><td class="RowStyleA">Data di nascita:<br /><font class="SmallText">Se decidi di rendere nota la tua data di nascita, gli altri utenti registrati del forum potranno vederla</font></td>
<td class="RowStyleA">
<table border=0 cellspacing=3 cellpadding=0>
 <tr class="GenText">
  <td align="center">Mese</td>
  <td align="center">Giorno</td>
  <td align="center">Anno</td>
 </tr>
 <tr>
  <td align="center"><select name="b_month"><?php echo $month_select; ?></select></td>
  <td align="center"><select name="b_day"><?php echo $day_select; ?></select></td>
  <td align="center"><input type="text" name="b_year" value="<?php echo $b_year; ?>" maxLength=4 size=5></td>
 </tr>
</table></td></tr>
<tr><td class="RowStyleA">Sesso:</td><td class="RowStyleA"><select name="reg_gender"><?php echo $gender_select; ?></select></td></tr>
<tr><td class="RowStyleA" valign="top">Bio:<br /><font class="SmallText">Alcune informazioni su te stesso, come i tuoi interessi, il tuo lavoro, ecc.</font></td><td class="RowStyleA"><textarea name="reg_bio" rows=5 cols=35><?php echo $reg_bio; ?></textarea></td></tr>
<tr><th colspan=2>Opzioni</th></tr>
<tr><td valign="top" class="RowStyleA">Signature:<br /><font class="SmallText">Signature aggiuntiva che puoi far comparire in fondo ai tuoi messaggi.<br /></font><?php echo $post_options.$sig_len_limit; ?></td><td class="RowStyleA"><?php echo $reg_sig_err; ?><textarea name="reg_sig" rows=8 cols=50><?php echo $reg_sig; ?></textarea></td></tr>
<tr><td class="RowStyleA">Fuso orario:</td><td class="RowStyleA"><select name="reg_time_zone" class="SmallText"><?php echo $timezone_select; ?></select></td></tr>
<tr><td class="RowStyleA">Ignora le comunicazioni dell&#39;amministratore del forum:</td><td class="RowStyleA"><?php echo $ignore_admin_radio; ?></td></tr>
<tr><td class="RowStyleA">Modalità invisibile:<br /><font class="SmallText">Nasconde il tuo status online.</font></td><td class="RowStyleA"><?php echo $invisible_mode_radio; ?></td></tr>
<tr><td class="RowStyleA">Mostra indirizzo email:<br /><font class="SmallText">Seleziona questa opzione se vuoi che il tuo indirizzo email venga reso visibile agli altri utenti.</font></td><td class="RowStyleA"><?php echo $show_email_radio; ?></td></tr>
<tr><td class="RowStyleA">Seleziona la notifica di default:<br /><font class="SmallText">Se la notifica è abilitata di default, può essere disabilitata durante la scrittura di un messaggio.</font></td><td class="RowStyleA"><?php echo $notify_default_radio; ?></td></tr>
<tr><td class="RowStyleA">Private Message Notification<br /><font class="SmallText">If enabled you will be notified the via your notification method of choice whenever a private message is sent to you.</font></td><td class="RowStyleA"><?php echo $pm_notify_default_radio; ?></td></tr>
<tr><td class="RowStyleA">Seleziona il metodo di notifica:<br /><font class="SmallText">Se desideri ricevere le notifiche via ICQ assicurati di abilitare &#39;EmailExpress&#39; nelle impostazioni di ICQ</font></td><td class="RowStyleA"><select name="reg_notify_method" onChange="javascript: re=/[^0-9]/g; a=document.fud_register.reg_icq.value.replace(re, ''); if(this.value=='ICQ' && !a.length ) { alert('Hai selezionato la notifica via ICQ, ma non hai specificato un numero di ICQ. Imposto come default Email&#46;'); this.value='EMAIL'; }"><?php echo $notification_select; ?></select></td></tr>
<tr><td class="RowStyleA">Consenti messaggi email:<br /><font class="SmallText">Consenti ad altri utenti di inviarti messaggi email tramite il forum (questo non renderà pubblico il tuo indirizzo).</font></td><td class="RowStyleA"><?php echo $accept_user_email; ?></td></tr>
<tr><td class="RowStyleA">Messaggi Privati<br /><font class="SmallText">Abilita la messaggistica privata</font></td><td class="RowStyleA"><?php echo $accept_pm; ?></td></tr>
<tr><td class="RowStyleA">Inserisci la signature di default:<br /><font class="SmallText">Aggiunge automaticamente la signature ad ogni messaggio che posti</font></td><td class="RowStyleA"><?php echo $append_sig_radio; ?></td></tr>
<tr><td class="RowStyleA">Mostra le signature:<br /><font class="SmallText">Ti permette di mostrare o nascondere le signature degli altri utenti registrati del forum</font></td><td class="RowStyleA"><?php echo $show_sig_radio; ?></td></tr>
<tr><td class="RowStyleA">Mostra gli avatar:<br /><font class="SmallText">Ti permette di non visualizzare gli avatar di altri utenti quando leggi i loro messaggi</font></td><td class="RowStyleA"><?php echo $show_avatar_radio; ?></td></tr>
<tr><td class="RowStyleA">Show IM indicators<br /><font class="SmallText">Whether or not to show IM indicators of the author beside their messages.</font></td><td class="RowStyleA"><?php echo $show_im_radio; ?></td></tr>
<tr><td class="RowStyleA">Numero di messaggi per pagina:</td><td class="RowStyleA"><select name="reg_posts_ppg"><?php echo $mppg_select; ?></select></td></tr>
<tr><td class="RowStyleA">Visualizzazione predefinita dei topic:</td><td class="RowStyleA"><select name="reg_default_view"><?php echo $view_select; ?></select></td></tr>
<tr><td class="RowStyleA">Theme:</td><td class="RowStyleA"><?php echo $theme_select; ?></td></tr>
<tr class="RowStyleC"><td colspan=2 align="center"><?php echo $submit_button; ?>&nbsp;<INPUT TYPE="reset" class="button" NAME="Reset" VALUE="Cancella"></td></tr>
</table>
<?php echo _hs; ?>
<input type="hidden" name="prev_loaded" value="1">
<input type="hidden" name="mod_id" value="<?php echo $mod_id; ?>">
</form>
<?php echo $page_stats; ?>
</td></tr></table>
<table width="100%" border="0" cellspacing="3" cellpadding="5"><tr><td class="ForumBackground" align="center">
<span class="SmallText">Powered by: FUDforum <?php echo $GLOBALS['FORUM_VERSION']; ?><br />Copyright &copy;2001-2003 <a href="http://fud.prohost.org/" class="GenLink">Advanced Internet Designs Inc.</a></span>
</td></tr></table>
<?php echo $GLOBALS['phpgw']->common->phpgw_footer(); ?>