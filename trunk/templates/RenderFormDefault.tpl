{literal}
<!-- below, you'll find the "standard CSS template" for displaying FormBuilder Forms
       You can edit it to make your form layout look any way you'd like.
       
       To make the form work, you'll need to always include the {$hidden} and {$submit}
       tags.
       
       You can access your form fields either using the $fields array, as demonstrated below,
       or by directly accessing fields by their names (e.g., {$myfield->input} )
       
       Each field has the following attributes:
			field->display                = 1 if the field should be displayed, 0 otherwise
			field->required              = 1 if the field is required, 0 otherwise
			field->required_symbol = the symbol for required fields
			field->css_class            = the CSS class specified for this field
			field->valid                   = 1 if this field has passed validation, 0 otherwise
			field->hide_name         = 1 if the field name should be hidden, 0 otherwise
			field->name                 = the field's name
			field->input                  = the field's input control (e.g., the input field itself)
			field->input_id              = the id of the field's input (useful for <label for="">)
			field->type                   = the field's data type
			field->multiple_parts	   = 1 if the field->input is actually a collection of controls
			
	   In certain cases, field->input is actually an array of objects rather than an input. This
	   happens, for example, in CheckBoxGroups or RadioButtonGroups. For them, you
	   can iterate through field->input->name, filed->input->title, and
	   field->input->inputs. The difference between "name" and "title" is that
	   "name" is wrapped in the appropriate html label tags, while "title" is
	   just raw text.
       
       Additional smarty variables that you can use include:
       {$total_pages}           - number of pages for multi-page forms
       {$this_page}              - number fo the current page for multi-page forms
       {$title_page_x_of_y} - displays "page x of y" for multi-page forms
       {$css_class}               - CSS Class for the form
       {$name_as_id}  		  - 1 if the user opted to have field IDs set to the field name class, 0 otherwise
       {$form_name}           - Form name
       {$form_id}                 - Form Database ID
       {$prev}                      - "Back" button for multipart forms
       Dunno why you'd want some of those, but there you go...

-->
{/literal}

{$hidden}{assign var="cols" value="3"}
<div{if $css_class != ''} class="{$css_class}"{/if}>
{if $total_pages gt 1}<tr><td colspan="2">{$title_page_x_of_y}</td></tr>{/if}
{foreach from=$fields item=entry}
	  {if $entry->display == 1}
	    	{strip}
	    	<div
	    	{if $entry->required == 1 || $entry->css_class != ''} class=" 
	    		{if $entry->required == 1}
	    			required
	    		{/if}
	    		{if $entry->required == 1 && $entry->css_class != ''} {/if}
	    		{if $entry->css_class != ''}
	    			{$entry->css_class}
	    		{/if}
	    		"
	    	{/if}
	    	>
	    	{if $entry->hide_name == 0}
	    		<label for="{$entry->input_id}">{$entry->name}</label>
	    		{if $entry->required_symbol != ''}
	    			{$entry->required_symbol}
	    		{/if}
	    	{/if}
	    	{if $entry->multiple_parts == 1}
    		<table>
					<tr>
				{section name=numloop loop=$entry->input}
	    			<td>{$entry->input[numloop]->input}&nbsp;{$entry->input[numloop]->name}</td>
	    			       {if not ($smarty.section.numloop.rownum mod $cols)}
                				{if not $smarty.section.numloop.last}
                        		</tr><tr>
                				{/if}
        					{/if}
       				{if $smarty.section.numloop.last}
                		{math equation = "n - a % n" n=$cols a=$entry->input|@count assign="cells"}
                		{if $cells ne $cols}
                			{section name=pad loop=$cells}
                        		<td>&nbsp;</td>
                			{/section}
               		 	{/if}
                		</tr>
        			{/if}
	    		{/section}
	    		</table>
	    	{else}
	    		{$entry->input}
	    	{/if}
	    	{if $entry->valid == 0} &lt;--- {/if}
	    	</div>
	    	{/strip}
	  {/if}
{/foreach}
<div class="submit">{$prev}{$submit}</div>
</div>
