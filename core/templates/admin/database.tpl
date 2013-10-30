<h2>Database</h2>
<?php foreach ($_['database'] as $data): ?>
 <strong><?php echo $data['name']; ?></strong>
 <table class="database">
  <tr>
   <?php foreach ($data['columns'] as $column): ?>
    <td><strong><?php echo $column; ?></strong></td>
   <?php endforeach; ?>
  </tr>

  <?php foreach ($data['rows'] as $row): ?>
   <tr>
    <?php foreach ($row as $item): ?>
	   <td><?php echo (strlen($item) > 200 ? substr($item, 0, 200) . '...' : $item); ?></td>
    <?php endforeach; ?>
   </tr>
  <?php endforeach; ?>
 </table>
 <br />
<?php endforeach; ?>