<form
	id="<?php echo $form['name']; ?>"
	onsubmit="return false;"
	data-optionals='<?php echo $form['optionals']; ?>'>

	<input type="hidden"
		name="nonce"
		value="<?php echo $form['nonce']; ?>">

	<div class="form_errors"></div>

	<?php foreach ($form['items'] as $item): ?>
	<?php if ($item['type'] == 'separator'): ?>
	<div class="form_separator"></div>


	<?php elseif ($item['type'] == 'section'): ?>
	<h3><?php echo $item['title']; ?></h3>
	<p class="form_section_text"><?php echo $item['text']; ?></p>


	<?php else: // input elements with a label ?>
	<p>
		<label><?php echo $item['title']; ?></label>


		<?php if ($item['type'] == 'text'): ?>
		<input type="text"
			name="<?php echo $item['name']; ?>"
			value="<?php echo $item['value']; ?>"
			maxlength="<?php echo $item['preg']['max']; ?>"
			placeholder="<?php echo (isset($item['placeholder']) ? $item['placeholder'] : ''); ?>"
			<?php if (!empty($item['subtitle'])) { echo 'data-tooltip="' . $item['subtitle'] . '"'; } ?>
			<?php if (isset($item['id'])): ?>id="<?php echo $item['id']; ?>"<?php endif; ?>
			<?php if (isset($item['class'])): ?>class="<?php echo $item['class']; ?>"<?php endif; ?>>


		<?php elseif ($item['type'] == 'multiline_text'): ?>
		<textarea
			name="<?php echo $item['name']; ?>"
			maxlength="<?php echo $item['preg']['max']; ?>"
			placeholder="<?php echo (isset($item['placeholder']) ? $item['placeholder'] : ''); ?>"
			<?php if (!empty($item['subtitle'])) { echo 'data-tooltip="' . $item['subtitle'] . '"'; } ?>
			<?php if (isset($item['id'])): ?>id="<?php echo $item['id']; ?>"<?php endif; ?>
			<?php if (isset($item['class'])): ?>class="<?php echo $item['class']; ?>"<?php endif; ?>><?php echo $item['value']; ?></textarea>


		<?php elseif ($item['type'] == 'email'): ?>
		<input type="email"
			name="<?php echo $item['name']; ?>"
			value="<?php echo $item['value']; ?>"
			maxlength="<?php echo $item['preg']['max']; ?>"
			placeholder="<?php echo (isset($item['placeholder']) ? $item['placeholder'] : ''); ?>"
			<?php if (!empty($item['subtitle'])) { echo 'data-tooltip="' . $item['subtitle'] . '"'; } ?>
			<?php if (isset($item['id'])): ?>id="<?php echo $item['id']; ?>"<?php endif; ?>
			<?php if (isset($item['class'])): ?>class="<?php echo $item['class']; ?>"<?php endif; ?>>


		<?php elseif ($item['type'] == 'tel'): ?>
		<input type="tel"
			name="<?php echo $item['name']; ?>"
			value="<?php echo $item['value']; ?>"
			maxlength="<?php echo $item['preg']['max']; ?>"
			placeholder="<?php echo (isset($item['placeholder']) ? $item['placeholder'] : ''); ?>"
			<?php if (!empty($item['subtitle'])) { echo 'data-tooltip="' . $item['subtitle'] . '"'; } ?>
			<?php if (isset($item['id'])): ?>id="<?php echo $item['id']; ?>"<?php endif; ?>
			<?php if (isset($item['class'])): ?>class="<?php echo $item['class']; ?>"<?php endif; ?>>


		<?php elseif ($item['type'] == 'password'): ?>
		<input type="hidden"
			name="<?php echo $item['name']; ?>"
			value='<?php echo $item['value']; ?>'
			<?php if (isset($item['id'])): ?>id="<?php echo $item['id']; ?>"<?php endif; ?>
			<?php if (isset($item['class'])): ?>class="<?php echo $item['class']; ?>"<?php endif; ?>
			data-type="password">

		<input type="password"
			value=""
			placeholder="<?php echo (isset($item['placeholder']) ? $item['placeholder'] : ''); ?>"
			<?php if (!empty($item['subtitle'])) { echo 'data-tooltip="' . $item['subtitle'] . '"'; } ?>
			data-name="<?php echo $item['name']; ?>">


		<?php elseif ($item['type'] == 'dropdown'): ?>
		<select
			name="<?php echo $item['name']; ?>"
			<?php if (!empty($item['subtitle'])) { echo 'data-tooltip="' . $item['subtitle'] . '"'; } ?>
			<?php if (isset($item['id'])): ?>id="<?php echo $item['id']; ?>"<?php endif; ?>
			<?php if (isset($item['class'])): ?>class="<?php echo $item['class']; ?>"<?php endif; ?>>
			<?php foreach ($item['options'] as $id => $name): ?>
			<option value="<?php echo $id; ?>" <?php echo ($id == $item['value'] ? 'selected="selected"' : ''); ?>><?php echo $name; ?></option>
			<?php endforeach; ?>
		</select>


		<?php elseif ($item['type'] == 'radios'): ?>
		<?php foreach ($item['options'] as $id => $name): ?>
		<label class="radio">
			<input type="radio"
				name="<?php echo $item['name']; ?>"
				value="<?php echo $id; ?>"
				<?php if (isset($item['id'])): ?>id="<?php echo $item['id']; ?>"<?php endif; ?>
				<?php if (isset($item['class'])): ?>class="<?php echo $item['class']; ?>"<?php endif; ?>
				<?php echo ($id == $item['value'] ? 'selected="selected"' : ''); ?>>
			<?php echo $name; ?>
		</label>
		<?php endforeach; ?>


		<?php elseif ($item['type'] == 'markdown'): ?>
		<div class="clear"></div>
		<div class="markdown-buttons">
			<a href="#" class="small-button insert-link" data-for-name="<?php echo $item['name']; ?>"><i class="fa fa-link"></i>&ensp;Insert link</a>
			<a href="#" class="small-button insert-image" data-for-name="<?php echo $item['name']; ?>"><i class="fa fa-picture-o"></i>&ensp;Insert image</a>
			<a href="#" class="small-button insert-asset" data-for-name="<?php echo $item['name']; ?>"><i class="fa fa-briefcase"></i>&ensp;Insert asset</a>
		</div>
		<textarea
			name="<?php echo $item['name']; ?>"
			maxlength="<?php echo $item['preg']['max']; ?>"
			placeholder="<?php echo (isset($item['placeholder']) ? $item['placeholder'] : ''); ?>"
			<?php if (!empty($item['subtitle'])) { echo 'data-tooltip="' . $item['subtitle'] . '"'; } ?>
			<?php if (isset($item['id'])): ?>id="<?php echo $item['id']; ?>"<?php endif; ?>
			class="markdown <?php echo (isset($item['class']) ? $item['class'] : ''); ?>"><?php echo $item['value']; ?></textarea>


		<?php elseif ($item['type'] == 'array'): ?>
		<input type="hidden"
			name="<?php echo $item['name']; ?>"
			value='<?php echo $item['value']; ?>'
			placeholder='<?php echo (isset($item['placeholder']) ? $item['placeholder'] : ''); ?>'
			<?php if (isset($item['id'])): ?>id="<?php echo $item['id']; ?>"<?php endif; ?>
			<?php if (isset($item['class'])): ?>class="<?php echo $item['class']; ?>"<?php endif; ?>
			data-type="array"
			data-template="template_<?php echo $item['name']; ?>"
			data-ul="ul_<?php echo $item['name']; ?>">
		<ul
			id="ul_<?php echo $item['name']; ?>"
			<?php if (!empty($item['subtitle'])) { echo 'data-tooltip="' . $item['subtitle'] . '"'; } ?>></ul>
		<div class="clear"></div>

		<script id="template_<?php echo $item['name']; ?>" type="text/x-dot-template">
			<li>
				<input type="text"
					value='{{=it.value}}'
					maxlength="<?php echo $item['preg']['max']; ?>"
					placeholder="{{=it.placeholder}}"
					class="array_item"
					data-name="<?php echo $item['name']; ?>">
				<span class="comma">,</span>
			</li>
		</script>


		<?php elseif ($item['type'] == 'parameters'): ?>
		<input type="hidden"
			name="<?php echo $item['name']; ?>"
			value='<?php echo $item['value']; ?>'
			<?php if (isset($item['id'])): ?>id="<?php echo $item['id']; ?>"<?php endif; ?>
			<?php if (isset($item['class'])): ?>class="<?php echo $item['class']; ?>"<?php endif; ?>
			data-type="parameters"
			data-template="template_<?php echo $item['name']; ?>"
			data-ul="ul_<?php echo $item['name']; ?>">
		<ul
			id="ul_<?php echo $item['name']; ?>"
			<?php if (!empty($item['subtitle'])) { echo 'data-tooltip="' . $item['subtitle'] . '"'; } ?>></ul>
		<div class="clear"></div>

		<script id="template_<?php echo $item['name']; ?>" type="text/x-dot-template">
			<li>
				<input type="text"
					value='{{=it.key}}'
					maxlength="<?php echo $item['preg']['max']; ?>"
					class="parameter_key"
					data-name="<?php echo $item['name']; ?>">
				<span class="equal">=</span>
				<input type="text"
					value='{{=it.value}}'
					maxlength="<?php echo $item['preg']['max']; ?>"
					class="parameter_val"
					data-name="<?php echo $item['name']; ?>">
			</li>
		</script>


		<?php endif; ?>
	</p>

	<div class="form_item_error" data-for-name="<?php echo $item['name']; ?>">
		<div class="box">
			<div class="arrow"></div>
			<div class="arrow-border"></div>
			<p class="pre_wrap">
				<i class="fa fa-exclamation-circle"></i>&ensp;<span></span>
			</p>
		</div>
	</div>
	<?php endif; ?>
	<?php endforeach; ?>

	<?php if ($form['submit']): ?>
	<button type="submit" class="form_button button"><?php echo $form['submit']; ?></button>
	<?php endif; ?>

	<div class="clear"></div>
</form>