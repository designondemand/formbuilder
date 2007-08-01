{* DEFAULT FORM LAYOUT / pure CSS *}
{$fb_form_header}
{if $fb_form_done == 1}
	{* This first section is for displaying submission errors *}
	{if $fb_submission_error}
		<div class="error_message">{$fb_submission_error}</div>
		{if $fb_show_submission_errors}
			<div class="error">
			<ul>
			{foreach from=$fb_submission_error_list item=thisErr}
				<li>{$thisErr}</li>
			{/foreach}
			</ul>
		</div>
		{/if}
	{/if}
{else}
	{* this section is for displaying the form *}
	{* we start with validation errors *}
	{if $fb_form_has_validation_errors}
		<div class="error_message">
		<ul>
		{foreach from=$fb_form_validation_errors item=thisErr}
			<li>{$thisErr}</li>
		{/foreach}
		</ul>
		</div>
	{/if}
	{if $captcha_error}
		<div class="error_message">{$captcha_error}</div>
	{/if}

	{* and now the form itself *}
	{$fb_form_start}
	{$fb_hidden}
	<div{if $css_class != ''} class="{$css_class}"{/if}>
	{if $total_pages gt 1}<span>{$title_page_x_of_y}</span>{/if}
	{foreach from=$fields item=entry}
		{if $entry->display == 1}
        	{strip}
         	{if $entry->needs_div == 1}
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
         	{/if}
         	{if $entry->hide_name == 0}
            	<label{if $entry->multiple_parts != 1} for="{$entry->input_id}"{/if}>{$entry->name}
            	{if $entry->required_symbol != ''}
               		{$entry->required_symbol}
            	{/if}
            	</label>
         	{/if}
         	{if $entry->multiple_parts == 1}
            	{section name=numloop loop=$entry->input}
               		{if $entry->label_parts == 1}
               			<div>{$entry->input[numloop]->input}&nbsp;{$entry->input[numloop]->name}</div>
               		{else}
               			{$entry->input[numloop]->input}
               		{/if}
               		{if $entry->input[numloop]->op}{$entry->input[numloop]->op}{/if}
            	{/section}
         	{else}
            	{if $entry->smarty_eval == '1'}{eval var=$entry->input}{else}{$entry->input}{/if}
         	{/if}
         	{if $entry->valid == 0} &lt;--- {$entry->error}{/if}
         	{if $entry->needs_div == 1}
            	</div>
         	{/if}
         	{/strip}
     	{/if}
	{/foreach}
	{if $has_captcha == 1}
		<div class="captcha">{$graphic_captcha}{$title_captcha}<br />{$input_captcha}</div>
	{/if}
	<div class="submit">{$prev}{$submit}</div>
	</div>
	{$fb_form_end}
{/if}
{$fb_form_footer}