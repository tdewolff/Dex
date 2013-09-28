<form method="post" id="{$form.name}_{$form.salt}" {if $form.mode != ''}class="form_{$form.mode}"{/if} {if isset($form_action)}action="{$form_action}"{/if} onsubmit="return form_submit(this, 'submitted_{$form.salt}');" autocomplete="off">
 <input type="hidden" id="submitted" value="0" />

 <h1>{$form.title}</h1>
 {if strlen($form.text)}<p class="form_titletext">{$form.text}</p>{/if}

 {foreach from=$form.errors item=error}
  <div class="form_error">{$error}</div>
 {/foreach}

 {foreach from=$form.items item=item}
  {if $item.type == 'separator'}
   <div class="form_separator"></div>

  {elseif $item.type == 'section'}
   <h2>{$item.title}</h2>
   <p class="form_section_text">{$item.text}</p>

  {elseif $item.type == 'submit'}
   <input type="hidden" name="{$item.name}_{$form.salt}" value="" />
   <a class="button submit" href="#">{$item.title}</a>
   {if strlen($form.response)}<span class="form_response">{$form.response}</span>{/if}

  {else}
    <p>
     <label {if $item.subtitle == ''}class="empty_small"{/if}>{$item.title} <span class="small">{$item.subtitle}</span></label>

     {if $item.type == 'text'}
      <input type="text" name="{$item.name}_{$form.salt}" value="{$item.value}" maxlength="{$item.preg.max}" class="{if isset($item.error)}invalid{/if} {if $item.cssEmptyTogether}unused{/if}" oninput="form_input(this, {$item.jsEmptyTogether});" />

      {elseif $item.type == 'wysiwyg'}
       <textarea class="markitup" name="{$item.name}_{$form.salt}">{$item.value}</textarea>

      {elseif $item.type == 'password'}
       <input type="password" name="{$item.name}_{$form.salt}" maxlength="{$item.preg.max}" class="{if isset($item.error)}invalid{/if} {if $item.cssEmptyTogether}unused{/if}" oninput="form_input(this, {$item.jsEmptyTogether});" />
       <input type="hidden" name="{$item.name}_{$form.salt}_hash" value="" />

      {elseif $item.type == 'dropdown'}
       <select name="{$item.name}_{$form.salt}" class="{if isset($item.error)}invalid{/if} {if $item.cssEmptyTogether}unused{/if}" oninput="form_input(this, {$item.jsEmptyTogether});">
         {foreach from=$item.options key=id item=name}
          <option value="{$id}" {if $id == $item.value}selected="selected"{/if}>{$name}</option>
         {/foreach}
        </select>

      {elseif $item.type == 'array'}
       <input type="text" name="{$item.name}_{$form.salt}" data-type="array" data-i="0" value="{$item.value}" maxlength="{$item.preg.max}" class="{if isset($item.error)}invalid{/if} {if $item.cssEmptyTogether}unused{/if}" oninput="form_array(this, {$item.jsEmptyTogether});" />

      {elseif $item.type == 'parameters'}
       <input type="hidden" name="{$item.name}_{$form.salt}" value="{$item.value|escape_quotes}" data-type="parameters" />
       <input type="text" data-name="{$item.name}_{$form.salt}" data-type="parameter_key" data-i="0" value="" oninput="form_parameter(this);" />
       <span class="parameter_equal">=</span>
       <input type="text" data-name="{$item.name}_{$form.salt}" data-type="parameter_val" data-i="0" value="" oninput="form_parameter(this);" />

    {/if}
    </p>
  {/if}

  {if isset($item.error)}
   <div class="form_item_error">{$item.error}</div>
  {/if}
 {/foreach}
</form>