<?php
function listRecursion($menu, $level)
{
    while (count($menu)
    {
        array_shift($menu);

        echo '<li ' . ($item['selected'] == '1' ? 'class="selected"' : '') . '>';
        echo '<a href="/' . $_['base_url'] . $item['url'] . '">' . $item['name'] . '</a>';
        if ($menu[$i + 1]['level'] > $level)
        {
            echo '<ul>';
            $menu = listRecursion($menu, $menu[$i + 1]['level']);
            echo '</ul>';
        }
        echo '</li>';
    }
    return $menu;
}

if (count($_['menu']))
{
    echo '<ul id="nav">';
    listRecursion($_['menu'], 0);
    echo '</ul>';
}
?>