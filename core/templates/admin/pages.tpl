<h2>Pages</h2>
<a href="/<?php echo $_['base_url']; ?>admin/pages/new/" class="button" style="margin-left:20px;"><i class="icon-plus"></i>&ensp;New page</a>
<ul id="pages" class="table">
  <li>
	<div style="width:120px;"></div>
	<div style="width:120px;">Title</div>
	<div style="width:200px;">Link</div>
	<div style="width:120px;">Template</div>
	<div style="width:340px;">Content</div>
  </li>
</ul>

<script id="page_item" type="text/x-dot-template">
	<li id="page_{{=it.link_id}}">
		<div style="width:120px; overflow:visible;">
			<div class="dropdown">
				<a href="/<?php echo $_['base_url']; ?>admin/pages/{{=it.link_id}}/" class="dropdown-select list-button"><i class="icon-pencil"></i>&ensp;Edit</a><a href="#" class="dropdown-toggle list-button"><i class="icon-caret-down"></i></a>
				<ul class="dropdown-menu" role="menu">
					<li>
						<a href="#" class="halt"><i class="icon-fixed-width icon-trash"></i>&ensp;Delete</a>
						<a href="#" class="sure" data-link-id="{{=it.link_id}}" title="Click to confirm"><i class="icon-fixed-width icon-trash"></i>&ensp;Really?</a>
					</li>
				</ul>
			</div>
		</div>
		<div style="width:120px;">{{=it.title}}</div>
		<div style="width:200px;"><a href="/<?php echo $_['base_url']; ?>{{=it.url}}">/{{=it.url}}</a></div>
		<div style="width:120px;">{{=it.template_name}}</div>
		<div style="width:340px;">{{=it.content}}</div>
	</li>
</script>

<script type="text/javascript">
    var pages = $('#pages');
    var page_item = doT.template($('#page_item').text());
    api('/' + base_url + 'api/core/pages.php', {
        action: 'get_pages'
    }, function(data) {
        $.each(data['pages'], function() {
            pages.append(page_item(this));
        });
    });

    pages.on('click', 'a.sure', function() {
    	var item = $(this);
        api('/' + base_url + 'api/core/pages.php', {
            action: 'delete_page',
            link_id: $(this).attr('data-link-id')
        }, function() {
            $('.dropdown-menu').fadeOut('fast');
            $('#page_' + item.attr('data-link-id')).remove();
        });
    });
</script>