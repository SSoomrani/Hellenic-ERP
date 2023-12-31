function hideForm(data) {
    data.offsetParent.style.display = "none";
}

function cleanHideForm(form) {
    var elements = document.getElementById("added-elements");
    while (elements.firstChild) {
        elements.removeChild(elements.firstChild);
    }
    form.offsetParent.style.display = "none";
}

function displayErrorForm(errorMsg) {
    document.getElementById('error-form-message').innerText = errorMsg;
    document.getElementById('error-form').style.display = "block";
}

function displayEditForm(n, table) {
    document.getElementById('edit-form').style.display = "block";
    var rows = table.rows;
    n++;
    document.getElementById('edit-form-identity').value = rows[n].getElementsByTagName("TD")[0].innerText;
    console.log(document.getElementById('edit-form-identity'));
    for (var i = 0; i < table.rows[0].getElementsByTagName("TH").length; i++) {
        var columnName = rows[0].getElementsByTagName("TH")[i].innerText.replace(/\s/g, "").toUpperCase() + "_edit";
        if (document.getElementById(columnName)) {
            document.getElementById(columnName).value = rows[n].getElementsByTagName("TD")[i].innerText;
        }
    }
}

function displayDeleteForm(tableIndex, n) {
    document.getElementById('delete-form').style.display = "block";
    var rows = getTables()[tableIndex].rows;
    if (selected.length == 0) {
        document.getElementById('delete_id').value = rows[n + 1].getElementsByTagName("TD")[0].innerText;
    } else {
        for (i = 0; i < selected.length; i++) {
            console.log(rows[selected[i]].getElementsByTagName("TD")[0].innerText);
            document.getElementById('delete_id').value += rows[selected[i]].getElementsByTagName("TD")[0].innerText + ",";
        }
    }
}

function displayEmailForm(customerIdentifiers) {
    document.getElementById('email-invoice-form').style.display = "block";
    document.getElementById('selectedCount').value = selected.length;
    if (selected.length == 0) {
        document.getElementById('select-error').innerText = "No invoices selected!";
    } else {
        document.getElementById('select-error').innerText = "";
    }

    var table = document.getElementById("tableView");
    var rows = table.rows;
    var headerRow = rows[0];
    var customerColumnIndex = -1;
    var nameColumnIndex = -1;
    var totalColumnIndex = -1;
    const array = [0, 0, 0];
    for (i = 0; i < headerRow.cells.length; i++) {
        if (headerRow.cells[i].innerText == "Customer ID") {
            array[0] = i;
        } else if (headerRow.cells[i].innerText == "Title") {
            array[1] = i;
        } else if (headerRow.cells[i].innerText == "Total") {
            array[2] = i;
        }
    }
    var data = [0, 0, 0, 0, 0];
    var headerNames = ["Title", "First Name", "Surname", "Email", "Total"];
    const newTable = document.createElement("table");
    const element = document.getElementById("added-elements");
    newTable.classList.add("form-table");
    element.appendChild(newTable);

    const tableRow = document.createElement("tr");
    newTable.appendChild(tableRow);
    for (i = 0; i < headerNames.length; i++) {
        const para = document.createElement("th");
        const node = document.createTextNode(headerNames[i]);
        para.appendChild(node);
        tableRow.appendChild(para);
    }

    for (i = 0; i < selected.length; i++) {

        const tableRow = document.createElement("tr");
        newTable.appendChild(tableRow);

        data[0] = rows[selected[i]].getElementsByTagName("TD")[array[1]].innerText;
        data[4] = rows[selected[i]].getElementsByTagName("TD")[array[2]].innerText;

        var customerID = rows[selected[i]].getElementsByTagName("TD")[array[0]].innerText;
        var startingIndex = customerIdentifiers.indexOf(customerID);
        if (startingIndex == -1) {
            document.getElementById('select-error').innerText = "There is no user with ID " + customerID + " for invoice " + data[0] + "!";
        } else {
            data[1] = customerIdentifiers[1];
            data[2] = customerIdentifiers[2];
            data[3] = customerIdentifiers[3];
            for (k = 0; k < data.length; k++) {
                const input = document.createElement("input");
                input.setAttribute("name", "selected" + i + "-" + k);
                input.setAttribute("value", data[k]);
                input.setAttribute("type", "hidden");
                element.appendChild(input);
                const td = document.createElement("td");
                const tableData = document.createTextNode(data[k]);
                td.appendChild(tableData);
                tableRow.appendChild(td);
            }
            const td = document.createElement("td");
            const icon = document.createElement("i");
            const node = document.createTextNode("send");
            icon.classList.add("material-icons");
            td.classList.add("edit-column");
            icon.appendChild(node);
            td.appendChild(icon);
            tableRow.appendChild(td);
        }
    }
}

function printForm() {
    if (checkSelect()) {
        document.getElementById("print-row-id").value = getTables()[0].rows[selected[0]].getElementsByTagName("TD")[0].innerText;
        document.getElementById("print-form").submit();
    }
}