<div class="dex-popup-wrapper">
	<div class="popup">
		<div>
			<h2><?php echo __('Insert link'); ?></h2>
			<h3><?php echo __('Website link'); ?></h3>
			<div id="external-link">
				<a href="#" class="properties inline-button"><i class="fa fa-arrow-right"></i>&ensp;<?php echo __('Properties'); ?></a>
				<div class="external-link-input"><input type="text" placeholder="http://www.domain.com/"></div>
			</div>

			<h3><?php echo __('Page links'); ?></h3>
			<ul id="links" class="table">
			  <li>
				<div style="width:120px;"><?php echo __('Title'); ?></div>
				<div style="width:380px;"><?php echo __('Link'); ?></div>
			  </li>
			</ul>
		</div>
		<div>
			<a href="#" class="back button left"><i class="fa fa-chevron-left"></i>&ensp;<?php echo __('Back'); ?></a>
			<h2><?php echo __('Link properties'); ?></h2>
			<form>
				<input id="insert_url" type="hidden"></p>
				<p><label><?php echo __('Text'); ?></label><input id="insert_text" type="text" data-tooltip="<?php echo __('Clickable text'); ?>"></p>
				<p><label><?php echo __('Description'); ?></label><input id="insert_title" type="text" data-tooltip="<?php echo __('Shown when hovering'); ?>"></p>
				<input id="insert_submit" type="hidden">
				<a href="#" class="insert button"><i class="fa fa-plus-square"></i>&ensp;<?php echo __('Done'); ?></a>
			</form>
		</div>
	</div>
</div>

<script id="link_item" type="text/x-dot-template">
	<li data-title="{{=it.title}}" data-url="/<?php echo $_['base_url']; ?>{{=it.url}}">
		<div style="width:120px;">{{=it.title}}</div>
		<div style="width:380px;">/{{=it.url}}</div>
	</li>
</script>

<script type="text/javascript">
	var links = $('#links');
	var link_item = doT.template($('#link_item').text());
	api('/' + base_url + 'api/core/pages/', {
		action: 'get_pages'
	}, function (data) {
		var items = '';
		$.each(data['pages'], function () {
			items += link_item(this);
		});
		links.append(items);
		parent.$.fancybox.update();
	});

	var popup = $('.popup');
	$('a.back').on('click', function () {
		switchBackPopupFrame(popup);
	});

	links.on('click', 'li:not(:first)', function () {
		$('#insert_title').val($(this).attr('data-title'));
		$('#insert_url').val($(this).attr('data-url'));
		if ($('#insert_text').val().length === 0) {
			$('#insert_text').val($(this).attr('data-title'));
		}
		switchPopupFrame(popup);
	});

	popup.on('keyup', '#external-link input', function (e) {
		if (e.keyCode === 13) {
			popup.find('#external-link a').click();
		}
	});

	popup.on('click', '#external-link a', function () {
		var url = $('#external-link input').val();
		if (url.indexOf(':') === -1) {
			url = 'http://' + url;
		}

		if (!/(ftp|https?):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/.test(url)) {
			$('#external-link input').val('Invalid URL');
			return;
		}

		$('#insert_url').val(url);
		if ($('#insert_text').val().length === 0) {
			$('#insert_text').val($('#insert_url').val());
		}
		switchPopupFrame(popup);
	});

	$('html').on('keyup', function (e) {
		if (e.keyCode === 13 && currentPopupFrame === 1) {
			popup.find('a.insert').click();
		}
	});

	popup.on('click', 'a.insert', function () {
		$('#insert_submit').val('1');
		parent.$.fancybox.close();
	});
</script>