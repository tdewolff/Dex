<h2>Assets</h2>
<div id="assets">
	<div id="create-directory">
		<input type="text"><a href="#" class="inline-button"><i class="fa fa-asterisk"></i>&ensp;Create directory</a>
	</div>

	<form id="upload" method="post" action="/<?php echo $_['base_url']; ?>api/core/assets/" enctype="multipart/form-data">
		<div id="drop-mask"></div>
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

	<div id="breadcrumbs"><a href="#!dir=" data-dir="">Assets</a></div>
	<ul id="directories-assets" class="table">
		<li>
			<div>Filename</div>
			<div>Size</div>
			<div></div>
		</li>
		<li id="load_status_directories_assets" class="api load-status">
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

<script id="directory_item" type="text/x-dot-template">
	<li data-name="{{=it.name}}" class="directory">
		<div><img src="/<?php echo $_['base_url']; ?>res/core/images/icons/{{=it.icon}}" width="16" height="16"><a data-dir="{{=it.dir}}">{{=it.name}}</a></div>
		<div>-</div>
		<div>
			{{?it.is_deletable}}
			<a href="#" class="halt inline-rounded"><i class="fa fa-trash-o"></i></a>
			<a href="#" class="sure inline-rounded" data-tooltip="Click to confirm" data-name="{{=it.name}}"><i class="fa fa-trash-o"></i></a>
			{{?}}
		</div>
	</li>
</script>

<script id="asset_item" type="text/x-dot-template">
	<li data-name="{{=it.name}}" class="asset">
		<div><img src="/<?php echo $_['base_url']; ?>res/core/images/icons/{{=it.icon}}" width="16" height="16">{{=it.title}}</div>
		<div>{{=it.size}}</div>
		<div>
			<a href="#" class="halt inline-rounded"><i class="fa fa-trash-o"></i></a>
			<a href="#" class="sure inline-rounded" data-tooltip="Click to confirm" data-name="{{=it.name}}"><i class="fa fa-trash-o"></i></a>
		</div>
	</li>
</script>

<script id="image_item" type="text/x-dot-template">
	<li data-name="{{=it.name}}">
		<div class="delete">
			<a href="#" class="halt inline-rounded"><i class="fa fa-trash-o"></i></a>
			<a href="#" class="sure inline-rounded" data-tooltip="Click to confirm" data-name="{{=it.name}}"><i class="fa fa-trash-o"></i></a>
		</div>
		<div class="caption"><strong>{{=it.title}}</strong></div>
		{{? it.width > 200}}
		<a href="/<?php echo $_['base_url']; ?>res/{{=it.url}}" rel="gallery" class="fancybox">
			<img src="/<?php echo $_['base_url']; ?>res/{{=it.url}}/200/"
				 alt="{{=it.name}}"
				 title="{{=it.title}}"
				 {{=it.attr}}>
		</a>
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
	$(function () {
		// preliminaries
		var breadcrumbs = $('#breadcrumbs');
		var directories_assets = $('#directories-assets');
		var images = $('#images');

		var directory_item = doT.template($('#directory_item').text());
		var asset_item = doT.template($('#asset_item').text());
		var image_item = doT.template($('#image_item').text());

		// loading initial data
		var dir = '';
		function loadDir(newDir) {
			dir = newDir;
			$('#upload input[name="dir"]').val(dir);

			directories_assets.find('li:not(:first):not(.load-status)').slideUp(100, function () { $(this).remove(); });
			images.find('li:not(.load-status)').slideUp(100, function () { $(this).remove(); });

			setTimeout(function () {
				api('/' + base_url + 'api/core/assets/', {
					action: 'get_breadcrumbs',
					dir: dir
				}, function (data) {
					breadcrumbs.find('*:not(a:first)').remove();
					$.each(data['breadcrumbs'], function (i) {
						breadcrumbs.append('<span>&gt;</span><a href="#!dir=' + this.dir + '">' + this.name + '</a>');
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
						$(directory_item(this)).hide().appendTo(directories_assets).slideDown(100);
					});

					$.each(data['assets'], function () {
						$(asset_item(this)).hide().appendTo(directories_assets).slideDown(100);
					});
				}, function () {
					apiLoadStatusError($('#load_status_directories_assets'));
				});

				apiLoadStatusWorking($('#load_status_images'));
				$('#load_status_images').show();
				api('/' + base_url + 'api/core/assets/', {
					action: 'get_images',
					dir: dir,
					max_width: 200
				}, function (data) {
					if (!data['images'].length) {
						apiLoadStatusEmpty($('#load_status_images'));
						return;
					}
					$('#load_status_images').hide();

					$.each(data['images'], function () {
						$(image_item(this)).hide().appendTo(images).slideDown(100);
					});
				}, function () {
					apiLoadStatusError($('#load_status_images'));
				});
			}, 100);
		}

		function hashchange() {
			// use copy-pastable AJAX links for directory navigation
			if (window.location.hash.substr(0, 6) == '#!dir=') {
				dir = window.location.hash.substr(6);
			}
			loadDir(dir);
		}
		window.onhashchange = hashchange;
		hashchange();

		directories_assets.on('click', '.directory > div:nth-child(1)', function (e) {
			e.stopPropagation();
			$(this).find('a').click();
		});

		directories_assets.on('click', '.directory > div:nth-child(1) > a', function (e) {
			e.stopPropagation();
			if (typeof $(this).attr('data-dir') !== 'undefined') {
				window.location.hash = '!dir=' + $(this).attr('data-dir');
			}
		});

		// deleting directories, assets or images
		directories_assets.on('click', 'li.directory a.sure', function () {
			apiStatusWorking('Deleting directory...');
			var item = $(this);
			api('/' + base_url + 'api/core/assets/', {
				action: 'delete_directory',
				name: item.attr('data-name'),
				dir: dir
			}, function () {
				apiStatusSuccess('Deleted directory');
				item.closest('li').slideUp('fast', function () {
					$(this).remove();

					if (directories_assets.find('li').length == 2) {
						apiLoadStatusEmpty($('#load_status_directories_assets'));
						$('#load_status_directories_assets').slideDown(100)
					}
				});
			}, function () {
				apiStatusError('Deleting directory failed');
			});
		});

		directories_assets.on('click', 'li.asset a.sure', function () {
			apiStatusWorking('Deleting asset...');
			var item = $(this);
			api('/' + base_url + 'api/core/assets/', {
				action: 'delete_file',
				name: item.attr('data-name'),
				dir: dir
			}, function () {
				apiStatusSuccess('Deleted asset');
				item.closest('li').slideUp('fast', function () {
					$(this).remove();

					if (directories_assets.find('li').length == 2) {
						apiLoadStatusEmpty($('#load_status_directories_assets'));
						$('#load_status_directories_assets').slideDown(100);
					}
				});
			}, function () {
				apiStatusError('Deleting asset failed');
			});
		});

		images.on('click', 'a.sure', function () {
			apiStatusWorking('Deleting image...');
			var item = $(this);
			api('/' + base_url + 'api/core/assets/', {
				action: 'delete_file',
				name: item.attr('data-name'),
				dir: dir
			}, function () {
				apiStatusSuccess('Deleted image');
				item.closest('li').slideUp('fast', function () {
					$(this).remove();

					if (images.find('li').length == 2) {
						apiLoadStatusEmpty($('#load_status_images'));
						$('#load_status_images').slideDown(100)
					}
				});
			}, function () {
				apiStatusError('Deleting image failed');
			});
		});

		$('#create-directory').on('keyup', 'input', function (e) {
		    if (e.keyCode == 13) {
		        $("#create-directory a").click();
		    }
		});

		$('#create-directory').on('click', 'a', function () {
			apiStatusWorking('Creating directory...');
			api('/' + base_url + 'api/core/assets/', {
				action: 'create_directory',
				name: $(this).prev('input').val(),
				dir: dir
			}, function (data) {
				apiStatusSuccess('Created directory');
				$('#create-directory input').val('');
				var item = directory_item(data['directory']);
				if (directories_assets.find('li.directory').length) {
					addAlphabetically(directories_assets.find('li.directory'), item, data['directory']['name']);
				} else {
					$(item).hide().insertAfter(directories_assets.find('li:first')).slideDown('fast');
				}
			}, function () {
				apiStatusError('Creating directory failed');
			});
		});

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