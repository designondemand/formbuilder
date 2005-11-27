<?php
// Feedback Form. 02/2005 SjG <feedbackform_cmsmodule@fogbound.net>
// A Module for CMS Made Simple, (c)2005 by Ted Kulp (wishy@cmsmadesimple.org)
// This project's homepage is: http://www.cmsmadesimple.org

class ffCountryInput extends ffInput {

	function ffCountryInput(&$mod_globals, $formRef, $params=array())
	{
        $this->ffInput($mod_globals, $formRef, $params);
		$this->Type = 'CountryInput';
		$this->DisplayType = $this->mod_globals->Lang('field_type_country');
		$this->addListParam('select', 'selectname', 'selectvalue', $params);
        if (ffUtilityFunctions::def($params['default']))
            {
            $this->AddOption('default','default',$params['default']);
            }
        if (ffUtilityFunctions::def($params['defaultsel']))
            {
            $this->AddOption('defaultsel','defaultsel',$params['defaultsel']);
            }
		$this->ValidationTypes = array(
            $this->mod_globals->Lang('validation_none')=>'none',
            $this->mod_globals->Lang('validation_option_selected')=>'selected'
            );
        $this->Countries = array('Afghanistan'=>'AF','Aland Islands'=>'AX','Albania'=>'AL',
'Algeria'=>'DZ','American Samoa'=>'AS','Andorra'=>'AD','Angola'=>'AO','Anguilla'=>'AI','Antarctica'=>'AQ',
'Antigua and Barbuda'=>'AG','Argentina'=>'AR','Armenia'=>'AM','Aruba'=>'AW','Australia'=>'AU','Austria'=>'AT',
'Azerbaijan'=>'AZ','Bahamas'=>'BS','Bahrain'=>'BH','Barbados'=>'BB','Bangladesh'=>'BD','Belarus'=>'BY',
'Belgium'=>'BE','Belize'=>'BZ','Benin'=>'BJ','Bermuda'=>'BM','Bahamas'=>'BS','Bhutan'=>'BT',
'Botswana'=>'BW','Bolivia'=>'BO','Bosnia and Herzegovina'=>'BA','Bouvet Island'=>'BV',
'Brazil'=>'BR','British Indian Ocean Territory'=>'IO','Brunei Darussalam'=>'BN','Bulgaria'=>'BG','Burkina Faso'=>'BF',
'Burundi'=>'BI','Cambodia'=>'KH','Cameroon'=>'CM','Canada'=>'CA','Cape Verde'=>'CV','Cayman Islands'=>'KY',
'Central African Republic'=>'CF','Chad'=>'TD','Chile'=>'CL','China'=>'CN','Christmas Island'=>'CX',
'Cocos (Keeling) Islands'=>'CC','Colombia'=>'CO','Comoros'=>'KM','Congo'=>'CG','Congo, Democratic Republic'=>'CD',
'Cook Islands'=>'CK','Costa Rica'=>'CR','Cote D\'Ivoire (Ivory Coast)'=>'CI','Croatia (Hrvatska)'=>'HR',
'Cuba'=>'CU','Cyprus'=>'CY','Czech Republic'=>'CZ','Denmark'=>'DK','Djibouti'=>'DJ','Dominica'=>'DM',
'Dominican Republic'=>'DO','East Timor'=>'TP','Ecuador'=>'EC','Egypt'=>'EG','El Salvador'=>'SV',
'Equatorial Guinea'=>'GQ','Eritrea'=>'ER','Estonia'=>'EE','Ethiopia'=>'ET','Falkland Islands (Malvinas)'=>'FK',
'Faroe Islands'=>'FO','Fiji'=>'FJ','Finland'=>'FI','France'=>'FR','France, Metropolitan'=>'FX',
'French Guiana'=>'GF','French Polynesia'=>'PF','French Southern Territories'=>'TF',
'F.Y.R.O.M. (Macedonia)'=>'MK','Gabon'=>'GA','Gambia'=>'GM','Georgia'=>'GE','Germany'=>'DE',
'Ghana'=>'GH','Gibraltar'=>'GI','Great Britain (UK)'=>'GB','Greece'=>'GR','Greenland'=>'GL',
'Grenada'=>'GD',
'Guadeloupe'=>'GP',
'Guam'=>'GU',
'Guatemala'=>'GT',
'Guernsey'=>'GF',
'Guinea'=>'GN',
'Guinea-Bissau'=>'GW',
'Guyana'=>'GY',
'Haiti'=>'HT',
'Heard and McDonald Islands'=>'HM',
'Honduras'=>'HN',
'Hong Kong'=>'HK',
'Hungary'=>'HU',
'Iceland'=>'IS',
'India'=>'IN',
'Indonesia'=>'ID',
'Iran'=>'IR',
'Iraq'=>'IQ',
'Ireland'=>'IE',
'Israel'=>'IL',
'Isle of Man'=>'IM',
'Italy'=>'IT',
'Jersey'=>'JE',
'Jamaica'=>'JM',
'Japan'=>'JP',
'Jordan'=>'JO',
'Kazakhstan'=>'KZ',
'Kenya'=>'KE',
'Kiribati'=>'KI',
'Korea (North)'=>'KP',
'Korea (South)'=>'KR',
'Kuwait'=>'KW',
'Kyrgyzstan'=>'KG',
'Laos'=>'LA',
'Latvia'=>'LV',
'Lebanon'=>'LB',
'Liechtenstein'=>'LI',
'Liberia'=>'LR',
'Libya'=>'LY',
'Lesotho'=>'LS',
'Lithuania'=>'LT',
'Luxembourg'=>'LU',
'Macau'=>'MO',
'Madagascar'=>'MG',
'Malawi'=>'MW',
'Malaysia'=>'MY',
'Maldives'=>'MV',
'Mali'=>'ML',
'Malta'=>'MT',
'Marshall Islands'=>'MH',
'Martinique'=>'MQ',
'Mauritania'=>'MR',
'Mauritius'=>'MU',
'Mayotte'=>'YT',
'Mexico'=>'MX',
'Micronesia'=>'FM',
'Monaco'=>'MC',
'Moldova'=>'MD',
'Morocco'=>'MA',
'Mongolia'=>'MN',
'Montserrat'=>'MS',
'Mozambique'=>'MZ',
'Myanmar'=>'MM',
'Namibia'=>'NA',
'Nauru'=>'NR',
'Nepal'=>'NP',
'Netherlands'=>'NL',
'Netherlands Antilles'=>'AN',
'Neutral Zone'=>'NT',
'New Caledonia'=>'NC',
'New Zealand (Aotearoa)'=>'NZ',
'Nicaragua'=>'NI',
'Niger'=>'NE',
'Nigeria'=>'NG',
'Niue'=>'NU',
'Norfolk Island'=>'NF',
'Northern Mariana Islands'=>'MP',
'Norway'=>'NO',
'Oman'=>'OM',
'Pakistan'=>'PK',
'Palau'=>'PW',
'Palestinian Territory'=>'PS',
'Panama'=>'PA',
'Papua New Guinea'=>'PG',
'Paraguay'=>'PY',
'Peru'=>'PE',
'Philippines'=>'PH',
'Pitcairn'=>'PN',
'Poland'=>'PL',
'Portugal'=>'PT',
'Puerto Rico'=>'PR',
'Qatar'=>'QA',
'Reunion'=>'RE',
'Romania'=>'RO',
'Russian Federation'=>'RU',
'Rwanda'=>'RW',
'S. Georgia and S. Sandwich Isls.'=>'GS',
'Saint Kitts and Nevis'=>'KN',
'Saint Lucia'=>'LC',
'Saint Vincent &amp; the Grenadines'=>'VC',
'Samoa'=>'WS',
'San Marino'=>'SM',
'Sao Tome and Principe'=>'ST',
'Saudi Arabia'=>'SA',
'Senegal'=>'SN',
'Seychelles'=>'SC',
'Sierra Leone'=>'SL',
'Singapore'=>'SG',
'Slovenia'=>'SI',
'Slovak Republic'=>'SK',
'Solomon Islands'=>'SB',
'Somalia'=>'SO',
'South Africa'=>'ZA',
'Spain'=>'ES',
'Sri Lanka'=>'LK',
'St. Helena'=>'SH',
'St. Pierre and Miquelon '=>'PM',
'Sudan'=>'SD',
'Suriname'=>'SR',
'Svalbard &amp; Jan Mayen Islands'=>'SJ',
'Swaziland'=>'SZ',
'Sweden'=>'SE',
'Switzerland'=>'CH',
'Syria'=>'SY',
'Taiwan'=>'TW',
'Tajikistan'=>'TJ',
'Tanzania'=>'TZ',
'Thailand'=>'TH',
'Togo'=>'TG',
'Tokelau'=>'TK',
'Tonga'=>'TO',
'Trinidad and Tobago'=>'TT',
'Tunisia'=>'TN',
'Turkey'=>'TR',
'Turkmenistan'=>'TM',
'Turks and Caicos Islands'=>'TC',
'Tuvalu'=>'TV',
'Uganda'=>'UG',
'Ukraine'=>'UA',
'United Arab Emirates'=>'AE',
'United Kingdom'=>'UK',
'United States'=>'US',
'US Minor Outlying Islands'=>'UM',
'Uruguay'=>'UY',
'Uzbekistan'=>'UZ',
'Vanuatu'=>'VU',
'Vatican City State (Holy See)'=>'VA',
'Venezuela'=>'VE',
'Viet Nam'=>'VN',
'Virgin Islands (British)'=>'VG',
'Virgin Islands (U.S.)'=>'VI',
'Wallis and Futuna Islands'=>'WF',
'Western Sahara'=>'EH',
'Yemen'=>'YE',
'Yugoslavia'=>'YU',
'Zambia'=>'ZM',
'Zimbabwe'=>'ZW' );

	}


    function StatusInfo()
	{
		if (ffUtilityFunctions::def($this->ValidationType))
		  {
		  	return array_search($this->ValidationType,$this->ValidationTypes);
		  }
	}

	function WriteToPublicForm($id, &$params, $return_id)
	{
		$defVals = $this->GetOptionByKind('default');
        $defSel = $this->GetOptionByKind('defaultsel');
	   if (strlen($this->CSSClass)>0)
        	{
        	echo "<div class=\"".$this->CSSClass."\">";
        	}

		if (ffUtilityFunctions::def($defVals[0]->Value))
			{
			$this->Countries[$defVals[0]->Value]='';
			}
		else
			{
			$this->Countries[$this->mod_globals->Lang('select_one')]='';
			}
		asort($this->Countries);
		if ($this->Value == '')
		  {
		  $this->Value = $defSel[0]->Value;
		  }
        echo CMSModule::CreateInputDropdown($id, $this->Alias, $this->Countries, -1, $this->Value,$this->mod_globals->UseIDAndName?'id="'.$this->Alias.'"':'');
	   if (strlen($this->CSSClass)>0)
        	{
        	echo "</div>";
        	}
	}

	function RenderAdminForm($formDescriptor)
	{
		$defVals = $this->GetOptionByKind('default');
		$defSel = $this->GetOptionByKind('defaultsel');
		return array(
            $this->mod_globals->Lang('title_select_default_country').':'=>CMSModule::CreateInputDropdown($formDescriptor, 'defaultsel', $this->Countries, -1, ffUtilityFunctions::def($defSel[0]->Value)?$defSel[0]->Value:''),
            $this->mod_globals->Lang('title_select_one_message').':'=>CMSModule::CreateInputText($formDescriptor, 'default',
				ffUtilityFunctions::def($defVals[0]->Value)?$this->NerfHTML($defVals[0]->Value):'Select One',25));
	}

	function GetValue()
	{
		if (ffUtilityFunctions::def($this->Value))
			{
			return $this->Value;
			}
		else
			{
			return $this->mod_globals->Lang('unspecified');
			}	
	}


	function Validate()
	{
		$result = true;
		$message = '';

		switch ($this->ValidationType)
		  {
		  	   case 'none':
		  	       break;
		  	   case 'selected':
		  	       if (! ffUtilityFunctions::def($this->Value))
		  	           {
		  	           $result = false;
		  	           $message = $this->mod_globals->Lang('please_select_something').' "'.$this->Name.'"';
		  	           }
		  	       break;
		  }
		return array($result, $message);
	}

}

?>
