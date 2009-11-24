<?php
/* 
   FormBuilder. Copyright (c) 2005-2006 Samuel Goldstein <sjg@cmsmodules.com>
   More info at http://dev.cmsmadesimple.org/projects/formbuilder
   
   A Module for CMS Made Simple, Copyright (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
  This project's homepage is: http://www.cmsmadesimple.org
*/
class fbCountryPickerField extends fbFieldBase {

	var $Countries;
	
	function fbCountryPickerField(&$form_ptr, &$params)
	{
        $this->fbFieldBase($form_ptr, $params);
        $mod = $form_ptr->module_ptr;
		$this->Type = 'CountryPickerField';
		$this->DisplayInForm = true;
		$this->ValidationTypes = array(
            );

        $this->Countries = array($mod->Lang('no_default')=>'',
        'Afghanistan'=>'AF', 'Aland Islands'=>'AX', 
        'Albania'=>'AL',  'Algeria'=>'DZ', 'American Samoa'=>'AS', 'Andorra'=>'AD',
         'Angola'=>'AO', 'Anguilla'=>'AI', 'Antarctica'=>'AQ',  'Antigua and Barbuda'=>'AG',
        'Argentina'=>'AR', 'Armenia'=>'AM', 'Aruba'=>'AW', 'Australia'=>'AU', 'Austria'=>'AT', 		'Azerbaijan'=>'AZ', 'Bahamas'=>'BS', 'Bahrain'=>'BH', 'Barbados'=>' BB',
        'Bangladesh'=>'BD', 'Belarus'=>'BY', 'Belgium'=>'BE',
        'Belize'=>'BZ', 'Benin'=>'BJ', 'Bermuda'=>'BM', 'Bhutan'=>'BT', 'Botswana'=>'BW',
        'Bolivia'=>'BO', 'Bosnia and Herzegovina'=>'BA', 'Bouvet Island'=>'BV',
        'Brazil'=>'BR', 'British Indian Ocean Territory'=>'IO', 'Brunei Darussalam'=>'BN', 'Bulgaria'=>'BG',
        'Burkina Faso'=>'BF', 'Burundi'=>'BI', 'Cambodia'=>'KH',
        'Cameroon'=>'CM', 'Canada'=>'CA' , 'Cape Verde'=>'CV', 'Cayman Islands'=>'KY', 
        'Central African Republic'=>'CF', 'Chad'=>'TD',
        'Chile'=>'CL', 'China'=>'CN', 'Christmas Island'=>'CX', 
        'Cocos (Keeling) Islands'=>'CC', 'Colombia'=>'CO', 'Comoros'=>'KM',
        'Congo'=>'CG', 'Congo,  Democratic Republic'=>'CD',  'Cook Islands'=>'CK',
        'Costa Rica'=>'CR', 'Cote D\'Ivoire (Ivory Coast)'=>'CI', 'Croatia (Hrvatska)'=>'HR',  'Cuba'=>'CU',
        'Cyprus'=>'CY', 'Czech Republic'=>'CZ', 'Denmark'=>'DK',
        'Djibouti'=>'DJ', 'Dominica'=>'DM ',  'Dominican Republic'=>'DO',
        'East Timor'=>'TP', 'Ecuador'=>'EC',
        'Egypt'=>'EG', 'El Salvador'=>'SV', 'Equatorial Guinea'=>'GQ',
        'Eritrea'=>'ER', 'Estonia'=>'EE', 'Ethiopia'=>'ET', 'Falkland Islands (Malvinas)'=>'FK',
        'Faroe Islands'=>'FO', 'Fiji'=>'FJ',
        'Finland'=>'FI', 'France'=>'FR', 'France,  Metropolitan'=>'FX',
         'French Guiana'=>'GF', 'French Polynesia'=>'PF', 'French Southern Territories'=>'TF',
         'F.Y.R.O.M. (Macedonia)'=>'MK',
        'Gabon'=>'GA', 'Gambia'=>'GM', 'Georgia'=>'GE', 'Germany'=>'DE',
         'Ghana'=>'GH', 'Gibraltar'=>'GI', 'Great Britain (UK)'=>'GB',
        'Greece'=>'GR', 'Greenland'=>'GL',  'Grenada'=>'GD',
        'Guadeloupe'=>'GP',  'Guam'=>'GU',  'Guatemala'=>'GT',
        'Guernsey'=>'GF',  'Guinea'=>'GN',  'Guinea-Bissau'=>'GW',
        'Guyana'=>'GY',  'Haiti'=>'HT',  'Heard and McDonald Islands'=>'HM',
        'Honduras'=>'HN',  'Hong Kong'=>'HK',
        'Hungary'=>'HU',  'Iceland'=>'IS',  'India'=>'IN',
        'Indonesia'=>'ID',  'Iran'=>'IR',  'Iraq'=>'IQ', 
        'Ireland'=>'IE', 'Israel'=>'IL',  'Isle of Man'=>'IM', 
        'Italy'=>'IT', 'Jersey'=>'JE',  'Jamaica'=>'JM',  'Japan'=>'JP',
         'Jordan'=>'JO', 'Kazakhstan'=>'KZ',  'Kenya'=>'KE', 
        'Kiribati'=>'KI',  'Korea (North)'=>'KP',  'Korea (South)'=>'KR', 
        'Kuwait'=>'KW', 'Kyrgyzstan'=>'KG', 
        'Laos'=>'LA',  'Latvia'=>'LV', 'Lebanon'=>'LB', 
        'Liechtenstein'=>'LI',  'Liberia'=>'LR', 'Libya'=>'LY', 
        'Lesotho'=>'LS',  'Lithuania'=>'LT', 'Luxembourg'=>'LU', 
        'Macau'=>'MO',  'Madagascar'=>'MG', 'Malawi'=>'MW', 
        'Malaysia'=>'MY',  'Maldives'=>'MV', 'Mali'=>'ML', 
        'Malta'=>'MT',  'Marshall Islands'=>'MH', 'Martinique'=>'MQ', 
        'Mauritania'=>'MR',  'Mauritius'=>'MU', 'Mayotte'=>'YT', 
        'Mexico'=>'MX',  'Micronesia'=>'FM', 'Monaco'=>'MC', 
        'Moldova'=>'MD',  'Morocco'=>'MA', 'Mongolia'=>'MN', 
        'Montserrat'=>'MS',  'Mozambique'=>'MZ', 'Myanmar'=>'MM', 
        'Namibia'=>'NA',  'Nauru'=>'NR',  'Nepal'=>'NP',
        'Netherlands'=>'NL',  'Netherlands Antilles'=>'AN',  'Neutral Zone'=>'NT',
        'New Caledonia'=>'NC',  'New Zealand (Aotearoa)'=>'NZ',  'Nicaragua'=>'NI',  'Niger'=>'NE',
        'Nigeria'=>'NG',  'Niue'=>'NU',  'Norfolk Island'=>'NF', 
        'Northern Mariana Islands'=>'MP',  'Norway'=>'NO', 
        'Oman'=>'OM', 'Pakistan'=>'PK',  'Palau'=>'PW',  'Palestinian Territory'=>'PS',
        'Panama'=>'PA',  'Papua New Guinea'=>'PG', 
        'Paraguay'=>'PY', 'Peru'=>'PE',  'Philippines'=>'PH', 
        'Pitcairn'=>'PN', 'Poland'=>'PL',  'Portugal'=>'PT',  'Puerto Rico'=>'PR',
        'Qatar'=>'QA',  'Reunion'=>'RE',  'Romania'=>'RO', 
        'Russian Federation'=>'RU',  'Rwanda'=>'RW',  'S. Georgia and S. Sandwich Isls.'=>'GS',
        'Saint Kitts and Nevis'=>'KN',  'Saint Lucia'=>'LC',  'Saint Vincent &amp; the Grenadines'=>'VC',
        'Samoa'=>'WS',  'San Marino'=>'SM',  'Sao Tome and Principe'=>'ST',
         'Saudi Arabia'=>'SA',  'Senegal'=>'SN',
        'Seychelles'=>'SC',  'Sierra Leone'=>'SL',  'Singapore'=>'SG',
        'Slovenia'=>'SI',  'Slovak Republic'=>'SK',  'Solomon Islands'=>'SB',  'Somalia'=>'SO', 
        'South Africa'=>'ZA',
        'Spain'=>'ES',  'Sri Lanka'=>'LK',  'St. Helena'=>'SH',  'St. Pierre and Miquelon '=>'PM',
        'Sudan'=>'SD',  'Suriname'=>'SR',
        'Svalbard &amp; Jan Mayen Islands'=>'SJ',  'Swaziland'=>'SZ',
        'Sweden'=>'SE',  'Switzerland'=>'CH',  'Syria'=>'SY',
        'Taiwan'=>'TW',  'Tajikistan'=>'TJ',  'Tanzania'=>'TZ',
        'Thailand'=>'TH',  'Togo'=>'TG',  'Tokelau'=>'TK', 
        'Tonga'=>'TO', 'Trinidad and Tobago'=>'TT',  'Tunisia'=>'TN', 
        'Turkey'=>'TR', 'Turkmenistan'=>'TM',  'Turks and Caicos Islands'=>'TC',
        'Tuvalu'=>'TV',  'Uganda'=>'UG', 
        'Ukraine'=>'UA',  'United Arab Emirates'=>'AE',  'United Kingdom'=>'UK',
        'United States'=>'US', 'US Minor Outlying Islands'=>'UM',  'Uruguay'=>'UY', 
        'Uzbekistan'=>'UZ', 
        'Vanuatu'=>'VU',  'Vatican City State (Holy See)'=>'VA', 
        'Venezuela'=>'VE',  'Viet Nam'=>'VN',  'Virgin Islands (British)'=>'VG',
        'Virgin Islands (U.S.)'=>'VI',  'Wallis and Futuna Islands'=>'WF', 
        'Western Sahara'=>'EH', 'Yemen'=>'YE', 
        'Yugoslavia'=>'YU',  'Zambia'=>'ZM', 'Zimbabwe'=>'ZW' );
	}


    function StatusInfo()
	{
		return '';
	}

	function GetHumanReadableValue($as_string=true)
	{
		$ret = array_search($this->Value,$this->Countries);
		if ($as_string)
			{
			return $ret;
			}
		else
			{
			return array($ret);
			}
	}

	function GetFieldInput($id, &$params, $returnid)
	{
		$mod = $this->form_ptr->module_ptr;

		unset($this->Countries[$mod->Lang('no_default')]);
		$js = $this->GetOption('javascript','');
		if ($this->GetOption('select_one','') != '')
			{
			$this->Countries = array_merge(array($this->GetOption('select_one','')=>''),$this->Countries);
			}
		else
			{
			$this->Countries = array_merge(array($mod->Lang('select_one')=>''),$this->Countries);
			}

		if (! $this->HasValue() && $this->GetOption('default','') != '')
		  {
		  $this->SetValue($this->GetOption('default',''));
		  }

		return $mod->CreateInputDropdown($id, 'fbrp__'.$this->Id, $this->Countries, -1,
         $this->Value, $js.$this->GetCSSIdTag());
	}

	function PrePopulateAdminForm($formDescriptor)
	{
		$mod = $this->form_ptr->module_ptr;
		ksort($this->Countries);

		$main = array(
			array($mod->Lang('title_select_default_country'),
            		$mod->CreateInputDropdown($formDescriptor, 'fbrp_opt_default',
            		$this->Countries, -1, $this->GetOption('default',''))),
			array($mod->Lang('title_select_one_message'),
            		$mod->CreateInputText($formDescriptor, 'fbrp_opt_select_one',
            		$this->GetOption('select_one',$mod->Lang('select_one'))))
		);
		return array('main'=>$main,array());
	}


}

?>
