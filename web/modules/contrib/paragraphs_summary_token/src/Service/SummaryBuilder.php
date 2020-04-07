<?php

namespace Drupal\paragraphs_summary_token\Service;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\entity_reference_revisions\EntityReferenceRevisionsFieldItemList;

/**
 * Class SummaryBuilder.
 *
 * @package Drupal\paragraphs_summary_token\Service
 */
class SummaryBuilder {

  /**
   * The paragraph fields.
   *
   * @var array
   */
  protected $paragraphFields = [];

  /**
   * The text fields on paragraph entities.
   *
   * @var array
   */
  protected $paragraphTextFields = [];

  /**
   * The field storage config storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $fieldStorageConfigStorage;

  /**
   * SummaryBuilder constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
    $this->fieldStorageConfigStorage = $entityTypeManager->getStorage('field_storage_config');
  }

  /**
   * Builds a paragraph summary.
   *
   * @param \Drupal\entity_reference_revisions\EntityReferenceRevisionsFieldItemList $paragraphs_field
   *   The paragraphs field entity.
   * @param int $trim
   *   The length of the text.
   *
   * @return string
   *   The summary.
   *
   * @throws \Drupal\Core\TypedData\Exception\MissingDataException
   */
  public function build(EntityReferenceRevisionsFieldItemList $paragraphs_field, $trim = 300) {
    return text_summary($this->buildSummary($paragraphs_field), NULL, $trim);
  }

  /**
   * Build the summary for the given content entity based on paragraph fields.
   *
   * @param \Drupal\entity_reference_revisions\EntityReferenceRevisionsFieldItemList $paragraphs_field
   *   The paragraphs field entity.
   *
   * @return null|string
   *   A string when a summary is found, else NULL.
   *
   * @throws \Drupal\Core\TypedData\Exception\MissingDataException
   */
  private function buildSummary(EntityReferenceRevisionsFieldItemList $paragraphs_field) {
    $summary = NULL;

    if (!$paragraphs_field->isEmpty()) {
      /** @var \Drupal\paragraphs\ParagraphInterface $paragraph */
      foreach ($paragraphs_field->referencedEntities() as $paragraph) {
        // First, loop over all text_long fields and check if one of those
        // fields contain content.
        foreach ($this->getParagraphTextFields() as $text_field) {
          if ($paragraph->hasField($text_field) &&
            !$paragraph->get($text_field)->isEmpty()
          ) {
            $summary = $paragraph->get($text_field)->value;
            break 2;
          }
        }

        // No summary found, check if the paragraph has a reference field to
        // add nested paragraphs.
        foreach ($this->getParagraphFields($paragraph->getEntityType()) as $paragraphs_field_name) {
          if ($paragraph->hasField($paragraphs_field_name)) {
            $summary = $this->buildSummary($paragraph->get($paragraphs_field_name));
            if (!empty($summary)) {
              break 2;
            }
          }
        }
      }
    }

    return $summary;
  }

  /**
   * Get all paragraph reference fields for the given entity type.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entityType
   *   The entity type.
   *
   * @return array
   *   An array of all paragraph fields.
   */
  private function getParagraphFields(EntityTypeInterface $entityType) {
    if (empty($this->paragraphFields[$entityType->id()])) {
      $paragraph_fields = [];
      /** @var \Drupal\field\FieldStorageConfigInterface[] $entity_reference_revision_fields */
      $entity_reference_revision_fields = $this->fieldStorageConfigStorage->loadByProperties([
        'entity_type' => $entityType->id(),
        'type' => 'entity_reference_revisions',
      ]);

      foreach ($entity_reference_revision_fields as $entity_reference_revision_field) {
        if ($entity_reference_revision_field->getSetting('target_type') === 'paragraph') {
          $paragraph_fields[] = $entity_reference_revision_field->get('field_name');
        }
      }

      sort($paragraph_fields);
      $this->paragraphFields[$entityType->id()] = $paragraph_fields;
    }

    return $this->paragraphFields[$entityType->id()];
  }

  /**
   * Get all text fields on paragraph entities.
   *
   * @return array
   *   An array of all text fields on paragraph entities.
   */
  private function getParagraphTextFields() {
    if (empty($this->paragraphTextFields)) {
      $paragraph_text_fields = [];
      /** @var \Drupal\field\FieldStorageConfigInterface[] $paragraph_text_fields_config */
      $paragraph_text_fields_config = $this->fieldStorageConfigStorage->loadByProperties([
        'entity_type' => 'paragraph',
        'type' => 'text_long',
      ]);

      foreach ($paragraph_text_fields_config as $paragraph_field) {
        $paragraph_text_fields[] = $paragraph_field->get('field_name');
      }

      sort($paragraph_text_fields);
      $this->paragraphTextFields = $paragraph_text_fields;
    }

    return $this->paragraphTextFields;
  }

}
