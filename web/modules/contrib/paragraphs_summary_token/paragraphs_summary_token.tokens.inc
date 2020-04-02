<?php

/**
 * @file
 * Contains tokens for paragraphs_summary_token module.
 */

use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Implements hook_token_info_alter().
 */
function paragraphs_summary_token_token_info_alter(&$info) {
  foreach (\Drupal::entityTypeManager()->getDefinitions() as $entity_type_id => $entity_type) {
    if (!$entity_type->entityClassImplements(ContentEntityInterface::class)) {
      continue;
    }

    // Make sure a token type exists for this entity.
    $token_type = \Drupal::service('token.entity_mapper')->getTokenTypeForEntityType($entity_type_id);
    if (empty($token_type) || !isset($info['types'][$token_type])) {
      continue;
    }

    $fields = \Drupal::entityTypeManager()->getStorage('field_storage_config')->loadByProperties([
      'entity_type' => $entity_type_id,
      'type' => 'entity_reference_revisions',
    ]);
    foreach ($fields as $field) {
      if (!$field->getSetting('target_type') === 'paragraph') {
        continue;
      }
      $field_token_name = $token_type . '-' . $field->getName();
      $info['tokens']["list<$field_token_name>"]['summary'] = [
        'name' => t('The paragraph summary'),
        'module' => 'paragraphs_summary_token',
        'type' => "list<$field_token_name>",
      ];
    }
  }
}

/**
 * Implements hook_tokens().
 */
function paragraphs_summary_token_tokens($type, $tokens, array $data = [], array $options = []) {
  $return = [];

  if (!empty($data['field_property'])) {
    foreach ($tokens as $name => $original) {
      if ($name === 'summary') {
        $return[$original] = \Drupal::service('paragraphs_summary_token.summary_builder')->build($data[$data['field_name']], 300);
      }
    }
  }

  return $return;
}
