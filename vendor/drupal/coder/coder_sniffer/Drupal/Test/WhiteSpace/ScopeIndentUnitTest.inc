<?php

// Test correct indenting of trait conflict resolution.
class MyClass {
  use MyTrait {
     myTraitMethod as renamedTraitMethod;
  }
}

setTimeout(function() {
  $timer = parseInt($target, 10);
  if (timer > 0) {
    $target--;
  }
  else {
    // <dt>.
       $kint->toggleChildren(target . parentNode);
       if ($kint->currentPlus !== -1) {
      $kint->fetchVisiblePluses();
       }
  }
}, 300);

/**
 * Implements hook_field_formatter_view().
 */
function example_field_formatter_view($entity_type, $entity, $field, $instance, $langcode, $items, $display) {

  $element = array();
  switch ($display['type']) {
    case 'example_schema_format':
            foreach ($items as $delta => $item) {
        $element[$delta]['#markup'] = theme('example_formatter_default', $item);
            }
      break;
  }
  return $element;

}

/**
 * A test function.
 */
function test_form() {
  /*
   * Here is some long form comment which is discouraged but not forbidden,
   * I think? It goes over multiple lines in this pseudo doc comment style.
   * And even more text now.
   */
  $form['submit_connection'] = [
    '#prefix' => "<br style='clear:both'/>",
    '#name' => 'enter_connection_settings',
    '#type' => 'submit',
    '#value' => t('Enter connection settings'),
    '#weight' => 100,
  ];
}

/**
 * Provides the necessary user permissions for entity operations.
 *
 * @param string $entity_type_id
 *   The entity type.
 * @param string $operation
 *   The operation, one of 'view', 'create', 'update' or 'delete'.
 *
 * @return array
 *   The set of user permission strings.
 */
function entity_permissions($entity_type_id, $operation) {
  switch ($entity_type_id) {
    case 'entity_test':
      switch ($operation) {
        case 'view':
          return ['view test entity'];
        case 'create':
        case 'update':
        case 'delete':
          return ['administer entity_test content'];
      }
    case 'node':
      switch ($operation) {
        case 'view':
          return ['access content'];
        case 'create':
          return ['create resttest content'];
        case 'update':
          return ['edit any resttest content'];
        case 'delete':
          return ['delete any resttest content'];
      }

    case 'comment':
      switch ($operation) {
        case 'view':
          return ['access comments'];

        case 'create':
          return ['post comments', 'skip comment approval'];

        case 'update':
          return ['edit own comments'];

        case 'delete':
          return ['administer comments'];
      }
      break;

    case 'user':
      switch ($operation) {
        case 'view':
          return ['access user profiles'];

        default:
          return ['administer users'];
      }

    default:
      if (isConfigEntity($entity_type_id)) {
        $entity_type = \Drupal::entityTypeManager()->getDefinition($entity_type_id);
        if ($admin_permission = $entity_type->getAdminPermission()) {
          return [$admin_permission];
        }
      }
  }
  return [];
}

$tests = [
  [
    'original' => '$no_index_value_scalar = TRUE;',
    'settings' => [
      'no_index_value_scalar' => (object) [
        'value' => FALSE,
        'comment' => 'comment',
      ],
    ],
    'expected' => '$no_index_value_scalar = false; // comment',
  ],
  [
    'original' => '$no_index_value_scalar = TRUE;',
    'settings' => [
      'no_index_value_foo' => [
        'foo' => [
          'value' => (object) [
            'value' => NULL,
            'required' => TRUE,
            'comment' => 'comment',
          ],
        ],
      ],
    ],
    'expected' => <<<'EXPECTED'
$no_index_value_scalar = TRUE;
$no_index_value_foo['foo']['value'] = NULL; // comment
EXPECTED
  ],
  [
    'original' => '$no_index_value_array = array("old" => "value");',
    'settings' => [
      'no_index_value_array' => (object) [
        'value' => FALSE,
        'required' => TRUE,
        'comment' => 'comment',
      ],
    ],
    'expected' => '$no_index_value_array = array("old" => "value");
$no_index_value_array = false; // comment',
  ],
  [
    'original' => '$has_index_value_scalar["foo"]["bar"] = NULL;',
    'settings' => [
      'has_index_value_scalar' => [
        'foo' => [
          'bar' => (object) [
            'value' => FALSE,
            'required' => TRUE,
            'comment' => 'comment',
          ],
        ],
      ],
    ],
    'expected' => '$has_index_value_scalar["foo"]["bar"] = false; // comment',
  ],
  [
    'original' => '$has_index_value_scalar["foo"]["bar"] = "foo";',
    'settings' => [
      'has_index_value_scalar' => [
        'foo' => [
          'value' => (object) [
            'value' => ['value' => 2],
            'required' => TRUE,
            'comment' => 'comment',
          ],
        ],
      ],
    ],
    'expected' => <<<'EXPECTED'
$has_index_value_scalar["foo"]["bar"] = "foo";
$has_index_value_scalar['foo']['value'] = array (
'value' => 2,
); // comment
EXPECTED
  ],
];
