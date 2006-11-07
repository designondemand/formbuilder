<?php
/* 
   FormBuilder. Copyright (c) 2005-2006 Samuel Goldstein <sjg@cmsmodules.com>
   More info at http://dev.cmsmadesimple.org/projects/formbuilder
   
   A Module for CMS Made Simple, Copyright (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
  This project's homepage is: http://www.cmsmadesimple.org
*/
		
if (!isset($params['f']) || !isset($params['r']) || !isset($params['c']))
	{
	echo $this->Lang('validation_param_error');
	}
$params['response_id']=$params['r'];
$params['form_id']=$params['f'];
$params['user_form_validate']=true;
$aeform = new fbForm($this, $params, true);

if (!$aeform->CheckResponse($params['f'], $params['r'], $params['c']))
	{
	echo $this->Lang('validation_response_error');
	}

$fields = $aeform->GetFields();
$confirmationField = -1;
for($i=0;$i<count($fields);$i++)
	{
	if ($fields[$i]->GetFieldType() == 'DispositionEmailConfirmation')
		{
		$confirmationField = $i;
		}
	}
if ($confirmationField != -1)
	{
	$fields[$confirmationField]->ApproveToGo($params['r']);
	$results = $aeform->Dispose($returnid);
	if ($results[0] == true)
		{
		$ret = $fields[$confirmationField]->GetOption('redirect_page','-1');
		if ($ret != -1)
			{
			$this->RedirectContent($ret);
			}
		}
	else
		{
		echo "Error!: ";
		foreach ($results[1] as $thisRes)
			{
			echo $thisRes . '<br />';
			}
		}
	}
else
	{
		echo $this->Lang('validation_no_field_error');
	}
?>