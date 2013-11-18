<h2>Templates</h2>
<ul id="templates" class="table">
    <li>
    	<div style="width:120px;">&nbsp;</div>
    	<div style="width:120px;">Name</div>
    	<div style="width:120px;">Author</div>
    	<div style="width:540px;">Description</div>
    </li>
</ul>

<script id="template_item" type="text/x-dot-template">
    <li id="template_{{=it.name}}">
        <div style="width:120px;">&nbsp;</div>
        <div style="width:120px;">{{=it.title}}</div>
        <div style="width:120px;">{{=it.author}}</div>
        <div style="width:540px;">{{=it.description}}</div>
    </li>
</script>

<script type="text/javascript">
    var templates = $('#templates');
    var template_item = doT.template($('#template_item').text());
    api(null, function(data) {
        $.each(data['templates'], function() {
            templates.append(template_item(this));
        });
    });
</script>
