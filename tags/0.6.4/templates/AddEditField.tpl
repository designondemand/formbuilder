{if $message != ''}<div class="pagemcontainer"><p class="pagemessage">{$message}</p></div>{/if}
{$backtoform_nav}<br />
{$start_form}{$fb_hidden}{$op}{$tab_start}
{$maintab_start}
	{foreach from=$mainList item=entry}
		<div class="pageoverflow">
			<p class="pagetext">{$entry->title}:</p>
			<div class="pageinput">{$entry->input}</div>
			{if $entry->help != ''}{$entry->help}{/if}
		</div>
	{/foreach}
{$tab_end}
{$advancedtab_start}
	{foreach from=$advList item=entry}
		<div class="pageoverflow">
			<p class="pagetext">{$entry->title}:</p>
			<div class="pageinput">{$entry->input}</div>
			{if $entry->help != ''}{$entry->help}{/if}
		</div>
	{/foreach}
{$tab_end}
{$tabs_end}
{if $add != '' or $del != ''}
	<div class="pageoverflow">
		<p class="pagetext">&nbsp;</p>
		<p class="pageinput">{$add}{$del}</p>
	</div>
{/if}
	<div class="pageoverflow">
		<p class="pagetext">&nbsp;</p>
		<p class="pageinput">{$submit}</p>
	</div>
{$end_form}
