<?php

/**
 * @file
 * Module's admin forms.
 */

/**
 * The default module generation page.
 *
 * @param String $op
 *   the operation.
 *
 * @return String
 *   HTML of the rendered form.
 */
function _easy_module_generation_page($op = NULL) {
  return drupal_get_form('_easy_module_generation_form');
}

/**
 * The module generation form.
 *
 * @return Assoc
 *   renderable form.
 */
function _easy_module_generation_form() {

  drupal_add_library('system', 'ui.dialog');

  // Include the "DataTables" jQuery library if not present.
  if (!module_exists('datatables')) {
    drupal_add_library(EasyModuleConstants::MODULE_NAME, 'datatables');
  }

  if (!module_exists('syntaxhighlighter')) {
    drupal_add_library(EasyModuleConstants::MODULE_NAME, 'syntaxhighlighter');
  }

  $module_path = drupal_get_path('module', EasyModuleConstants::MODULE_NAME);
  drupal_add_css($module_path . '/css/easy_module.css', array('weight' => 999));
  drupal_add_js(array('easy_module_tableTitle' => t('Hooks List')), 'setting');
  drupal_add_js($module_path . '/js/easy_module.js');

  // Fieldset for grouping general settings fields.
  $fieldset_general = array(
    '#type' => 'fieldset',
    '#title' => t('General settings'),
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
  );

  $fieldset_general[EasyModuleConstants::NAME] = array(
    '#type' => 'textfield',
    '#required' => TRUE,
    '#size' => 50,
    '#maxlength' => 50,
    '#title' => t('Name'),
    '#description' => t("The human-readable name of the module to be created. E.g.: Easy Breadcrumb.
      The machine-name version will be auto-deduced from this human-readable name by default;
      but you can manually enter it if you wish."),
  );

  $fieldset_general[EasyModuleConstants::MACHINE_NAME] = array(
    '#type' => 'machine_name',
    '#required' => TRUE,
    '#size' => 50,
    '#maxlength' => 50,
    '#title' => t('Machine name'),
    '#default_value' => variable_get(EasyModuleConstants::MACHINE_NAME),
    '#description' => t("The machine name of the module to be created. E.g.: easy_breadcrumb."),
    '#machine_name' => array(
      'source' => array('general-fieldset', EasyModuleConstants::NAME),
      'exists' => '_easy_module_machine_name_exists',
    ),
  );

  $fieldset_general[EasyModuleConstants::DESCRIPTION] = array(
    '#type' => 'textarea',
    '#required' => TRUE,
    '#rows' => 1,
    '#title' => t('Description'),
    '#description' => t("The description of the module to be created."),
  );

  $fieldset_general[EasyModuleConstants::DEPENDENCIES] = array(
    '#type' => 'textarea',
    '#rows' => 1,
    '#title' => t('Dependencies'),
    '#description' => t("A space-sparated list of dependencies of the module. E.g.: ctools jquery_update"),
  );

  $fieldset_general[EasyModuleConstants::DRUPAL_VERSION] = array(
    '#type' => 'select',
    '#required' => TRUE,
    '#title' => t("Version of Drupal the module is oriented"),
    '#options' => EasyModuleConstants::getSupportedDrupalVersions(),
    '#default_value' => variable_get(EasyModuleConstants::DRUPAL_VERSION, EasyModuleConstants::DEFAULT_DRUPAL_VERSION),
    '#description' => t("Choose the version of Drupal the module is oriented. The available hooks will vary depending on the selected Drupal version."),
  );

  // Fieldset for grouping hooks related info.
  $fieldset_hooks = array(
    '#type' => 'fieldset',
    '#title' => t('Hooks to implement'),
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
  );

  $hooks_data = _easy_module_get_hooks();
  $hooks_options = array();
  foreach ($hooks_data as $data) {
    $hooks_options[$data->id] = $data->name;
  }

  $fieldset_hooks[EasyModuleConstants::HOOKS] = array(
    '#type' => 'select',
    '#multiple' => TRUE,
    '#title' => t('Implemented Hooks'),
    '#options' => $hooks_options,
    '#prefix' => '<div id="hooks_select_wrapper" class="visuallyhidden">',
    '#suffix' => '</div>',
  );

  $fieldset_hooks[EasyModuleConstants::INCLUDE_SAMPLE_CODE] = array(
    '#type' => 'checkbox',
    '#title' => t("Include Sample Code in the implemented Hooks'"),
    '#default_value' => variable_get(EasyModuleConstants::INCLUDE_SAMPLE_CODE, TRUE),
    '#description' => t("Should I include the sample code in your Hooks implementations?"),
  );

  $fieldset_hooks[EasyModuleConstants::HOOKS . '_view'] = array(
    '#markup' => theme('easy_module-hooks', array('hooks_data' => $hooks_data)),
  );

  $form = array();

  // Inserts the fieldset for grouping general settings fields.
  $form['general-fieldset'] = $fieldset_general;
  $form['hooks-fieldset'] = $fieldset_hooks;

  // Adds buttons for processing the form.
  $form['buttons'] = array(
    'submit' => array(
      '#type' => 'submit',
      '#value' => t('Generate'),
    ),
  );

  // Specifies the callback function for processing the form submission.
  $form['#submit'] = array('_easy_module_generation_form_submit');

  // Specifies the theme for the form.
  $form['#theme'] = 'system_settings_form';

  return $form;
}

/**
 * Function 'exists' required by fields of type "machine_name".
 *
 * @param string $name
 *   name to be validated.
 *
 * @return boolean
 *   TRUE is the name already exists, FALSE otherwise.
 */
function _easy_module_machine_name_exists($name) {
  // Always returns FALSE. No need to validate names exitence.
  return FALSE;
}

/**
 * Obtain the Hooks data.
 *
 * @param array $ids
 *   list of Hooks' IDs to be retrieved.
 * @param string $drupal_version
 *   alternatively, it's possible to filter hooks by its Drupal version.
 *
 * @return array
 *   list of Drupal's hooks.
 */
function _easy_module_get_hooks(array $ids = NULL, $drupal_version = NULL) {

  // Obtain hooks data from database.
  $query = db_select('easy_module_hooks', 'h');
  $query->fields('h', array('id', 'name', 'link', 'short_description', 'description',
      'documentation', 'params_docs', 'signature', 'sample_code', 'drupal_version'))
        ->orderBy('h.name')
        ->orderBy('h.drupal_version');

  // If IDs were passed, then only search by IDs as them are uniques.
  if ($ids) {
    $query->condition('h.id', $ids, 'IN');
  }
  elseif ($drupal_version) {
    // Search hooks by the Drupal version.
    $query->condition('h.drupal_version', $drupal_version, '=');
  }

  $hooks = $query->execute();
  $hooks = $hooks->fetchAll();

  return $hooks;
}

/**
 * Obtain the Hook data.
 *
 * @param string $criteria
 *   criteria for searching the Hook.
 *
 * @return stdClass
 *   hook object.
 */
function _easy_module_get_hook(array $criteria) {

  // Obtain hooks data from database.
  $query = db_select('easy_module_hooks', 'h');
  $query->fields('h', array(
    'id',
    'name',
    'documentation',
    'params_docs',
    'signature',
    'sample_code',
  ));

  // If ID was passed, then only search by the ID since it's unique.
  if ($criteria['id']) {
    $query->condition('h.id', $criteria['id'], '=');
  }
  else {
    // Search by the name and the Drupal version of the hook.
    $query->condition('h.name', $criteria['name'], '=')
          ->condition('h.drupal_version', $criteria['drupal_version'], '=');
  }

  $result = $query->execute();

  // Fetch next row as a stdClass object.
  $hook = $result->fetchObject();

  return $hook;
}

/**
 * Obtain the Hook code.
 *
 * @param stdClass $hook
 *   hook object to extract data from.
 * @param int $include_sample
 *   0 for not including sample code; 1 for including commented sample code, and
 *   2 for including sample code.
 * @param string $module_machine_name
 *   name of the module implementing the hook, if so.
 *
 * @return string
 *   hook's info.
 */
function _easy_module_hook_code($hook, $include_sample = 1, $module_machine_name = NULL) {

  $inner = '';

  // If implementing the Hook.
  if (isset($module_machine_name)) {
    // Adds "Implements hook_HOOK().".
    $hook->documentation = "\r\n * Implements " . $hook->signature . ".\r\n * " . $hook->documentation;
    // Replaces the prefix 'hook' with the name of the module implementing it.
    $hook->signature = preg_replace('/^hook/', $module_machine_name, $hook->signature);
    // Adds TODO.
    if ($include_sample !== 0) {
      $inner = "remove the Following Sample Code and ";
    }
    $inner .= "put Your Custom Code instead.";
    // Adds a 'TODO' message for warning the developer.
    $inner = "  // TODO: " . $inner . "\r\n";
  }

  // If include the sample code.
  if ($include_sample !== 0) {
    // For sakes of security, the sample code will be commented out by default.
    $inner .= $include_sample === 1 ? EasyModuleTextHelper::commentCode($hook->sample_code) : $hook->sample_code;
  }

  // Build the string representing the code of the hook.
  $resp = "/**";
  $resp .= $hook->documentation;
  $resp .= $hook->params_docs;
  $resp .= "\r\n */\r\n";
  $resp .= "function " . $hook->signature . " {\r\n";
  $resp .= $inner;
  $resp .= "\r\n";
  $resp .= "}";

  return $resp;
}

/**
 * Prints the Hook info. For AJAX usage.
 *
 * @param string $hook_name
 *   name of the Hook.
 * @param string $drupal_version
 *   version of Drupal.
 */
function _easy_module_hook_code_ajax($hook_name, $drupal_version) {
  // Build the criteria using the given arguments.
  $criteria = array('name' => $hook_name, 'drupal_version' => $drupal_version);
  // Obtains the hook's data.
  $hook = _easy_module_get_hook($criteria);
  // Get the formatted hook's code.
  $hook_code = _easy_module_hook_code($hook, 2);
  // Prints the formatted hook's code.
  echo $hook_code;
  exit;
}

/**
 * Process the submitting of the module generation form.
 *
 * @param Assoc $form
 *   renderable form.
 * @param Assoc $form_state
 *   form's state.
 */
function _easy_module_generation_form_submit($form, &$form_state) {

  $values = $form_state['values'];

  $name = trim($values[EasyModuleConstants::NAME]);
  $machine_name = trim($values[EasyModuleConstants::MACHINE_NAME]);
  $description = trim($values[EasyModuleConstants::DESCRIPTION]);
  // Parse the dependencies to an array. Replaces line-endings by spaces and
  // splits the dependencies by spaces.
  $dependencies = preg_replace("/\r*\n+/", " ", $values[EasyModuleConstants::DEPENDENCIES]);
  $dependencies = trim($dependencies);
  $dependencies = $dependencies === '' ? array() : preg_split("/\s+/", $dependencies);
  $drupal_version = $values[EasyModuleConstants::DRUPAL_VERSION];
  $include_sample = (int) $values[EasyModuleConstants::INCLUDE_SAMPLE_CODE];
  // Store the IDs of the hooks to be implemented.
  $implemented_hooks_ids = $values[EasyModuleConstants::HOOKS];

  // Stores hooks organized by its destination file.
  $hooks = array('install' => array(), 'module' => array());

  if ($implemented_hooks_ids) {
    // Get the names of the hooks to put in the '.install' file.
    $install_hooks = EasyModuleConstants::getInstallHooks();
    $criteria = array();
    foreach ($implemented_hooks_ids as $id) {
      $criteria['id'] = $id;
      // Get the hook's data.
      $hook = _easy_module_get_hook($criteria);
      // Get the formatted hook's code.
      $hook_code = _easy_module_hook_code($hook, $include_sample, $machine_name);
      // Organize hooks by its destination file.
      if (isset($install_hooks[$hook->name])) {
        $hooks['install'][] = $hook_code;
      }
      else {
        $hooks['module'][] = $hook_code;
      }
    }
  }

  // Generate the Zip file and check if there are some error message.
  try {
    _easy_module_pack_generated_module($name, $machine_name, $dependencies, $description, $hooks, $drupal_version);
  } catch (Exception $exc) {
    // Alert about error messages.
    $error_msg = t($exc->getMessage());
    drupal_set_message(check_plain($error_msg), 'error');
  }
}

/**
 * Generated the Zip file containing the module's files.
 *
 * @param string $name
 *   human-readable name of the module to be generated.
 * @param string $machine_name
 *   machine-name of the module to be generated.
 * @param array $dependencies
 *   list of module's dependencies.
 * @param array $description
 *   module's $description.
 * @param array $hooks
 *   list of implemented Hooks.
 * @param int $drupal_version
 *   the Drupal version.
 */
function _easy_module_pack_generated_module($name, $machine_name, array $dependencies, $description,
array $hooks, $drupal_version) {

  // Check if the 'zip' extension is loaded.
  if (!extension_loaded('zip')) {
    throw new Exception("The Zip extension is not loaded.");
  }

  // Get the Drupal temporary path.
  $temp_dir = file_directory_temp();

  // Build the path for putting the zip file.
  $module_dir = $temp_dir . '/' . EasyModuleConstants::MODULE_NAME . '/' . $machine_name;
  // Zip file path.
  $zip_path = $module_dir . '.zip';

  $zip = new ZipArchive();
  if (!$zip->open($zip_path, ZIPARCHIVE::CREATE)) {
    throw new Exception("Error while creating the Zip file.");
  }

  // Add the main dir of the generated module.
  $zip->addEmptyDir($machine_name);

  // Stores the content of the 'README.txt' file.
  $readme_file_content = $description;
  $zip->addFromString($machine_name . '/README.txt', $readme_file_content);

  // Stores the content of the '.info' file.
  $info_file_content = "name = " . $name;
  $info_file_content .= "\r\ndescription = " . $description;
  // If there are module's dependencies then add it to the .info file.
  if ($dependencies) {
    foreach ($dependencies as $dependence) {
      $info_file_content .= "\r\ndependencies[] = " .  $dependence;
    }
  }
  $info_file_content .= "\r\ncore = " . $drupal_version . ".x";
  // Add the '.info' file.
  $zip->addFromString($machine_name . '/' . $machine_name . '.info', $info_file_content);

  // Stores the content of the '.module' file.
  $module_file_content = "<?php\r\n";
  $module_file_content .= "/**";
  $module_file_content .= "\r\n * @file";
  $module_file_content .= EasyModuleTextHelper::textAsDocs($description);
  $module_file_content .= "\r\n */\r\n\n";
  // If implementing some generic hooks.
  if ($hooks['module']) {
    $module_file_content .= join("\r\n\n", $hooks['module']);
  }
  $module_file_content .= "\r\n";
  // Add the '.module' file.
  $zip->addFromString($machine_name . '/' . $machine_name . '.module', $module_file_content);

  // If there are some install hooks.
  if ($hooks['install']) {
    // Stores the content of the '.install' file.
    $file_install_description = "/**";
    $file_install_description .= "\r\n * @file";
    $file_install_description .= "\r\n * Install file for the module.";
    $file_install_description .= "\r\n */\r\n\n";
    $file_install = "<?php\r\n";
    $file_install .= $file_install_description;
    $file_install .= join("\r\n\n", $hooks['install']);
    $file_install .= "\r\n";
    // Add the '.install' file.
    $zip->addFromString($machine_name . '/' . $machine_name . '.install', $file_install);
  }

  // Close the zip.
  $success = $zip->close();
  if ($success === FALSE) {
    throw new Exception("Error while creating the Zip file.");
  }

  // Outputting a ZIP.
  drupal_add_http_header('Content-Type', 'application/zip; charset=utf-8');
  // Set the name of the file to be downloaded.
  drupal_add_http_header('Content-Disposition', 'attachment; filename="' .  $machine_name . '.zip"');
  // Set the filesize.
  drupal_add_http_header('Content-Length', filesize($zip_path));
  // Read the file and send it.
  readfile($zip_path);
  // Clean up the tmp zip file after read it.
  drupal_unlink($zip_path);

  exit;
}
