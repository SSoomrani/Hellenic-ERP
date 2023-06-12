<?php
    session_start();

    if (!isset($_SESSION["current_table"])) {
      $tableName = "customers";
    } else {
      $tableName = $_SESSION["current_table"];
    }
    $_SESSION["current_table"] = $tableName;

    require 'dbh.php';
    $query = $conn->query('SHOW FULL COLUMNS FROM '. $tableName);
    $clearNames = array();
    $fieldNames = array();
    $formFields = array();
    while($row = $query->fetch_assoc()) {
        $clearNames[] = $row['Comment'];
        $fieldNames[] = $row['Field'];
        if ($row['Extra'] == null)
        {
          $formFields[] = $row['Comment'];
        }
    }

    $query = $conn->query('SELECT * FROM '. $tableName);
    $rows = $query->fetch_all(MYSQLI_ASSOC);

    $query = $conn->query('SHOW TABLES;');
    $tables = $query->fetch_all(MYSQLI_ASSOC);

    $sqlError = "";
    if (isset($_SESSION['mysql_error'])) {
      $sqlError = $_SESSION['mysql_error'];
    }

    $rowID = -1;
    if (isset($_SESSION['row_id'])) {
      $rowID = $_SESSION['row_id'];
    }
?>
<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
	<meta charset="UTF-8">
	<title>View <?php echo $tableName?></title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
  <link rel="stylesheet" href="styles.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Source+Code+Pro&display=swap" rel="stylesheet">
</head>
<body>
  <div class="sidenav border-radius-xl ms-3 my-3">
      <div class="sidenav-header">
          <h4>
            <i class="material-icons inline-icon">list</i>
            Hellenic Dashboard</h4>
        </div>
        <hr class="dark horizontal my-0">
        <ul>
            <li class="nav-item">
                <h5>PAGES</h5>
            </li>
            <li class="nav-item">
                <a href="./overview.php" class="transport-button">
                <i class="material-icons inline-icon">dashboard</i>
                Dashboard</a>
            </li>
            <li class="nav-item">
                <a href="/view.php" class="transport-button">
                <i class="material-icons inline-icon">table_view</i>
                Tables</a>
            </li>
            <li class="nav-item">
                <a href="/invoices.php" class="transport-button">
                <i class="material-icons inline-icon">receipt_long</i>
                Invoices</a>
            </li>
            <li class="nav-item">
                <a class="transport-button">
                <i class="material-icons inline-icon">warehouse</i>
                Warehouses</a>
            </li>
        </ul>
    </div>
    <div class="main main-content">
      <h5>Pages / Tables</h5>
      <label for="column_select">Choose a column:</label>
        <select id="column_select">
          <?php foreach($clearNames as $key => $value): ?>
            <option value="<?php echo($key); ?>"><?php echo(ucfirst($clearNames[$key])); ?></option>
          <?php endforeach; ?>
        </select>
        <input type="text" id="filter" onkeyup="filterTable()" placeholder="Search.." title="Type in a category">
      <br><br>
      <a onclick="editMode()">Edit</a>
      <a onclick="document.getElementById('id02').style.display='block'">Add <?php echo(ucfirst($tableName)); ?></a>
      <a onclick="document.getElementById('changeTable').style.display='block'">Change Table</a>
      <table id="tableView">
          <tr>
            <?php foreach($clearNames as $key => $value): ?>
              <th onclick="sortTable(<?php echo $key; ?>)"><?php echo $clearNames[$key]; ?></th>
            <?php endforeach; ?>
            <th></th>
          </tr>
          <?php foreach($rows as $key => $row): ?>
              <tr>
                  <?php foreach($fieldNames as $fieldName): ?>
                      <td><?php echo $rows[$key][$fieldName]; ?></td>
                  <?php endforeach; ?>
                  <td onclick="displayEditForm(<?php echo($key); ?>)">Edit</td>
              </tr>
          <?php endforeach; ?>
      </table>
    </div>

    <div id="id02" class="popupForm">
      <form class="addForm-content animate" action="./manageData.php" method="post">
        <input type="hidden" name="table_name" value="<?php echo($tableName);?>">
        <div class="container" id="addForm">
          <p id="add_error"></p>
          <br>
          <?php foreach($formFields as $key => $value): ?>
              <label><?php echo "$formFields[$key]: "; ?></label>
              <br>
              <input id="<?php echo str_replace(' ', '', $formFields[$key]); ?>" type="text" name="<?php echo($fieldNames[$key + 1]); ?>">
          <?php endforeach; ?>
          <div class="buttons">
            <button class="buttons" type="submit" name="add">Submit</button>
          </div>
        </div>
        <div class="container" style="background-color: rgb(128, 128, 128);">
          <p>Cancel</p>
        </div>
      </form>
    </div>
    <div id="id03" class="popupForm">
      <form class="popupForm-content animate" action="./manageData.php" method="post">
        <input type="hidden" id="identity" name="id" value="">
        <input type="hidden" name="table_name" value="<?php echo($tableName);?>">
        <div class="container" id="editForm">
          <p id="edit_error"></p>
          <br>
          <?php foreach($formFields as $key => $value): ?>
              <label><?php echo "$formFields[$key]: "; ?></label>
              <br>
              <input id="<?php echo strtoupper(str_replace(' ', '', $formFields[$key]))."_edit"; ?>" type="text" name="<?php echo $fieldNames[$key + 1];?>" value="">
          <?php endforeach; ?>
          <div class="buttons">
            <button class="buttons" type="submit" name="append">Save</button>
            <button class="buttons" type="submit" name="delete">Delete</button>
          </div>
        </div>
        <div class="container" style="background-color: rgb(128, 128, 128);">
          <p onclick=hideForm(this);>Cancel</p>
        </div>
      </form>
    </div>
    <div id="changeTable" class="popupForm">
      <form class="popupForm-content animate" action="./manageData.php" method="post">
        <h1>Change Table</h1>
        <label for="table">Choose a table:</label>
        <select id="table" name="table_name">
          <?php foreach($tables as $table): ?>
            <option value="<?php echo($table["Tables_in_hellenic"]); ?>"><?php echo(ucfirst($table["Tables_in_hellenic"])); ?></option>
          <?php endforeach; ?>
        </select>
        <br><br>
        <button class="buttons" type="submit" name="change_table">Select</button>
        <div class="container" style="background-color: rgb(128, 128, 128);">
          <p onclick=hideForm(this);>Cancel</p>
        </div>
      </form>
    </div>
    
</body>
</html>
<script>

checkEditError();
window.onclick = function(event) {
    hideForm2(event);
}

function changeTable(table_name) {
  console.log("Changing table");
  var xmlhttp = new XMLHttpRequest();
  xmlhttp.open("GET", "manageData.php?q=" + table_name, true);
  xmlhttp.send();
  window.location.reload();
}

function filterTable() {
  var column = document.getElementById("column_select").value;
  var filter = document.getElementById("filter").value.toUpperCase();
  var table = document.getElementById("tableView");
  var rows = table.rows;
  for (i = 1; i < rows.length; i++) {
    var item = rows[i].getElementsByTagName("TD")[column].innerHTML.toUpperCase();
    if (item.indexOf(filter) > -1) {
      rows[i].style.display = "";
    } else {
      rows[i].style.display = "none";
    }
  }
}

function checkEditError() {
  var error = "<?php echo $sqlError; ?>";
  var rowID = "<?php echo $rowID; ?>"
  if (error != "") {
    var errorMsg = document.getElementById("edit_error");
    errorMsg.innerText = error;
    if (rowID != -1) {
      displayEditForm(rowID - 1);
    }
    <?php session_unset(); ?>
  }
}

function hideForm2(event) {
  var addForm = document.getElementById('id02');
  var editForm = document.getElementById('id03');
  if (event.target == addForm) {
    addForm.style.display = "none";
  }
  if (event.target.innerText == "Cancel") {
    editForm.style.display = "none";
    var errorMsg = document.getElementById("error");
    errorMsg.innerHTML = " ";
  }
}

function hideForm(data) {
  data.offsetParent.style.display = "none";
}

function openNav() {
  document.getElementById("mySidenav").style.width = "250px";
  document.getElementById("main").style.marginLeft = "250px";
}

function closeNav() {
  document.getElementById("mySidenav").style.width = "0";
  document.getElementById("main").style.marginLeft= "0";
}
function editMode() {
  var table = document.getElementById("tableView");
  var rows = table.rows;
  for (var i = 1; i < rows.length; i++) {
    (function(i) {
      var item = rows[i].getElementsByTagName("TD")[0];
      item.onclick = function() { displayEditForm(i); };
      if (item.style.textDecoration == "underline") {
        item.style.textDecoration="none";
      }
      else {
        item.style.textDecoration="underline";
      }
    })(i);
  }
}
function sortTable(n) {
  var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
  table = document.getElementById("tableView");
  switching = true;
  // Set the sorting direction to ascending:
  dir = "asc";
  /* Make a loop that will continue until
  no switching has been done: */
  while (switching) {
    // Start by saying: no switching is done:
    switching = false;
    rows = table.rows;
    /* Loop through all table rows (except the
    first, which contains table headers): */
    for (i = 1; i < (rows.length - 1); i++) {
      // Start by saying there should be no switching:
      shouldSwitch = false;
      /* Get the two elements you want to compare,
      one from current row and one from the next: */
      x = rows[i].getElementsByTagName("TD")[n];
      y = rows[i + 1].getElementsByTagName("TD")[n];
      /* Check if the two rows should switch place,
      based on the direction, asc or desc: */
      if (dir == "asc") {
        if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
          // If so, mark as a switch and break the loop:
          shouldSwitch = true;
          break;
        }
      } else if (dir == "desc") {
        if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
          // If so, mark as a switch and break the loop:
          shouldSwitch = true;
          break;
        }
      }
    }
    if (shouldSwitch) {
      /* If a switch has been marked, make the switch
      and mark that a switch has been done: */
      rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
      switching = true;
      // Each time a switch is done, increase this count by 1:
      switchcount ++;
    } else {
      /* If no switching has been done AND the direction is "asc",
      set the direction to "desc" and run the while loop again. */
      if (switchcount == 0 && dir == "asc") {
        dir = "desc";
        switching = true;
      }
    }
  }
}
function displayEditForm(n) {
  document.getElementById('id03').style.display="block";
  var table = document.getElementById("tableView");
  var rows = table.rows;
  n++;
  document.getElementById('identity').value = rows[n].getElementsByTagName("TD")[0].innerText;
  for (var i = 0; i < table.rows[0].getElementsByTagName("TH").length; i++)
  {
    var columnName = rows[0].getElementsByTagName("TH")[i].innerText.replace(/\s/g, "").toUpperCase() + "_edit";
    console.log(columnName);
    if (document.getElementById(columnName))
    {
      document.getElementById(columnName).value = rows[n].getElementsByTagName("TD")[i].innerText;
    }
  }
}
</script>