<form
	id="<?php echo $form['name']; ?>"
	onsubmit="return false;"
	data-optionals='<?php echo $form['optionals']; ?>'>

	<input type="hidden"
		name="nonce"
		value="<?php echo $form['nonce']; ?>">

	<div class="errors"></div>

	<?php foreach ($form['items'] as $item): ?>
	<?php if ($item['type'] == 'separator'): ?>
	<div class="separator"></div>


	<?php elseif ($item['type'] == 'section'): ?>
	<h3><?php echo $item['title']; ?></h3>
	<p class="section-text"><?php echo $item['text']; ?></p>


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
			<option value="<?php echo $id; ?>" <?php echo ($id == $item['value'] ? 'selected' : ''); ?>><?php echo $name; ?></option>
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
				<?php echo ($id == $item['value'] ? 'checked' : ''); ?>>
			<?php echo $name; ?>
		</label>
		<?php endforeach; ?>


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
					class="array-item"
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
					class="parameter-key"
					data-name="<?php echo $item['name']; ?>">
				<span class="equal">=</span>
				<input type="text"
					value='{{=it.value}}'
					maxlength="<?php echo $item['preg']['max']; ?>"
					class="parameter-val"
					data-name="<?php echo $item['name']; ?>">
			</li>
		</script>


		<?php endif; ?>
	</p>

	<div class="input-error-below" data-for-name="<?php echo $item['name']; ?>">
		<div class="box">
			<div class="arrow"></div>
			<div class="arrow-border"></div>
			<p>
				<i class="fa fa-exclamation-circle"></i>&ensp;<span></span>
			</p>
		</div>
	</div>
	<?php endif; ?>
	<?php endforeach; ?>

	<?php if ($form['submit']): ?>
	<button type="submit" class="button"><?php echo $form['submit']; ?></button>
	<?php endif; ?>

	<div class="clear"></div>
</form>