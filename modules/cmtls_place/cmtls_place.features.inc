<?php

/**
 * Implementation of hook_ctools_plugin_api().
 */
function cmtls_place_ctools_plugin_api() {
  list($module, $api) = func_get_args();
  if ($module == "feeds" && $api == "feeds_importer_default") {
    return array("version" => 1);
  }
  elseif ($module == "strongarm" && $api == "strongarm") {
    return array("version" => 1);
  }
}

/**
 * Implementation of hook_node_info().
 */
function cmtls_place_node_info() {
  $items = array(
    'cmtls_place' => array(
      'name' => t('Community Tools: Place'),
      'module' => 'features',
      'description' => '',
      'has_title' => '1',
      'title_label' => t('Pealkiri'),
      'has_body' => '1',
      'body_label' => t('Tekst'),
      'min_word_count' => '0',
      'help' => '',
    ),
  );
  return $items;
}

/**
 * Implementation of hook_views_api().
 */
function cmtls_place_views_api() {
  return array(
    'api' => '2',
  );
}
