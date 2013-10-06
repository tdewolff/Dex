<form method="post"
 id="<?php echo $form['name'] . '_' . $form['salt']; ?>"
 <?php if (strlen($form['mode'])): ?>class="form_<?php echo $form['mode']; ?>"<?php endif; ?>
 <?php if (isset($form['action'])): ?>action="<?php echo $form['action']; ?>"<?php endif; ?>
 onsubmit="return form_submit(this, 'submitted_<?php echo $form['salt']; ?>');"
 autocomplete="off">

 <input type="hidden"
  name="submitted_<?php echo $form['salt']; ?>"
  value="0" />


 <h1><?php echo $form['title']; ?></h1>
 <?php if (strlen($form['text'])): ?><p class="form_titletext"><?php echo $form['text']; ?></p><?php endif; ?>


 <?php foreach ($form['errors'] as $error): ?>
  <div class="form_error"><?php echo $form['error']; ?></div>
 <?php endforeach; ?>


 <?php foreach ($form['items'] as $item): ?>
  <?php if ($item['type'] == 'separator'): ?>
   <div class="form_separator"></div>


  <?php elseif ($item['type'] == 'section'): ?>
   <h2><?php echo $item['title']; ?></h2>
   <p class="form_section_text"><?php echo $item['text']; ?></p>


  <?php elseif ($item['type'] == 'submit'): ?>
   <input type="hidden"
    name="<?php echo $item['name'] . '_' . $form['salt']; ?>"
    value="" />
   <a class="button submit" href="#"><?php echo $item['title']; ?></a>
   <?php if (strlen($form['response'])): ?><span class="form_response"><?php echo $form['response']; ?></span><?php endif; ?>


  <?php else: // input elements with a label ?>
   <p>
    <label <?php if (empty($item['subtitle'])) { echo 'class="empty_small"'; } ?>><?php echo $item['title']; ?> <span class="small"><?php echo $item['subtitle']; ?></span></label>


    <?php if ($item['type'] == 'text'): ?>
     <input type="text"
      name="<?php echo $item['name'] . '_' . $form['salt']; ?>"
      value="<?php echo $item['value']; ?>"
      maxlength="<?php echo $item['preg']['max']; ?>"
      class="<?php echo (isset($item['error']) ? 'invalid' : ''); ?> <?php echo ($item['cssEmptyTogether'] ? 'unused' : ''); ?>"
      oninput="form_input(this, '<?php echo $item['jsEmptyTogether']; ?>');" />


    <?php elseif ($item['type'] == 'markdown'): ?>
     <div class="clear"></div>
     <textarea
      name="<?php echo $item['name'] . '_' . $form['salt']; ?>"
      class="markdown"><?php echo $item['value']; ?></textarea>


    <?php elseif ($item['type'] == 'password'): ?>
     <input type="password"
      name="<?php echo $item['name'] . '_' . $form['salt']; ?>"
      maxlength="<?php echo $item['preg']['max']; ?>"
      class="<?php echo (isset($item['error']) ? 'invalid' : ''); ?> <?php echo ($item['cssEmptyTogether'] ? 'unused' : ''); ?>"
      oninput="form_input(this, '<?php echo $item['jsEmptyTogether']; ?>');" />

     <input type="hidden"
      name="<?php echo $item['name'] . '_' . $form['salt']; ?>_hash"
      value="" />


    <?php elseif ($item['type'] == 'dropdown'): ?>
     <select
      name="<?php echo $item['name'] . '_' . $form['salt']; ?>"
      class="<?php echo (isset($item['error']) ? 'invalid' : ''); ?> <?php echo ($item['cssEmptyTogether'] ? 'unused' : ''); ?>"
      oninput="form_input(this, '<?php echo $item['jsEmptyTogether']; ?>');">
       <?php foreach ($item['options'] as $id => $name): ?>
        <option value="<?php echo $id; ?>" <?php echo ($id == $item['value'] ? 'selected="selected"' : ''); ?>><?php echo $name; ?></option>
       <?php endforeach; ?>
      </select>


    <?php elseif ($item['type'] == 'array'): ?>
     <input type="text"
      name="<?php echo $item['name'] . '_' . $form['salt']; ?>"
      data-type="array"
      data-i="0"
      value="<?php echo $item['value']; ?>"
      maxlength="<?php echo $item['preg']['max']; ?>"
      class="<?php echo (isset($item['error']) ? 'invalid' : ''); ?> <?php echo ($item['cssEmptyTogether'] ? 'unused' : ''); ?>"
      oninput="form_array(this);" />


    <?php elseif ($item['type'] == 'parameters'): ?>
     <input type="hidden"
      name="<?php echo $item['name'] . '_' . $form['salt']; ?>"
      value="<?php echo $item['value']; ?>"
      data-type="parameters" />

     <input type="text"
      data-name="<?php echo $item['name'] . '_' . $form['salt']; ?>"
      data-type="parameter_key"
      data-i="0"
      value=""
      oninput="form_parameter(this);" />

     <span class="parameter_equal">=</span>
     <input type="text"
      data-name="<?php echo $item['name'] . '_' . $form['salt']; ?>"
      data-type="parameter_val"
      data-i="0"
      value=""
      oninput="form_parameter(this);" />


    <?php endif; ?>
   </p>
  <?php endif; ?>


  <?php if (isset($item['error'])): ?><div class="form_item_error"><?php echo $item['error']; ?></div><?php endif; ?>

 <?php endforeach; ?>
</form>