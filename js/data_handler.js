function isColumnAllIntegers(table, columnIndex) {
    var rows = table.rows;
    for (var i = 1; i < rows.length; i++) {
        var cells = rows[i].getElementsByTagName('td');
        var cellValue = cells[columnIndex].textContent.trim();

        if (!Number.isInteger(parseInt(cellValue)) || cellValue.includes("-")) {
            return false;
        }
    }
    return true;
}

function isValidDate(dateString) {
    var dateRegex = /^\d{4}-\d{2}-\d{2}$/;
    return dateRegex.test(dateString);
}

function isInt(value) {
    return !isNaN(value) &&
        parseInt(Number(value)) == value &&
        !isNaN(parseInt(value, 10));
}