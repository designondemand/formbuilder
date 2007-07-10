<script type="text/javascript">
{literal}
function getTemplate()
	{		
	var selector = document.getElementById('fb_template_load');
	if (selector)
		{
		var templ = selector[selector.selectedIndex].value;

		if (templ.length > 0 && confirm('{/literal}{$template_are_you_sure}{literal}'))
			{
			var url = '{/literal}{$mod_path}{literal}';
			var pars = '{/literal}{$mod_param}{literal}&m1_tid='+templ;
		
			var myAjax = new Ajax.Request(
				url, 
				{
					method: 'get', 
					parameters: pars,
					onFailure: reportError,
					onComplete: replaceTemplate
				});		
			}
		}
	}
function reportError(request)
	{
		alert('Sorry. There was an error.');
	}

function replaceTemplate(originalRequest)
	{
		//put returned template in the textarea
		$('fb_form_template').value = originalRequest.responseText;
	}

{/literal}
</script>
{if $message != ''}<h4>{$message}</h4>{/if}
{$formstart}{$formid}{$fb_hidden}{$tab_start}{$maintab_start}
<fieldset class="module_fb_fieldset"><legend>{$title_form_main}</legend>
	<div class="pageoverflow">
		<p class="pagetext">{$title_form_name}:</p>
		<p class="pageinput">{$input_form_name}</p>
	</div>

{if $adding == 0}
	<div class="pageoverflow">
		<p class="pagetext">{$title_form_status}:</p>
		<p class="pageinput">{if $hasdisposition == 1}{$text_ready}{else}{$link_notready}{/if}</p>
	</div>
{/if}
	<div class="pageoverflow">
		<p class="pagetext">{$title_form_alias}:</p>
		<p class="pageinput">{$input_form_alias}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$title_form_css_class}:</p>
		<p class="pageinput">{$input_form_css_class}</p>
	</div>
</fieldset>
{if $adding==0}
<fieldset><legend>{$title_form_fields}</legend>
	{if $fastadd==1}
		<div class="pageoverflow">
			<p class="pagetext">{$title_fastadd}</p>
			<div class="pageinput">
				{$input_fastadd}
			</div>
		</div>
	{/if}
	<div class="pageoverflow">
		<p class="pagetext">{$title_form_fields}</p>
		<div class="pageinput">
			<table class="module_fb_table">
                <thead><tr><th>{$title_field_name}</th>
                	<th>{$title_field_type}</th>
                	<th>{$title_field_required_abbrev}</th>
                	<th>{$title_information}</th>
                    <th colspan="2">{$title_order}</th>
                    <th>&nbsp;</th><th>&nbsp;</th></tr>
				</thead>
				<tbody>
				{foreach from=$fields item=entry}
					<tr class="{$entry->rowclass}" onmouseover="this.className='{$entry->rowclass}hover';" onmouseout="this.className='{$entry->rowclass}';">
					<td>{$entry->name}</td>
					<td>{$entry->type}</td>
					<td>{$entry->disposition}</td>
					<td>{$entry->field_status}</td>
					<td>{$entry->up}</td>
					<td>{$entry->down}</td>
					<td>{$entry->editlink}</td>
					<td>{$entry->deletelink}</td>
					</tr>
				{/foreach}
               	<tr><td colspan="8" class="row2">&nbsp;</td></tr>
               	<tr><td colspan="8" class="row1">{$add_field_link} &nbsp; {$order_field_link}</td></tr>
				</tbody>
            </table>
        </div>
     </div>
</fieldset>
{/if}
{$tab_end}{$submittab_start}
	<fieldset>
	<div class="pageoverflow">
		<p class="pageinput">{$title_submit_help}</p>
	</div>
	</fieldset>
<fieldset class="module_fb_fieldset"><legend>{$title_submit_actions}</legend>
	<div class="pageoverflow">
		<p class="pagetext">{$title_submit_action}:</p>
		<p class="pageinput">{$input_submit_action}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$title_redirect_page}:</p>
		<p class="pageinput">{$input_redirect_page}</p>
	</div>
</fieldset>
<fieldset class="module_fb_fieldset"><legend>{$title_submit_labels}</legend>
	<div class="pageoverflow">
		<p class="pagetext">{$title_submit_button_safety}:</p>
		<p class="pageinput">{$input_submit_button_safety}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$title_form_submit_button}:</p>
		<p class="pageinput">{$input_form_submit_button}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$title_form_next_button}:</p>
		<p class="pageinput">{$input_form_next_button}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$title_form_prev_button}:</p>
		<p class="pageinput">{$input_form_prev_button}</p>
	</div>
</fieldset>
{$tab_end}{$symboltab_start}
	<div class="pageoverflow">
		<p class="pagetext">{$title_form_required_symbol}:</p>
		<p class="pageinput">{$input_form_required_symbol}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$title_form_unspecified}:</p>
		<p class="pageinput">{$input_form_unspecified}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$title_list_delimiter}:</p>
		<p class="pageinput">{$input_list_delimiter}</p>
	</div>
{$tab_end}{$captchatab_start}
{if $captcha_installed}
	<div class="pageoverflow">
		<p class="pagetext">{$title_use_captcha}:</p>
		<p class="pageinput">{$input_use_captcha}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$title_title_user_captcha}:</p>
		<p class="pageinput">{$input_title_user_captcha}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$title_user_captcha_error}:</p>
		<p class="pageinput">{$input_title_user_captcha_error}</p>
	</div>
{else}
	<div class="pageoverflow">
		<p class="pageinput">{$title_install_captcha}</p>
	</div>
{/if}
{$tab_end}
{$templatetab_start}
	<div class="pageoverflow">
		<p class="pagetext">{$title_load_template}:</p>
		<p class="pageinput">{$input_load_template}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$title_form_template}:</p>
		<p class="pageinput">{$input_form_template}</p>
	</div>
	<div class="pageoverflow">
		<p class="pageinput">{$help_template_variables}</p>
	</div>
{$tab_end}
{$submittemplatetab_start}
	<fieldset>
	<div class="pageoverflow">
		<p class="pageinput">{$title_submit_response_help}</p>
	</div>
	</fieldset>
	<div class="pageoverflow">
		<p class="pagetext">{$title_submit_response}:</p>
		<p class="pageinput">{$input_submit_response}</p>
		{$help_submit_response}
	</div>
{$tab_end}
{$tabs_end}
	<div class="pageoverflow">
		<p class="pagetext">&nbsp;</p>
		<p class="pageinput">{$save_button}{$submit_button}</p>
	</div>
{$form_end}
