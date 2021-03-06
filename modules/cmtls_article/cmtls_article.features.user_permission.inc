<?php

/**
 * Implementation of hook_user_default_permissions().
 */
function cmtls_article_user_default_permissions() {
  $permissions = array();

  // Exported permission: create cmtls_article content
  $permissions['create cmtls_article content'] = array(
    'name' => 'create cmtls_article content',
    'roles' => array(
      '0' => 'admin',
      '1' => 'authenticated user',
    ),
  );

  // Exported permission: delete any cmtls_article content
  $permissions['delete any cmtls_article content'] = array(
    'name' => 'delete any cmtls_article content',
    'roles' => array(
      '0' => 'admin',
    ),
  );

  // Exported permission: delete own cmtls_article content
  $permissions['delete own cmtls_article content'] = array(
    'name' => 'delete own cmtls_article content',
    'roles' => array(
      '0' => 'admin',
      '1' => 'authenticated user',
    ),
  );

  // Exported permission: edit any cmtls_article content
  $permissions['edit any cmtls_article content'] = array(
    'name' => 'edit any cmtls_article content',
    'roles' => array(
      '0' => 'admin',
    ),
  );

  // Exported permission: edit own cmtls_article content
  $permissions['edit own cmtls_article content'] = array(
    'name' => 'edit own cmtls_article content',
    'roles' => array(
      '0' => 'admin',
      '1' => 'authenticated user',
    ),
  );

  return $permissions;
}
