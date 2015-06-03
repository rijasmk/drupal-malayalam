function showCalendar(id, dateFormat) {
  var el = document.getElementById(id);
  if (calendar != null) {
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

function submit_in(pressbutton) {
  if (!document.getElementById("load_or_no")) {
    alert(Drupal.t("Please wait while page loading"));
    return;
  }
  else {
    if (document.getElementById("load_or_no").value == "0") {
      alert(Drupal.t("Please wait while page loading"));
      return;
    }
  }
  document.getElementById("all_Form_Maker").action = Drupal.settings.form_maker.string_form_submitin + pressbutton + "&id=" + Drupal.settings.form_maker.row_id;
  document.getElementById("all_Form_Maker").submit();
}

function submitbutton(pressbutton) {
  var form = document.all_Form_Maker;
  if (pressbutton == "cancel") {
    submit_in(pressbutton);
    return;
  }

  if (form.title.value == "") {
    alert(Drupal.t("The form must have a title."));
    return;
  }
  if (form.mail.value != "") {
    subMailArr = form.mail.value.split(",");
    emailListValid = true;
    for (subMailIt = 0; subMailIt < subMailArr.length; subMailIt ++) {
      trimmedMail = subMailArr[subMailIt]. replace(/^\s+|\s+$/g, "");
      if (trimmedMail.search(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/) == -1) {
        alert(Drupal.t("This is not a list of valid email addresses."));
        emailListValid = false;
        break;
      }
    }
    if (!emailListValid) {
      return
    }
  }
  if (Drupal.settings.form_maker.row_counter == 0) {
    var form_view = document.getElementById('form_view');
    GLOBAL_tr = form_view.firstChild;
    tox = '';
    for (x = 0; x < GLOBAL_tr.childNodes.length; x++) {
      td = GLOBAL_tr.childNodes[x];
      tbody = td.firstChild.firstChild;
      for (y = 0; y < tbody.childNodes.length; y++) {
        tr = tbody.childNodes[y];
        l_label = document.getElementById(tr.id + '_element_label').innerHTML;
        l_label = l_label.replace(/(\r\n|\n|\r)/gm," ");
        tox = tox + tr.id + '#**id**#' + l_label + '#**label**#' + tr.getAttribute('type') + '#****#';
      }
    }
  }
  else {
    var form_view = document.getElementById("form_view");
    GLOBAL_tr = form_view.firstChild;
    tox = "";
    l_id_array = Drupal.settings.form_maker.labels_id.split(",");
    l_id_removed = [];
    for (x = 0; x < l_id_array.length; x++) {
      l_id_removed[x] = true;
    }
    l_label_array = Drupal.settings.form_maker.labels_label.split(",");;
    l_type_array = Drupal.settings.form_maker.labels_type.split(",");;
    for (x = 0; x < GLOBAL_tr.childNodes.length; x++) {
      td = GLOBAL_tr.childNodes[x];
      tbody = td.firstChild.firstChild;
      for (y = 0; y < tbody.childNodes.length; y++) {
        is_in_old = false;
        tr = tbody.childNodes[y];
        l_id = tr.id;

        l_label = document.getElementById(tr.id + "_element_label").innerHTML;
        l_label = l_label.replace(/(\r\n|\n|\r)/gm, " ");
        l_type = tr.getAttribute("type");
        for (z = 0; z < l_id_array.length; z++) {
          if (l_id_array[z] == l_id) {
            l_id_removed[z] = false;
          }
        }
        tox = tox + l_id + "#**id**#" + l_label + "#**label**#" + l_type + "#****#";
      }
    }
    for (x = 0; x < l_id_array.length; x++) {
      if (l_id_removed[x]) {
        tox = tox + l_id_array[x] + "#**id**#" + l_label_array[x] + "#**label**#" + l_type_array[x] + "#****#";
      }
    }
  }
  document.getElementById("label_order").value = tox;
  submit_in(pressbutton);
}

function enable() {
  document.getElementById("formmakerDiv").style.display	= (document.getElementById("formmakerDiv").style.display == "block" ? "none" : "block");
  document.getElementById("formmakerDiv1").style.display	= (document.getElementById("formmakerDiv1").style.display == "block" ? "none" : "block");
  if (document.getElementById("formmakerDiv").offsetWidth) {
    document.getElementById("formmakerDiv1").style.width = (document.getElementById("formmakerDiv").offsetWidth - 60) + "px";
  }
  document.getElementById("when_edit").style.display = "none";
}

function enable2() {
  document.getElementById("formmakerDiv").style.display	= (document.getElementById("formmakerDiv").style.display == "block" ? "none" :  "block");
  document.getElementById("formmakerDiv1").style.display	= (document.getElementById("formmakerDiv1").style.display == "block" ? "none" : "block");
  if (document.getElementById("formmakerDiv").offsetWidth) {
    document.getElementById("formmakerDiv1").style.width	= (document.getElementById("formmakerDiv").offsetWidth - 60) + "px";
  }
  document.getElementById("when_edit").style.display = "block";
  if (document.getElementById("field_types").offsetWidth) {
    document.getElementById("when_edit").style.width = document.getElementById("field_types").offsetWidth + "px";
  }
  if(document.getElementById("field_types").offsetHeight) {
    document.getElementById("when_edit").style.height	= document.getElementById("field_types").offsetHeight + "px";
  }
  //document.getElementById("when_edit").style.position="none";
}

function submit_form_postid(x) {
  var val = x.options[x.selectedIndex].value;
  document.getElementById("post_id").value = val;
}

function formOnload() {
  for(t = 0; t < Drupal.settings.form_maker.row_counter; t ++) {
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
  document.getElementById("load_or_no").value = 1;
  document.getElementById("form").value = document.getElementById("take").innerHTML;
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

function submit_in_css(x) {
  document.getElementById("edit_css").action = Drupal.settings.form_maker.string_form_css + x + "&id=" + Drupal.settings.form_maker.row_id;
  document.getElementById("edit_css").submit();
}

function submit_in_java(x) {
  document.getElementById("edit_js").action = Drupal.settings.form_maker.string_form_java + x + "&id=" + Drupal.settings.form_maker.row_id_java;
  document.getElementById("edit_js").submit();
}

function submit_in_textinemail(x) {
  document.getElementById("all_Form_Maker").action = Drupal.settings.form_maker.string_textinemail_submitin + x + "&id=" + Drupal.settings.form_maker.id;
  document.getElementById("all_Form_Maker").submit();
}

function val() {
  document.getElementById("adminForm").action = Drupal.settings.form_maker.string_forchrome + Drupal.settings.form_maker.id_forchrome;
  document.getElementById("adminForm").submit();
}

function submit_in_editsub(x) {
  document.getElementById("edit_submit").action = Drupal.settings.form_maker.string_edit_submission + x + "&submission_id=" + Drupal.settings.form_maker.get_submission_id;
  document.getElementById("edit_submit").submit();
}
