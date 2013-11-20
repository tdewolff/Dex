<form
    id="<?php echo $form['name'] . '_' . $form['salt']; ?>"
    autocomplete="off"
    onsubmit="return false;"
    data-salt="<?php echo $form['salt']; ?>"
    data-optionals='<?php echo $form['optionals']; ?>'>

    <div class="form_errors"></div>

    <?php foreach ($form['items'] as $item): ?>
    <?php if ($item['type'] == 'separator'): ?>
    <div class="form_separator"></div>


    <?php elseif ($item['type'] == 'section'): ?>
    <h3><?php echo $item['title']; ?></h3>
    <p class="form_section_text"><?php echo $item['text']; ?></p>


    <?php else: // input elements with a label ?>
    <p>
        <label <?php if (empty($item['subtitle'])) { echo 'class="empty_small"'; } ?>><?php echo $item['title']; ?> <span class="small"><?php echo $item['subtitle']; ?></span></label>


        <?php if ($item['type'] == 'text'): ?>
        <input type="text"
            name="<?php echo $item['name'] . '_' . $form['salt']; ?>"
            value="<?php echo $item['value']; ?>"
            maxlength="<?php echo $item['preg']['max']; ?>"
            placeholder="<?php echo (isset($item['placeholder']) ? $item['placeholder'] : ''); ?>">


        <?php elseif ($item['type'] == 'multiline_text'): ?>
        <textarea
            name="<?php echo $item['name'] . '_' . $form['salt']; ?>"
            maxlength="<?php echo $item['preg']['max']; ?>"
            placeholder="<?php echo (isset($item['placeholder']) ? $item['placeholder'] : ''); ?>"><?php echo $item['value']; ?></textarea>


        <?php elseif ($item['type'] == 'email'): ?>
        <input type="email"
            name="<?php echo $item['name'] . '_' . $form['salt']; ?>"
            value="<?php echo $item['value']; ?>"
            maxlength="<?php echo $item['preg']['max']; ?>"
            placeholder="<?php echo (isset($item['placeholder']) ? $item['placeholder'] : ''); ?>">


        <?php elseif ($item['type'] == 'tel'): ?>
        <input type="tel"
            name="<?php echo $item['name'] . '_' . $form['salt']; ?>"
            value="<?php echo $item['value']; ?>"
            maxlength="<?php echo $item['preg']['max']; ?>"
            placeholder="<?php echo (isset($item['placeholder']) ? $item['placeholder'] : ''); ?>">


        <?php elseif ($item['type'] == 'password'): ?>
        <input type="hidden"
            name="<?php echo $item['name'] . '_' . $form['salt']; ?>"
            value='<?php echo $item['value']; ?>'
            data-type="password">

        <input type="password"
            value=""
            placeholder="<?php echo (isset($item['placeholder']) ? $item['placeholder'] : ''); ?>"
            data-name="<?php echo $item['name'] . '_' . $form['salt']; ?>">


        <?php elseif ($item['type'] == 'dropdown'): ?>
        <select
            name="<?php echo $item['name'] . '_' . $form['salt']; ?>">
            <?php foreach ($item['options'] as $id => $name): ?>
            <option value="<?php echo $id; ?>" <?php echo ($id == $item['value'] ? 'selected="selected"' : ''); ?>><?php echo $name; ?></option>
            <?php endforeach; ?>
        </select>


        <?php elseif ($item['type'] == 'markdown'): ?>
        <div class="clear"></div>
        <div class="markdown-buttons">
            <a href="#" class="small-button fancybox"><i class="icon-link"></i>&ensp;Insert link</a>
            <a href="#" class="small-button fancybox"><i class="icon-picture"></i>&ensp;Insert image</a>
        </div>
        <textarea
            name="<?php echo $item['name'] . '_' . $form['salt']; ?>"
            class="markdown"
            maxlength="<?php echo $item['preg']['max']; ?>"
            placeholder="<?php echo (isset($item['placeholder']) ? $item['placeholder'] : ''); ?>"><?php echo $item['value']; ?></textarea>


        <?php elseif ($item['type'] == 'array'): ?>
        <input type="hidden"
            name="<?php echo $item['name'] . '_' . $form['salt']; ?>"
            value='<?php echo $item['value']; ?>'
            data-type="array"
            data-template="template_<?php echo $item['name'] . '_' . $form['salt']; ?>"
            data-ul="ul_<?php echo $item['name'] . '_' . $form['salt']; ?>">
        <ul id="ul_<?php echo $item['name'] . '_' . $form['salt']; ?>"></ul>
        <div class="clear"></div>

        <script id="template_<?php echo $item['name'] . '_' . $form['salt']; ?>" type="text/x-dot-template">
            <li>
                <input type="text"
                    value='{{=it.value}}'
                    maxlength="<?php echo $item['preg']['max']; ?>"
                    placeholder="<?php echo (isset($item['placeholder']) ? $item['placeholder'] : ''); ?>"
                    class="array_item"
                    data-name="<?php echo $item['name'] . '_' . $form['salt']; ?>">
                <span class="comma">,</span>
            </li>
        </script>


        <?php elseif ($item['type'] == 'parameters'): ?>
        <input type="hidden"
            name="<?php echo $item['name'] . '_' . $form['salt']; ?>"
            value='<?php echo $item['value']; ?>'
            data-type="parameters"
            data-template="template_<?php echo $item['name'] . '_' . $form['salt']; ?>"
            data-ul="ul_<?php echo $item['name'] . '_' . $form['salt']; ?>">
        <ul id="ul_<?php echo $item['name'] . '_' . $form['salt']; ?>"></ul>
        <div class="clear"></div>

        <script id="template_<?php echo $item['name'] . '_' . $form['salt']; ?>" type="text/x-dot-template">
            <li>
                <input type="text"
                    value='{{=it.key}}'
                    maxlength="<?php echo $item['preg']['max']; ?>"
                    class="parameter_key"
                    data-name="<?php echo $item['name'] . '_' . $form['salt']; ?>">
                <span class="equal">=</span>
                <input type="text"
                    value='{{=it.value}}'
                    maxlength="<?php echo $item['preg']['max']; ?>"
                    class="parameter_val"
                    data-name="<?php echo $item['name'] . '_' . $form['salt']; ?>">
            </li>
        </script>


        <?php endif; ?>
    </p>

    <div class="form_item_error" data-for-name="<?php echo $item['name'] . '_' . $form['salt']; ?>">
        <div class="box">
            <div class="arrow"></div>
            <div class="arrow-border"></div>
            <p class="pre_wrap">
                <i class="icon-exclamation-sign"></i>&ensp;<span></span>
            </p>
        </div>
    </div>
    <?php endif; ?>
    <?php endforeach; ?>

    <?php if ($form['submit']): ?>
    <button type="submit" class="button"><?php echo $form['submit']; ?></button>
    <?php endif; ?>

    <div class="form_response">
        <span class="loading"></span>
        <span class="success"><?php echo $form['response']['success']; ?></span>
        <span class="error"><?php echo $form['response']['error']; ?></span>
    </div>
</form>