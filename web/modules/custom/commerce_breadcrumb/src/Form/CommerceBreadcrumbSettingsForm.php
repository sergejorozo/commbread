<?php

namespace Drupal\commerce_breadcrumb\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\commerce_breadcrumb\CommerceBreadcrumbConstants;
use Drupal\system\Entity\Menu;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Build Commerce Breadcrumb settings form.
 */
class CommerceBreadcrumbSettingsForm extends ConfigFormBase {

  /**
   * Flag for including categories in breadcrumbs trail.
   *
   * @var string
   */
  protected string $include_categories;

  /**
   * Flag to choose category for each product manually.
   *
   * @var string
   */
  protected string $include_manual_categories;

  /**
   * ID for field categories.
   *
   * @var string
   */
  protected string $categories_field;

  /**
   * ID for field categories.
   *
   * @var string
   */
  protected string $categories_field_tag_id;

  /**
   * Use product title as a last breadcrumb segment.
   *
   * @var string
   */
  protected string $include_last_segment;

  /**
   * Module's settings.
   *
   * @var string
   */
  protected string $module_settings;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->include_categories = CommerceBreadcrumbConstants::INCLUDE_CATEGORIES;
    $instance->include_manual_categories = CommerceBreadcrumbConstants::INCLUDE_MANUAL_CATEGORIES;
    $instance->categories_field = CommerceBreadcrumbConstants::CATEGORIES_FIELD;
    $instance->categories_field_tag_id = CommerceBreadcrumbConstants::CATEGORIES_FIELD_TAG_ID;
    $instance->include_last_segment = CommerceBreadcrumbConstants::INCLUDE_LAST_SEGMENT;
    $instance->module_settings = CommerceBreadcrumbConstants::MODULE_SETTINGS;
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'commerce_breadcrumb_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return $this->module_settings;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->configFactory->getEditable($this->module_settings);

    $applies_categories = $config->get($this->include_categories);
    $applies_last_segment = $config->get($this->include_last_segment);

    $form[$this->include_categories] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Include categories'),
      '#description' => $this->t('Check to include categories in breadcrumb trail.'),
      '#default_value' => $applies_categories,
      '#ajax' => [
        'callback' => '::ajaxShowCategoryField',
        'disable-refocus' => FALSE,
        'event' => 'change',
        'wrapper' => $this->categories_field_tag_id,
        'progress' => [
          'type' => 'throbber',
        ],
      ]
    ];

    $form[$this->categories_field_tag_id] = [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#attributes' => [
        'id' => $this->categories_field_tag_id
        ]
    ];

    $form[$this->include_last_segment] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Include last segment'),
      '#description' => $this->t("Check to include product title as a last segment of breadcrumbs. Otherwise you can use any other module to manage last segment of breadcrumbs. For example 'Easy Breadcrumbs' module."),
      '#default_value' => $applies_last_segment,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function ajaxShowCategoryField(array &$form, FormStateInterface $form_state) {
    $config = $this->configFactory->getEditable($this->module_settings);
    $applies_categories_field = $config->get($this->categories_field);
    $applies_manual_categories = $config->get($this->include_manual_categories);
    $values = $form_state->getValues();
    $description = $this->t('Enter machine name of field you are used for product categories.');
    $description_manual_categories = $this->t('A field will appear in each product in which you can select a category for breadcrumbs manually from the list of categories assigned to the product.');
    if (isset($values[$this->include_categories]) && $values[$this->include_categories] == 1) {
      $details = [
        '#type' => 'details',
        '#title' => $this->t('Category settings'),
        '#open' => TRUE,
        '#prefix' => '<div id="' . $this->categories_field_tag_id . '">',
        '#suffix' => '</div>',
      ];
      $details[$this->categories_field] = [
        '#type' => 'textfield',
        '#title' => $this->t('Category field'),
        '#size' => '60',
        '#suffix' => '<div class="form-item__description">' . $description . '</div>',
        '#default_value' => $applies_categories_field,
        '#name' => $this->categories_field,
      ];
      $details[$this->include_manual_categories] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Manual category selection'),
        '#suffix' => '<div class="form-item__description">' . $description_manual_categories . '</div>',
        '#default_value' => $applies_manual_categories,
        '#name' => $this->include_manual_categories,
      ];

    } else {
      $details = [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#attributes' => [
          'id' => $this->categories_field_tag_id
        ],
        '#name' => $this->categories_field,
      ];
    }

    return $details;
  }

}
