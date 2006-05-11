<?php
// English_US Localization File
$lang['friendlyname'] = 'Form Builder';

// field types
$lang['field_type_']='Field Type Not Set';
$lang['field_type_TextField']='Text Input';
$lang['field_type_TextAreaField']='Text Area';
$lang['field_type_CheckboxField']='Check Box';
$lang['field_type_CheckboxGroupField']='Check Box Group';
$lang['field_type_PulldownField']='Pulldown';
$lang['field_type_StatePickerField']='U.S. State Picker';
$lang['field_type_CountryPickerField']='Country Picker';
$lang['field_type_DatePickerField']='Date Picker';
$lang['field_type_RadioGroupField']='Radio Button Group';
$lang['field_type_DispositionDirector']='*Email Results Based on Pulldown';
$lang['field_type_DispositionEmail']='*Email Results to set Address(es)';
$lang['field_type_DispositionEmailConfirmation']='*Validate-via-Email Address';
$lang['field_type_DispositionFile']='*Write Results to Flat File';
$lang['field_type_DispositionDatabase']='*Store Results in Database';
$lang['field_type_PageBreakField']='-Page Break';
$lang['field_type_FileUploadField']='File Upload';
$lang['field_type_FromEmailAddressField']='Email "From Address" Field';
$lang['field_type_FromEmailNameField']='Email "From Name" Field';
$lang['field_type_StaticTextField']='-Static Text';


// validation types
$lang['validation_none']='No Validation';
$lang['validation_numeric']='Numeric';
$lang['validation_integer']='Integer';
$lang['validation_email_address']='Email Address';
$lang['validation_must_check']='Must Be Checked';
$lang['validation_regex_match']='Match Regular Expression';
$lang['validation_regex_nomatch']='Doesn\'t match Regular Expression';

// validation error messages and other alerts
$lang['please_enter_a_value']='Please enter a value for "%s"';
$lang['please_enter_a_number']='Please enter a number for "%s"';
$lang['please_enter_valid'] = 'Please enter a valid entry for "%s"';
$lang['please_enter_an_integer']='Please enter an integer value for "%s"';
$lang['please_enter_an_email']='Please enter a valid email address for "%s"';
$lang['not_valid_email']='"%s" does not appear to be a valid email address!';
$lang['please_enter_no_longer']='Please enter a value that is no longer than %s characters';
$lang['title_list_delimiter'] = 'Character to use as delimiter in results that return more than one value';
$lang['you_need_permission']='You need the "%s" permission to perform that operation.';
$lang['lackpermission']='Sorry! You don\'t have adequate privileges to access this section.';
$lang['field_order_updated']='Field order updated.';
$lang['form_deleted']='Form deleted.';
$lang['field_deleted']='Field deleted.';
$lang['configuration_updated']='Configuration Updated.';
$lang['you_must_check']='You must check "%s" in order to continue.';
$lang['must_specify_one_destination']='You need to specify at least one destination address!';
$lang['are_you_sure_delete_form']='Are you sure you want to delete the form %s?';
$lang['are_you_sure_delete_field']='Are you sure you want to delete the field %s?';
$lang['notice_select_type']='Advanced options are not available until the field type has been set.';
$lang['field_name_in_use']='The field name "%s" is already in use. Please use unique field names.';

// abbreviations, verbs, and other general terms
$lang['abbreviation_length']='Len: %s';
$lang['boxes']='%s boxes';
$lang['options']='%s options';
$lang['text_length']='%s chars.';
$lang['order']='Order';
$lang['unspecified']='[unspecified]';
$lang['added']='added';
$lang['updated']='updated';
$lang['select_one']='Select One';
$lang['select_type']='Select Type';
$lang['to']='To';
$lang['recipients']='recipients';
$lang['destination_count'] = '%s destinations';
$lang['save']='Save';
$lang['add']='Add';
$lang['update']='Update';
$lang['save_and_continue']='Save and Continue Editing';
$lang['information']='Information';
$lang['automatic']='Automatic';
$lang['forms']='Forms';
$lang['form']='Form %s';
$lang['configuration']='Configuration';
$lang['tab_main']='Main';
$lang['tab_additional']='Form Settings';
$lang['tab_advanced']='Advanced Settings';
$lang['tab_tablelayout']='Table-based Layout Options';
$lang['tab_templatelayout'] = 'Template Layout Options';
$lang['field_requirement_updated'] = 'Field required state updated.';
$lang['maximum_size']='Max. Size';
$lang['permitted_extensions']='Extensions';
$lang['permitted_filetypes']='Allowed file types';
$lang['file_too_large']='Uploaded file is too large! Maximum size is:';
$lang['illegal_file_type']='This type of file may not be uploaded. Please check that the extension is correct.';

$lang['uninstalled'] = 'Module uninstalled.';
$lang['installed'] = 'Module version %s installed.';
$lang['upgraded'] = 'Module upgraded to version %s.';

$lang['button_previous'] = 'Back...';
$lang['button_submit'] = 'Submit Form';
$lang['button_continue'] = 'Continue...';

$lang['value_checked'] = 'Checked';
$lang['value_unchecked'] = 'Unchecked';

$lang['add_options'] = 'Add More Options';
$lang['delete_options'] = 'Delete Marked Options';
$lang['add_checkboxes'] = 'Add More Checkboxes';
$lang['delete_checkboxes'] = 'Delete Marked Checkboxes';
$lang['add_address'] = 'Add Another Address';
$lang['delete_address'] = 'Delete Marked Addresses';
$lang['add_destination'] = 'Add Another Destination';
$lang['delete_destination'] = 'Delete Marked Destinations';

// Field Attribute Titles
$lang['title_maximum_length']='Maximum Length';
$lang['title_checkbox_label']='Checkbox label';
$lang['title_checked_value']='Value when checked';
$lang['title_unchecked_value']='Value when not checked.';
$lang['title_checkbox_details']='Checkbox Group Details';
$lang['title_delete'] = 'Delete?';
$lang['title_select_one_message']='"Select One" Text';
$lang['title_selection_subject']='Selection Subject';
$lang['title_select_default_country']='Default Selection';
$lang['title_select_default_state']='Default Selection';
$lang['title_option_name']="Option Name";
$lang['title_option_value']="Value Submitted";
$lang['title_pulldown_details']='Pulldown Options';
$lang['title_destination_address']='Destination Email Address';
$lang['title_email_from_name']='"From name" for email';
$lang['title_relaxed_email_regex']='Use relaxed email validation';
$lang['title_relaxed_regex_long']='Use relaxed email address validation (e.g., allow "x@y" instead of requiring "x@y.tld")';
$lang['title_email_from_address']='"From address" for email';
$lang['title_email_encoding']='Email character set encoding';
$lang['title_director_details']='Pulldown-based Emailer Details';
$lang['title_file_name']='File Name';
$lang['title_email_subject']='Email Subject Line';
$lang['title_form_name']='Form Name';
$lang['title_form_status']='Form Status';
$lang['title_ready_for_deployment']='Ready for Deployment';
$lang['title_not_ready1']='Not Ready';
$lang['title_redirect_page']='Page to redirect to after form submission';
$lang['title_not_ready2']='Please add a field to the form so that the user\'s input gets handled. You can';
$lang['title_not_ready_link']='use this shortcut';
$lang['title_form_alias']='Form Alias';
$lang['title_form_fields']='Form Fields';
$lang['title_field_name']='Field Name';
$lang['title_radiogroup_details']='Radio Button Group Details';
$lang['title_field_type']='Field Type';
$lang['title_not_ready3']='to create a form handling field.';
$lang['title_form_alias']='Form Alias';
$lang['title_add_new_form']='Add New Form';
$lang['title_show_version']='Show Form Builder Version?';
$lang['title_show_version_long']='This will embed your installed version number of Form Builder module in a comment, to aid in debugging';
$lang['title_add_new_field']='Add New Field';
$lang['title_form_submit_button']='Form Submit Button Text';
$lang['title_form_next_button']='Form "Next" Button Text (used for multipage forms)';
$lang['title_form_prev_button'] = 'Form "Previous" Button Text (used for multipage forms)';
$lang['title_field_validation']='Field Validation';
$lang['title_form_css_class']='CSS Class for this form';
$lang['title_field_css_class']='CSS Class for this field';
$lang['title_form_required_symbol']='Symbol to mark required Fields';
$lang['title_field_required']='Required';
$lang['title_field_required_long']='Require a response for this Field';
$lang['title_hide_label']='Hide Label';
$lang['title_hide_label_long']='Hide this field\'s name on Form';
$lang['title_text']='Static text to display';
$lang['title_field_regex']='Validation Regex';
$lang['no_default']='No Default';
$lang['redirect_after_approval']='Page to redirect after approval';
$lang['title_regex_help']='This regular expression will only be used if "validation type" is set to a regex-related option. Include a full Perl-style regex, including the start/stop slashes and flags (e.g., "/image\.(\d+)/i")';
$lang['title_field_required_abbrev']='Req\'d';
$lang['title_title_position']='Field Title Position (non-CSS Layout only)';
$lang['title_table_layout_left']='Title on Left';
$lang['title_table_layout_above']='Title Above';
$lang['title_hide_errors']='Hide Errors';
$lang['title_form_displaytype'] = 'Form Display Type';
$lang['title_hide_errors_long']='Prevent debug / error messages from being seen by users.';
$lang['title_email_template']='Email Template';
$lang['title_maximum_size']='Maximum upload file size (kilobytes)';
$lang['title_permitted_extensions']='Permitted Extensions';
$lang['title_permitted_extensions_long']='Enter a comma-separated list, excluding the dot (e.g., "jpg,gif,jpeg"). Spaces will be ignored. Leaving this blank means there will be no restrictions.';
$lang['title_show_limitations']='Display restrictions?';
$lang['title_show_limitations_long']='Display any size and extension restrictions with the upload field?';
$lang['title_form_template']='Template to use to Display Form';
$lang['title_page_x_of_y'] = 'Page %s of %s';
$lang['title_no_advanced_options']='Field has no advanced options.';
$lang['title_form_unspecified']='Text to return for unspecified field values';
$lang['title_enable_fastadd_long']='Enable fast field adding pulldown for forms?';
$lang['title_enable_fastadd']='Enable fast field add pulldown?';
$lang['title_fastadd']='Fast field adder';

$lang['help_variables_for_template']='Variables For Template';
$lang['help_submission_date']='Date of Submission';
$lang['help_server_name']='Your server';
$lang['help_sub_source_ip']='IP address of person using form';
$lang['help_sub_url']='URL of page containing form';
$lang['help_other_fields']='Other fields will be available as you add them to the form.';
$lang['help_date_format']='See <a href="http://www.php.net/manual/en/function.date.php" target=_NEW>the PHP Manual</a> for formatting help.';
$lang['help_variable_name']='Variable';
$lang['help_form_field']='Field Represented';
$lang['link_back_to_form']='&#171; Back to Form';
$lang['title_create_sample_template']='Create Sample Template';
$lang['title_create_sample_header']='Create Sample Header';
$lang['help_tab_symbol']='a tab character';
$lang['title_file_template']='Template for one line of output file';
$lang['title_file_header']='Template for the header of output file';
$lang['title_confirmation_url']='URL to click for form confirmation';
$lang['no_referrer_info']='No HTTP_REFERER info available';
$lang['validation_param_error']='Validation Parameter Error. Please make sure you copy the URL from your email correctly!';
$lang['validation_response_error']='Validation Repsonse Error. Please make sure you copy the URL from your email correctly!';
$lang['validation_no_field_error']='Validation Repsonse Error. No email validation field in this form!';

$lang['title_date_format']='Date Format (standard <a href="http://www.php.net/manual/en/function.date.php">PHP Date Formats</a>)';
$lang['title_use_wysiwyg']='Use WYSIWYG editor for text Area?';

$lang['disptype_table']='Table/CSS';
$lang['disptype_css']='Pure CSS';
$lang['disptype_template']='Custom Template';

$lang['admindesc']='Add, edit and manage interactive Forms';

$lang['date_january']='January';
$lang['date_february']='February';
$lang['date_march']='March';
$lang['date_april']='April';
$lang['date_may']='May';
$lang['date_june']='June';
$lang['date_july']='July';
$lang['date_august']='August';
$lang['date_september']='September';
$lang['date_october']='October';
$lang['date_november']='November';
$lang['date_december']='December';

// Form Submission Headers
$lang['submit_error']='FormBuilder submit error: %s';
$lang['upload_attach_error'] = 'Upload/Attachment error on file %s (tmp_name: %s, of type %s)';
$lang['submission_error_file_lock'] = 'Error. Unable to obtain lock for file.';

$lang['email_default_template'] = "FormBuilder Submission\n";
$lang['email_template_not_set'] = '<br/>Email Template not yet set!';      
$lang['missing_cms_mailer'] = 'FormBuilder: Cannot find required module CMSMailer!';  		
$lang['user_approved_submission']='User approved submission %s from %s';
  		
// post-install message
$lang['post_install']="
<p>Make sure to set the \"Modify Forms\" permissions
on users who will be administering feedback forms. Also, if you'll be emailing form
results, be sure to update the Configuration appropriately.</p>
<p>Please be aware that a feedback form should not be active (e.g., usable by the public) while
you are still editing the form. You should create the form, and place the tag into an active
content page only when you have finished editing. Otherwise, erroneous results could be returned.</p>
<p>Additionally, this version does not support parallel editing of forms. Please take care that
only one admin is editing a given form at a given time.</p>";

$lang['help'] = "<h3>What Does This Do?</h3>
<p>The Form Builder Module allows you to create forms for use by other modules such as the FeedbackForm module. These forms may be inserted
into templates and/or content pages. Feedback forms may contain many kinds of inputs, and may have
validation applied to these inputs. The results of these forms may be handled in a variety of ways.</p>

<h3>How Do I Use it?</h3>
<P>Install it, and poke around the menus. Play with it. Try creating forms, and adding them to your content.
If you get stuck, chat with me on the #cms IRC channel, post to the forum, send me email, or, if you're
really desperate, read the rest of this page.</P>

<h3>How Do I Create a Form</h3>
<p>In the CMS Admin Menu, you should get a new menu item called FeedbackForm. Click on this. On the page
that gets shown, there are options (at the bottom of the list of Forms) to Add a New Form or Modify
Configuration.</p>

<h3>Adding a Form to a Page</h3>
<p>In the main FeedbackForm admin page, you can see an example of the tag used to display each form. It looks
something like {cms_module module=\"FeedbackForm\" form=\"feedback_form_example\"}</p>
<p>By copying this tag into the content of a page, or into a template, will cause that form to be displayed.
In theory, you can have multiple forms on a page if you really want to. Be careful when pasting the tag
into a page's content if you use a WYSIWYG editor such as TinyMCE or HTMLArea. These editors may stealthily
change the quote marks (\") into HTML entities (&amp;quot;), and the forms will not show up. Try using
single quotes (') or editing the HTML directly.

<h3>Adding Fields to a Form</h3>
<p>By clicking on a Form's name, you enter the Form Edit page. You will see a number of options for the form
(like what you want the text of the submit button to say, what message should be displayed to the user when
they submit the form, etc). There is also a list of fields that make up the form. The types of fields that
are currently supported fit into four groups: standard input fields, display control fields, email-only input
fields, and form result handling fields (also
called Form Dispositions in places):</p>
<ul>
<li>Standard Input Fields - these are inputs that allow entry of typical form elements.</li>
<li>Display Control Fields - these input control how the user will see the display of the form.</li>
<li>Email-only Input Fields - these are inputs that allow entry of typical form elements, but apply only
for form dispositions that send the results via email.</li>
<li>Special Purpose Fields - these inputs are used for interfacing programmatically with other modules.</li>
<li>Form Dispositions - These determine what happens when the user
submits the form; for each result handling field, some method of transmitting, saving, or emailing the
form contents takes place. A form may have multiple form dispositions.</li>
</ul>
<p>Form fields are assigned names. These names identify the field, not only on the screen as labels for the user,
but in the data when it's submitted so you know what the user is responding to. Phrasing the name like a question
is a handy way of making it clear to the user what is expected. Similarly, some fields have both Names and Values.
The Names are what gets shown to the user; the Value is what gets saved or transmitted when the user submits
the form. The Values are never seen by the user, nor are they visible in the HTML, so it's safe to use for
email addresses and such.</p>
<p>Some fields can have multiple values, or multiple name/value pairs. When you first create such a field,
there may not be sufficient inputs for you to specify all the values you want. To get more space for inputting
these values, simply save the field, and then click on its name to edit it again. Every time you edit such a
field, you will receive more inputs.</p>
<p>Fields can be assigned validation rules, which vary according to the type of the field. These rules help
ensure that the user enters valid data. They may also be
separately marked \"Required\", which will force the user to enter a response.</p>
<p>Fields also may be assigned a CSS class. This simply wraps the input in a div with that class, so as to allow
customized layouts. To use this effectively, you may need to \"view source\" on the generated form, and then
write your CSS.</p>
<ul>
<li>Standard Inputs
<ul><li>Text Input. This is a standard text field. You can limit the length, and apply various validation
functions to the field.</li>
<li>Text Area. This is a big, free-form text input field.</li>
<li>Checkbox. This is a standard check box. If you turn on validation, you can require that the user checks
the box. This is useful for forms where the user is obligated to agree to something in order to submit
the form.</li>
<li>Checkbox Group. This is a collection of checkboxes. The only difference between this input and a
collection of Checkbox inputs is that they are presented as a group, with one name, and can have a validation
function requiring that you check one or more of the boxes in the group.</li>
<li>Radio Group. This is a collection of radio buttons. Only one of the group may be selected by the user.</li>
<li>Pulldown. This is a standard pulldown menu. It's really conceptually the same thing as a radio button
group, but is better when there are a large number of options.</li>
<li>State. This is a pulldown listing the States of the U.S.</li>
<li>Countries. This is a pulldown listing the Countries of the world (as of July 2005).</li>
<li>Date Picker. This is a triple pulldown allowing the user to select a date.</li>
</ul></li>

<li>Email-only Inputs
<ul><li>File Upload. This allows a user to upload a file, which will be included as an attachment to any email
disposition. File uploads do not currently work with the Flat File disposition method.</li>
<li>Email From Field. This allows users to provide their name and email address. The email generated when the
form gets handled will use this name and address in the \"From\" field.</li>
</ul></li>

<li>Display Control Fields<ul>
<li>Page Break. This allows you to split your feedback form into multiple pages. Each page is
independently validated. This is good for applications like online surveys.</li></ul></li>

<li>Special Purpose Fields<ul>
<li>Function Callback \"Input\". This allows programmers to access information from other modules (such as getting user name data from a user authentication module).</li></ul></li>

<li>Form Handling Inputs (Dispositions)
<ul><li>Email Results Based on Pulldown. This is useful for web sites where comments get routed based on
their subject matter, e.g., bugs get sent to one person, marketing questions to another person, sales requests
to someone else, etc. The pulldown is populated with the subjects, and each gets directed to a specific email
address. You set up these mappings in the when you create or edit a field of this type. If you use one of
these \"Director\" pulldowns, the user must make a selection in order to submit the
form. This input is part of the form the user sees, although the email addresses are not made visible nor
are they embedded in the HTML.</li>
<li>Email Results to set Address(es). This simply sends the form results to one or more email addresses that
you enter when you create or edit this type of field. This field and its name are not visible in the
form that the user sees. The email addresses are not made visible nor
are they embedded in the HTML.</li>
<li>Email Results to User-Selected Site User. This input is our first end-user suggestion! It creates a 
pulldown of usernames in the form. The usernames are other users who are registered with the site.
When the form is submitted, the data is sent to the user whose name was selected. When you create
a field of this type, you may select specific groups from which the userlist is created. This input is
part of the form the user sees, although the email addresses are not made visible nor are they embedded
in the HTML.</li>
<li>Write Results to Flat File. This takes the form results and writes them into a text file. You may
select the name of the file, and its format. You can choose from \"page\" format, which looks like the
emails that get sent by the other handlers, or you can choose a tab-delimited format useful for reading
into Excel or similar programs. If you choose tab-delimited, you can opt to have the file start with
a header row, which names the columns. These files are written to the \"output\" directory under the
module's installation directory.
</ul></li></li></ul>

<h3>Known Issues</h3>
<ul>
<li>File Upload Inputs may be used on multi-page feedback forms, <strong>but they must be on the last page</strong> of
the form.</li>
<li>File Upload \"maximum size\" cannot be larger than the value set in your php.ini file. This is not a bug -- this is a feature.</li>
<li>File Upload \"maximum size\" uses units called \"kilobytes\" that are 1000 bytes, not 1024 bytes.</li>
<li>File Uploads do not currently work with the Flat File disposition method.</li>
<li>File Uploads could allow users to submit offensive, obscene, or illegal material. It could also be used to transmit
dangerous files such as trojan-horses, viruses, or other security threats. Use File Uploads with caution and common sense.</li>
</ul>

<h3>Troubleshooting</h3>
<ol><li> First step is to check you're running CMS 0.10.x or later.</li>
<li> Second step is to read and understand the caveat about WYSIWYG editors up in the
section <em>Adding a Form to a Page</em>.</li>
<li> Just mess around and try clicking on links and icons and stuff. See what happens.</li>
<li> Last resport is to email me or catch me on IRC and we can go from there.</li>
</ol> 
<p>This may no longer be such an early version, but it is probably still buggy. While I've done all I can
to make sure no egregious bugs have crept in, I have to admit that during testing, this program
revealed seven cockroaches, two earwigs, a small family of aphids, and a walking-stick insect. It also
ate the neighbor's nasty little yap dog, for which I was inappropriately grateful.</p>
<p>The final release will include bug fixes, documentation, and unconditional love.</p>
<h3>Support</h3>
<p>This module does not include commercial support. However, there are a number of resources available to help you with it:</p>
<ul>
<li>For the latest version of this module, FAQs, or to file a Bug Report or buy commercial support, please visit SjG's
module homepage at <a href=\"http://www.cmsmodules.com\">CMSModules.com</a>.</li>
<li>Additional discussion of this module may also be found in the <a href=\"http://forum.cmsmadesimple.org\">CMS Made Simple Forums</a>.</li>
<li>The author, SjG, can often be found in the <a href=\"irc://irc.freenode.net/#cms\">CMS IRC Channel</a>.</li>
<li>Lastly, you may have some success emailing the author directly.</li>  
</ul>
<p>As per the GPL, this software is provided as-is. Please read the text
of the license for the full disclaimer.</p>

<h3>Copyright and License</h3>
<p>Copyright &copy; 2006, Samuel Goldstein <a href=\"mailto:sjg@cmsmodules.com\">&lt;sjg@cmsmodules.com&gt;</a>. All Rights Are Reserved.</p>
<p>This module has been released under the <a href=\"http://www.gnu.org/licenses/licenses.html#GPL\">GNU Public License</a>. You must agree to this license before using the module.</p>
		
";

$lang['changelog'] = 		"
		    <ul>
		    <li>Version 0.1 -  2006. Initial Release</li>
		";


?>
