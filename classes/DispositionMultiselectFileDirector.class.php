<?php
/* 
   FormBuilder. Copyright (c) 2005-2006 Samuel Goldstein <sjg@cmsmodules.com>
   More info at http://dev.cmsmadesimple.org/projects/formbuilder
   
   A Module for CMS Made Simple, Copyright (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
  This project's homepage is: http://www.cmsmadesimple.org

   This file: Copyright (c) 2007 Robert Campbell <calguy1000@hotmail.com>
   All rights reserved.
*/

class fbDispositionMultiselectFileDirector extends  fbFieldBase 
{
  var $fileCount;
  var $fileAdd;
  var $sampleTemplateCode;
  var $sampleHeader;
  var $dflt_filepath;

  function fbDispositionMultiselectFileDirector(&$form_ptr, &$params)
  {
    $this->fbFieldBase($form_ptr, $params);
    $mod = &$form_ptr->module_ptr;
    $this->Type = 'DispositionMultiselectFileDirector';
    $this->IsDisposition = true;
    $this->DisplayInForm = true;
    $this->HasAddOp = true;
    $this->HasDeleteOp = true;
    $this->hasMultipleFormComponents = true;
    $this->sortable = false;
    $this->fileAdd = 0;


    $this->sampleTemplateCode = "<script type=\"text/javascript\">\n
function populate_file(formname)
    {
    var fname = 'IDfbrp_opt_file_template';
    formname[fname].value=TEMPLATE;
    }
</script>
<input type=\"button\" value=\"".$mod->Lang('title_create_sample_template')."\" onClick=\"javascript:populate_file(this.form)\" />";
    $this->sampleHeader = "<script type=\"text/javascript\">\n
function populate_header(formname)
    {
      var fname = 'IDfbrp_opt_file_header';
      formname[fname].value=TEMPLATE;
    }
</script>
<input type=\"button\" value=\"".$mod->Lang('title_create_sample_header')."\" onClick=\"javascript:populate_header(this.form)\" />";

    global $gCms;
    $config =& $gCms->getConfig();
    $this->dflt_filepath = $config['uploads_path'];
  }


  function DoOptionAdd(&$params)
  {
    $this->fileAdd = 1;
  }

  function DoOptionDelete(&$params)
  {
    $delcount = 0;
    foreach ($params as $thisKey=>$thisVal)
      {
	if (substr($thisKey,0,9) == 'fbrp_del_')
	  {
	    $this->RemoveOptionElement('destination_filename', $thisVal - $delcount);
	    $this->RemoveOptionElement('destination_displayname', $thisVal - $delcount);
	    $delcount++;
	  }
      }
  }

  function countFiles()
  {
    $tmp = &$this->GetOptionRef('destination_filename');
    if (is_array($tmp))
      {
	$this->fileCount = count($tmp);
      }
    elseif ($tmp !== false)
      {
	$this->fileCount = 1;
      }
    else
      {
	$this->fileCount = 0;
      }
  }

  function GetFieldInput($id, &$params, $returnid)
  {
    $mod = &$this->form_ptr->module_ptr;
    
    // why all this? Associative arrays are not guaranteed to preserve
    // order, except in "chronological" creation order.
    $displaynames = &$this->GetOptionRef('destination_displayname');
    $displayfiles = &$this->GetOptionRef('destination_filename');

    $fields = array();
    for( $i = 0; $i < count($displaynames); $i++ )
      {
	$label = '';
	$ctrl = new stdClass();
	$ctrl->name = '<label for="'.$id.'_'.$this->Id.'_'.$i.'">'.$displaynames[$i].'</label>';
	$ctrl->title = $displaynames[$i];
	$ctrl->input = $mod->CreateInputCheckbox($id,
						 'fbrp__'.$this->Id.'[]', 
						 $i,'-1',
						 sprintf(' id="%s_%s_%s"',
							 $id, $this->Id,$i));
	$fields[] = $ctrl;
      }
    return $fields;
  }


  function StatusInfo()
  {
    $this->countFiles();
    $mod=&$this->form_ptr->module_ptr;
    $ret= $mod->Lang('file_count',$this->fileCount);
    return $ret;
  }

  function DisposeForm($returnid)
  {

    $mod=&$this->form_ptr->module_ptr;
    $count = 0;
    while (! $mod->GetFileLock() && $count<200)
      {
	$count++;
	usleep(500);
      }
    if ($count == 200)
      {
	return array(false, $mod->Lang('submission_error_file_lock'));
      }


    $dir = $this->GetOption('file_path',$this->dflt_filepath).'/';

    // setup some smarty
    $mod->smarty->assign('sub_form_name',$this->form_ptr->GetName());
    $mod->smarty->assign('sub_date',date('r'));
    $mod->smarty->assign('sub_host',$_SERVER['SERVER_NAME']);
    $mod->smarty->assign('sub_source_ip',$_SERVER['REMOTE_ADDR']);
    $mod->smarty->assign('sub_url',$_SERVER['HTTP_REFERER']);
    $mod->smarty->assign('TAB',"\t");
    $others = &$this->form_ptr->GetFields();
    $unspec = $this->form_ptr->GetAttr('unspecified',$mod->Lang('unspecified'));
    for($i=0;$i<count($others);$i++)
      {
	$replVal = '';
	$replVals = array();
	if ($others[$i]->DisplayInSubmission())
	  {
	    $replVal = $others[$i]->GetHumanReadableValue();
	    if ($replVal == '')
	      {
		$replVal = $unspec;
	      }
	    if ($others[$i]->HasMultipleValues())
	      {
		$replVals = $others[$i]->GetValue();
	      }
	  }
	$mod->smarty->assign($this->MakeVar($others[$i]->Getname()),$replVal);
	$mod->smarty->assign('fld_'.$others[$i]->GetId(),$replVal);
	$mod->smarty->assign($this->MakeVar($others[$i]->Getname()).'_array',$replVals);
	$mod->smarty->assign('fld_'.$others[$i]->GetId().'_array',$replVals);
      }

    $header = $this->GetOption('file_header','');
    if ($header == '')
      {
	$header = $this->createSampleHeader();
      } 
    $header .= "\n";

    $template = $this->GetOption('file_template','');
    if ($template == '')
      {
	$template = $this->createSampleTemplate();
      }
	
    // Begin output to files
    foreach( $this->Value as $idx )
      {
	// I dunno why it's empty sometimes, but...
	if( empty($idx) ) continue;

	// get the filename
	$filespec = $dir.
	  preg_replace("/[^\w\d\.]|\.\./", "_", 
		       $this->GetOptionElement('destination_filename',$idx));
	
	$line = $template;
	if (! file_exists($filespec))
	  {
	    $line = $header.$template;
	  }

	$newline = $mod->ProcessTemplateFromData( $line );
	if (substr($newline,-1,1) != "\n")
	  {
	    $newline .= "\n";
	  }	

	$f2 = fopen($filespec,"a");
	fwrite($f2,$newline);
	fclose($f2); 

      } // foreach
    $mod->ReturnFileLock();
    return array(true,'');        
  }

  function MakeVar($string)
  {
    $maxvarlen = 24;
    $string = strtolower(preg_replace('/\s+/','_',$string));
    $string = strtolower(preg_replace('/\W/','_',$string));
    if (strlen($string) > $maxvarlen)
      {
	$string = substr($string,0,$maxvarlen);
	$pos = strrpos($string,'_');
	if ($pos !== false)
	  {
	    $string = substr($string,0,$pos);
	  }
      }
    return $string;
  }

  function createSampleHeader()
  {
    $mod = &$this->form_ptr->module_ptr;
    $others = &$this->form_ptr->GetFields();
    $fields = array();
    for($i=0;$i<count($others);$i++)
      {
	if ($others[$i]->DisplayInSubmission())
	  {
	    array_push($fields,$others[$i]->GetName());
	  }
      }
    return implode('{$TAB}',$fields);
  }


  function createSampleTemplate()
  {
    $mod = &$this->form_ptr->module_ptr;
    $others = &$this->form_ptr->GetFields();
    $fields = array();
    for($i=0;$i<count($others);$i++)
      {
	if ($others[$i]->DisplayInSubmission())
	  {
	    array_push($fields,'{$' . $this->MakeVar($others[$i]->GetName()) . '}');
	  }
      }
    return implode('{$TAB}',$fields);
  }


  function PrePopulateAdminForm($formDescriptor)
  {
    $mod = &$this->form_ptr->module_ptr;

    $this->countFiles();
    if( $this->fileAdd > 0 )
      {
	$this->fileCount += $this->fileAdd;
	$this->fileAdd = 0;
      }

    $dests = '<table class="module_fb_table"><tr><th>'.$mod->Lang('title_selection_displayname').'</th><th>'.
      $mod->Lang('title_destination_filename').'</th><th>'.
      $mod->Lang('title_delete').'</th></tr>';

    for ($i=0;$i<($this->fileCount>1?$this->fileCount:1);$i++)
      {
	$dests .=  '<tr><td>'.
	  $mod->CreateInputText($formDescriptor, 'fbrp_opt_destination_displayname[]',$this->GetOptionElement('destination_displayname',$i),25,128).
	  '</td><td>'.
	  $mod->CreateInputText($formDescriptor, 'fbrp_opt_destination_filename[]',$this->GetOptionElement('destination_filename',$i),25,128).
	  '</td><td>'.
	  $mod->CreateInputCheckbox($formDescriptor, 'fbrp_del_'.$i, $i,-1).
	  '</td></tr>';
      }
    $dests .= '</table>';


    $ret = '<table class="module_fb_legend"><tr><th colspan="2">'.$mod->Lang('help_variables_for_template').'</th></tr>';
    $ret .= '<tr><th>'.$mod->Lang('help_variable_name').'</th><th>'.$mod->Lang('help_form_field').'</th></tr>';
    $ret .= '<tr><td>{$sub_form_name}</td><td>'.$mod->Lang('title_form_name').'</td></tr>';
    $ret .= '<tr><td>{$sub_date}</td><td>'.$mod->Lang('help_submission_date').'</td></tr>';
    $ret .= '<tr><td>{$sub_host}</td><td>'.$mod->Lang('help_server_name').'</td></tr>';
    $ret .= '<tr><td>{$sub_source_ip}</td><td>'.$mod->Lang('help_sub_source_ip').'</td></tr>';
    $ret .= '<tr><td>{$source_url}</td><td>'.$mod->Lang('help_sub_url').'</td></tr>';
    $others = &$this->form_ptr->GetFields();
    for($i=0;$i<count($others);$i++)
      {
	if ($others[$i]->DisplayInSubmission())
	  {                
	    $ret .= '<tr><td>${'.$this->MakeVar($others[$i]->GetName()) .'}</td><td>' .$others[$i]->GetName() . '</td></tr>';
	  }
      }

    $ret .= '<tr><td>{$TAB}</td><td>'.$mod->Lang('help_tab_symbol').'</td></tr>';       	
    $ret .= '<tr><td colspan="2">'.$mod->Lang('help_other_fields').'</td></tr>';
        
    $escapedSample = preg_replace('/\'/',"\\'",$this->createSampleTemplate());
    $escapedSample = preg_replace('/\n/',"\\n'+\n'", $escapedSample);
    $this->sampleTemplateCode = preg_replace('/TEMPLATE/',"'".$escapedSample."'",$this->sampleTemplateCode);
    $this->sampleTemplateCode = preg_replace('/ID/',$formDescriptor, $this->sampleTemplateCode);
	   
    $escapedHeader = preg_replace('/\'/',"\\'",$this->createSampleHeader());
    $escapedHeader = preg_replace('/\n/',"\\n'+\n'", $escapedHeader);
    $this->sampleHeader = preg_replace('/TEMPLATE/',"'".$escapedHeader."'",$this->sampleHeader);
    $this->sampleHeader = preg_replace('/ID/',$formDescriptor, $this->sampleHeader);
	   
    $ret .= '<tr><td colspan="2">'.$this->sampleHeader.'</td></tr>';
    $ret .= '<tr><td colspan="2">'.$this->sampleTemplateCode.'</td></tr>';

    $ret .= '</table>';


    $main = array();
    $adv = array();
    array_push($main,array($mod->Lang('title_select_one_message'),
			   $mod->CreateInputText($formDescriptor, 
						 'fbrp_opt_select_one',
	    $this->GetOption('select_one',$mod->Lang('select_one')),25,128)));
    array_push($main,array($mod->Lang('title_director_details'),$dests));

    array_push($adv,array($mod->Lang('title_file_path'),
			  $mod->CreateInputText($formDescriptor,
						'fbrp_opt_file_path',
						$this->GetOption('file_path',$this->dflt_filepath),40,128)));
    array_push($adv,array($mod->Lang('title_file_template'),
			  array($mod->CreateTextArea(false, $formDescriptor,
						     htmlspecialchars($this->GetOption('file_template','')),'fbrp_opt_file_template', 'module_fb_area_short', '','',0,0),$ret)));
    array_push($adv,array($mod->Lang('title_file_header'),
			  $mod->CreateTextArea(false, $formDescriptor,
					       htmlspecialchars($this->GetOption('file_header','')),'fbrp_opt_file_header', 'module_fb_area_short', '','',0,0)));

    return array('main'=>$main,'adv'=>$adv);
  }

  function PostPopulateAdminForm(&$mainArray, &$advArray)
  {
    $this->HiddenDispositionFields($mainArray, $advArray);
  }
}

?>
