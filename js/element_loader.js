function loadElement(file, elementID, callback) {
    var container = document.getElementById(elementID);
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "templates/" + file, true);
    xhr.onreadystatechange = function() {
      if (xhr.readyState === 4 && xhr.status === 200) {
        container.insertAdjacentHTML('beforeend', xhr.responseText);
        if (callback && typeof callback === 'function') {
          callback();
        }
      }
    };
    xhr.send();
  }
function configureWidgets(widgetNumber, widgetTitle, widgetIcon, widgetValue, widgetTextValue, widgetText) {
    document.getElementById("widget-title-" + widgetNumber).innerHTML = widgetTitle;
    document.getElementById("widget-icon-" + widgetNumber).innerHTML = widgetIcon;
    document.getElementById("widget-value-" + widgetNumber).innerHTML = widgetValue;
    document.getElementById("widget-text-value-" + widgetNumber).innerHTML = widgetTextValue;
    document.getElementById("widget-text-" + widgetNumber).lastChild.textContent = widgetText;
}
