var allSelected = false;
var selected = [];

function clearFilters() {
    document.getElementById("advanced-filter").value = "";
    document.getElementById("filter").value = "";
    var table = document.getElementById("tableView");
    var rows = table.rows;
    var columnLength = rows[0].cells.length - 1;
    for (i = 1; i < rows.length; i++) {
        rows[i].style.display = "";
    }
    for (i = 0; i < columnLength - 1; i++) {
        rows[0].getElementsByTagName("TH")[i].style.display = "";
        for (k = 1; k < rows.length; k++) {
            rows[k].getElementsByTagName("TD")[i].style.display = "";
        }
    }
}
function filterTable() {
    var column = document.getElementById("column_select").value;
    var filter = document.getElementById("advanced-filter").value.toUpperCase();
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
function searchTable() {
    var filter = document.getElementById("filter").value.toUpperCase();
    var table = document.getElementById("tableView");
    var rows = table.rows;
    var columnLength = rows[0].cells.length - 1;
    var located = false;
    for (i = 1; i < rows.length; i++) {
        for (k = 1; k < columnLength; k++) {
            var item = rows[i].getElementsByTagName("TD")[k].innerHTML.toUpperCase();
            if (item.indexOf(filter) > -1) {
            located = true
            }
        }
        if (located == false) {
            rows[i].style.display = "none";
        } else {
            rows[i].style.display = "";
            located = false;
        }
    }
}
function sortTable(n) {
    var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
    table = document.getElementById("tableView");
    switching = true;
    dir = "asc";
    var integerSort = isColumnAllIntegers(table, n)
    while (switching) {
        switching = false;
        rows = table.rows;
        for (i = 1; i < (rows.length - 1); i++) {
            shouldSwitch = false;
            x = rows[i].getElementsByTagName("TD")[n];
            y = rows[i + 1].getElementsByTagName("TD")[n];
            if (integerSort) {
                var intX = parseInt(x.innerHTML.toLowerCase().trim());
                var intY = parseInt(y.innerHTML.toLowerCase().trim());
                if (dir == "asc") {
                    if (intX > intY) {
                    shouldSwitch = true;
                    break;
                    }
                    } else if (dir == "desc") {
                        if (intX < intY) {
                        shouldSwitch = true;
                        break;
                    }
                }
            } else {
                if (dir == "asc") {
                        if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                        shouldSwitch = true;
                        break;
                        }
                    } else if (dir == "desc") {
                        if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                        shouldSwitch = true;
                        break;
                    }
                }
            }
        }
        if (shouldSwitch) {
            rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
            switching = true;
            switchcount ++;
        } else {
            if (switchcount == 0 && dir == "asc") {
                dir = "desc";
                switching = true;
            }
        }
    }
}
function select(td) {
    var table = document.getElementById("tableView");
    var rows = table.rows;
    var rowIndex = td.parentNode.rowIndex;
    if (!selected.includes(rowIndex)) {
        selected.push(rowIndex);
        rows[rowIndex].classList.add('row-selected');
    } else {
        rows[rowIndex].classList.remove('row-selected');
        selected.pop(rowIndex);
    }
}
function selectAll() {
    allSelected = !allSelected;
    var table = document.getElementById("tableView");
    var rows = table.rows;
    if (allSelected) {
        for (i = 1; i < rows.length; i++) {
            selected[i] = i;
            rows[i].classList.add('row-selected');
        }
    } else {
        for (i = 1; i < rows.length; i++) {
            rows[i].classList.remove('row-selected');
        }
        selected = [];
    }
}
function deleteMode() {
    var table = document.getElementById("tableView");
    var rows = table.rows;
    var columnLength = rows[0].cells.length;
    var button = document.getElementById("delete-button");
    if (button == null) { // if the table is in edit mode
        for (i = 1; i < rows.length; i++) {
            var item = rows[i].getElementsByTagName("TD")[columnLength];
            item.lastChild.innerHTML = "&#xe3c9;";
            item.setAttribute("onclick", "displayEditForm(" + (i - 1) + ");" );
        }
        button = document.getElementById("edit-button");
        button.id = "delete-button";
        button.innerHTML = "&#xe872;"
    } else {
        for (i = 1; i < rows.length; i++) {
            var item = rows[i].getElementsByTagName("TD")[columnLength];
            item.lastChild.innerHTML = "&#xe872;";
            item.setAttribute("onclick", "displayDeleteForm(" + (i - 1) + ");" );
        }
        button.id = "edit-button";
        button.innerHTML = "&#xe3c9;"
    }
}