<div class="dex popup-wrapper">
	<div class="popup">
		<div>
			<h2>Insert link</h2>
			<h3>Website link</h3>
			<div id="external-link">
				<input type="text" placeholder="http://www.domain.com/"><a href="#" class="properties inline-button"><i class="fa fa-arrow-right"></i>&ensp;Properties</a>
			</div>

			<h3>Page links</h3>
			<ul id="links" class="small-table">
			  <li>
				<div style="width:120px;">Title</div>
				<div style="width:380px;">Link</div>
			  </li>
			</ul>
		</div>
		<div>
			<h2>Link properties</h2>
			<form>
				<input id="insert_url" type="hidden"></p>
				<p><label>Text</label><input id="insert_text" type="text" data-tooltip="Clickable text"></p>
				<p><label>Description</label><input id="insert_title" type="text" data-tooltip="Shown when hovering"></p>
				<input id="insert_submit" type="hidden">
				<a href="#" class="insert button"><i class="fa fa-plus-square"></i>&ensp;Done</a>
			</form>
		</div>
	</div>
</div>

<script id="link_item" type="text/x-dot-template">
	<li data-title="{{=it.title}}" data-url="/<?php echo $_['base_url']; ?>{{=it.url}}">
		<div style="width:120px;">{{=it.title}}</div>
		<div style="width:380px;"><a href="#">/{{=it.url}}</a></div>
	</li>
</script>

<script type="text/javascript">
	var links = $('#links');
	var link_item = doT.template($('#link_item').text());
	api('/' + base_url + 'api/core/pages/', {
		action: 'get_pages'
	}, function (data) {
		$.each(data['pages'], function () {
			links.append(link_item(this));
		});
	});

	var popup = $('.popup');
	links.on('click', 'li:not(:first)', function () {
		$('#insert_title').val($(this).attr('data-title'));
		$('#insert_url').val($(this).attr('data-url'));
		if ($('#insert_text').val().length === 0) {
			$('#insert_text').val($(this).attr('data-title'));
		}
		switchPopupFrame(popup);
	});

	popup.on('click', '#external-link a', function () {
		$('#insert_url').val($('#external-link input').val());
		if ($('#insert_text').val().length === 0) {
			$('#insert_text').val($('#insert_url').val());
		}
		switchPopupFrame(popup);
	});

	popup.on('click', 'a.insert', function () {
		$('#insert_submit').val('1');
		parent.$.fancybox.close();
	});
</script>