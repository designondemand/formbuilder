<?php
// Feedback Form. 02/2005 SjG <feedbackform_cmsmodule@fogbound.net>
// A Module for CMS Made Simple, (c)2005 by Ted Kulp (wishy@cmsmadesimple.org)
// This project's homepage is: http://www.cmsmadesimple.org

class fbOption {

    var $OptionId=-1;
    var $FieldId;
    var $FormId;
    var $Kind;
    var $Name;
    var $Value;
    
    var $mod_globals;

	function ffOption(&$mod_globals, $params=array())
	{
	   $this->mod_globals = &$mod_globals;
	   if (ffUtilityFunctions::def($params['option_id']))
	       {
	       $this->OptionId = $params['option_id'];
	       }
	   if (ffUtilityFunctions::def($params['form_id']))
	       {
	       $this->FormId = $params['form_id'];
	       }
	   if (ffUtilityFunctions::def($params['field_id']))
	       {
	       $this->FieldId = $params['field_id'];
	       }
	   if (ffUtilityFunctions::def($params['kind']))
	       {
	       $this->Kind = $params['kind'];
	       }
	   if (ffUtilityFunctions::def($params['name']))
	       {
	       $this->Name = $params['name'];
	       }
	   if (ffUtilityFunctions::def($params['value']))
	       {
	       $this->Value = $params['value'];
	       }
	}
	
	function DebugDisplay()
	{
		echo "--- Option Id: ".$this->OptionId."<br />";
		echo "--- Option Kind: ".$this->Kind."<br />";
		echo "--- Option Name: ".$this->Name."<br />";
		echo "--- Option Value: ".$this->Value."<br />";

	}

    function Store()
    {
        if ($this->OptionId == -1)
            {
            $this->OptionId = $this->mod_globals->DBHandle->GenID($this->mod_globals->OptionTableName.'_seq');
			$sql = 'INSERT INTO ' . $this->mod_globals->OptionTableName . " (option_id, field_id, form_id, ".
                    "kind, name, value) VALUES (?, ?, ?, ?, ?, ?)";
			$res = $this->mod_globals->DBHandle->Execute($sql, array($this->OptionId, $this->FieldId,
                $this->FormId, $this->Kind, $this->Name, $this->Value));
            }
        else
            {
			$sql = 'UPDATE ' . $this->mod_globals->OptionTableName . " set kind=?, name=?, value=? " .
                "where option_id=?";
			$res = $this->mod_globals->DBHandle->Execute($sql, array($this->Kind, $this->Name,
                $this->Value, $this->OptionId));
            }
            
        return $res;
    }

    function Delete()
    {
		if ($this->OptionId == -1)
		  {
		  return false;
		  }
		$sql = 'DELETE FROM ' . $this->mod_globals->OptionTableName . " where option_id=?";
		$res = $this->mod_globals->DBHandle->Execute($sql, array($this->OptionId));
		return true;
    }

}

?>
