<?php

function listRecursion(&$menu, $level)
{
    while (count($menu))
    {
        echo '<li ' . ($menu[0]['selected'] == '1' ? 'class="selected"' : '') . '>';
        echo '<a href="' . $menu[0]['url'] . '">' . $menu[0]['name'] . '</a>';

        if (isset($menu[1]) && $menu[1]['level'] > $level)
        {
            echo '<ul>';
            $menu = array_slice($menu, 1);
            listRecursion($menu, $menu[0]['level']);
            echo '</ul>';
            echo '</li>';
            continue;
        }

        echo '</li>';
        $menu = array_slice($menu, 1);
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