var allSelected = false;
var selected = [];

function checkSelect() {
    if (selected.length == 0) {
        displayErrorForm("No items selected!");
        return false;
    }
    return true;
}


function clearFilters() {
    var tables = getTables();
    document.getElementById("advanced-filter").value = "";
    document.getElementById("filter").value = "";
    for (k = 0; k < tables.length; k++) {
        var rows = tables[k].rows;
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
}

function filterTable() {
    var tables = getTables();
    var column = document.getElementById("column_select").value;
    var filter = document.getElementById("advanced-filter").value.toUpperCase();
    for (k = 0; k < tables.length; k++) {
        var rows = tables[k].rows;
        for (i = 1; i < rows.length; i++) {
            var item = rows[i].getElementsByTagName("TD")[column].innerHTML.toUpperCase();
            if (item.indexOf(filter) > -1) {
                rows[i].style.display = "";
            } else {
                rows[i].style.display = "none";
            }
        }
    }
}

function searchTableFilter(table, column, filter, d) {
    filter = filter.toUpperCase();
    var rows = table.rows;
    for (i = 1; i < rows.length; i++) {
        var item = rows[i].getElementsByTagName("TD")[column].innerHTML.toUpperCase();
        if (item.indexOf(filter) != -1) {
            rows[i].style.display = "";
        } else {
            if (d) {
                table.deleteRow(i);
                i--;
            } else {
                rows[i].style.display = "none";
            }
        }
    }
}

function searchTableDateFilter(table, column, filter, d) {
    var currentDate = new Date();
    var currentYear = currentDate.getFullYear();
    var currentMonth = currentDate.getMonth() + filter;
    var currentDay = currentDate.getDate();
    var rows = table.rows;
    for (i = 1; i < rows.length; i++) {
        var expiryDate = rows[i].getElementsByTagName("TD")[column].innerHTML.toUpperCase();
        var inputYear = parseInt(expiryDate.substring(0, 4));
        var inputMonth = parseInt(expiryDate.substring(5, 7));
        var inputDay = parseInt(expiryDate.substring(8, 10));
        var monthDiff = (inputYear - currentYear) * 12 + (inputMonth - currentMonth);
        if (monthDiff <= 1 && (inputDay <= currentDay || monthDiff < 1)) {
            rows[i].style.display = "";
        } else {
            if (d) {
                table.deleteRow(i);
                i--;
            } else {
                rows[i].style.display = "none";
            }
        }
    }
}

function searchTable(d) {
    var tables = getTables();
    var filter = document.getElementById("filter").value.toUpperCase();
    for (k = 0; k < tables.length; k++) {
        var rows = tables[k].rows;
        var columnLength = rows[0].cells.length - 1;
        var located = false;
        for (i = 1; i < rows.length; i++) {
            for (h = 1; h < columnLength; h++) {
                var item = rows[i].getElementsByTagName("TD")[h].innerHTML.toUpperCase();
                if (item.indexOf(filter) > -1) {
                    located = true
                }
            }
            if (located == false) {
                if (d) {
                    tables[k].deleteRow(i);
                    i--;
                } else {
                    rows[i].style.display = "none";
                }
            } else {
                rows[i].style.display = "";
                located = false;
            }
        }
    }
}

function sortTable(table, n) {
    var rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
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
            switchcount++;
        } else {
            if (switchcount == 0 && dir == "asc") {
                dir = "desc";
                switching = true;
            }
        }
    }
}

function select(tr) {
    var rowIndex = tr.rowIndex;
    if (!selected.includes(rowIndex)) {
        selected.push(rowIndex);
        tr.classList.add('row-selected');
    } else {
        tr.classList.remove('row-selected');
        selected.pop(rowIndex);
    }
}

function selectAll() {
    var tables = getTables();
    allSelected = !allSelected;
    for (k = 0; k < tables.length; k++) {
        var rows = tables[k].rows;
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
}

function deleteMode() {
    var tables = getTables();
    for (k = 0; k < tables.length; k++) {
        var rows = tables[k].rows;
        var columnLength = rows[0].cells.length;
        var button = document.getElementById("delete-button");
        if (button == null) { // if the table is in edit mode
            for (i = 1; i < rows.length; i++) {
                var item = rows[i].getElementsByTagName("TD")[columnLength];
                item.lastChild.innerHTML = "&#xe3c9;";
                item.setAttribute("onclick", "displayEditForm(" + k + ", " + (i - 1) + ");");
            }
            button = document.getElementById("edit-button");
            button.id = "delete-button";
            button.innerHTML = "&#xe872;"
        } else {
            for (i = 1; i < rows.length; i++) {
                var item = rows[i].getElementsByTagName("TD")[columnLength];
                item.lastChild.innerHTML = "&#xe872;";
                item.setAttribute("onclick", "displayDeleteForm(" + k + ", " + (i - 1) + ");");
            }
            button.id = "edit-button";
            button.innerHTML = "&#xe3c9;"
        }
    }
}

function findColumnIndexByName(table, name) {
    name = name.toUpperCase();
    for (i = 0; i < table.rows[0].getElementsByTagName("TH").length; i++) {
        var column = table.rows[0].getElementsByTagName("TH")[i].innerHTML.toUpperCase();
        if (column == name) {
            return i;
        }
    }
    console.log("findColumnIndexByName ERROR: Could not find column!");
    return -1;
}

function clearEditColumn(table) {
    var rows = table.rows;
    var columnLength = rows[0].cells.length;
    for (i = 1; i < rows.length; i++) {
        var item = rows[i].getElementsByTagName("TD")[columnLength];
        item.style.display = "none";
    }
}

function clearEditColumns(tables) {
    for (k = 0; k < tables.length; k++) {
        var rows = tables[k].rows;
        var columnLength = rows[0].cells.length;
        for (i = 1; i < rows.length; i++) {
            var item = rows[i].getElementsByTagName("TD")[columnLength];
            item.style.display = "none";
        }
    }
}

function getTables() {
    return document.getElementsByTagName("TABLE");
}

function removeEmptyTable() {
    var tables = getTables();
    for (k = 0; k < tables.length; k++) {
        if (tables[k].rows.length < 2) {
            tables[k].remove();
            k = k - 1;
        }
    }
}

function viewAssoc() {
    if (selected.length == 0) {
        displayErrorForm("Nothing selected!");
    } else {
        var inputs = document.getElementById("view-assoc-form").elements;
        var rowData = getTables()[0].rows[selected[0]].getElementsByTagName("TD");
        for (i = 0; i < inputs.length - 1; i++) {
            inputs[i + 1].value = rowData[i].innerText;
        }
        document.getElementById("view-assoc-form").submit();
    }
}

function goto() {

}