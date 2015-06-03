<?php
/**
 * @file
 * This file contains all functons wich need for form maker.
 */

/**
 * Edit form.
 */
function form_maker_edit($id) {
  if (arg(0) == 'node' && is_numeric(arg(1))) {
    $nodeid = arg(1);
  }
  $value = db_query("SELECT * FROM {form_maker_table} WHERE vid = :vid", array(':vid' => $nodeid));
  $row = $value->fetchObject();
  $forms_title = check_plain('Form - ' . $row->title);
  $string_form_cancel = url('node/' . $nodeid . '/form_maker', array('absolute' => FALSE));
  $frontend_edit = '
    <table width="95%" rules="none" style="border:none;">
			<tr>	
				<td style="width: 100%; text-align:right;font-size:16px; padding:20px; padding-right:50px; right:0; border:none;">
					<a href="http://web-dorado.com/products/drupal-form-builder.html" target="_blank" style="color:red; text-decoration:none;">
						<img src="' . base_path() . drupal_get_path('module', 'form_maker') . '/images/header.png" border="0" alt="www.web-dorado.com" width="215"><br>
					Get the full version&nbsp;&nbsp;&nbsp;&nbsp;
					</a>
				</td>
			</tr>
		</table>
		<table width="95%" rules="none" style="border:none;">
			<tr>
				<td width="100%"><h2>' . $forms_title . '</h2></td>
				<td>
					<input type="button" onclick=submitbutton("Edit_JavaScript") value="' . t('Edit JavaScript') . '" class="form-submit" />
				</td>
				<td>
					<input type="button" onclick=submitbutton("Edit_CSS") value="' . t('Edit CSS') . '" class="form-submit" />
				</td>  
				<td style="width:300px">
					<input type="button" onclick=submitbutton("Custom_text_in_email") value="' . t('Custom text in email') . '" class="form-submit" />
				</td>
				<td align="right">
					<input type="button" onclick=submitbutton("Save") value="' . t('Save') . '" class="form-submit" />
				</td>
				<td align="right" style="border:none;">
					<input type="button" onclick=window.location.href="' . $string_form_cancel . '" value="' . t('Cancel') . '" class="form-submit" />
				</td> 
			</tr>
		</table>
		<input type="hidden" id="file_location_root" value="' . base_path() . drupal_get_path('module', 'form_maker') . '"  />
		<input type="hidden" id="upload_location" value="' . base_path() . drupal_get_path('module', 'form_maker') . '"  />';

  $labels = array();
  $label_id = array();
  $label_order_original = array();
  $label_type = array();
  $label_all = explode('#****#', $row->label_order);
  $label_all = array_slice($label_all, 0, count($label_all) - 1);
  foreach ($label_all as $key => $label_each) {
    $label_id_each = explode('#**id**#', $label_each);
    array_push($label_id, $label_id_each[0]);
    $label_oder_each = explode('#**label**#', $label_id_each[1]);
    array_push($label_order_original, $label_oder_each[0]);
    array_push($label_type, $label_oder_each[1]);
  }
  $labels["id"] = implode(",", $label_id);
  $labels["label"] = implode(",", $label_order_original);
  $labels["type"] = implode(",", $label_type);
  $string_form_submitin = url('node/' . $nodeid . '/form_maker', array('query' => array('task' => ''), 'absolute' => FALSE));
  drupal_add_js(drupal_get_path('module', 'form_maker') . '/js/form_maker_edit.js');
  drupal_add_js(array(
    'form_maker' => array(
      'string_form_submitin' => $string_form_submitin,
      'row_id' => $row->id,
      'labels_id' => $labels["id"],
      'labels_label' => str_replace(" ", "", $labels['label']),
      'labels_type' => $labels["type"],
      'row_counter' => $row->counter,
    ),
    ),
    'setting');
  drupal_add_js('gen=' . $row->counter . ';', array('type' => 'inline', 'scope' => 'footer'));
  drupal_add_js('var main_location=document.getElementById("file_location_root").value;', array('type' => 'inline', 'scope' => 'footer'));
  drupal_add_js('var form_maker_captcha="' . url('form_maker/captcha', array('query' => array('digit' => ''), 'absolute' => FALSE)) . '";', array('type' => 'inline', 'scope' => 'footer'));
  drupal_add_css(drupal_get_path('module', 'form_maker') . '/js/form_maker_edit.css');
  drupal_add_css(drupal_get_path('module', 'form_maker') . '/js/form_maker_cal.css');
  drupal_add_js(drupal_get_path('module', 'form_maker') . '/js/formmaker.js');
  drupal_add_js(drupal_get_path('module', 'form_maker') . '/js/form_maker_cal.js');
  if (file_exists("sites/all/libraries/tinymce/jscripts/tiny_mce/tiny_mce.js")) {
    drupal_add_js('sites/all/libraries/tinymce/jscripts/tiny_mce/tiny_mce.js');
    drupal_add_js('tinyMCE.init({
				// General options
				mode : "specific_textareas",
        editor_selector : "form_maker_editor",
				theme : "advanced",
				plugins : "autolink,lists,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
				// Theme options
				theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
				theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
				theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
				theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
				theme_advanced_toolbar_location : "top",
				theme_advanced_toolbar_align : "left",
				theme_advanced_statusbar_location : "bottom",
				theme_advanced_resizing : true,

				// Skin options
				skin : "o2k7",
				skin_variant : "silver",

				// Example content CSS (should be your site CSS)
				//content_css : "css/example.css",
				
				// Drop lists for link/image/media/template dialogs
				template_external_list_url : "js/template_list.js",
				external_link_list_url : "js/link_list.js",
				external_image_list_url : "js/image_list.js",
				media_external_list_url : "js/media_list.js",

				// Replace values for the template plugin
				template_replace_values : {
					username : "Some User",
					staffid : "991234"
				}
			});', array('type' => 'inline', 'scope' => 'footer'));
  }
  drupal_add_js('var formOldFunctionOnLoad = null;formLoadBody();', array('type' => 'inline'));
  $frontend_edit .= '		
		
			<table style="border:6px #00aeef solid; background-color:#00aeef; min-width:600px"  width="95%" cellpadding="0" cellspacing="0" >
				<tbody>
					<tr style="height:27px;">
						<td align="left" valign="middle" rowspan="3" style="padding:10px; border:none;">
							<img src="' . base_path() . drupal_get_path('module', 'form_maker') . '/images/formmaker.png">
            </td>
            <td></td>
            <td>
							<input type="hidden" id="title" name="title" style="background:none; width:150px; height:15px; border:none; font-size:10px;" value="' . $row->title . '">
						</td>
					</tr>
					<tr>
						<td width="300" align="right" valign="middle">
							<span style="font-size:16.76pt; font-family:BauhausItcTEEMed; color:#FFFFFF; vertical-align:middle;">' . t('Email to send submissions to:') . '&nbsp;&nbsp;</span>
						</td>
						<td width="153" align="center" valign="middle">
							<div style="background-image:url(' . base_path() . drupal_get_path('module', 'form_maker') . '/images/input.png); height:19px; width:153px">
								<input id="mail" name="mail" style="background:none; width:151px; height:15px; border:none; font-size:11px" value="' . $row->mail . '">
							</div>
						</td>
					</tr>
					<tr>
            <td width="450" align="right" valign="middle">
							<span style="font-size:16.76pt; font-family:BauhausItcTEEMed; color:#FFFFFF; vertical-align:middle;">' . t('The page, which appears after submission (url):') . '&nbsp;&nbsp;</span>
						</td>
						<td width="153" align="center" valign="middle">
							<div style="background-image:url(' . base_path() . drupal_get_path('module', 'form_maker') . '/images/input.png); height:19px; width:153px">
								<input id="redirect_url" name="redirect_url" style="background:none; width:151px; height:15px; border:none; font-size:11px" value="' . $row->redirect_url . '">
							</div>
						</td>
					</tr>

					<tr>
						<td align="left" colspan="3">
							<img src="' . base_path() . drupal_get_path('module', 'form_maker') . '/images/addanewfield.png" onclick="enable()" style="cursor:pointer;margin:10px;">
						</td>
					</tr>
				</tbody>
			</table>
  			
			<div id="formmakerDiv" onclick="enable()"></div>
  				<div id="formmakerDiv1" align="center">
    				<table border="0" width="100%" cellpadding="0" cellspacing="0" height="100%" style="border:6px #00aeef solid; background-color:#FFF; font-size:10px" >
						<tr>
							<td style="padding:0px">
								<table rules="none" border="0" cellpadding="0" cellspacing="0" width="100%" height="100%" id="fonti" >
									<tr valign="top">
										<td width="92" height="100%" style="border-right:dotted black 1px;" id="field_types">
											<div id="when_edit" style="display:none"></div>
											<table rules="none" style="border-style:none"  width="100%" >
												<tr>
													<td align="center" onClick=addRow("editor") style="cursor:pointer; padding-left:0px; padding-right:0px; padding-top:0px; padding-bottom:0px;" id="table_editor">
														<img src="' . base_path() . drupal_get_path('module', 'form_maker') . '/images/customHTML.png" style="margin:5px"/>
													</td>
													<td align="center" onClick=addRow("text") style="border-style:none; cursor:pointer; padding-left:0px; padding-right:0px; padding-top:0px; padding-bottom:0px;" id="table_text">
														<img src="' . base_path() . drupal_get_path('module', 'form_maker') . '/images/text.png" style="margin:5px"/>
													</td>
												</tr>
												<tr>
													<td align="center" onClick=addRow("time_and_date") style="cursor:pointer; padding-left:0px; padding-right:0px; padding-top:0px; padding-bottom:0px;" id="table_time_and_date">
														<img src="' . base_path() . drupal_get_path('module', 'form_maker') . '/images/time_and_date.png" style="margin:1px"/>
													</td>
													<td align="center" onClick=addRow("select") style="border-style:none; cursor:pointer;padding-left:0px; padding-right:0px; padding-top:0px; padding-bottom:0px;" id="table_select">
														<img src="' . base_path() . drupal_get_path('module', 'form_maker') . '/images/select.png"style="margin:1px"/>
													</td>
												</tr>
												<tr>
													<td align="center" onClick=addRow("checkbox") style="cursor:pointer;padding-left:0px; padding-right:0px; padding-top:0px; padding-bottom:0px;" id="table_checkbox">
														<img src="' . base_path() . drupal_get_path('module', 'form_maker') . '/images/checkbox.png"style="margin:1px"/>
													</td>
													<td align="center" onClick=addRow("radio") style="border-style:none; cursor:pointer;padding-left:0px; padding-right:0px; padding-top:0px; padding-bottom:0px;" id="table_radio">
														<img src="' . base_path() . drupal_get_path('module', 'form_maker') . '/images/radio.png"style="margin:1px"/>
													</td>
												</tr>
												<tr>
													<td align="center" onClick=addRow("file_upload") style="cursor:pointer;padding-left:0px; padding-right:0px; padding-top:0px; padding-bottom:0px;" id="table_file_upload">
														<img src="' . base_path() . drupal_get_path('module', 'form_maker') . '/images/file_upload.png"style="margin:1px"/>
													</td>
													<td align="center" onClick=addRow("captcha") style="border-style:none; cursor:pointer;padding-left:0px; padding-right:0px; padding-top:0px; padding-bottom:0px;" id="table_captcha">
														<img src="' . base_path() . drupal_get_path('module', 'form_maker') . '/images/captcha.png"style="margin:1px"/>
													</td>
												</tr>
												<tr>
													<td align="center" onClick=addRow("map") style="cursor:pointer;padding-left:0px; padding-right:0px; padding-top:0px; padding-bottom:0px;" id="table_map">
														<img src="' . base_path() . drupal_get_path('module', 'form_maker') . '/images/map.png"style="margin:1px"/>
													</td>
													<td align="center" onClick=addRow("button") style="border-style:none; cursor:pointer;padding-left:0px; padding-right:0px; padding-top:0px; padding-bottom:0px;" id="table_button">
														<img src="' . base_path() . drupal_get_path('module', 'form_maker') . '/images/button.png"style="margin:1px"/>
													</td>
												</tr>
											</table>
										</td>
										<td width="30%" height="100%" align="left">
											<div id="edit_table" style="padding:0px; overflow-y:scroll; height:520px">
											</div>
										</td>
										<td align="center" valign="top" style="background:url(' . base_path() . drupal_get_path('module', 'form_maker') . '/images/border2.png) repeat-y;">&nbsp;
										</td>
										<td width="60%" style="padding:15px">
											<table rules="none" border="0" cellpadding="0" cellspacing="0" width="100%" height="100%" style="font-size:11px; border:none;" >
												<tr>
													<td align="right" style="border:none;">
														<input type="radio" value="end" name="el_pos" checked="checked" id="pos_end" onclick="Disable()"/>
                  										At The End
														<input type="radio" value="begin" name="el_pos" id="pos_begin" onclick="Disable()" style=""/>
										                At The Beginning
														<input type="radio" value="before" name="el_pos" id="pos_before" onclick="Enable()"/>
														Before
														<select style="width:100px; margin-left:5px" id="sel_el_pos" disabled="disabled" class="form-select">
														</select>
														<img alt="ADD" title="add" style="cursor:pointer; vertical-align:middle; margin:5px" src="' . base_path() . drupal_get_path('module', 'form_maker') . '/images/save.png" onClick="add(0)"/>
														<img alt="CANCEL" title="cancel"  style=" cursor:pointer; vertical-align:middle; margin:5px" src="' . base_path() . drupal_get_path('module', 'form_maker') . '/images/cancel_but.png" onClick="close_window()"/>
														<hr style=" margin-bottom:10px" />
													</td>
												</tr>
												<tr height="100%" valign="top">
													<td  id="show_table" style="border:none;"></td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
					<input type="hidden" id="old" />
					<input type="hidden" id="old_selected" />
					<input type="hidden" id="element_type" />
					<input type="hidden" id="editing_id" />
					<input type="hidden" id="post_id" name="post_id">
					<div id="main_editor" style="position:absolute; display:none; z-index:140; padding-left:0px;">
						<div  style=" max-width:500px height:300px;text-align:left" id="poststuff">
							<textarea id="textAreaContent" class="form_maker_editor"></textarea>
						</div>
					</div>
				</div>
  
				<br />
				<br />
				
				<fieldset style="padding:10px; border-color:#00aeef">
					<legend><h2 style="color:#00aeef;">&nbsp' . t('Form') . '&nbsp</h2></legend>';
  $frontend_edit .= '
					<style>' . $row->css . '</style>
					<div id="take">';
  if ($row->form) {
    $frontend_edit .= $row->form;
  }
  else {
    $frontend_edit .= '<table border="0" cellpadding="4" cellspacing="0" class="form_view" ><tbody  id="form_view" ><tr><td id="column_0" valign="top" bgcolor="#FFFFFF"><table ><tbody></tbody></table></td></tr></tbody></table>';
  }
  $frontend_edit .= '
					</div>
				</fieldset>
				<input type="hidden" id="label_order" name="label_order" value="' . $row->label_order . '" />
				<input type="hidden"  value="0" id="load_or_no" />
				<input type="hidden" name="form" id="form">
				<input type="hidden" name="counter" id="counter" value="' . $row->counter . '">				
		';
  $form = array();
  $form['all_Form_Maker'] = array(
    '#type' => 'fieldset',
    '#value' => $frontend_edit,
  );
  $form['#id'] = 'all_Form_Maker';
  $form['#attributes'] = array('name' => 'all_Form_Maker');
  return $frontend_edit;
}

/**
 * Edit css for form.
 */
function form_maker_edit_css($id) {
  if (arg(0) == 'node' && is_numeric(arg(1))) {
    $nodeid = arg(1);
  }
  $value = db_query("SELECT * FROM {form_maker_table} WHERE id = :id", array(':id' => $id));
  $row = $value->fetchObject();
  $forms_title = 'Edit CSS - ' . $row->title;
  $string_form_css = url('node/' . $nodeid . '/form_maker', array('query' => array('task' => ''), 'absolute' => FALSE));
  $string_form_css_cancel = url('node/' . $nodeid . '/form_maker', array('query' => array('task' => 'edit_form', 'id' => ''), 'absolute' => FALSE));
  drupal_add_js(drupal_get_path('module', 'form_maker') . '/js/form_maker_edit.js');
  drupal_add_js(drupal_get_path('module', 'form_maker') . '/js/form_maker_onload.js');
  drupal_add_js(array('form_maker' => array('string_form_css' => $string_form_css, 'row_id' => $row->id)), 'setting');
  $frontend_css = '
  			<table width="95%" style="border:none">
  				<tr>
  					<td width="100%"><h2>' . $forms_title . '</h2></td>
					<td align="right">
						<input type="button" onclick=submit_in_css("Save_edit_css") value="Save" class="form-submit" />
					</td>  
					<td align="right">
						<input type="button" onclick=submit_in_css("Apply_edit_css") value="Apply"  class="form-submit"/>
					</td> 
					<td align="right" style="border:none">
						<input type="button" onclick=window.location.href="' . $string_form_css_cancel . $row->id . '" value="Cancel" class="form-submit" />
					</td> 
				</tr>
			</table>
			
			<br />
			<br />
  
			<table>
				<tbody>
					<tr>
			            <th align="left">
							<label for="message">CSS</label>
							<button onclick=\'document.getElementById("css").value=document.getElementById("def").innerHTML; return false;\' style="margin-left:15px;">Restore default CSS</button>
			            </th>
					</tr>
					<tr>
						<td>
              <textarea class="text-full form-textarea" style="margin: 0px;" cols="110" rows="25" name="css" id="css">' . $row->css . '</textarea>
						</td>
					</tr>

				</tbody>
			</table>
  		
		<textarea style="visibility:hidden" id="def">
			.form_view, .form_view table
				{
				width:inherit !important;
				-webkit-border-horizontal-spacing: 0px;
				-webkit-border-vertical-spacing: 0px;
				border-bottom-color: gray;
				border:0px  !important;
				border-bottom-width: 0px;
				border-collapse: separate;
				border-left-color: gray;
				border-left-width: 0px;
				border-right-color: gray;
				border-right-width: 0px;
				border-top-color: gray;
				border-top-width: 0px;
				color: black;
				display: table;
				font-family: Helvetica, Arial, sans-serif;
				font-size: 14px !important;
				font-weight: normal;
				height: inherit !important;
				line-height: 15px;
				margin-bottom: 0px;
				margin-left: 0px;
				margin-right: 0px;
				margin-top: 0px;
				padding-bottom: 0px;
				padding-left: 0px;
				padding-right: 0px;
				padding-top: 0px;
				text-align: left !important;
				
				}
				
				.form_view, .form_view tr
				{
				-webkit-border-horizontal-spacing: 0px;
				-webkit-border-vertical-spacing: 0px;
				border:0px  !important;
				border-bottom-color: gray;
				border-collapse: separate;
				border-left-color: gray;
				border-right-color: gray;
				border-top-color: gray;
				color: black;
				display: table-row;
				font-family: Helvetica, Arial, sans-serif;
				font-size: 14px;
				font-weight: normal;
				height: inherit !important;
				line-height: 15px;
				margin-bottom: 0px;
				margin-left: 0px;
				margin-right: 0px;
				margin-top: 0px;
				padding-bottom: 0px;
				padding-left: 0px;
				padding-right: 0px;
				padding-top: 0px;
				text-align: left;
				vertical-align: middle;
				width:inherit !important;
				}
				
				.form_view, .form_view td
				{
				-webkit-border-horizontal-spacing: 2px;
				-webkit-border-vertical-spacing: 2px;
				border-bottom-color: black;
				border-collapse: separate;
				border-left-color: black;
				border-right-color: black;
				border-top-color: black;
				border:0px !important;
				color: black;
				display: table-cell;
				font-family: Helvetica, Arial, sans-serif;
				font-size: 14px;
				font-weight: normal;
				height:inherit !important;
				line-height: 15px;
				margin-bottom: 0px;
				margin-left: 0px;
				margin-right: 0px;
				margin-top: 0px;
				padding-bottom: 1px !important;
				padding-left: 1px !important;
				padding-right: 1px !important;
				padding-top: 3px !important;
				text-align: left !important;
				width:inherit !important;
				vertical-align:top;
				}
				.form_view, .form_view tr
				{
				-webkit-border-horizontal-spacing: 0px;
				-webkit-border-vertical-spacing: 0px;
				border:0px  !important;
				border-bottom-color: gray;
				border-collapse: separate;
				border-left-color: gray;
				border-right-color: gray;
				border-top-color: gray;
				color: black;
				display: table-row;
				font-family: Helvetica, Arial, sans-serif;
				font-size: 14px;
				font-weight: normal;
				height: inherit !important;
				line-height: 15px;
				margin-bottom: 0px;
				margin-left: 0px;
				margin-right: 0px;
				margin-top: 0px;
				padding-bottom: 0px;
				padding-left: 0px;
				padding-right: 0px;
				padding-top: 0px;
				text-align: left;
				vertical-align: middle;
				width:inherit !important;
				}
				
				.form_view, .form_view input,  .form_view  textarea
				{
				line-height:inherit  !important;
				margin:0px !important;
				min-height: 18px !important;
				 font-size: 14px !important;
				}
				.form_view, .form_view select
				{
				margin:0px !important;
				font-size: 14px !important;
				}
				.form_view, .form_view label
				{
				font-size: 14px;
				 vertical-align:inherit !important;
				}
				.time_box
				{
				border-width:1px;
				margin: 0px;
				padding: 0px;
				text-align:right;
				width:30px;
				vertical-align:middle
				}
				
				
				.mini_label
				{
				color: #000 !important;
				font-size:14px;
				font-family: Lucida Grande, Tahoma, Arial, Verdana, sans-serif;
				}
				
				.ch_rad_label
				{
				color:#000 !important;
				display:inline;
				margin-left:5px;
				margin-right:15px;
				float:none;
				}
				
				.label
				{
				-webkit-border-horizontal-spacing: 2px;
				-webkit-border-vertical-spacing: 2px;
				border-bottom-color: black;
				border-bottom-style: none;
				border-collapse: separate;
				border-left-color: black;
				border-left-style: none;
				border-right-color: black;
				border-right-style: none;
				border-top-color: black;
				border-top-style: none;
				color: black;
				display: inline;
				font-family: Helvetica, Arial, sans-serif;
				font-size: 14px;
				font-weight: normal;
				height: auto;
				line-height: 15px;
				margin-bottom: 0px;
				margin-left: 0px;
				margin-right: 0px;
				margin-top: 0px;
				padding-bottom: 0px;
				padding-left: 0px;
				padding-right: 0px;
				padding-top: 0px;
				text-align: -webkit-left;
				width: auto;
				}
				
				
				.td_am_pm_select
				{
				padding-left:5;
				}
				
				.am_pm_select
				{
				height: 16px;
				margin:0;
				padding:0
				}
				
				.input_deactive
				{
				background-color: #FFFFFF;
				border-bottom-style: inset;
				border-bottom-width: 1px;
				border-collapse: separate;
				border-left-color: #EEE;
				border-left-style: inset;
				border-left-width: 1px;
				border-right-color: #EEE;
				border-right-style: inset;
				border-right-width: 1px;
				border-top-color: #EEE;
				border-top-style: inset;
				border-top-width: 1px;
				font-style: italic;
				color: #999;
				cursor: auto;
				display: inline-block;
				font-family: Arial;
				font-size: 14px !important;
				font-weight: normal;
				letter-spacing: normal;
				line-height: normal;
				margin-bottom: 0px;
				margin-left: 0px;
				margin-right: 0px;
				margin-top: 0px;
				padding-bottom: 0px;
				padding-left: 0px;
				padding-right: 0px;
				padding-top: 0px;
				text-align: -webkit-auto;
				text-indent: 0px;
				text-shadow: none;
				text-transform: none;
				word-spacing: 0px;
				}
				
				.input_active
				{
				background-color: #FFFFFF;
				-webkit-appearance: none;
				-webkit-border-horizontal-spacing: 2px;
				-webkit-border-vertical-spacing: 2px;
				-webkit-rtl-ordering: logical;
				-webkit-user-select: text;
				background-color: white;
				border-bottom-color: #EEE;
				border-bottom-style: inset;
				border-bottom-width: 1px;
				border-collapse: separate;
				border-left-color: #EEE;
				border-left-style: inset;
				border-left-width: 1px;
				border-right-color: #EEE;
				border-right-style: inset;
				border-right-width: 1px;
				border-top-color: #EEE;
				border-top-style: inset;
				border-top-width: 1px;
				color: black;
				cursor: auto;
				display: inline-block;
				font-family: Arial;
				font-size: 14px !important;
				font-style: normal;
				font-weight: normal;
				height: 16px;
				letter-spacing: normal;
				line-height: normal;
				margin-bottom: 0px;
				margin-left: 0px;
				margin-right: 0px;
				margin-top: 0px;
				padding-bottom: 0px;
				padding-left: 0px;
				padding-right: 0px;
				padding-top: 0px;
				text-align: -webkit-auto;
				text-indent: 0px;
				text-shadow: none;
				text-transform: none;
				width: 200px;
				word-spacing: 0px;
				}
				
				.required
				{
				border:none;
				color:red
				}
				
				.captcha_img
				{
				border-width:0px;
				margin: 0px;
				padding: 0px;
				cursor:pointer;
				
				
				}
				
				.captcha_refresh
				{
				width:18px;
				border-width:0px;
				margin: 0px;
				padding: 0px;
				vertical-align:middle;
				cursor:pointer;
				}
				
				.captcha_input
				{
				height:20px;
				border-width:1px;
				margin: 0px;
				padding: 0px;
				vertical-align:middle;
				}
				
				.file_upload
				{
				-webkit-appearance: none;
				-webkit-border-horizontal-spacing: 2px;
				-webkit-border-vertical-spacing: 2px;
				-webkit-box-align: baseline;
				-webkit-rtl-ordering: logical;
				-webkit-user-select: text;
				background-color: transparent;
				border-bottom-color: black;
				border-bottom-style: none;
				border-bottom-width: 0px;
				border-collapse: separate;
				border-left-color: black;
				border-left-style: none;
				border-left-width: 0px;
				border-right-color: black;
				border-right-style: none;
				border-right-width: 0px;
				border-top-color: black;
				border-top-style: none;
				border-top-width: 0px;
				color: black;
				cursor: auto;
				display: inline-block;
				font-family: Arial;
				font-size: 13px;
				font-weight: normal;
				height: 22px;
				letter-spacing: normal;
				line-height: normal;
				margin-bottom: 0px;
				margin-left: 0px;
				margin-right: 0px;
				margin-top: 0px;
				padding-bottom: 0px;
				padding-left: 0px;
				padding-right: 0px;
				padding-top: 0px;
				text-align: start;
				text-indent: 0px;
				text-shadow: none;
				text-transform: none;
				width: 238px;
				word-spacing: 0px;
				}         
				.captcha_table , .captcha_table input
				{
				  font-size: 15px !important;
				}      
		</textarea>';
  return $frontend_css;
}

/**
 * Edit javascript for form.
 */
function form_maker_Edit_JavaScript($id) {
  if (arg(0) == 'node' && is_numeric(arg(1))) {
    $nodeid = arg(1);
  }
  $value = db_query("SELECT * FROM {form_maker_table} WHERE id = :id", array(':id' => $id));
  $row = $value->fetchObject();
  $forms_title = 'Edit JavaScript - ' . $row->title;
  $string_form_java = url('node/' . $nodeid . '/form_maker', array('query' => array('task' => ''), 'absolute' => FALSE));
  $string_form_java_cancel = url('node/' . $nodeid . '/form_maker', array('query' => array('task' => 'edit_form', 'id' => ''), 'absolute' => FALSE));
  drupal_add_js(drupal_get_path('module', 'form_maker') . '/js/form_maker_edit.js');
  drupal_add_js(drupal_get_path('module', 'form_maker') . '/js/form_maker_onload.js');
  drupal_add_js(array('form_maker' => array('string_form_java' => $string_form_java, 'row_id_java' => $row->id)), 'setting');
  $frontend_java = '
	  	<table width="95%" style="border:none">
	  		<tr>
	  			<td width="100%"><h2>' . $forms_title .  '</h2></td>
	  			<td align="right">
					<input type="button" onclick=submit_in_java("Save_edit_JavaScript") value="Save" class="form-submit" />
				</td>  
				<td align="right">
					<input type="button" onclick=submit_in_java("Apply_edit_JavaScript") value="Apply"  class="form-submit"/>
				</td> 
	  			<td align="right" style="border:none">
					<input type="button" onclick=window.location.href="' . $string_form_java_cancel . $row->id . '" value="Cancel" class="form-submit" />
				</td> 
			</tr>
		</table>
	  	
		<br />
		<br />
		
		<table width="95%" >
			<tbody>
				<tr>
					<th align="left">
						<label for="message">Javascript</label>
					</th>
				</tr>
				<tr>
					<td>
						<textarea class="text-full form-textarea" style="margin: 0px;" cols="110" rows="25" name="javascript" id="javascript">' . $row->javascript . '</textarea>
					</td>
				</tr>
			</tbody>
		</table>';
  $form = array();
  $form['edit_js'] = array(
    '#type' => 'fieldset',
    '#value' => $frontend_java,
  );
  $form['#id'] = 'edit_js';
  $form['#attributes'] = array('name' => 'edit_js');
  return $frontend_java;
}

/**
 * Edit text in Email.
 */
function form_maker_text_in_email($id) {
  if (arg(0) == 'node' && is_numeric(arg(1))) {
    $nodeid = arg(1);
  }
  $value = db_query("SELECT * FROM {form_maker_table} WHERE id = :id", array(':id' => $id));
  $row = $value->fetchObject();
  $mail_title = 'Custom text in email - ' . $row->title;
  $string_textinemail_cancel = url('node/' . $nodeid . '/form_maker', array('query' => array('task' => 'edit_form', 'id' => ''), 'absolute' => FALSE));
  $string_textinemail = url('node/' . $nodeid . '/form_maker', array('query' => array('task' => 'Custom_text_in_email', 'id' => ''), 'absolute' => FALSE));
  $string_textinemail_submitin = url('node/' . $nodeid . '/form_maker', array('query' => array('task' => ''), 'absolute' => FALSE));
  drupal_add_js(drupal_get_path('module', 'form_maker') . '/js/form_maker_edit.js');
  drupal_add_js(drupal_get_path('module', 'form_maker') . '/js/form_maker_onload.js');
  drupal_add_js(array('form_maker' => array('string_textinemail_submitin' => $string_textinemail_submitin, 'id' => $id)), 'setting');
  $frontend_mail = '
		<table width="95%" style="border:none">
  			<tr>
  				<td width="100%"><h2>' . $mail_title . '</h2></td>
  				<td align="right">
					<input type="button" onclick=submit_in_textinemail("custom_text_Save") value="Save" class="form-submit">
				</td>  
  				<td align="right">
					<input type="button" onclick=submit_in_textinemail("Custom_text_apply") value="Apply" class="form-submit">
				</td> 
  				<td align="right" style="border:none">
					<input type="button" onclick=window.location.href="' . $string_textinemail_cancel . $id . '" value="Cancel" class="form-submit">
				</td> 
			</tr>
		</table>
		
		<br/>
		<br/>
		<br/>

			<table width="95%" style="border-color:#000; border:medium;" >
        		<tbody>
			        <tr>
			            <th style="text-align:left">
			                <label for="message"  style="text-align:left"> Text before Message </label>
            			    <br/>
             			</th>
            		</tr>
            		<tr>
            	        <td style="width:95%; min-width:500px">
			 ///////////////////////////////////////////////
							<textarea class="text-full form-textarea" style="width:100%" name="text_mail_befor" id>' . $row->script1 . '</textarea>
			   				<br />
            			</td>
					</tr>
            		<tr>
            			<td>
             				<hr />
             				<h2 align="center">MESSAGE</h2>
             				<hr />
             				<br />
            			</td>
            		</tr>
             		<tr>
			            <th style="text-align:left">
			                <label for="message"  style="text-align:left"> Text after Message </label>
            			    <br />
            			</th>
            		</tr>
                    <tr>
            			<td style="width:70%; min-width:500px">
		   /////////////////////
							<textarea class="text-full form-textarea" style="width:100%" name="text_mail_after">' . $row->script2 . '</textarea>
						</td>
					</tr>
        		</tbody>
        	</table>';
  $form = array();
  $form['all_Form_Maker'] = array(
    '#type' => 'fieldset',
    '#value' => $frontend_mail,
  );
  $form['#action'] = $string_textinemail . $id;
  $form['#id'] = 'all_Form_Maker';
  $form['#attributes'] = array('name' => 'all_Form_Maker');
  return $frontend_mail;
}

/**
 * Delete form.
 */
function form_maker_form_delete($id) {
  if (0 <= $id) {
    db_query("DELETE FROM {form_maker_table} WHERE id= :id", array(':id' => $id));
    db_query("DELETE FROM {form_maker_submits_table} WHERE id= :id", array(':id' => $id));
    drupal_set_message(t('Item Deleted'), 'status', FALSE);
  }
  else {
    die("Error");
  }
}

/**
 * For browser Google Chrome.
 */
function form_maker_forchrome($id) {
  if (arg(0) == 'node' && is_numeric(arg(1))) {
    $nodeid = arg(1);
  }
  $string_forchrome = url('node/' . $nodeid . '/form_maker', array('query' => array('task' => 'gotoedit', 'id' => ''), 'absolute' => FALSE));
  drupal_add_js(drupal_get_path('module', 'form_maker') . '/js/form_maker_edit.js');
  drupal_add_js(array('form_maker' => array('string_forchrome' => $string_forchrome, 'id_forchrome' => $id)), 'setting');
  drupal_add_js('val();', array('type' => 'inline', 'scope' => 'footer'));
  $frontend_chrome = '
			<input type="hidden" name="option" value="com_formmaker" />		
			<input type="hidden" name="id" value="' . $id . '" />		
			<input type="hidden" name="task" value="gotoedit" />';
  return $frontend_chrome;
}

/**
 * Form apply.
 */
function form_maker_apply($id) {
  $form_no_slash = stripslashes(check_plain($_POST["form"]));
  $savedd = db_query("UPDATE {form_maker_table} SET title= :title, mail= :mail, form= :form, counter= :counter, article_id= :article_id, label_order= :label_order WHERE id = :id", array(
    ':title' => check_plain($_POST["title"]),
    ':mail' => filter_xss($_POST["mail"]),
    ':form' => $form_no_slash,
    ':counter' => check_plain($_POST["counter"]),
    ':article_id' => check_plain($_POST["post_name"]),
    ':label_order' => check_plain($_POST["label_order"]),
    ':id' => $id,
    ));
  drupal_set_message(t('Item Saved'), 'status', FALSE);
  return;
}

/**
 * Form save.
 */
function form_maker_save() {
  if (isset($_POST["label_order"]) && isset($_POST["title"]) && isset($_POST["form"])) {
    $no_slash_form = stripslashes($_POST["form"]);

    if (arg(0) == 'node' && is_numeric(arg(1))) {
      $nodeid = arg(1);
    }
    $save_or_no = db_query("UPDATE {form_maker_table} SET title= :title, mail= :mail, form= :form, counter= :counter, label_order= :label_order, redirect_url= :redirect_url WHERE vid = :vid", array(
      ':title' => check_plain($_POST["title"]),
      ':mail' => filter_xss($_POST["mail"]),
      ':form' => $no_slash_form,
      ':counter' => check_plain($_POST["counter"]),
      ':label_order' => check_plain($_POST["label_order"]),
      ':redirect_url' => check_url($_POST["redirect_url"]),
      ':vid' => $nodeid,
      ));
    if (!$save_or_no) {
      drupal_set_message(t('Error. Please install module again'), 'error', FALSE);
      return FALSE;
    }
    drupal_set_message(t('Item Saved'), 'status', FALSE);
  }
}

/**
 * CSS save.
 */
function form_maker_save_edit_css($id) {
  if ($id) {
    if (arg(0) == 'node' && is_numeric(arg(1))) {
      $nodeid = arg(1);
    }
    db_query("UPDATE {form_maker_table} SET css= :css WHERE vid = :vid", array(':css' => check_plain($_POST["css"]), ':vid' => $nodeid));
    drupal_set_message(t('CSS Successfully Saved'), 'status', FALSE);
  }
}

/**
 * Javascript save.
 */
function form_maker_save_javascript($id) {
  if ($id) {
    if (arg(0) == 'node' && is_numeric(arg(1))) {
      $nodeid = arg(1);
    }
    db_query("UPDATE {form_maker_table} SET javascript= :javascript WHERE vid = :vid", array(':javascript' => check_plain($_POST["javascript"]), ':vid' => $nodeid));
    drupal_set_message(t('JavaScript Successfully Saved'), 'status', FALSE);
  }
}

/**
 * Text in message save.
 */
function form_maker_update_custom_text($id) {
  if (isset($_POST["text_mail_befor"])) {
    if (isset($_POST["text_mail_after"])) {
      if (arg(0) == 'node' && is_numeric(arg(1))) {
        $nodeid = arg(1);
      }
      db_query("UPDATE {form_maker_table} SET script1= :script1, script2= :script2 WHERE vid = :vid", array(
        ':script1' => check_plain($_POST["text_mail_befor"]),
        ':script2' => check_plain($_POST["text_mail_after"]),
        ':vid' => $nodeid,
        ));
      drupal_set_message(t('Custom text in email successfully saved.'), 'status', FALSE);
    }
    else {
      drupal_set_message(t('Error. After text massage not found.'), 'error', FALSE);
    }
  }
  else {
    drupal_set_message(t('Error. Before text message not found.'), 'error', FALSE);
  }
}

/**
 * Save fields.
 */
function form_maker_savedata($id, $front_end, $sescaptcha) {
  $all_files = array();
  if (isset($_POST["captcha_input"])) {
    $captcha_input = check_plain($_POST["captcha_input"]);
  }
  if (arg(0) == 'node' && is_numeric(arg(1))) {
    $nodeid = arg(1);
  }
  $value = db_query("SELECT * FROM {form_maker_table} WHERE vid = :vid", array(':vid' => $nodeid));
  $form = $value->fetchObject();
  if (isset($form->counter)) {
    $counter = $form->counter;
  }
  if (isset($form->counter)) {
    if (isset($_POST["captcha_input"])) {
      $session_wd_captcha_code = isset($sescaptcha) ? $sescaptcha : '-';
      if ($captcha_input == $session_wd_captcha_code) {
        $all_files = form_maker_save_db($counter, $id, $front_end);
        if (is_numeric($all_files)) {
          form_maker_remove($all_files);
        }
        else {
          if (isset($counter)) {
            form_maker_sendmail($counter, $all_files, $id, $front_end);
          }
        }
      }
      else {
        drupal_set_message(t('Error, incorrect Security code.'), 'error', FALSE);
      }
    }
    else {
      $all_files = form_maker_save_db($counter, $id, $front_end);
      if (is_numeric($all_files)) {
        form_maker_remove($all_files);
      }
      else {
        if (isset($counter)) {
          form_maker_sendmail($counter, $all_files, $id, $front_end);
        }
      }
    }
    return $all_files;
  }
  return $all_files;
}

/**
 * Save fields in data base.
 */
function form_maker_save_db($counter, $id, $front_end) {
  $chgnac = TRUE;
  $all_files = array();
  $max = db_query("SELECT MAX( group_id ) FROM {form_maker_submits_table}")->fetchField();

  for ($i = 0; $i < $counter; $i++) {
    if (isset($_POST[$i . "_type"])) {
      $type = check_plain($_POST[$i . "_type"]);
    }
    else {
      $type = "";
    }
    if ($type != "type_map" and $type != "type_captcha" and $type != "type_submit_reset" and $type != "type_button") {
      if (isset($_POST[$i . "_element_label"])) {
        $element_label = check_plain($_POST[$i . "_element_label"]);
      }
      if (isset($_POST[$i . "_element_label"])) {
        $value = '';
        if (isset($_POST[$i . "_element"])) {
          $element = check_plain($_POST[$i . "_element"]);
        }
        if ($type == "type_hidden") {
          $value = check_plain($_POST[$element_label]);
        }
        else {
          if (isset($_POST[$i . "_element"])) {
            $value = $element;
          }
          else {
            if (isset($_POST[$i . "_hh"])) {
              $hh = check_plain($_POST[$i . "_hh"]);
            }
            if (isset($_POST[$i . "_hh"])) {
              $ss = check_plain($_POST[$i . "_ss"]);
              if (isset($_POST[$i . "_ss"])) {
                $value = check_plain($_POST[$i . "_hh"]) . ':' . check_plain($_POST[$i . "_mm"]) . ':' . check_plain($_POST[$i . "_ss"]);
              }
              else {
                $value = check_plain($_POST[$i . "_hh"]) . ':' . check_plain($_POST[$i . "_mm"]);
              }
              if (isset($_POST[$i . "_am_pm"])) {
                $am_pm = check_plain($_POST[$i . "_am_pm"]);
              }
              if (isset($_POST[$i . "_am_pm"])) {
                $value = $value . ' ' . check_plain($_POST[$i . "_am_pm"]);
              }
            }
            else {
              if (isset($_POST[$i . "_element_first"])) {
                $element_first = check_plain($_POST[$i . "_element_first"]);
              }
              if (isset($_POST[$i . "_element_first"])) {
                if (isset($_POST[$i . "_element_title"])) {
                  $element_title = check_plain($_POST[$i . "_element_title"]);
                }
                if (isset($_POST[$i . "_element_title"])) {
                  $value = check_plain($_POST[$i . "_element_title"]) . ' ' . check_plain($_POST[$i . "_element_first"]) . ' ' . check_plain($_POST[$i . "_element_last"]) . ' ' . check_plain($_POST[$i . "_element_middle"]);
                }
                else {
                  $value = check_plain($_POST[$i . "_element_first"]) . ' ' . check_plain($_POST[$i . "_element_last"]);
                }
              }
              else {
                if (isset($_FILES[$i . '_file'])) {
                  $file = $_FILES[$i . '_file'];
                }
                if (isset($_FILES[$i . '_file'])) {
                  if ($file['name']) {
                    $value = db_query("SELECT * FROM {form_maker_table} WHERE id = :id", array(':id' => $id));
                    $form = $value->fetchObject();
                    $untilupload = $form->form;
                    $pos1 = strpos($untilupload, "***destinationskizb" . $i . "***");
                    $pos2 = strpos($untilupload, "***destinationverj" . $i . "***");
                    $destination = drupal_substr($untilupload, $pos1 + (23 + (drupal_strlen($i) - 1)), $pos2 - $pos1 - (23 + (drupal_strlen($i) - 1)));
                    $destination = drupal_get_path('module', 'form_maker') . "/uploads";
                    $pos1 = strpos($untilupload, "***extensionskizb" . $i . "***");
                    $pos2 = strpos($untilupload, "***extensionverj" . $i . "***");
                    $extension = drupal_substr($untilupload, $pos1 + (21 + (drupal_strlen($i) - 1)), $pos2 - $pos1 - (21 + (drupal_strlen($i) - 1)));
                    $pos1 = strpos($untilupload, "***max_sizeskizb" . $i . "***");
                    $pos2 = strpos($untilupload, "***max_sizeverj" . $i . "***");
                    $max_size = drupal_substr($untilupload, $pos1 + (20 + (drupal_strlen($i) - 1)), $pos2 - $pos1 - (20 + (drupal_strlen($i) - 1)));
                    $filename = $file['name'];

                    /*if($fileSize > $max_size*1024) {
                    drupal_set_message();
                    return ($max+1);
                    }*/
                    $uploadedfilenameparts = explode('.', $filename);
                    $uploadedfileextension = array_pop($uploadedfilenameparts);
                    $to = drupal_strlen($filename) - drupal_strlen($uploadedfileextension) - 1;
                    $filenamefree = drupal_substr($filename, 0, $to);
                    $invalidfileexts = explode(',', $extension);
                    $extok = FALSE;

                    foreach ($invalidfileexts as $key => $value) {
                      if (is_numeric(strpos(drupal_strtolower($value), drupal_strtolower($uploadedfileextension)))) {
                        $extok = TRUE;
                      }
                    }
                    if ($extok == FALSE) {
                      drupal_set_message(t('Sorry, you are not allowed to upload this type of file'), 'error', FALSE);
                      return ($max + 1);
                    }
                    $filetemp = $file['tmp_name'];
                    $p = 1;
                    while (file_exists($destination . '/' . $filename)) {
                      $to = drupal_strlen($file['name']) - drupal_strlen($uploadedfileextension) - 1;
                      $filename = drupal_substr($filename, 0, $to) . '(' . $p . ') . ' . $uploadedfileextension;
                      $p++;
                    }
                    if (!move_uploaded_file($filetemp, $destination . "/" . $filename)) {
                      drupal_set_message(t('Error, file cannot be moved'), 'error', FALSE);
                      return ($max + 1);
                    }
                    $value = base_path() . $destination . '/' . $filename . '*@@url@@*';
                    $file['tmp_name'] = $destination . "/" . $filename;
                    array_push($all_files, $file);
                  }
                }
                else {
                  $start = -1;
                  for ($j = 0; $j < 100; $j++) {
                    if (isset($_POST[$i . "_element" . $j])) {
                      $start = $j;
                      break;
                    }
                  }
                  if ($start != -1) {
                    for ($j = $start; $j < 100; $j++) {
                      $ij = isset($_POST[$i . "_element" . $j]);
                      if ($ij) {
                        $value = $value . check_plain($_POST[$i . "_element" . $j]) . '<br/>';
                      }
                    }
                  }
                }
              }
            }
          }
        }
        $date = date('r');
        // $_SERVER['REMOTE_ADDR'];
        $ip = ip_address();
        $ptn = "/[^a-zA-Z0-9_]/";
        $rpltxt = "";
        $element_label = preg_replace($ptn, $rpltxt, $element_label);
        $a = addslashes($element_label);
        $b = addslashes($value);
        $c = ($max + 1);
        $d = "NOW()";
        $r = db_insert('form_maker_submits_table')
        ->fields(array(
          'form_id' => $id,
          'element_label' => $a,
          'element_value' => $b,
          'group_id' => $c,
          'date' => date('Y-m-d H:i:s'),
          'ip' => $ip,
          ))
        ->execute();
        $chgnac = FALSE;
      }
    }
  }
  if ($chgnac) {
    if (count($all_files) == 0) {
      drupal_set_message(t('Nothing was submitted'), 'error', FALSE);
    }
  }
  return $all_files;

}

/**
 * Send mail.
 */
function form_maker_sendmail($counter, $all_files, $id, $front_end) {
  if (arg(0) == 'node' && is_numeric(arg(1))) {
    $nodeid = arg(1);
  }
  $value = db_query("SELECT * FROM {form_maker_table} WHERE id = :id", array(':id' => $id));
  $row = $value->fetchObject();
  if (TRUE) {
    $cc = array();
    $label_order_original = array();
    $label_order_ids = array();
    $label_all = explode('#****#', $row->label_order);
    $label_all = array_slice($label_all, 0, count($label_all) - 1);
    foreach ($label_all as $key => $label_each) {
      $label_id_each = explode('#**id**#', $label_each);
      $label_id = $label_id_each[0];
      array_push($label_order_ids, $label_id);
      $label_oder_each = explode('#**label**#', $label_id_each[1]);
      $label_order_original[$label_id] = $label_oder_each[0];
    }
    $list = '<table border="0" cellpadding="3" cellspacing="0" style="width:600px; border-top:1px solid #888888; border-left:1px solid #888888;">';
    foreach ($label_order_ids as $key => $label_order_id) {
      $i = $label_order_id;
      if (isset($_POST[$i . "_element_label"])) {
        $element_label = check_plain($_POST[$i . "_element_label"]);
      }
      if (isset($_POST[$i . "_element_label"])) {
        $element_label = $label_order_original[$element_label];
        $type = check_plain($_POST[$i . "_type"]);
        if ($type == "type_submitter_mail") {
          if ($_POST[$i . "_send"] == "yes") {
            array_push($cc, check_plain($_POST[$i . "_element"]));
          }
        }
        if (isset($_POST[$i . "_element"])) {
          $element = check_plain($_POST[$i . "_element"]);
        }
        if (isset($_POST[$i . "_element"])) {
          $list = $list . '<tr valign="top"><td style="border-right:1px solid #888888; border-bottom:1px solid #888888;">' . $element_label . '</td><td style="border-right:1px solid #888888; border-bottom:1px solid #888888;">' . check_plain($_POST[$i . "_element"]) . '</td></tr>';
        }
        else {
          if (isset($_POST[$i . "_hh"])) {
            $hh = check_plain($_POST[$i . "_hh"]);
          }
          if (isset($_POST[$i . "_hh"])) {
            if (isset($_POST[$i . "_ss"])) {
              $ss = check_plain($_POST[$i . "_ss"]);
            }
            if (isset($_POST[$i . "_ss"])) {
              $list = $list . '<tr valign="top"><td style="border-right:1px solid #888888; border-bottom:1px solid #888888;">' . $element_label . '</td><td style="border-right:1px solid #888888; border-bottom:1px solid #888888;">' . check_plain($_POST[$i . "_hh"]) . ':' . check_plain($_POST[$i . "_mm"]) . ':' . check_plain($_POST[$i . "_ss"]);
            }
            else {
              $list = $list . '<tr valign="top"><td style="border-right:1px solid #888888; border-bottom:1px solid #888888;">' . $element_label . '</td><td style="border-right:1px solid #888888; border-bottom:1px solid #888888;">' . check_plain($_POST[$i . "_hh"]) . ':' . check_plain($_POST[$i . "_mm"]);
            }
            if (isset($_POST[$i . "_am_pm"])) {
              $am_pm = check_plain($_POST[$i . "_am_pm"]);
            }
            if (isset($_POST[$i . "_am_pm"])) {
              $list = $list . ' ' . check_plain($_POST[$i . "_am_pm"]) . '</td></tr>';
            }
            else {
              $list = $list . '</td></tr>';
            }
          }
          else {
            if (isset($_POST[$i . "_element_first"])) {
              $element_first = check_plain($_POST[$i . "_element_first"]);
            }
            if (isset($_POST[$i . "_element_first"])) {
              if (isset($_POST[$i . "_element_title"])) {
                $element_title = check_plain($_POST[$i . "_element_title"]);
              }
              if (isset($_POST[$i . "_element_title"])) {
                $list = $list . '<tr valign="top"><td style="border-right:1px solid #888888; border-bottom:1px solid #888888;">' . $element_label . '</td><td style="border-right:1px solid #888888; border-bottom:1px solid #888888;">' . check_plain($_POST[$i . "_element_title"]) . ' ' . check_plain($_POST[$i . "_element_first"]) . ' ' . check_plain($_POST[$i . "_element_last"]) . ' ' . check_plain($_POST[$i . "_element_middle"]) . '</td></tr>';
              }
              else {
                $list = $list . '<tr valign="top"><td style="border-right:1px solid #888888; border-bottom:1px solid #888888;">' . $element_label . '</td><td style="border-right:1px solid #888888; border-bottom:1px solid #888888;">' . check_plain($_POST[$i . "_element_first"]) . ' ' . check_plain($_POST[$i . "_element_last"]) . '</td></tr>';
              }
            }
            else {
              if (isset($_FILES[$i . '_file'])) {
                $file = $_FILES[$i . '_file'];
              }
              if (isset($_FILES[$i . '_file'])) {
              }
              else {
                $list = $list . '<tr valign="top"><td style="border-right:1px solid #888888; border-bottom:1px solid #888888;">' . $element_label . '</td><td style="border-right:1px solid #888888; border-bottom:1px solid #888888;">';
                $start = -1;
                for ($j = 0; $j < 100; $j++) {
                  if (isset($_POST[$i . "_element" . $j])) {
                    $element = check_plain($_POST[$i . "_element" . $j]);
                  }
                  if (isset($_POST[$i . "_element" . $j])) {
                    $start = $j;
                    break;
                  }
                }
                if ($start != -1) {
                  for ($j = $start; $j < 100; $j++) {
                    if (isset($_POST[$i . "_element" . $j])) {
                      $element = check_plain($_POST[$i . "_element" . $j]);
                    }
                    if (isset($_POST[$i . "_element" . $j])) {
                      $list = $list . check_plain($_POST[$i . "_element" . $j]) . '<br />';
                    }
                  }
                  $list = $list . '</td></tr>';
                }
              }
            }
          }
        }
      }
    }
    $list .= '</table>';
    $attachments = array();
    for ($k = 0; $k < count($all_files); $k++) {
      $attachments[$k] = $all_files[$k]['tmp_name'];
    }
    $body = $row->script1 . '<br />' . $list . '<br />' . $row->script2;
    $body = wordwrap($body, 70, "\n", TRUE);
    $params['body'] = $body;
    $params['subject'] = $row->title;
    $params['attachment'] = $attachments;
    $site_email = variable_get('site_mail', '');
    array_push($cc, $row->mail);
    $send = new Attachmentemail(implode(',', $cc), $site_email, $row->title, $body, $attachments);
    $send->send();
    if ($send == TRUE) {
      if ($row->redirect_url == '') {
        $_SESSION['submited' . $nodeid] = t("Your form was successfully submitted");
        header('Location: ' . url('node/') . $nodeid);
      }
      else {
        header('Location: ' . $row->redirect_url);
      }
    }
    else {
      $_SESSION['submited' . $nodeid] = t("Error, email was not sent");
      header('Location: ' . url('node/') . $nodeid);
    }
  }
  else {
    $_SESSION['submited' . $nodeid] = t("Your form was successfully submitted");
    header('Location: ' . url('node/') . $nodeid);
  }
}


/**
 * Send the email.
 */
class Attachmentemail {
  protected $to = '';
  protected $from = '';
  protected $subject = '';
  protected $message = '';
  protected $attachedfiles = '';

  /**
   * Constructor.
   */
  public function __construct($to, $from, $subject, $message, $attachedfiles = array()) {
    $this->to = $to;
    $this->from = $from;
    $this->subject = $subject;
    $this->message = $message;
    $this->attachedfiles = $attachedfiles;
    $this->boundary = md5(date('r', time()));
  }

  /**
   * Send the email.
   */
  public function send() {
    $header = "From: " . ($this->from) . " <" . ($this->from) . ">" . PHP_EOL;
    $header .= "Reply-To: " . ($this->from) . PHP_EOL;

    // $header .= "Content-Transfer-Encoding: 7bit" . PHP_EOL;
    $header .= "Content-Type: multipart/mixed; boundary=\"" . $this->boundary . '"' . PHP_EOL;
    $header .= "MIME-Version: 1.0" . PHP_EOL;
    $message = "This is a multi-part message in MIME format." . PHP_EOL . PHP_EOL;
    $message .= "--" . $this->boundary . PHP_EOL;
    // $message .= "Content-Transfer-Encoding: binary" . PHP_EOL;
    $message .= "Content-type:text/html; charset=UTF-8; format=flowed;" . PHP_EOL . PHP_EOL;
    $message .= $this->message . PHP_EOL . PHP_EOL;
    $message .= $this->getbinaryattachments() . PHP_EOL;
    $message .= '--' . $this->boundary . '--' . PHP_EOL;
    $this->message = $message;

    if (mail($this->to, $this->subject, $this->message, $header)) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  /**
   * Get the attachments in a base64 format.
   */
  public function getbinaryattachments() {
    $output = '';

    foreach ($this->attachedfiles as $attachment) {
      $info = pathinfo($attachment);
      switch ($info['extension']) {
        case 'jpg':
        case 'jpe':
        case 'jpeg':
        case 'bmp':
        case 'gif':
          $mime = 'image/jpeg jpg';
          break;

        case 'xls':
        case 'xlsx':
          $mime = 'application/msexcel';
          break;

        case 'png':
          $mime = 'image/png';
          break;

        case 'doc':
        case 'docx':
          $mime = 'application/msword';
          break;

        case 'txt':
          $mime = 'text/plain';
          break;

        case 'zip':
          $mime = 'application/zip';
          break;

        case 'avi':
          $mime = 'video/x-msvideo';
          break;

        case 'htm':
        case 'html':
          $mime = 'text/html';
          break;

        case 'mp3':
          $mime = 'audio/mpeg';
          break;

        case 'mpa':
        case 'mpe':
        case 'mpeg':
        case 'mpg':
          $mime = 'video/mpeg';
          break;

        default:
          $mime = 'image/jpeg jpg';
          break;
      }
      $file = array(
        'path' => $attachment,
        'filename' => $info['basename'],
        'type' => $mime,
      );
      $attachment_bin = file_get_contents($file['path']);
      $attachment_bin = chunk_split(base64_encode($attachment_bin));
      $output .= '--' . $this->boundary . PHP_EOL;
      $output .= 'Content-Type: ' . $file['type'] . '; name="' . basename($file['path']) . '"' . PHP_EOL;
      $output .= 'Content-Transfer-Encoding: base64' . PHP_EOL;
      $output .= 'Content-Disposition: attachment' . PHP_EOL . PHP_EOL;
      $output .= $attachment_bin . PHP_EOL . PHP_EOL;
    }
    return $output;
  }
}

/**
 * Delete submissions.
 */
function form_maker_remove($group_id) {
  db_query("DELETE FROM {form_maker_submits_table} WHERE group_id = :group_id", array(':group_id' => $group_id));
}

/**
 * Delete submissions.
 */
function form_maker_delete_submishions() {
  if (isset($_POST["delete"])) {
    if ($_POST["delete"] != '0') {
      if (db_query("DELETE FROM {form_maker_submits_table} WHERE group_id = :group_id", array(':group_id' => $_POST["delete"]))) {
        drupal_set_message(t("Item Deleted."), 'status', FALSE);
      }
    }
  }
  if (isset($_POST["cid"])) {
    if ($_POST["idd"] != '0') {
      $b = TRUE;
      foreach ($_POST["cid"] as $delete_id) {
        if (db_query("DELETE FROM {form_maker_submits_table} WHERE group_id = :group_id", array(':group_id' => $delete_id))) {
        }
        else {
          $b = FALSE;
        }
      }
      if ($b) {
        drupal_set_message(t("Items Deleted."), 'status', FALSE);
      }
    }
  }
}

/**
 * Edit submissions.
 */
function form_maker_Edit_Submission($id) {
  if (arg(0) == 'node' && is_numeric(arg(1))) {
    $nodeid = arg(1);
  }
  $string_edit_submission = url('node/' . $nodeid . '/submissions', array('query' => array('task' => ''), 'absolute' => FALSE));
  $string_edit_submission_cancel = url('node/' . $nodeid . '/submissions', array('absolute' => FALSE));
  drupal_add_js(drupal_get_path('module', 'form_maker') . '/js/form_maker_edit.js');
  drupal_add_js(drupal_get_path('module', 'form_maker') . '/js/form_maker_onload.js');
  drupal_add_js(array('form_maker' => array('string_edit_submission' => $string_edit_submission, 'get_submission_id' => $_GET['submission_id'])), 'setting');
  $frontend_edit_submit = '
		  	<table width="95%" rules="none" style="border:none">
	  		<tr>
	  			<td width="100%"><h2>Edit submission ' . $_GET['submission_id'] . '</h2></td>
	  			<td align="right">
					<input type="button" onclick=submit_in_editsub("Save_Edit_Submission") value="Save" class="form-submit" />
				</td> 
	  			<td align="right" style="border:none" >
					<input type="button" onclick=window.location.href="' . $string_edit_submission_cancel . '" value="Cancel" class="form-submit" />
				</td>
			</tr>
		</table>';

  $value = db_query("SELECT * FROM {form_maker_submits_table} WHERE form_id= :form_id", array(':form_id' => $id));
  $rows = array();
  while ($a = $value->fetchObject()) {
    $rows[] = $a;
  }
  $n = count($rows);
  $labels = array();
  for ($i = 0; $i < $n; $i++) {
    $row = &$rows[$i];
    if (!in_array($row->element_label, $labels)) {
      array_push($labels, $row->element_label);
    }
  }
  $sorted_labels_id = array();
  $sorted_labels = array();
  $label_titles = array();
  if ($labels) {
    $label_id = array();
    $label_order = array();
    $label_order_original = array();
    $label_type = array();
    $value = db_query("SELECT * FROM {form_maker_table} WHERE id= :id", array(':id' => $id));
    $this_form = $value->fetchObject();
    $label_all = explode('#****#', $this_form->label_order);
    $label_all = array_slice($label_all, 0, count($label_all) - 1);
    foreach ($label_all as $key => $label_each) {
      $label_id_each = explode('#**id**#', $label_each);
      array_push($label_id, $label_id_each[0]);
      $label_oder_each = explode('#**label**#', $label_id_each[1]);
      array_push($label_order_original, $label_oder_each[0]);
      $ptn = "/[^a-zA-Z0-9_]/";
      $rpltxt = "";
      $label_temp = preg_replace($ptn, $rpltxt, $label_oder_each[0]);
      array_push($label_order, $label_temp);
      array_push($label_type, $label_oder_each[1]);
    }
    foreach ($label_id as $key => $label) {
      if (in_array($label, $labels)) {
        array_push($sorted_labels, $label_order[$key]);
        array_push($sorted_labels_id, $label);
        array_push($label_titles, $label_order_original[$key]);
      }
    }
    $i = 0;
    foreach ($sorted_labels_id as $idd) {
      $labelll[$idd] = $label_titles[$i];
      $i++;
    }
  }
  $element_labels = db_query("SELECT element_label FROM {form_maker_submits_table} WHERE group_id= :group_id", array(':group_id' => $_GET['submission_id']))->fetchCol();
  $value = db_query("SELECT * FROM {form_maker_submits_table} WHERE group_id= :group_id", array(':group_id' => $_GET['submission_id']));
  $row = $value->fetchObject();
  $frontend_edit_submit .= '
		<fieldset style="border:none">
		<table >
			<tr>
				<td>
					<span>ID: </span>
				</td>
				<td>' .	$row->group_id . '
				</td>
			</tr>
			<tr>
				<td>
					<span>Submit date: </span>
				</td>
				<td>' . $row->date . '
				</td>
			</tr>
			<tr>
				<td>
					<span>Submitter\'s IP: </span>
				</td>
				<td>' . $row->ip . '
				</td>
			</tr>';
  foreach ($element_labels as $key => $element_label) {

    $value = db_query("SELECT element_value FROM {form_maker_submits_table} WHERE group_id= :group_id and element_label= :element_label", array(':group_id' => $_GET['submission_id'], ':element_label' => $element_label));
    $element_value = $value->fetchField();
    if ($element_value == '::' || $element_value == '--') {
      $element_value = '';
    }
    $frontend_edit_submit .= '
			<tr>
				<td>
					<span>' . $labelll[$element_label] . '</span>
				</td>
				<td>
					<input class="form-text" type="textfield" name="' . $element_label . '_lid" id="' . $element_label . '_lid" value ="' . $element_value . '">					
				</td>
			</tr>';
  }
  $frontend_edit_submit .= '
		</table>
		</fieldset>';
  $form = array();
  $form['edit_submit'] = array(
    '#type' => 'fieldset',
    '#value' => $frontend_edit_submit,
  );

  $form['#action'] = "#";
  $form['#id'] = 'edit_submit';
  $form['#attributes'] = array('name' => 'edit_submit');
  return $form;
}

/**
 * Save submission.
 */
function form_maker_Save_Edit_Submission($id) {
  $element_labels = db_query("SELECT element_label FROM {form_maker_submits_table} WHERE group_id= :group_id", array(':group_id' => $_GET['submission_id']))->fetchCol();
  foreach ($element_labels as $key => $element_label) {
    db_query("UPDATE {form_maker_submits_table} SET element_value= :element_value where element_label= :element_label and group_id= :group_id", array(
      ':element_value' => $_POST["" . $element_label . "_lid"],
      ':element_label' => $element_label,
      ':group_id' => $_GET['submission_id'],
      ));
  }
  drupal_set_message(t('Submission @get_submission_id Successfully Saved', array('@get_submission_id' => $_GET['submission_id'])), 'status', FALSE);
}
