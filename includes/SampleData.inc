<?php

$id_form = $db->GenID($this->form_table_name."_seq");
$query = "INSERT INTO ".$this->form_table_name." (form_id, name, alias, submit_button_text, next_button_text, css_class, required_field_symbol, thanks_message) VALUES (".
    $id_form.",'Sample Form','sample_form','Submit Your Feedback', 'Continue...', 'feedbacktable','*','<p>Thanks for Your Feedback!</p>\r\n\r\n<p>Your comments and opinions will help me make a better module.</p>\r\n<br />\r\n__Samuel___')";
$dbresult = $db->Execute($query);

$id_field1 = $db->GenID($this->field_table_name."_seq");
$query = "INSERT INTO ".$this->field_table_name." (field_id,form_id,name,type,length,validation_type,css_class,required,order_by
) VALUES (".$id_field1.",".$id_form.",'Mail It!','DispositionEmail',-1,NULL,'',1,1)";
$dbresult = $db->Execute($query);

$id_field2 = $db->GenID($this->field_table_name."_seq");
$query = "INSERT INTO ".$this->field_table_name." (field_id,form_id,name,type,length,validation_type,css_class,required,order_by
) VALUES (".$id_field2.",".$id_form.",'I find using the Feedback Form Module','RadioGroupInput',-1,'none','feedbackRadio',0,2)";
$dbresult = $db->Execute($query);

$id_field3 = $db->GenID($this->field_table_name."_seq");
$query = "INSERT INTO ".$this->field_table_name." (field_id,form_id,name,type,length,validation_type,css_class,required,order_by
) VALUES (".$id_field3.",".$id_form.",'Features I\'d most like to see in Future Versions of the Feedback Module','CheckboxGroupInput',-1,'none','feedbackCheck',0,3)";
$dbresult = $db->Execute($query);

$id_field4 = $db->GenID($this->field_table_name."_seq");
$query = "INSERT INTO ".$this->field_table_name." (field_id,form_id,name,type,length,validation_type,css_class,required,order_by
) VALUES (".$id_field4.",".$id_form.",'Additional Comments, bug reports, etc.','TextAreaInput',-1,'none','feedbackTextarea',0,4)";
$dbresult = $db->Execute($query);

$id = $db->GenID($this->option_table_name."_seq");
$query = "INSERT INTO ".$this->option_table_name." VALUES (".$id.",".$id_field1.",".$id_form.",'address','feedbackform_cmsmodule@fogbound.net','feedbackform_cmsmodule@fogbound.net')";
$dbresult = $db->Execute($query);

$id = $db->GenID($this->option_table_name."_seq");
$query = "INSERT INTO ".$this->option_table_name." VALUES (".$id.",".$id_field2.",".$id_form.",'radio','Very Difficult','very difficult')";
$dbresult = $db->Execute($query);

$id = $db->GenID($this->option_table_name."_seq");
$query = "INSERT INTO ".$this->option_table_name." VALUES (".$id.",".$id_field2.",".$id_form.",'radio','Difficult','difficult')";
$dbresult = $db->Execute($query);

$id = $db->GenID($this->option_table_name."_seq");
$query = "INSERT INTO ".$this->option_table_name." VALUES (".$id.",".$id_field2.",".$id_form.",'radio','Average for a Module','average')";
$dbresult = $db->Execute($query);

$id = $db->GenID($this->option_table_name."_seq");
$query = "INSERT INTO ".$this->option_table_name." VALUES (".$id.",".$id_field2.",".$id_form.",'radio','Easy','easy')";
$dbresult = $db->Execute($query);

$id = $db->GenID($this->option_table_name."_seq");
$query = "INSERT INTO ".$this->option_table_name." VALUES (".$id.",".$id_field2.",".$id_form.",'radio','Very Easy','very easy')";
$dbresult = $db->Execute($query);

$id = $db->GenID($this->option_table_name."_seq");
$query = "INSERT INTO ".$this->option_table_name." VALUES (".$id.",".$id_field3.",".$id_form.",'checkbox','Poll Module based on this code','poll')";
$dbresult = $db->Execute($query);

$id = $db->GenID($this->option_table_name."_seq");
$query = "INSERT INTO ".$this->option_table_name." VALUES (".$id.",".$id_field3.",".$id_form.",'checkbox','New Handler: Store Form in Database','store to database')";
$dbresult = $db->Execute($query);

$id = $db->GenID($this->option_table_name."_seq");
$query = "INSERT INTO ".$this->option_table_name." VALUES (".$id.",".$id_field3.",".$id_form.",'checkbox','New Handler: Transmit results as XML to URL','send xml')";
$dbresult = $db->Execute($query);

$id = $db->GenID($this->option_table_name."_seq");
$query = "INSERT INTO ".$this->option_table_name." VALUES (".$id.",".$id_field3.",".$id_form.",'checkbox','New Handler: Email to Site Group','email to group')";
$dbresult = $db->Execute($query);

$id = $db->GenID($this->option_table_name."_seq");
$query = "INSERT INTO ".$this->option_table_name." VALUES (".$id.",".$id_field3.",".$id_form.",'checkbox','New Field Type: Rate Items (e.g., from 1-10)','rate items')";
$dbresult = $db->Execute($query);

$id = $db->GenID($this->option_table_name."_seq");
$query = "INSERT INTO ".$this->option_table_name." VALUES (".$id.",".$id_field3.",".$id_form.",'checkbox','New Field Type: Rank Items in Order','rank items')";
$dbresult = $db->Execute($query);

$id = $db->GenID($this->option_table_name."_seq");
$query = "INSERT INTO ".$this->option_table_name." VALUES (".$id.",".$id_field3.",".$id_form.",'checkbox','Other Validation types (e.g., \"check 3 or more\")','more validation')";
$dbresult = $db->Execute($query);

$id = $db->GenID($this->option_table_name."_seq");
$query = "INSERT INTO ".$this->option_table_name." VALUES (".$id.",".$id_field3.",".$id_form.",'checkbox','Javascript Validation','javascript validation')";
$dbresult = $db->Execute($query);

$id = $db->GenID($this->option_table_name."_seq");
$query = "INSERT INTO ".$this->option_table_name." VALUES (".$id.",".$id_field3.",".$id_form.",'checkbox','Admin interface for downloading flat file','flat file download')";
$dbresult = $db->Execute($query);

$id = $db->GenID($this->option_table_name."_seq");
$query = "INSERT INTO ".$this->option_table_name." VALUES (".$id.",".$id_field3.",".$id_form.",'checkbox','Bug Fixes','bug fixes')";
$dbresult = $db->Execute($query);

$id = $db->GenID($this->option_table_name."_seq");
$query = "INSERT INTO ".$this->option_table_name." VALUES (".$id.",".$id_field3.",".$id_form.",'checkbox','Provide sample style sheets','provide css')";
$dbresult = $db->Execute($query);

$id = $db->GenID($this->option_table_name."_seq");
$query = "INSERT INTO ".$this->option_table_name." VALUES (".$id.",".$id_field3.",".$id_form.",'checkbox','Better/more help for admin','more help')";
$dbresult = $db->Execute($query);

?>
