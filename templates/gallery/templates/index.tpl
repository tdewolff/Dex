<a id="back" class="hidden" href="#!">Back</a>
<h3></h3>
<ul id="gallery" class="gallery"></ul>

<script id="album_item" type="text/x-dot-template">
	<li data-name="{{=it.name}}">
		<figure>
			<a href="#!album={{=it.name}}" title="{{=it.title}}">
				<img src="/<?php echo $_['base_url']; ?>res/{{=it.url}}/w={{=it.width}}/h={{=it.height}}/t={{=it.mtime}}/" {{=it.attr}}>
			</a>
			<figcaption>{{=it.name}}</figcation>
		</figure>
	</li>
</script>

<script id="image_item" type="text/x-dot-template">
	<li data-name="{{=it.name}}">
		<figure>
			<a href="/<?php echo $_['base_url']; ?>res/{{=it.url}}" title="{{=it.title}}" rel="gallery" class="fancybox">
				<img src="/<?php echo $_['base_url']; ?>res/{{=it.url}}/w={{=it.width}}/h={{=it.height}}/t={{=it.mtime}}/" {{=it.attr}}>
			</a>
		</figure>
	</li>
</script>

<script type="text/javascript">
	var max_width = (document.documentElement.clientWidth < 1206 ? 133 : 266);
	var max_height = max_width / 1.75;
	$(function () {
		var gallery = $('#gallery');

		var album_item = doT.template($('#album_item').text());
		var image_item = doT.template($('#image_item').text());

		var album = '';
		function load(newAlbum) {
			album = newAlbum;
			gallery.find('li').slideUp(100, function () { $(this).remove(); });

			setTimeout(function () {
				$('h3').text('');
				if (album === '') {
					api('/' + base_url + 'api/template/gallery/index/', {
						action: 'get_albums',
						link_id: link_id,
						max_width: max_width,
						max_height: max_height
					}, function (data) {
						$('a#back').hide();
						$('h3').text('<?php echo __('Albums'); ?>');
						var items = '';
						$.each(data['albums'], function () {
							items += album_item(this);
						});
						$(items).hide().appendTo(gallery).slideDown(100);
					}, function (data) {
					});
				} else {
					api('/' + base_url + 'api/template/gallery/index/', {
						action: 'get_album',
						link_id: link_id,
						album: album,
						max_width: max_width,
						max_height: max_height
					}, function (data) {
						$('a#back').show();
						$('h3').text(album);
						var items = '';
						$.each(data['images'], function () {
							items += image_item(this);
						});
						$(items).hide().appendTo(gallery).slideDown(100);
					}, function () {
					});
				}
			}, 100);
		}

		function hashchange() {
			// use copy-pastable AJAX links for directory navigation
			if (window.location.hash.substr(0, 8) == '#!album=') {
				load(window.location.hash.substr(8));
			} else {
				load('');
			}
		}
		window.onhashchange = hashchange;
		hashchange();

		$('.fancybox').fancybox({
			closeClick: true,
			autoScale: false,
			openEffect: 'elastic',
			openSpeed: 150,
			closeEffect: 'elastic',
			closeSpeed: 150,
			padding: 0,
			helpers: {
				overlay: {
					locked: false
				}
			}
		});
	});
</script>