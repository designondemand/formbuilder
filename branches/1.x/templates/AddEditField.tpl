{$start_form}{$fb_hidden}{$op}{$tab_start}
{$maintab_start}

		<div class="pageoverflow">
			<p class="pagetext">{$mod->Lang('title_field_type')}:</p>
			<div class="pageinput">{$field_type}</div>
		</div>
	
	{foreach from=$mainList item=entry}
		<div class="pageoverflow">
			<p class="pagetext">{$entry->title}:</p>
			<div class="pageinput">{$entry->input}</div>
			{if $entry->help != ''}<div class="inputhelp">{$entry->help}</div>{/if}
		</div>
	{/foreach}
	
{$tab_end}

{$advancedtab_start}
{if count($advList) > 0}

	{foreach from=$advList item=entry}
		<div class="pageoverflow">
			<p class="pagetext">{$entry->title}:</p>
			<div class="pageinput">{$entry->input}</div>
			{if $entry->help != ''}<div class="inputhelp">{$entry->help}</div>{/if}
		</div>
	{/foreach}
	
{else}

	<div class="pageoverflow">
		<p class="pagetext">{$mod->Lang('notice_select_type')}</p>
	</div>
		
{/if}
{$tab_end}

{$tabs_end}

{if isset($add) || isset($del)}
	<div class="pageoverflow">
		<p class="pagetext">&nbsp;</p>
		<p class="pageinput">{$opt_num}{$add}{$del}</p>
	</div>
{/if}

	<div class="pageoverflow">
		<p class="pagetext">&nbsp;</p>
		<p class="pageinput">{$submit}{$cancel}</p>
	</div>
{$end_form}
