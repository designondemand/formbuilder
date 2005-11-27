{if $message != ''}<h4>{$message}</h4>{/if}
{$start_form}{$hidden}{$op}{$tab_start}
{$maintab_start}
	<div class="pageoverflow">
		<p class="pagetext">{$title_field_name}:</p>
		<p class="pageinput">{$input_field_name}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$title_field_type}:</p>
		<p class="pageinput">{$input_field_type}</p>
	</div>
{if $type_set == 1}
	{if $requirable == 1}
		<div class="pageoverflow">
			<p class="pagetext">{$title_field_required}:</p>
			<p class="pageinput">{$input_field_required}</p>
		</div>
	{/if}
		<div class="pageoverflow">
			<p class="pagetext">{$title_field_validation}:</p>
			<p class="pageinput">{$input_field_validation}</p>
		</div>
	{foreach from=$mainList item=entry}
		<div class="pageoverflow">
			<p class="pagetext">{$entry->title}:</p>
			<p class="pageinput">{$entry->input}</p>
			{if $entry->help != ''}{$entry->help}{/if}
		</div>
	{/foreach}
{/if}
{$tab_end}
{$advancedtab_start}
{if $type_set == 1}
	{if $displayinform == 1}
		<div class="pageoverflow">
			<p class="pagetext">{$title_hide_label}:</p>
			<p class="pageinput">{$input_hide_label}</p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext">{$title_field_css_class}:</p>
			<p class="pageinput">{$input_field_css_class}</p>
		</div>
	{/if}
	{foreach from=$advList item=entry}
		<div class="pageoverflow">
			<p class="pagetext">{$entry->title}:</p>
			<p class="pageinput">{$entry->input}</p>
			{if $entry->help != ''}{$entry->help}{/if}
		</div>
	{/foreach}
{else}
			<div class="pageoverflow">
			<p class="pagetext">{$notice_select_type}</p>
			</div>
{/if}
{$tab_end}
{$tabs_end}
	<div class="pageoverflow">
		<p class="pagetext">&nbsp;</p>
		<p class="pageinput">{$submit}</p>
	</div>
{$end_form}
