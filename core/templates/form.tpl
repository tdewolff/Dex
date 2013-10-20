<form method="post"
 id="<?php echo $form['name'] . '_' . $form['salt']; ?>"
 <?php if (strlen($form['mode'])): ?>class="form_<?php echo $form['mode']; ?>"<?php endif; ?>
 <?php if (isset($form['action'])): ?>action="<?php echo $form['action']; ?>"<?php endif; ?>
 onsubmit="return form_submit(this, 'submitted_<?php echo $form['salt']; ?>');"
 autocomplete="off"
 data-ajax="<?php echo ($form['ajax'] ? 'true' : 'false'); ?>">

 <input type="hidden"
  name="submitted_<?php echo $form['salt']; ?>"
  value="0" />


 <?php if (count($form['errors'])): ?>
  <div class="form_error">
   <?php foreach ($form['errors'] as $error): ?>
    <?php echo $error; ?><br />
   <?php endforeach; ?>
  </div>
 <?php endif; ?>


 <?php foreach ($form['items'] as $item): ?>
  <?php if ($item['type'] == 'separator'): ?>
   <div class="form_separator"></div>


  <?php elseif ($item['type'] == 'section'): ?>
   <h3><?php echo $item['title']; ?></h3>
   <p class="form_section_text"><?php echo $item['text']; ?></p>


  <?php elseif ($item['type'] == 'submit'): ?>
   <input type="hidden"
    name="<?php echo $item['name'] . '_' . $form['salt']; ?>"
    value="" />
   <a class="button submit" href="#"><?php echo $item['title']; ?></a>
   <span class="form_response"><?php echo (strlen($form['response']) ? $form['response'] : ''); ?></span>


  <?php else: // input elements with a label ?>
   <p>
    <label <?php if (empty($item['subtitle'])) { echo 'class="empty_small"'; } ?>><?php echo $item['title']; ?> <span class="small"><?php echo $item['subtitle']; ?></span></label>


    <?php if ($item['type'] == 'text'): ?>
     <input type="text"
      name="<?php echo $item['name'] . '_' . $form['salt']; ?>"
      value="<?php echo $item['value']; ?>"
      maxlength="<?php echo $item['preg']['max']; ?>"
      pattern="<?php echo $item['preg']['regex']; ?>"
      placeholder="<?php echo (isset($item['placeholder']) ? $item['placeholder'] : ''); ?>"
      class="<?php echo (isset($item['error']) ? 'invalid' : ''); ?> <?php echo ($item['preg']['min'] == 0 || $item['cssEmptyTogether'] ? 'unused' : ''); ?>"
      oninput="form_input(this, <?php echo $item['jsEmptyTogether']; ?>);" />


    <?php elseif ($item['type'] == 'multiline_text'): ?>
     <textarea
      name="<?php echo $item['name'] . '_' . $form['salt']; ?>"
      maxlength="<?php echo $item['preg']['max']; ?>"
      placeholder="<?php echo (isset($item['placeholder']) ? $item['placeholder'] : ''); ?>"
      class="<?php echo (isset($item['error']) ? 'invalid' : ''); ?> <?php echo ($item['preg']['min'] == 0 || $item['cssEmptyTogether'] ? 'unused' : ''); ?>"
      oninput="form_input(this, <?php echo $item['jsEmptyTogether']; ?>);"><?php echo $item['value']; ?></textarea>


    <?php elseif ($item['type'] == 'email'): ?>
     <input type="email"
      name="<?php echo $item['name'] . '_' . $form['salt']; ?>"
      value="<?php echo $item['value']; ?>"
      maxlength="<?php echo $item['preg']['max']; ?>"
      pattern="<?php echo $item['preg']['regex']; ?>"
      placeholder="<?php echo (isset($item['placeholder']) ? $item['placeholder'] : ''); ?>"
      class="<?php echo (isset($item['error']) ? 'invalid' : ''); ?> <?php echo ($item['preg']['min'] == 0 || $item['cssEmptyTogether'] ? 'unused' : ''); ?>"
      oninput="form_input(this, <?php echo $item['jsEmptyTogether']; ?>);" />


    <?php elseif ($item['type'] == 'tel'): ?>
     <input type="tel"
      name="<?php echo $item['name'] . '_' . $form['salt']; ?>"
      value="<?php echo $item['value']; ?>"
      maxlength="<?php echo $item['preg']['max']; ?>"
      pattern="<?php echo $item['preg']['regex']; ?>"
      placeholder="<?php echo (isset($item['placeholder']) ? $item['placeholder'] : ''); ?>"
      class="<?php echo (isset($item['error']) ? 'invalid' : ''); ?> <?php echo ($item['preg']['min'] == 0 || $item['cssEmptyTogether'] ? 'unused' : ''); ?>"
      oninput="form_input(this, <?php echo $item['jsEmptyTogether']; ?>);" />


    <?php elseif ($item['type'] == 'password'): ?>
     <input type="password"
      name="<?php echo $item['name'] . '_' . $form['salt']; ?>"
      maxlength="<?php echo $item['preg']['max']; ?>"
      pattern="<?php echo $item['preg']['regex']; ?>"
      placeholder="<?php echo (isset($item['placeholder']) ? $item['placeholder'] : ''); ?>"
      class="<?php echo (isset($item['error']) ? 'invalid' : ''); ?> <?php echo ($item['preg']['min'] == 0 || $item['cssEmptyTogether'] ? 'unused' : ''); ?>"
      oninput="form_input(this, <?php echo $item['jsEmptyTogether']; ?>);" />

     <input type="hidden"
      name="<?php echo $item['name'] . '_' . $form['salt']; ?>_hash"
      value="" />


    <?php elseif ($item['type'] == 'dropdown'): ?>
     <select
      name="<?php echo $item['name'] . '_' . $form['salt']; ?>"
      class="<?php echo (isset($item['error']) ? 'invalid' : ''); ?> <?php echo ($item['preg']['min'] == 0 || $item['cssEmptyTogether'] ? 'unused' : ''); ?>"
      oninput="form_input(this, <?php echo $item['jsEmptyTogether']; ?>);">
       <?php foreach ($item['options'] as $id => $name): ?>
        <option value="<?php echo $id; ?>" <?php echo ($id == $item['value'] ? 'selected="selected"' : ''); ?>><?php echo $name; ?></option>
       <?php endforeach; ?>
      </select>


    <?php elseif ($item['type'] == 'markdown'): ?>
     <div class="clear"></div>
     <textarea
      name="<?php echo $item['name'] . '_' . $form['salt']; ?>"
      class="markdown"><?php echo $item['value']; ?></textarea>


    <?php elseif ($item['type'] == 'array'): ?>
     <input type="text"
      name="<?php echo $item['name'] . '_' . $form['salt']; ?>"
      data-type="array"
      data-i="0"
      value="<?php echo $item['value']; ?>"
      maxlength="<?php echo $item['preg']['max']; ?>"
      class="<?php echo (isset($item['error']) ? 'invalid' : ''); ?> <?php echo ($item['preg']['min'] == 0 || $item['cssEmptyTogether'] ? 'unused' : ''); ?>"
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


  <?php if (isset($item['error'])): ?>
   <div class="form_item_error">
    <div class="box">
     <div class="arrow"></div>
     <div class="arrow-border"></div>

     <p class="pre_wrap"><i class="icon-exclamation-sign"></i>&ensp;<span><?php echo $item['error']; ?></span></p>
    </div>
   </div>
  <?php endif; ?>

 <?php endforeach; ?>
</form>