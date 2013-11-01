<?php
function listRecursion($menu, $level)
{
    global $_;

    while (count($menu))
    {
        echo '<li ' . ($menu[0]['selected'] == '1' ? 'class="selected"' : '') . '>';
        echo '<a href="/' . $_['base_url'] . $menu[0]['url'] . '">' . $menu[0]['name'] . '</a>';

        if (isset($menu[1]) && $menu[1]['level'] > $level)
        {
            echo '<ul>';
            $menu = listRecursion($menu, $menu[1]['level']);
            echo '</ul>';
            echo '</li>';
            continue;
        }

        echo '</li>';
        array_shift($menu);
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