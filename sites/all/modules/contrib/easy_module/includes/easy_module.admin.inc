<?php
/**
 * @file
 * Module's admin pages.
 */

/**
 * Get the settings page.
 *
 * @return string
 *   HTML of the rendered form.
 */
function _easy_module_general_settings_page() {
  return drupal_get_form('_easy_module_general_settings_form');
}

/**
 * Get the Form.
 *
 * @return Assoc
 *   renderable form.
 */
function _easy_module_general_settings_form() {
  // Fieldset for grouping general settings fields.
  $fieldset_general = array(
    '#type' => 'fieldset',
    '#title' => t('General settings'),
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
  );
  $fieldset_general['warning_msg'] = array(
    '#markup' => t("Are you sure about rebuilding Hooks' data? This would take around 2 minutes."),
  );
  $form = array();
  // Inserts the fieldset for grouping general settings fields.
  $form['general'] = $fieldset_general;
  // Adds buttons for processing the form.
  $form['buttons'] = array(
    'submit' => array(
      '#type' => 'submit',
      '#value' => t('Rebuild'),
    ),
  );
  // Specifies the callback function for processing the form submission.
  $form['#submit'] = array('_easy_module_rebuild_hooks_data');
  return $form;
}

/**
 * Rebuilds the whole Hooks' data.
 */
function _easy_module_rebuild_hooks_data() {

  _easy_module_rebuild_hooks_basic_data();
  _easy_module_rebuild_hooks_profile_data();

  $success_message = t("Hooks's data were successfully rebuilded.");
  $success_message = check_plain($success_message);
  drupal_set_message($success_message);
}

/**
 * Rebuilds the basic data of the hooks.
 */
function _easy_module_rebuild_hooks_basic_data() {
  // Obtains the hooks' basic data.
  $hooks = _easy_module_obtain_hooks_data();
  // Cleans the database table.
  db_truncate('easy_module_hooks')->execute();
  // Inserts the hooks' basic data.
  $query = db_insert('easy_module_hooks')->fields(array(
    'name',
    'link',
    'short_description',
    'drupal_version',
  ));
  foreach ($hooks as $record) {
    $query->values($record);
  }
  $query->execute();
}

/**
 * Rebuilds the hook's profile data.
 */
function _easy_module_rebuild_hooks_profile_data() {

  $query = db_select('easy_module_hooks', 'h');
  $query->fields('h',
      array('id', 'name', 'link', 'short_description', 'drupal_version'))
    ->orderBy('h.name');
  $result = $query->execute();

  foreach ($result as $record) {
    $hook = _easy_module_obtain_hook_profile($record);
    $query = db_update('easy_module_hooks');
    $query->condition('id', $record->id, '=')
          ->fields(array(
            'description' => $hook['description'],
            'documentation' => $hook['documentation'],
            'params_docs' => $hook['params_docs'],
            'signature' => $hook['signature'],
            'sample_code' => $hook['sample_code']));
    $query->execute();
  }
}

/**
 * Obtains the hooks' data.
 *
 * @return array
 *   hooks' data.
 */
function _easy_module_obtain_hooks_data() {

  $drupal_versions = EasyModuleConstants::getSupportedDrupalVersions();

  $hooks = array();

  $dom = new domdocument();
  $dom->preserveWhiteSpace = FALSE;

  $i = 0;

  foreach ($drupal_versions as $drupal_version => $label) {

    $url = 'http://api.drupal.org/api/drupal/includes!module.inc/group/hooks/' . $drupal_version;
    $html = file_get_contents($url);

    @$dom->loadHTML($html);
    unset($html);

    // The second "tbody" is the one containing the hooks' data. Thus obtains
    // its TDs.
    $table_idx = 0;
    if ($drupal_version === 7) {
      $table_idx = 1;
    }
    $tds = $dom->getElementsByTagName('table')->item($table_idx)->getElementsByTagName('td');

    foreach ($tds as $td) {
      // Link and name are in the same column (even columns).
      if ($i % 2 === 0) {
        // Used for preserving HTML tags. The HTML inside each TD would be
        // stripped out otherwise.
        $td_html = $dom->saveXML($td);
        // Extracts the link and the name of the hook.
        preg_match('/<a[^>]*href="(.*)"[^>]*>(.+)<\/a>/', $td_html, $matches);
        // Relative path to "http://api.drupal.org". Replaces '%21' with '!' for
        // readibility.
        $link = str_replace('%21', '!', $matches[1]);
        $link = trim($link);
        // Absolute URL.
        $hooks[$i]['link'] = 'http://api.drupal.org' . $link;
        // Hook's name, E.g. "hook_menu".
        $name = trim($matches[2]);
        $hooks[$i]['name'] = $name;
        $hooks[$i]['drupal_version'] = $drupal_version;
      }
      else {
        // Hook's short description (odd columns).
        // Replaces line-endings by spaces.
        $short_description = preg_replace("/\r*\n+/", ' ', $td->nodeValue);
        $short_description = trim($short_description);
        $hooks[$i - 1]['short_description'] = $short_description;
      }
      ++$i;
    }
  }

  return $hooks;
}

/**
 * Obtains the hook's data.
 *
 * @param stdClass $hook
 *   hook object.
 *
 * @return Assoc
 *   hook's data.
 */
function _easy_module_obtain_hook_profile($hook) {

  $html = file_get_contents($hook->link);
  $dom = new domdocument();
  $dom->preserveWhiteSpace = FALSE;
  @$dom->loadHTML($html);
  unset($html);

  $node = $dom->getElementById('api-function-signature');

  // Obtains the hook's signature.
  foreach ($node->getElementsByTagName('tr') as $tr) {
    if ($tr->getAttribute('class') === 'active') {
      foreach ($tr->getElementsByTagName('td') as $td) {
        if ($td->getAttribute('class') === 'signature') {
          $signature = trim($td->nodeValue);
          // Signature obtained, then stops scanning.
          break;
        }
      }
      // Signature is inside the 'tr' with the class 'active', then stops
      // scanning.
      break;
    }
  }

  // Obtains the 'description' of the hook.
  $description_lines = array();
  while ($node = $node->nextSibling) {
    // Starts a new section, then stops scanning for the hook's description.
    if (@$node->tagName === 'h3') {
      break;
    }
    $value = trim($node->nodeValue);
    if ($value) {
      $description_lines[] = $value;
    }
  }
  $description = join(' ', $description_lines);

  $param_number = 0;
  $params = array();
  $return = '';
  $sample_code = '';

  // Obtains 'paramaters', 'return' and 'sample code'.
  foreach ($dom->getElementsByTagName('h3') as $node) {
    switch ($node->nodeValue) {
      case 'Parameters':
        while ($node = $node->nextSibling) {
          // Starts a new section, then stops scanning for 'parameters'.
          if (@$node->tagName === 'h3') {
            break;
          }
          elseif (@$node->tagName === 'p') {
            // Start a new param.
            $aux = $node->firstChild;
            do {
              if (@$aux->tagName === 'strong') {
                $params[$param_number]['code'] = trim($aux->nodeValue);
                $params[$param_number]['description'] = trim($aux->nextSibling->nodeValue);
                ++$param_number;
                break;
              }
            } while ($aux = @$aux->nextSibling);
          }
          else {
            // Continuation of the description of the param.
            $value = trim($node->nodeValue);
            if ($value) {
              $params[$param_number - 1]['description'] .= ' ' . $value;
            }
          }
        }
        break;

      case 'Return value':
        $return_arr = array();
        while ($node = $node->nextSibling) {
          // Starts a new section, then stops scanning for 'return'.
          if (@$node->tagName === 'h3') {
            break;
          }
          $return_arr[] = trim($node->nodeValue);
        }
        $return = join(' ', $return_arr);
        $return = preg_replace('/^\s*Nothing\..*/', '', $return);
        break;

      case 'Code':
        // Sample code.
        while ($node = $node->nextSibling) {
          if (@$node->tagName === 'pre') {
            $sample_code = trim($node->nodeValue);
            $sample_code = preg_replace("/^(.*function\s+.*\(.*\).*\{\r*\n*)|\r*\n*\s*\}.*$/", '', $sample_code);
            // The scanning ends in the sample code, then stops the whole
            // scanning.
            break 2;
          }
        }
        break;
    }
  }

  // Build the 'documentation' for the hook.
  $description_summary = EasyModuleTextHelper::textSummary($description, 560);
  $documentation = EasyModuleTextHelper::textAsDocs($description_summary);

  $params_docs = '';

  // Build the 'parameters' info for the hook.
  if ($params) {
    $params_docs .= "\r\n *";
    foreach ($params as $param) {
      $params_docs .= "\r\n * @param " . $param['code'];
      $param['description'] = EasyModuleTextHelper::textSummary($param['description'], 800);
      $params_docs .= EasyModuleTextHelper::textAsDocs($param['description'], 3);
    }
  }

  // Build the 'return' info for the hook.
  if ($return) {
    $params_docs .= "\r\n *";
    $return = EasyModuleTextHelper::textSummary($return, 560);
    $params_docs .= "\r\n * @return";
    $params_docs .= EasyModuleTextHelper::textAsDocs($return, 3);
  }

  $hook = array();
  $hook['description'] = $description;
  $hook['documentation'] = $documentation;
  $hook['params_docs'] = $params_docs;
  $hook['signature'] = $signature;
  $hook['sample_code'] = $sample_code;

  return $hook;
}
