<div class="dex popup-wrapper">
	<div class="popup">
		<div id="assets">
			<h2>Images</h2>
			<div id="external-link">
				<input type="text" placeholder="http://www.domain.com/"><a href="#" class="properties inline-button">Properties&ensp;<i class="fa fa-arrow-right"></i></a>
			</div>

			<form id="upload" method="post" action="/<?php echo $_['base_url']; ?>api/core/assets/" enctype="multipart/form-data">
				<input type="hidden" name="dir" value="">
				<div id="drop">
					<span>Drop Here</span><br>
					<a class="inline-button"><i class="fa fa-search"></i>&ensp;Browse</a>
					<input type="file" name="upload" multiple>
					<div id="knob">
						<div id="big-knob"><input type="text" value="0" data-width="64" data-height="64" data-thickness=".23" data-fgColor="#477725" data-readOnly="1" data-displayInput=false data-bgColor="#FFFFFF"></div>
						<div id="small-knob"><input type="text" value="0" data-width="48" data-height="48" data-thickness=".25" data-fgColor="#477725" data-readOnly="1" data-displayInput=false data-bgColor="#F0F0F0"></div>
					</div>
				</div>
				<ul></ul>
			</form>

			<div id="breadcrumbs"><a href="#" data-dir="">Assets</a></div>
			<ul id="directories-assets" class="small-table">
				<li>
					<div>Filename</div>
					<div>Size</div>
					<div></div>
				</li>
				<li id="load_status_directories" class="dex api load-status">
					<div class="working"><i class="fa fa-cog fa-spin"></i></div>
					<div class="error"><i class="fa fa-times"></i></div>
					<div class="empty">empty</div>
				</li>
			</ul>
			<ul id="images" class="grid">
				<li id="load_status_images" class="api load-status">
					<div class="working"><i class="fa fa-cog fa-spin"></i></div>
					<div class="error"><i class="fa fa-times"></i></div>
					<div class="empty">empty</div>
				</li>
			</ul>
		</div>
		<div>
			<h2>Properties</h2>
			<form>
				<input id="insert_url" type="hidden">
				<p><label>Description</label><input id="insert_title" type="text" data-tooltip="Shown when hovering"></p>
				<p><label>Alternative text</label><input id="insert_alt" type="text" data-tooltip="Shown when image is unavailable"></p>
				<p><label>Caption</label><textarea id="insert_caption"></textarea></p>
				<input id="insert_submit" type="hidden">
				<a href="#" class="insert button"><i class="fa fa-plus-square"></i>&ensp;Done</a>
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
	<li data-title="{{=it.title}}" data-url="/<?php echo $_['base_url']; ?>res/{{=it.url}}">
		<div class="caption"><strong>{{=it.title}}</strong></div>
		{{? it.width > 100}}
		<img src="/<?php echo $_['base_url']; ?>res/{{=it.url}}?w=100"
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
		directories_assets.find('li:not(:first):not(.load-status)').slideUp('fast', function () { $(this).remove(); });
		images.find('li:not(.load-status)').slideUp('fast', function () { $(this).remove(); });

		setTimeout(function () {
			api('/' + base_url + 'api/core/assets/', {
				action: 'get_breadcrumbs',
				dir: dir
			}, function (data) {
				breadcrumbs.find('*:not(a:first)').remove();
				$.each(data['breadcrumbs'], function (i) {
					breadcrumbs.append('<span>&gt;</span><a href="#" data-dir="' + this.dir + '">' + this.name + '</a>');
				});
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

				$.each(data['directories'], function () {
					$(directory_item(this)).hide().appendTo(directories_assets).slideDown('fast');
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

				$.each(data['images'], function () {
					$(image_item(this)).hide().appendTo(images).slideDown('fast');
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
		switchPopupFrame(popup);
	});

	popup.on('click', '#external-link a', function () {
		$('#insert_url').val($('#external-link input').val());
		switchPopupFrame(popup);
	});

	popup.on('click', 'a.insert', function () {
		$('#insert_submit').val('1');
		parent.$.fancybox.close();
	});

	$(function () {
		initAdminUpload('#upload', function (data) {
			if (!data['file'].is_image) {
				var item = asset_item(data['file']);
				if (directories_assets.find('li.asset').length) {
					addAlphabetically(directories_assets.find('li.asset'), item, data['file']['name']);
				} else {
					$(item).hide().insertAfter(directories_assets.find('.directory:last')).slideDown('fast');
				}
			} else {
				var item = image_item(data['file']);
				if (images.find('li').length) {
					addAlphabetically(images.find('li'), item, data['file']['name']);
				} else {
					$(item).hide().appendTo(images).slideDown('fast');
				}
			}
		});
	});
</script>