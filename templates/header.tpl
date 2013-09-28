<!doctype html>
<html>
 <head>
  <meta charset="utf-8">
  <title>{$header_title}</title>

  {if isset($header_style)}<link type="text/css" rel="stylesheet" href="{$base_url}{$header_style}">{/if}

  <meta name="viewport" content="width=device-width, initial-scale=1">
  {if isset($header_description)}<meta name="description" content="{$header_description}">{/if}
  {if isset($header_keywords)}<meta name="keywords" content="{$header_keywords}">{/if}

  {if isset($header_script)}<script type="text/javascript" src="{$base_url}{$header_script}"></script>{/if}
 </head>

 <body>