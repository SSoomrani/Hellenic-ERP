<?php
    $rows = get_table_contents($conn, $table_name, $filter);
    $table_info = get_table_info($conn, $table_name);
    $formatted_names = $table_info[0];
    $field_names = $table_info[1];
    $editable_formatted_names = $table_info[2];
    $editable_field_names = $table_info[3];
?>
<table id="<?php echo($table_name); ?>">
    <tr>
        <?php foreach($formatted_names as $key => $value): ?>
            <?php if($key != 0): ?>
                <th onclick="sortTable(this.parentNode.parentNode.parentNode, <?php echo $key; ?>)"><?php echo $formatted_names[$key]; ?></th>
            <?php else: ?>
                <th onclick="selectAll()"><?php echo $formatted_names[$key]; ?></th>
            <?php endif; ?>
        <?php endforeach; ?>
    </tr>
    <?php foreach($rows as $key => $row): ?>
        <tr>
            <?php foreach($field_names as $field_key => $field_name): ?>
                <?php if ($field_key == 0): ?>
                    <td onclick="select(this.parentNode)"><?php echo $rows[$key][$field_names[$field_key]]; ?></td>
                <?php else: ?>
                    <td onclick="select(this.parentNode)"><?php echo $rows[$key][$field_name]; ?></td>
                <?php endif; ?>
            <?php endforeach; ?>
            <td class="edit-column" onclick="displayEditForm(<?php echo($key); ?>, this.parentNode.parentNode.parentNode)"><i class="inline-icon material-icons">edit</i></td>
        </tr>
    <?php endforeach; ?>
</table>