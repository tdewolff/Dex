<?php

if (!User::isAdmin())
	user_error('Forbidden access (' . $_SERVER['REQUEST_URI'] . ')', ERROR);

$database = array();
$tables = $db->query("SELECT * FROM sqlite_master WHERE type = 'table';");
while ($table = $tables->fetch())
{
	$table_columns = array();
	$table_rows = array();

	$columns = $db->query("PRAGMA table_info(" . $db->escape($table['name']) . ");");
	while ($column = $columns->fetch())
		$table_columns[] = $column['name'] . ' (' . $column['type'] . ')';

	$rows = $db->query("SELECT * FROM " . $db->escape($table['name']) . ";");
	while ($row = $rows->fetch())
	{
		$table_row = array();
		foreach ($row as $item)
			$table_row[] = htmlentities($item, ENT_QUOTES, 'UTF-8');
		$table_rows[] = $table_row;
	}

	$database[] = array(
		'name' => $table['name'],
		'columns' => $table_columns,
		'rows' => $table_rows
	);
}

Hooks::emit('admin-header');

Core::assign('database', $database);
Core::render('admin/database.tpl');

Hooks::emit('admin-footer');
exit;

?>
