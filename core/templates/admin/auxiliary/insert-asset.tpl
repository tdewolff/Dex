<div class="dex popup-wrapper">
	<div class="popup">
		<div id="assets">
			<h2>Assets</h2>
			<div id="breadcrumbs"><a href="#" data-dir="">Assets</a></div>
			<ul id="directories-assets" class="small-table">
				<li>
					<div>Filename</div>
					<div>Size</div>
					<div></div>
				</li>
				<li id="load_status_directories_assets" class="dex api load-status">
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
				<p><label>Text</label><input id="insert_text" type="text" data-tooltip="Clickable text"></p>
				<p><label>Description</label><input id="insert_title" type="text" data-tooltip="Shown when hovering"></p>
				<input id="insert_submit" type="hidden">
				<a href="#" class="insert button"><i class="fa fa-plus-square"></i>&ensp;Insert</a>
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

<script id="asset_item" type="text/x-dot-template">
	<li  data-title="{{=it.title}}" data-url="/<?php echo $_['base_url']; ?>res/{{=it.url}}" class="asset">
		<div><img src="/<?php echo $_['base_url']; ?>res/core/images/icons/{{=it.icon}}" width="16" height="16">{{=it.title}}</div>
		<div>{{=it.size}}</div>
		<div></div>
	</li>
</script>

<script type="text/javascript">
	// preliminaries
	var breadcrumbs = $('#breadcrumbs');
	var directories_assets = $('#directories-assets');

	var directory_item = doT.template($('#directory_item').text());
	var asset_item = doT.template($('#asset_item').text());

	// loading initial data
	function loadDir(dir) {
		directories_assets.find('li:not(:first):not(.load-status)').slideUp('fast', function () { $(this).remove(); });

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

			apiLoadStatusWorking($('#load_status_directories_assets'));
			$('#load_status_directories_assets').show();
			api('/' + base_url + 'api/core/assets/', {
				action: 'get_directories_assets',
				dir: dir
			}, function (data) {
				if (!data['directories'].length && !data['assets'].length) {
					apiLoadStatusEmpty($('#load_status_directories_assets'));
					return;
				}
				$('#load_status_directories_assets').hide();

				$.each(data['directories'], function () {
					$(directory_item(this)).hide().appendTo(directories_assets).slideDown('fast');
				});

				$.each(data['assets'], function () {
					$(asset_item(this)).hide().appendTo(directories_assets).slideDown('fast');
				});
			}, function () {
				apiLoadStatusError($('#load_status_directories_assets'));
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

	directories_assets.on('click', '.directory a', function () {
		e.stopPropagation();
		loadDir($(this).attr('data-dir'));
	});

	var popup = $('.popup');
	directories_assets.on('click', '.asset', function () {
		$('#insert_title').val($(this).attr('data-title'));
		$('#insert_url').val($(this).attr('data-url'));
		if (!$('#insert_text').val().length) {
			$('#insert_text').val($(this).attr('data-title'));
		}
		switchPopupFrame(popup);
	});

	popup.on('click', 'a.insert', function () {
		$('#insert_submit').val('1');
		parent.$.fancybox.close();
	});
</script>