<?php
// Feedback Form. 02/2005 SjG <feedbackform_cmsmodule@fogbound.net>
// A Module for CMS Made Simple, (c)2005 by Ted Kulp (wishy@cmsmadesimple.org)
// This project's homepage is: http://www.cmsmadesimple.org

class ffDispositionFile extends ffInput {

	function ffDispositionFile(&$mod_globals, $formRef, $params=array())
	{
        $this->ffInput($mod_globals, $formRef, $params);
		$this->Type = 'DispositionFile';
		$this->IsDisposition = true;
		$this->Required = true;
		$this->DisplayType = $this->mod_globals->Lang('field_type_disposition_file');
		$this->DisplayInForm = false;
        if (ffUtilityFunctions::def($params['format']))
            {
            $this->AddOption('format','format',$params['format']);
            }
        if (ffUtilityFunctions::def($params['filespec']))
            {
            $this->AddOption('filespec','filespec',$params['filespec']);
            }
        else
            {
            $this->AddOption('filespec','filespec','output.txt');
            }
        if (ffUtilityFunctions::def($params['clear_flock']))
            {
            $this->ClearFileLock();
            }
	}

    function StatusInfo()
	{
		$spec = $this->GetOptionByName('filespec');
		return ffUtilityFunctions::def($spec[0]->Value)?$spec[0]->Value:'';
	}


    function ClearFileLock()
    {
    	echo "Clearing File Lock.";
		$db = $this->mod_globals->DBHandle;
		$sql = "DELETE from ".$this->mod_globals->FlockTableName;
		$rs = $db->Execute($sql);
    }


    function GetFileLock()
    {
		$db = $this->mod_globals->DBHandle;
		$sql = "insert into ".$this->mod_globals->FlockTableName." (flock_id, flock) values (1,".$db->sysTimeStamp.")";
		$rs = $db->Execute($sql);
        if ($rs)
        	{
        	return true;
        	}
        $sql = "select flock_id from ".$this->mod_globals->FlockTableName.
        	" where flock + interval 15 second < ".$db->sysTimeStamp;
		$rs = $db->Execute($sql);
        if ($rs && $rs->RowCount() > 0)
        	{
        	$this->ClearFileLock();
        	return false;
        	}
        	 
		return false;
    }

    function ReturnFileLock()
    {
		$db = $this->mod_globals->DBHandle;
		$sql = "delete from ".$this->mod_globals->FlockTableName;
		$rs = $db->Execute($sql);
    }


    // Write Files
	function DisposeForm($formName, &$config, $results)
	{
		$count = 0;
        while (! $this->GetFileLock() && $count<200)
        	{
        	$count++;
        	usleep(500);
        	}
        if ($count == 200)
        	{
        	echo $this->mod_globals->Lang('submission_error_file');
        	}
        else
        	{
        	$spec = $this->GetOptionByName('filespec');
        	$filespec = $spec[0]->Value;
        	$filespec = 'modules/FeedbackForm/output/'.preg_replace("/[^\w\d\.]|\.\./", "_", $filespec);
        	$fmt = $this->GetOptionByName('format');
        	$exists = false;
        	if (file_exists($filespec))
        		{
        		$exists = true;
        		}
			$f2 = fopen($filespec,"a");
			if ($fmt[0]->Value == 'tabhead' && !$exists)
				{
				fwrite($f2, $this->mod_globals->Lang('submission_date')."\t".$this->mod_globals->Lang('submission_host').
                    "\t".$this->mod_globals->Lang('submission_source')."\t");
				for ($i=0;$i<count($results);$i++)
					{
					fwrite($f2, $results[$i][0]);
					if ($i<count($results) - 1)
						{
						fwrite($f2,"\t");
						}
					else
						{
						fwrite($f2,"\n");
						}
					}
				}
			switch ($fmt[0]->Value)
				{
				case 'tab':
				case 'tabhead':
					fwrite($f2, date('r')."\t");
					fwrite($f2, $_SERVER['SERVER_NAME']."\t");
					fwrite($f2, $_SERVER['REMOTE_ADDR']."\t");
					for ($i=0;$i<count($results);$i++)
						{
						if (is_array($results[$i][1]))
							{
							$vals = '';
							foreach($res[1] as $elem)
								{
								$vals .= $elem . ", ";
								}
							fwrite($f2,rtrim($vals,", "));
							}
						else
							{
							fwrite($f2, $results[$i][1]);
							}
						if ($i<count($results) - 1)
							{
							fwrite($f2,"\t");
							}
						else
							{
							fwrite($f2,"\n");
							}
						}
					break;
				case 'page':
					fwrite($f2,"================================\n");
					fwrite($f2, $this->mod_globals->Lang('submission_title')."\n");
					fwrite($f2, $this->mod_globals->Lang('submission_form_name').":\t".$formName."\n");
					fwrite($f2, $this->mod_globals->Lang('submission_date').":\t".date('r')."\n");
					fwrite($f2, $this->mod_globals->Lang('submission_host').":\t".$_SERVER['SERVER_NAME']."\n");
					fwrite($f2, $this->mod_globals->Lang('submission_source').":\t".$_SERVER['REMOTE_ADDR']."\n");
					fwrite($f2,"--------------------------------\n");
					foreach ($results as $res)
						{
						fwrite($f2,$res[0]);
						fwrite($f2,"\t");
						if (is_array($res[1]))
							{
							$vals = '';
							foreach($res[1] as $elem)
								{
								$vals .= $elem . ", ";
								}
							fwrite($f2,rtrim($vals,", ") . "\n");
							}
						else
							{
							fwrite($f2,$res[1] . "\n");
							}
						}
					fwrite($f2,"\n");
					break;
				}
			fclose($f2); 
        	$this->ReturnFileLock();
        	}
        
	}


	function RenderAdminForm($formDescriptor)
	{
        $fmt = $this->GetOptionByName('format');
        $spec = $this->GetOptionByName('filespec');
        return array($this->mod_globals->Lang('title_file_format').':'=>CMSModule::CreateInputDropdown($formDescriptor, 'format',
            array($this->mod_globals->Lang('title_file_format_tab')=>'tab',
                $this->mod_globals->Lang('title_file_format_tab_header')=>'tabhead',
                $this->mod_globals->Lang('title_file_format_page')=>'page'), -1,
                ffUtilityFunctions::def($fmt[0]->Value)?$this->NerfHTML($fmt[0]->Value):''),
            $this->mod_globals->Lang('title_file_name').':'=>CMSModule::CreateInputText($formDescriptor, 'filespec',
				ffUtilityFunctions::def($spec[0]->Value)?$this->NerfHTML($spec[0]->Value):'',25),
			$this->mod_globals->Lang('title_erase_filelock').':'=>CMSModule::CreateInputCheckbox($formDescriptor, 'clear_flock', 'clear', '').
			' '.$this->mod_globals->Lang('title_erase_usage'),
			$this->mod_globals->Lang('note').':'=>$this->mod_globals->Lang('title_file_note')
            );
	}


	function Validate()
	{
		return array(true, '');
	}

}

?>
