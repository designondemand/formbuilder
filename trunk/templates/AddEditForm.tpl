{if $message != ''}<h4>{$message}</h4>{/if}
{$formstart}{$formid}{$hidden}{$tab_start}{$maintab_start}

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
	<div class="pageoverflow">
		<p class="pagetext">{$title_form_displaytype}:</p>
		<p class="pageinput">{$input_form_displaytype}</p>
	</div>
{if $adding==0}
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
               	<tr><td colspan="8" class="row1">{$add_field_link}</td></tr>
				</tbody>
            </table>
        </div>
     </div>
{/if}

{$tab_end}{$additionaltab_start}

	<div class="pageoverflow">
		<p class="pagetext">{$title_redirect_page}:</p>
		<p class="pageinput">{$input_redirect_page}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$title_form_submit_button}:</p>
		<p class="pageinput">{$input_form_submit_button}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$title_submit_button_safety}:</p>
		<p class="pageinput">{$input_submit_button_safety}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$title_form_next_button}:</p>
		<p class="pageinput">{$input_form_next_button}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$title_form_prev_button}:</p>
		<p class="pageinput">{$input_form_prev_button}</p>
	</div>
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
{$tab_end}
{$tabletab_start}
	<div class="pageoverflow">
		<p class="pagetext">{$title_title_position}:</p>
		<p class="pageinput">{$input_title_position}</p>
	</div>

{$tab_end}
{$templatetab_start}
	<div class="pageoverflow">
		<p class="pagetext">{$title_form_template}:</p>
		<p class="pageinput">{$input_form_template}</p>
	</div>
{$tab_end}
{$tabs_end}
	<div class="pageoverflow">
		<p class="pagetext">&nbsp;</p>
		<p class="pageinput">{$save_button}{$submit_button}</p>
	</div>
{$form_end}