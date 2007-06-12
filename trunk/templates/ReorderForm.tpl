{$scriptaculous}
{literal}
<script type="text/javascript">
function send_order_var(form_desc)
   {
   alert("hi");
   return false;
   }
</script>
{/literal}

{$start_form}
<ul id="parent0" class="sortableList">
{foreach from=$fields item=thisField}
		<li id="fld_{$thisField->id}"><strong>{$thisField->name}</strong> - {$thisField->type}</li>
{/foreach}

</ul>
<br />
<input type="hidden" name="order" value="" />
{$submit}
{$end_form}

{literal}
<script type="text/javascript">
Sortable.create('parent0',{tag:'li'});
</script>
{/literal}