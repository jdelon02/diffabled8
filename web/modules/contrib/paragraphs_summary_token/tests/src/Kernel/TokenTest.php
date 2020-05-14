<?php

namespace Drupal\Tests\paragraphs_summary_token\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\Tests\paragraphs\FunctionalJavascript\ParagraphsTestBaseTrait;
use Drupal\Tests\token\Functional\TokenTestTrait;

/**
 * Class TokenTest.
 *
 * @group paragraphs_summary_token
 */
class TokenTest extends KernelTestBase {

  use TokenTestTrait;
  use ParagraphsTestBaseTrait;

  /**
   * The summary builder.
   *
   * @var \Drupal\paragraphs_summary_token\Service\SummaryBuilder
   */
  protected $summaryBuilder;

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'system',
    'field',
    'file',
    'text',
    'entity_reference_revisions',
    'node',
    'user',
    'token',
    'paragraphs',
    'paragraphs_summary_token',
  ];

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->installEntitySchema('node');
    $this->installEntitySchema('paragraph');
    $this->installEntitySchema('user');
    $this->installSchema('system', ['sequences']);
    $this->container->get('module_handler')->loadInclude('paragraphs', 'install');
    $this->summaryBuilder = $this->container->get('paragraphs_summary_token.summary_builder');

    // Add text paragraph.
    $this->addParagraphsType('text');
    $this->addFieldtoParagraphType('text', 'field_body', 'text_long');
    $this->addParagraphedContentType('page');

    // Add container paragraph.
    $this->addParagraphsType('paragraphs_container');
    $this->addParagraphsField('paragraphs_container', 'paragraphs_container_paragraphs', 'paragraph');
  }

  /**
   * Test the module with a single paragraph.
   */
  public function testParagraphSummaryTokenWithSingleParagraph() {
    $paragraph = Paragraph::create([
      'type' => 'text',
      'field_body' => '<p>' . $this->getRandomGenerator()->paragraphs(300) . '</p>',
    ]);
    $paragraph->save();

    // Add test content with paragraph container.
    $node = Node::create([
      'type' => 'page',
      'title' => 'Paragraphs Test',
      'field_paragraphs' => [$paragraph],
    ]);
    $node->save();

    $data = [
      'field_property' => TRUE,
      'field_name' => 'node-field_paragraphs',
      'node-field_paragraphs' => $node->get('field_paragraphs'),
    ];
    $tokens = [
      'summary' => $this->summaryBuilder->build($node->get('field_paragraphs'), 300),
    ];
    $this->assertTokens('node:field_paragraphs', $data, $tokens);
  }

  /**
   * Test the module with multiple paragraphs.
   */
  public function testParagraphSummaryTokenWithMultipleParagraphs() {
    $paragraph1 = Paragraph::create([
      'type' => 'text',
      'field_body' => '<p>Paragraph 1</p>',
    ]);
    $paragraph1->save();
    $paragraph2 = Paragraph::create([
      'type' => 'text',
      'field_body' => '<p>Paragraph 2</p>',
    ]);
    $paragraph2->save();
    $paragraph3 = Paragraph::create([
      'type' => 'text',
      'field_body' => '<p>Paragraph 3</p>',
    ]);
    $paragraph3->save();

    // Add test content with paragraph container.
    $node = Node::create([
      'type' => 'page',
      'title' => 'Paragraphs Test',
      'field_paragraphs' => [$paragraph1, $paragraph2, $paragraph3],
    ]);
    $node->save();

    $data = [
      'field_property' => TRUE,
      'field_name' => 'node-field_paragraphs',
      'node-field_paragraphs' => $node->get('field_paragraphs'),
    ];
    $tokens = [
      'summary' => '<p>Paragraph 1</p>',
    ];
    $this->assertTokens('node:field_paragraphs', $data, $tokens);
  }

  /**
   * Test the module with nested paragraphs.
   */
  public function testParagraphSummaryTokenWithNestedParagraphs() {
    $text_paragraph_1 = Paragraph::create([
      'type' => 'text',
      'field_body' => '',
    ]);
    $text_paragraph_1->save();
    $text_paragraph_2 = Paragraph::create([
      'type' => 'text',
      'field_body' => '',
    ]);
    $text_paragraph_2->save();
    $text_paragraph_3 = Paragraph::create([
      'type' => 'text',
      'field_body' => '',
    ]);
    $text_paragraph_3->save();
    $text_paragraph_4 = Paragraph::create([
      'type' => 'text',
      'field_body' => '<p>Paragraph 4</p>',
    ]);
    $text_paragraph_3->save();
    $text_paragraph_5 = Paragraph::create([
      'type' => 'text',
      'field_body' => '<p>Paragraph 5</p>',
    ]);
    $text_paragraph_5->save();

    // Create container that contains the first two text paragraphs.
    $paragraph_1 = Paragraph::create([
      'type' => 'paragraphs_container',
      'paragraphs_container_paragraphs' => [
        $text_paragraph_1,
        $text_paragraph_2,
      ],
    ]);
    $paragraph_1->save();

    $paragraph_2 = Paragraph::create([
      'type' => 'paragraphs_container',
      'paragraphs_container_paragraphs' => [
        $text_paragraph_3,
        $text_paragraph_4,
      ],
    ]);
    $paragraph_2->save();

    $paragraph_3 = Paragraph::create([
      'type' => 'paragraphs_container',
      'paragraphs_container_paragraphs' => [
        $text_paragraph_5,
      ],
    ]);
    $paragraph_3->save();

    // Add test content with paragraph container.
    $node = Node::create([
      'type' => 'page',
      'title' => 'Paragraphs Test',
      'field_paragraphs' => [$paragraph_1, $paragraph_2, $paragraph_3],
    ]);
    $node->save();

    $data = [
      'field_property' => TRUE,
      'field_name' => 'node-field_paragraphs',
      'node-field_paragraphs' => $node->get('field_paragraphs'),
    ];
    $tokens = [
      'summary' => '<p>Paragraph 4</p>',
    ];
    $this->assertTokens('node:field_paragraphs', $data, $tokens);
  }

}
