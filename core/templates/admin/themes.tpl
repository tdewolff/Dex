<h2>Themes</h2>
<ul id="themes" class="grid">
</ul>

<script id="theme_item" type="text/x-dot-template">
    <li id="theme_{{=it.name}}" data-theme-name="{{=it.name}}" {{?it.current}}class="current"{{?}}>
        <h4>{{=it.title}}</h4>
        <div>({{=it.author}})</div>
        <img src="/<?php echo $_['base_url']; ?>res/theme/{{=it.name}}/preview.png" alt="{{=it.name}}" width="256" height="256">
    </li>
</script>

<script type="text/javascript">
    $(function() {
        var themes = $('#themes');
        var theme_item = doT.template($('#theme_item').text());
        api('/' + base_url + 'api/core/themes.php', {
            action: 'get_themes'
        }, function(data) {
            $.each(data['themes'], function() {
                themes.append(theme_item(this));
            });
        });

        themes.on('click', 'li', function() {
            var item = $(this);
            if (!item.hasClass('current'))
                api('/' + base_url + 'api/core/themes.php', {
                    action: 'change_theme',
                    theme_name: item.attr('data-theme-name')
                }, function() {
                    themes.find('li.current').removeClass('current');
                    item.addClass('current');
                });
        });
    });
</script>