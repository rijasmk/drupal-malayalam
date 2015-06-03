<?php
/**
 * @file
 * Module's contants.
 */

/**
 * Common point for storing module's contants.
 */
class EasyModuleConstants {

  /**
   * The name of this module.
   */
  const MODULE_NAME = 'easy_module';

  /**
   * Default version of Drupal.
   */
  const DEFAULT_DRUPAL_VERSION = 7;

  /**
   * The human-readable name of the module to be created.
   */
  const NAME = 'name';

  /**
   * The machine-name of the module to be created.
   */
  const MACHINE_NAME = 'machine_name';

  /**
   * Drupal version the module will be oriented.
   */
  const DRUPAL_VERSION = 'drupal_version';

  /**
   * The dependencies of the module.
   */
  const DEPENDENCIES = 'dependencies';

  /**
   * The description of the module to be created.
   */
  const DESCRIPTION = 'description';

  /**
   * Flag saying if the sample code should be included in your hooks.
   */
  const INCLUDE_SAMPLE_CODE = 'include_sample_code';

  /**
   * List of hooks the module implements.
   */
  const HOOKS = 'hooks';

  /**
   * Get the names of the hooks to put inside the '.install' file of the module.
   *
   * @staticvar Assoc $install_hooks
   *   install hooks.
   *
   * @return array
   *   install hooks.
   */
  public static function getInstallHooks() {
    static $install_hooks = array(
      'hook_disable' => 1,
      'hook_enable' => 1,
      'hook_install' => 1,
      'hook_requirements' => 1,
      'hook_schema' => 1,
      'hook_field_schema' => 1,
      'hook_uninstall' => 1,
      'hook_update_last_removed' => 1,
      'hook_update_N' => 1,
    );
    return $install_hooks;
  }

  /**
   * Get a list of the supported Drupal versions.
   *
   * @staticvar array $versions
   *   supported Drupal versions.
   *
   * @return array
   *   supported Drupal versions.
   */
  public static function getSupportedDrupalVersions() {
    static $drupal_versions = array(6 => '6', 7 => '7');
    return $drupal_versions;
  }
}
