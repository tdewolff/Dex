<!doctype html>
<html>
 <head>
  <meta charset="utf-8">
  <title>{$header_title}</title>

  {if isset($header_style)}<link type="text/css" rel="stylesheet" href="{$base_url}{$header_style}">{/if}

  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  {if isset($header_description)}<meta name="description" content="{$header_description}">{/if}
  {if isset($header_keywords)}<meta name="keywords" content="{$header_keywords}">{/if}

  {if isset($header_script)}<script type="text/javascript" src="{$base_url}{$header_script}"></script>{/if}
 </head>

 <body>
  {if isset($admin_links)}
   <div class="title"></div>
   <div id="left-column">
    <ul>
     {foreach from=$admin_links key=i item=link}
      {if empty($link)}
       <li class="separator"></li>
      {else}
       {if $link.admin == 0 || ($link.admin == 1 && $isAdmin)}
        {if $isAdmin || (!$isAdmin && (!isset($link.enabled) || $link.enabled == 1))}
         <li {if isset($link.enabled) && $link.enabled == 0}class="disabled"{/if}>
          <a href="{$base_url}{$link.uri}" {if isset($current_admin_i) && $i == $current_admin_i}id="selected"{/if}>
           <i class="icon-fixed-width {if strlen($link.icon)}{$link.icon}{else}icon-sign-blank{/if}"></i>&ensp;{$link.title}
          </a>
         </li>
        {/if}
       {/if}
      {/if}
     {/foreach}
    </div>
    <div id="right-column">
   {/if}