<div class="dex-popup-wrapper">
	<div class="popup">
		<div id="assets">
			<h2><?php echo _('Images'); ?></h2>
			<div id="external-link">
				<a href="#" class="properties inline-button"><?php echo _('Properties'); ?>&ensp;<i class="fa fa-arrow-right"></i></a>
				<div class="external-link-input"><input type="text" placeholder="http://www.domain.com/"></div>
			</div>

			<form id="upload" method="post" action="/<?php echo $_['base_url']; ?>api/core/assets/" enctype="multipart/form-data">
				<input type="hidden" name="dir" value="">
				<input type="hidden" name="max_width" value="100">
				<div id="drop">
					<span><?php echo _('Drop here'); ?></span><br>
					<a class="inline-button"><i class="fa fa-search"></i>&ensp;<?php echo _('Browse'); ?></a>
					<input type="file" name="upload" multiple>
					<div id="knob">
						<div id="big-knob"><input type="text" value="0" data-width="64" data-height="64" data-thickness=".23" data-fgColor="#477725" data-readOnly="1" data-displayInput=false data-bgColor="#FFFFFF"></div>
						<div id="small-knob"><input type="text" value="0" data-width="48" data-height="48" data-thickness=".25" data-fgColor="#477725" data-readOnly="1" data-displayInput=false data-bgColor="#F0F0F0"></div>
					</div>
				</div>
				<ul></ul>
			</form>

			<div id="breadcrumbs"><a href="#" data-dir=""><?php echo _('Assets'); ?></a></div>
			<ul id="directories-assets" class="small-table">
				<li>
					<div><?php echo _('File name'); ?></div>
					<div><?php echo _('Size'); ?></div>
					<div></div>
				</li>
				<li id="load_status_directories" class="dex-api load-status">
					<div class="working"><i class="fa fa-cog fa-spin"></i></div>
					<div class="error"><i class="fa fa-times"></i></div>
					<div class="empty"><?php echo _('empty'); ?></div>
				</li>
			</ul>
			<ul id="images" class="grid">
				<li id="load_status_images" class="dex-api load-status">
					<div class="working"><i class="fa fa-cog fa-spin"></i></div>
					<div class="error"><i class="fa fa-times"></i></div>
					<div class="empty"><?php echo _('empty'); ?></div>
				</li>
			</ul>
		</div>
		<div>
			<h2><?php echo _('Image properties'); ?></h2>
			<form>
				<input id="insert_url" type="hidden">
				<input id="insert_width" type="hidden" value="50">
				<p><label><?php echo _('Description'); ?></label><input id="insert_title" type="text" data-tooltip="<?php echo _('Shown when hovering'); ?>"></p>
				<p><label><?php echo _('Alternative text'); ?></label><input id="insert_alt" type="text" data-tooltip="<?php echo _('Shown when image is unavailable'); ?>"></p>
				<p><label><?php echo _('Caption'); ?></label><textarea id="insert_caption"></textarea></p>
				<input id="insert_submit" type="hidden">
				<a href="#" class="insert button"><i class="fa fa-plus-square"></i>&ensp;<?php echo _('Done'); ?></a>
			</form>
		</div>
	</div>
</div>

<script id="directory_item" type="text/x-dot-template">
	<li data-name="{{=it.name}}" class="directory">
		<div><img src="/<?php echo $_['base_url']; ?>res/core/images/icons/{{=it.icon}}" width="16" height="16"><a href="#" data-dir="{{=it.dir}}">{{=it.name}}</a></div>
		<div>-</div>
		<div></div>
	</li>
</script>

<script id="image_item" type="text/x-dot-template">
	<li data-title="{{=it.title}}" data-url="/<?php echo $_['base_url']; ?>res/{{=it.url}}" data-width="{{=it.width}}">
		<div class="caption"><strong>{{=it.title}}</strong></div>
		{{? it.width > 100}}
		<img src="/<?php echo $_['base_url']; ?>res/{{=it.url}}/w=100/t={{=it.mtime}}/"
			 alt="{{=it.name}}"
			 title="{{=it.title}}"
			 {{=it.attr}}>
		{{??}}
		<img src="/<?php echo $_['base_url']; ?>res/{{=it.url}}"
			 alt="{{=it.name}}"
			 title="{{=it.title}}"
			 {{=it.attr}}
			 class="small">
		{{?}}
	</li>
</script>

<script type="text/javascript">
	// preliminaries
	var breadcrumbs = $('#breadcrumbs');
	var directories_assets = $('#directories-assets');
	var images = $('#images');

	var directory_item = doT.template($('#directory_item').text());
	var image_item = doT.template($('#image_item').text());

	// loading initial data
	function loadDir(dir) {
		$('#upload input[name="dir"]').val(dir);
		$('#upload').find('#knob').stop().fadeOut();
		$('#upload').find('ul li').remove();

		directories_assets.find('li:not(:first):not(.load-status)').slideUp(100, function () { $(this).remove(); });
		images.find('li:not(.load-status)').slideUp(100, function () { $(this).remove(); });

		setTimeout(function () {
			api('/' + base_url + 'api/core/assets/', {
				action: 'get_breadcrumbs',
				dir: dir
			}, function (data) {
				breadcrumbs.find('*:not(a:first)').remove();
				var items = '';
				$.each(data['breadcrumbs'], function (i) {
					items += '<span>&gt;</span><a href="#" data-dir="' + this.dir + '">' + this.name + '</a>';
				});
				breadcrumbs.append(items);
			});

			apiLoadStatusWorking($('#load_status_directories'));
			$('#load_status_directories').show();
			api('/' + base_url + 'api/core/assets/', {
				action: 'get_directories',
				dir: dir
			}, function (data) {
				if (!data['directories'].length) {
					apiLoadStatusEmpty($('#load_status_directories'));
					return;
				}
				$('#load_status_directories').hide();

				var items = '';
				$.each(data['directories'], function () {
					items += directory_item(this);
				});
				$(items).hide().appendTo(directories_assets).slideDown(100, function () {
					parent.$.fancybox.update();
				});
			}, function () {
				apiLoadStatusError($('#load_status_directories'));
			});

			apiLoadStatusWorking($('#load_status_images'));
			$('#load_status_images').show();
			api('/' + base_url + 'api/core/assets/', {
				action: 'get_images',
				dir: dir,
				max_width: 100
			}, function (data) {
				if (!data['images'].length) {
					apiLoadStatusEmpty($('#load_status_images'));
					return;
				}
				$('#load_status_images').hide();

				var items = '';
				$.each(data['images'], function () {
					items += image_item(this);
				});
				$(items).hide().appendTo(images).slideDown(100, function () {
					parent.$.fancybox.update();
				});
			}, function () {
				apiLoadStatusError($('#load_status_images'));
			});
		}, 100);
	}
	loadDir('');

	// click events on directories, assets and images
	breadcrumbs.on('click', 'a', function () {
		loadDir($(this).attr('data-dir'));
	});

	directories_assets.on('click', '.directory', function (e) {
		e.stopPropagation();
		$(this).find('a').click();
	});

	directories_assets.on('click', '.directory a', function (e) {
		e.stopPropagation();
		loadDir($(this).attr('data-dir'));
	});

	var popup = $('.popup');
	images.on('click', 'li', function () {
		$('#insert_title').val($(this).attr('data-title'));
		$('#insert_url').val($(this).attr('data-url'));
		$('#insert_width').val($(this).attr('data-width'));
		switchPopupFrame(popup);
	});

	popup.on('click', '#external-link a', function () {
		$('#insert_url').val($('#external-link input').val());
		$('<img src="' + $('#external-link input').val() + '">').one('load', function() {
			$('#insert_width').val(this.width);
		});
		switchPopupFrame(popup);
	});

	popup.on('click', 'a.insert', function () {
		$('#insert_submit').val('1');
		var width = $('#insert_width').val();
		if (width > 500) {
			$('#insert_url').val($('#insert_url').val() + '/w=500/');
		}
		parent.$.fancybox.close();
	});

	$(function () {
		initAdminUpload('#upload', function (data) {
			if (!data['file'].is_image) {
				var item = asset_item(data['file']);
				if (directories_assets.find('li.asset').length) {
					addAlphabetically(directories_assets.find('li.asset'), item, data['file']['name']);
				} else {
					$(item).hide().insertAfter(directories_assets.find('.directory:last')).slideDown(100);
				}
			} else {
				var item = image_item(data['file']);
				if (images.find('li').length) {
					addAlphabetically(images.find('li'), item, data['file']['name']);
				} else {
					$(item).hide().appendTo(images).slideDown(100);
				}
			}
		});
	});
</script>