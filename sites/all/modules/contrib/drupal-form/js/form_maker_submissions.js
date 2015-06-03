function clear_serch_texts() {
  document.getElementById("search_events_by_title").value = "";
}

function clear_search() {
  document.getElementById("serch_or_not").value = "";
}

function submit_form_id(x) {
  var val = x.options[x.selectedIndex].value;
  window.location.href = Drupal.settings.form_maker.string_form_submission + val;
}

function clear_serch_texts() {
  document.getElementById("startdate").value = "";
  document.getElementById("enddate").value = "";
}

function submit_href(x,y) {
  if (document.getElementById("serch_or_not").value != "search") {
    clear_serch_texts();
  }
  document.getElementById("page_number").value = x;
  document.getElementById("page_left_or_right").value = y;
  document.getElementById("main_show_form").submit();
}

function clear_search() {
  document.getElementById("serch_or_not").value = "";
}

function ordering(x,y) {
  if (document.getElementById("serch_or_not").value != "search") {
    clear_serch_texts();
  }
  document.getElementById("asc_or_desc_by").value = x;
  document.getElementById("asc_or_desc").value = y;
  document.getElementById("main_show_form").submit();
}

function confirmation(href,title) {
  var answer = confirm("Are you sure you want to delete " + title + "?")
  if (answer) {
    document.getElementById("main_show_form").action = href;
    document.getElementById("main_show_form").submit();
  }
}

function renderColumns() {
  allTags = document.getElementsByTagName("*");
  for (curTag in allTags) {
    if (typeof(allTags[curTag].className) != "undefined") {
      if (allTags[curTag].className.indexOf("_fc") > 0) {
        var classNames = allTags[curTag].className;
        var indexOfFC = classNames.indexOf('_fc');
        curLabel = classNames.substring(classNames.lastIndexOf(' ', indexOfFC) + 1, indexOfFC);
        if (document.forms.main_show_form.hide_label_list.value.indexOf("@" + curLabel + "@") >= 0) {
          allTags[curTag].style.display = "none";
        }
        else {
          allTags[curTag].style.display = "";
        }
      }
    }
  }
}

function clickLabChB(label, ChB) {
  document.forms.main_show_form.hide_label_list.value = document.forms.main_show_form.hide_label_list.value.replace("@" + label + "@", "");
  if (document.forms.main_show_form.hide_label_list.value == "") {
    document.getElementById("ChBAll").checked = true;
  }
  if (!(ChB.checked)) {
    document.forms.main_show_form.hide_label_list.value += "@" + label + "@";
    document.getElementById("ChBAll").checked = false;
  }
  renderColumns();
}

function form_maker_getScrollWidth() {
  if (this.ie) {
    return Math.max(document.documentElement.offsetWidth,document.documentElement.scrollWidth);
  }
  if (this.webkit) {
    return document.body.scrollWidth;
  }
  return document.documentElement.scrollWidth;
}

function form_maker_getScrollHeight() {
  if (this.ie) {
    return Math.max(document.documentElement.offsetHeight,document.documentElement.scrollHeight);
  }
  if (this.webkit) {
    return document.body.scrollHeight;
  }
  return document.documentElement.scrollHeight;
}

function toggleChBDiv(b) {
  if (b) {
    var black_div = document.getElementById("sbox-overlay");
    black_div.setAttribute("id", "sbox-overlay");
    black_div.setAttribute("style", "z-index: 65555; position: fixed; top: 0px; left: 0px; visibility: visible; zoom: 1; background-color: #000000; opacity: 0.7;filter: alpha(opacity=70); display: block; width: 1349px; height: 429px; ");
    black_div.setAttribute("onclick", "toggleChBDiv(false)");
    document.getElementById("sbox-overlay").style.width = this.form_maker_getScrollWidth() + "px";
    document.getElementById("sbox-overlay").style.height = this.form_maker_getScrollHeight() + "px";
    document.getElementById("ChBDiv").style.left = Math.floor((this.form_maker_getScrollWidth() - 250) / 2) + "px";

    document.getElementById("ChBDiv").style.display = "block";
    document.getElementById("sbox-overlay").style.display = "block";
  }
  else {
    document.getElementById("ChBDiv").style.display = "none";
    document.getElementById("sbox-overlay").style.display = "none";
  }
}

function clickLabChBAll(ChBAll) {
  var getCheckboxes = ChBAll.parentNode.getElementsByTagName("input");
  if (ChBAll.checked) {
    document.forms.main_show_form.hide_label_list.value = "";
    for (i = 0; i <= getCheckboxes.length; i ++) {
      if (typeof(getCheckboxes[i]) != "undefined") {
        if (getCheckboxes[i].type == "checkbox") {
          getCheckboxes[i].checked = true;
        }
      }
    }
  }
  else {
    document.forms.main_show_form.hide_label_list.value = "@" + Drupal.settings.form_maker.templabels + "@";
    for (i = 0; i <= getCheckboxes.length; i ++) {
      if (typeof(getCheckboxes[i]) != "undefined") {
        if (getCheckboxes[i].type == "checkbox") {
          getCheckboxes[i].checked = false;
        }
      }
    }
  }
  renderColumns();
}

function checkAll(n, fldName) {
  if (!fldName) {
    fldName = "cb";
  }
  var f = document.main_show_form;
  var c = f.toggle.checked;
  var n2 = 0;
  for (i = 0; i < n; i ++) {
    cb = eval("f." + fldName + "" + i);
    if (cb) {
      cb.checked = c;
      n2 ++;
    }
  }
  if (c) {
    document.main_show_form.boxchecked.value = n2;
  }
  else {
    document.main_show_form.boxchecked.value = 0;
  }
}

var globalchekid;
function isChecked(isitchecked, chekedid) {
  if (isitchecked == true) {
    globalchekid = chekedid;
    document.main_show_form.boxchecked.value ++;
  }
  else {
    document.main_show_form.boxchecked.value --;
  }
}

function submit_in(pressbutton) {
  //alert(globalchekid);
  document.getElementById("main_show_form").action = Drupal.settings.form_maker.string_submission_submitin + pressbutton + "&submission_id=" + globalchekid + "";
  document.getElementById("main_show_form").submit();
}

function submitbutton(pressbutton) {
  var form = document.main_show_form;
  if (pressbutton == "cancel_submit"){
    submitform(pressbutton);
    return;
  }
  submit_in(pressbutton);
}

function submit1(){
  var answer = confirm("Selected rows will be deleted. Are you sure?")
    if (answer) {
      document.getElementById("idd").value = 1;
      document.getElementById("main_show_form").submit();
    }
}

function submit2(x) {
  document.getElementById("delete").value = x;
  document.getElementById("main_show_form").submit();
}
