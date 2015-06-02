<?php
/**
 * @file
 * Template for the hooks table.
 */
?>

<div id="easy_module_hooks_viewer_container" class="selections-container">
  <ul id="easy_module_hooks_viewer" class="selections"></ul>
</div>

<table id="easy_module-hooks">
  <thead>
    <tr>
      <th></th>
      <th><?php echo t('Hook'); ?></th>
      <th><?php echo t('Drupal Version'); ?></th>
      <th><?php echo t('Short description'); ?></th>
      <th><?php echo t('Sample code'); ?></th>
      <th><?php echo t('See'); ?></th>
    </tr>
  </thead>
  <tbody>
  <?php
    $str_code = t('sample code');
    $str_see = t('see');
    $code_link_attributes = array('attributes' => array('class' => 'easy-module_hook_code'));
    $see_link_attributes = array(
      'external' => TRUE,
      'absolute' => TRUE,
      'attributes' => array('target' => '_blank', 'class' => 'easy-module_hook_link'),
    );
  ?>
  <?php
  foreach ($hooks_data as $hook):
    $code_link_attributes['attributes']['title'] = $hook->name;
    $see_link_attributes['attributes']['title'] = $hook->name;
    $check_id = 'easy_module-hook_' . $hook->id;
  ?>
    <tr>
      <td>
        <input name="easy_module-hooks[<?php echo $hook->id; ?>]"
               value="<?php echo $hook->id; ?>"
               id="<?php echo $check_id; ?>"
               class="easy-module_hook-check"
               type="checkbox" />
      </td>
      <td>
        <label id="<?php echo $check_id; ?>_name" for="<?php echo $check_id; ?>">
          <?php echo $hook->name; ?>
        </label>
      </td>
      <td>
        <?php echo $hook->drupal_version; ?>
      </td>
      <td>
        <span title="<?php echo $hook->name; ?>"><?php echo $hook->short_description; ?></span>
      </td>
      <td><?php echo l($str_code, 'easy-module/hook-info/' . $hook->name . '/' . $hook->drupal_version, $code_link_attributes); ?></td>
      <td><?php echo l($str_see, $hook->link, $see_link_attributes); ?></td>
    </tr>
  <?php
  endforeach;
  ?>
  </tbody>
</table>

<div id="hook_code_dlg"></div>
