function showCalendar(id, dateFormat) {
  var el = document.getElementById(id);
  if (calendar != null) {
    // we already have one created, so just update dit.
    calendar.hide();
    calendar.parseDate(el.value);
  }
  else {
    // first-time call, create the calendar
    var cal = new Calendar(true, null, selected, closeHandler);
    // remember the calendar in the global
    calendar = cal;
    // min/max year allowed
    cal.setRange(1900, 2070);
    // optional date format
    if (dateFormat) {
      cal.setDateFormat(dateFormat);
    }
    // create a popup calendar
    calendar.create();
    // set it to a new date
    calendar.parseDate(el.value);
  }
  // inform it about the input field in use
  calendar.sel = el;
  // show the calendar next to the input field
  calendar.showAtElement(el);

  // catch mousedown on the document
  Calendar.addEvent(document, "mousedown", checkCalendar);

  return false;
}

function formOnload() {
  for (t = 0; t < Drupal.settings.form_maker.counter; t ++) {
    if (document.getElementById(t + "_type")) {
      if (document.getElementById(t + "_type").value == "type_map") {
        if_gmap_init(t + "_element", false);
      }
      else {
        if (document.getElementById(t + "_type").value == "type_date")
        Calendar.setup({
          inputField: t + "_element",
          ifFormat: document.getElementById(t + "_button").getAttribute("format"),
          button: t + "_button",
          align: "Tl",
          singleClick: true,
          firstDay: 0
        });
      }
    }
  }
}

function formAddToOnload() {
  if (formOldFunctionOnLoad) {
    formOldFunctionOnLoad();
  }
  formOnload();
}

function formLoadBody() {
  formOldFunctionOnLoad = window.onload;
  window.onload = formAddToOnload;
}

function form_maker_gettype() {
  n = Drupal.settings.form_maker.counter;
  for (i = 0; i < n; i ++) {
    if (document.getElementById(i)) {
      for (z = 0; z < document.getElementById(i).childNodes.length; z ++) {
        if (document.getElementById(i).childNodes[z].nodeType == 3) {
          document.getElementById(i).removeChild(document.getElementById(i).childNodes[z]);
        }
      }
      if (document.getElementById(i).childNodes[7]) {
        document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);
        document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);
        document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);
        document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);
        document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);
        document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);
      }
      else {
        document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);
        document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);
        document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);
        document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);
        document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);
        document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);
      }
    }
  }
  for (i = 0; i <= n; i ++) {
    if (document.getElementById(i)) {
      type = document.getElementById(i).getAttribute("type");
        switch (type) {
          case "type_text":
          case "type_password":
          case "type_submitter_mail":
          case "type_own_select":
          case "type_country":
          case "type_hidden":
          case "type_map": {
            remove_add_(i + "_element");
            break;

          }
          case "type_submit_reset":
            remove_add_(i + "_element_submit");
            if (document.getElementById(i + "_element_reset")) {
              remove_add_(i + "_element_reset");
            }
            break;

          case "type_captcha":
            remove_add_("wd_captcha");
            remove_add_("element_refresh");
            remove_add_("wd_captcha_input");
            break;

          case "type_file_upload":
            remove_add_(i + "_element");
            if (document.getElementById(i + "_element").value == "") {
              seted = false;
              break;
            }
            ext_available = getfileextension(i);
            if (!ext_available) {
              seted = false;
            }
            break;

          case "type_textarea":
            remove_add_(i + "_element");
            if (document.getElementById(i + "_element").innerHTML == document.getElementById(i + "_element").title || document.getElementById(i + "_element").innerHTML == "") {
              seted = false;
            }
            break;

          case "type_name":
            if (document.getElementById(i + "_element_title")) {
              remove_add_(i + "_element_title");
              remove_add_(i + "_element_first");
              remove_add_(i + "_element_last");
              remove_add_(i + "_element_middle");
                if (document.getElementById(i + "_element_title").value == "" || document.getElementById(i + "_element_first").value == "" || document.getElementById(i + "_element_last").value == "" || document.getElementById(i + "_element_middle").value == "") {
                  seted = false;
                }
              }
              else {
                remove_add_(i + "_element_first");
                remove_add_(i + "_element_last");
                if (document.getElementById(i + "_element_first").value == "" || document.getElementById(i + "_element_last").value == "") {
                  seted = false;
                }
              }
              break;

          case "type_checkbox":
          case "type_radio":
            is = true;
            for (j = 0; j < 100; j ++) {
              if (document.getElementById(i + "_element" + j)) {
                remove_add_(i + "_element" + j);
                if (document.getElementById(i + "_element" + j).checked) {
                  is = false;
                  break;
                }
              }
            }
            if (is) {
              seted = false;
            }
            break;

          case "type_button":
            for (j = 0; j < 100; j ++) {
              if (document.getElementById(i + "_element" + j)) {
                remove_add_(i + "_element" + j);
              }
            }
            break;

          case "type_time":
            if (document.getElementById(i + "_ss")) {
              remove_add_(i + "_ss");
              remove_add_(i + "_mm");
              remove_add_(i + "_hh");
              if (document.getElementById(i + "_ss").value == "" || document.getElementById(i + "_mm").value == "" || document.getElementById(i + "_hh").value == "") {
                seted = false;
              }
            }
            else {
              remove_add_(i + "_mm");
              remove_add_(i + "_hh");
              if (document.getElementById(i + "_mm").value == "" || document.getElementById(i + "_hh").value == "") {
                seted = false;
              }
            }
            break;

          case "type_date":
            remove_add_(i + "_element");
            remove_add_(i + "_button");
            if (document.getElementById(i + "_element").value == "") {
              seted = false;
            }
            break;

          case "type_date_fields":
            remove_add_(i + "_day");
            remove_add_(i + "_month");
            remove_add_(i + "_year");
            if (document.getElementById(i + "_day").value == "" || document.getElementById(i + "_month").value == "" || document.getElementById(i + "_year").value == "") {
              seted = false;
            }
            break;

        }
    }
  }
}

function check_year2(id) {
  year = document.getElementById(id).value;
  from = parseFloat(document.getElementById(id).getAttribute("from"));
  year = parseFloat(year);
  if (year < from) {
    document.getElementById(id).value = "";
    alert(Drupal.t("The value of year is not valid"));
  }
}

function remove_add_(id) {
  attr_name = new Array();
  attr_value = new Array();
  var input = document.getElementById(id);
  atr = input.attributes;
  for (v = 0; v < 30; v ++) {
    if (atr[v]) {
      if (atr[v].name.indexOf("add_") == 0) {
        attr_name.push(atr[v].name.replace("add_", ""));
        attr_value.push(atr[v].value);
        input.removeAttribute(atr[v].name);
        v --;
      }
    }
  }
  for (v = 0; v < attr_name.length; v ++) {
    input.setAttribute(attr_name[v],attr_value[v])
  }
}

function getfileextension(id) {
  var fileinput = document.getElementById(id + "_element");
  var filename = fileinput.value;
  if (filename.length == 0) {
    return true;
  }
  var dot = filename.lastIndexOf(".");
  var extension = filename.substr(dot + 1, filename.length);
  var exten = document.getElementById(id + "_extension").value.replace("***extensionverj" + id + "***", "").replace("***extensionskizb" + id + "***", "");
  exten = exten.split(",");
  for (x = 0; x < exten.length; x ++) {
    exten[x] = exten[x].replace(/\./g, "");
    exten[x] = exten[x].replace(/ /g, "");
    if (extension.toLowerCase() == exten[x].toLowerCase()) {
      return true;
    }
  }
  return false;
}

function check_required(but_type) {
  if (but_type == "reset") {
    window.location.reload(true);
    return;
  }
  n = Drupal.settings.form_maker.counter;;
  ext_available = true;
  seted = true;
  for (i = 0; i <= n; i ++) {
    if (seted) {
      if (document.getElementById(i)) {
        if (document.getElementById(i + "_required")) {
          if (document.getElementById(i + "_required").value == "yes") {
            type = document.getElementById(i).getAttribute("type");
            switch (type) {
              case "type_text":
              case "type_password":
              case "type_submitter_mail":
              case "type_own_select":
              case "type_country":
                if (document.getElementById(i + "_element").value == document.getElementById(i + "_element").title || document.getElementById(i + "_element").value == "") {
                  seted = false;
                }
                break;

              case "type_file_upload":
                if (document.getElementById(i + "_element").value == "") {
                  seted = false;
                  break;
                }
                ext_available = getfileextension(i);
                if (!ext_available) {
                  seted = false;
                }
                break;

              case "type_textarea":
                if (document.getElementById(i + "_element").innerHTML == document.getElementById(i + "_element").title || document.getElementById(i + "_element").innerHTML == "") {
                  seted = false;
                }
                break;

              case "type_name":
                if (document.getElementById(i + "_element_title")) {
                  if (document.getElementById(i + "_element_title").value == "" || document.getElementById(i + "_element_first").value == "" || document.getElementById(i + "_element_last").value == "" || document.getElementById(i + "_element_middle").value == "") {
                    seted = false;
                  }
                }
                else {
                  if (document.getElementById(i + "_element_first").value == "" || document.getElementById(i + "_element_last").value == "") {
                    seted = false;
                  }
                }
                break;

              case "type_checkbox":
              case "type_radio":
                is = true;
                for (j = 0; j < 100; j ++) {
                  if (document.getElementById(i + "_element" + j)) {
                    if (document.getElementById(i + "_element" + j).checked) {
                      is = false;
                      break;
                    }
                  }
                }
                if (is) {
                  seted = false;
                }
                break;

              case "type_time":
                if (document.getElementById(i + "_ss")) {
                  if (document.getElementById(i + "_ss").value == "" || document.getElementById(i + "_mm").value == "" || document.getElementById(i + "_hh").value == "") {
                    seted = false;
                  }
                }
                else {
                  if (document.getElementById(i + "_mm").value == "" || document.getElementById(i + "_hh").value == "") {
                    seted = false;
                  }
                }
                break;

              case "type_date":
                if (document.getElementById(i + "_element").value == "") {
                  seted = false;
                }
                break;

              case "type_date_fields":
                if (document.getElementById(i + "_day").value == "" || document.getElementById(i + "_month").value == "" || document.getElementById(i + "_year").value == "") {
                  seted = false;
                }
                break;

            }
          }
          else {
            type = document.getElementById(i).getAttribute("type");
            if (type == "type_file_upload") {
              ext_available = getfileextension(i);
            }
            if (!ext_available) {
              seted = false;
            }
          }
        }
      }
    }
    else {
      if (!ext_available) {
        alert(Drupal.t("Sorry, you are not allowed to upload this type of file"));
        break;
      }
      x = document.getElementById(i - 1 + "_element_label");
      while (x.firstChild) {
        x = x.firstChild;
      }
      alert(x.nodeValue + "field is required");
      break;
    }
  }
  if (seted) {
    for (i = 0; i <= n; i ++) {
      if (document.getElementById(i)) {
        if (document.getElementById(i).getAttribute("type") == "type_submitter_mail") {
          if (document.getElementById(i + "_element").value != "") {
            if (document.getElementById(i + "_element").value.search(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/) == -1) {
              alert(Drupal.t("This is not a valid email address"));
              return;
            }
          }
        }
      }
    }
  }
  if (seted) {
    create_headers();
  }
}

function create_headers() {
  form_ = document.getElementById("form");
  n = Drupal.settings.form_maker.counter;
  for (i = 0; i < n; i ++) {
    if (document.getElementById(i)) {
      if (document.getElementById(i).getAttribute("type") != "type_map") {
        if (document.getElementById(i).getAttribute("type") != "type_captcha") {
          if (document.getElementById(i).getAttribute("type") != "type_submit_reset") {
            if (document.getElementById(i).getAttribute("type") != "type_button") {
              if (document.getElementById(i + "_element_label")) {
                var input = document.createElement("input");
                input.setAttribute("type", "hidden");
                input.setAttribute("name", i + "_element_label");
                input.value = i;
                form_.appendChild(input);
                if (document.getElementById(i).getAttribute("type") == "type_date_fields") {
                  var input = document.createElement("input");
                  input.setAttribute("type", "hidden");
                  input.setAttribute("name", i + "_element");
                  input.value = document.getElementById(i + "_day").value + "-" + document.getElementById(i + "_month").value + "-" + document.getElementById(i + "_year").value;
                  form_.appendChild(input);
                }
              }
            }
          }
        }
      }
    }
  }
  form_.submit();
}
