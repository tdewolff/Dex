<?php
function listRecursion($_, $parent_id)
{
    echo '<ul ' . ($parent_id == 0 ? 'id="nav"' : '') . '>';
    foreach ($_['menu'][$parent_id] as $id => $item)
    {
        echo '<li ' . ($item['selected'] == '1' ? 'class="selected"' : '') . '>';
        echo '<a href="' . $_['base_url'] . $item['link'] . '">' . $item['name'] . '</a>';

        if (isset($_['menu'][$id]))
            listRecursion($_, $id);

        echo '</li>';
    }
    echo '</ul>';
}

listRecursion($_, 0);
?>