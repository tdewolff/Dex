<!doctype html>
<html>
 <head>
  <meta charset="utf-8">
  <title><?php if (isset($_['header_title'])) { echo $_['header_title']; } ?></title>

  <?php if (isset($_['header_style'])): ?><link type="text/css" rel="stylesheet" href="<?php echo $_['base_url'] . $_['header_style']; ?>"><?php endif; ?>

  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php if (isset($_['settings_description'])): ?><meta name="description" content="<?php echo $_['settings_description']; ?>"><?php endif; ?>
  <?php if (isset($_['settings_keywords'])): ?><meta name="keywords" content="<?php echo $_['settings_keywords']; ?>"><?php endif; ?>

  <?php if (isset($_['header_script'])): ?><script type="text/javascript" src="<?php echo $_['base_url'] . $_['header_script']; ?>"></script><?php endif; ?>
 </head>

 <?php ob_flush(); flush(); ?>

 <body>
  <?php if (isset($_['admin_links'])): ?>
   <div class="title"></div>
   <div id="left-column">
    <ul>
     <?php foreach ($_['admin_links'] as $i => $link): ?>
      <?php if (empty($link)): ?>
       <li class="separator"></li>
      <?php elseif ($link['admin_only'] == 0 || ($link['admin_only'] == 1 && $_['isAdmin'])): ?>
       <?php if ($_['isAdmin'] || (!$_['isAdmin'] && (!isset($link['enabled']) || $link['enabled'] == 1))): ?>
        <li <?php if (isset($link['enabled']) && $link['enabled'] == 0) { echo 'class="disabled"'; } ?>>
         <a href="<?php echo $_['base_url'] . $link['uri']; ?>" <?php if (isset($_['current_admin_i']) && $i == $_['current_admin_i']) { echo 'id="selected"'; } ?>>
          <i class="icon-fixed-width <?php echo (strlen($link['icon']) ? $link['icon'] : 'icon-sign-blank'); ?>"></i>&ensp;<?php echo $link['title']; ?>
         </a>
        </li>
       <?php endif; ?>
      <?php endif; ?>
     <?php endforeach; ?>
    </div>
    <div id="right-column">
   <?php endif; ?>