<!doctype html>
<html>
 <head>
  <meta charset="utf-8">
  <title><?php if (isset($_['header_title'])) { echo $_['header_title']; } ?></title>

  <?php if (isset($_['header_style'])): ?><link type="text/css" rel="stylesheet" href="/<?php echo $_['base_url'] . $_['header_style']; ?>"><?php endif; ?>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php if (isset($_['setting_description'])): ?><meta name="description" content="<?php echo $_['setting_description']; ?>"><?php endif; ?>
  <?php if (isset($_['setting_keywords'])): ?><meta name="keywords" content="<?php echo implode(',', $_['setting_keywords']); ?>"><?php endif; ?>

  <?php if (isset($_['header_script'])): ?><script type="text/javascript" src="/<?php echo $_['base_url'] . $_['header_script']; ?>"></script><?php endif; ?>
 </head>

 <?php ob_flush(); flush(); ?>

 <body>
 <section class="page-wrapper">
  <header>
   <?php if (isset($_['setting_title'])): ?><h1><?php echo $_['setting_title']; ?></h1><?php endif; ?>
   <?php if (isset($_['setting_subtitle'])): ?><h2><?php echo nl2br($_['setting_subtitle']); ?></h2><?php endif; ?>