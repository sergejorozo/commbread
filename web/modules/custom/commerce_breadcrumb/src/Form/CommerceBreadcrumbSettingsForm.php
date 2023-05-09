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
  protected string $includeCategories;

  /**
   * ID for field categories.
   *
   * @var string
   */
  protected string $categoriesField;

  /**
   * ID for field categories.
   *
   * @var string
   */
  protected string $categoriesFieldTagId;

  /**
   * Use product title as a last breadcrumb segment.
   *
   * @var string
   */
  protected string $includeLastSegment;

  /**
   * Module's settings.
   *
   * @var string
   */
  protected string $moduleSettings;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->includeCategories = CommerceBreadcrumbConstants::INCLUDE_CATEGORIES;
    $instance->categoriesField = CommerceBreadcrumbConstants::CATEGORIES_FIELD;
    $instance->categoriesFieldTagId = CommerceBreadcrumbConstants::CATEGORIES_FIELD_TAG_ID;
    $instance->includeLastSegment = CommerceBreadcrumbConstants::INCLUDE_LAST_SEGMENT;
    $instance->moduleSettings = CommerceBreadcrumbConstants::MODULE_SETTINGS;
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
    return $this->moduleSettings;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->configFactory->getEditable($this->moduleSettings);

    $applies_categories = $config->get($this->includeCategories);
    $applies_last_segment = $config->get($this->includeLastSegment);
    $applies_categoriesField = $config->get($this->categoriesField);
    $applies_manual_categories = $config->get($this->include_manual_categories);

    $form[$this->includeCategories] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Include categories'),
      '#description' => $this->t('Check to include categories in breadcrumb trail.'),
      '#default_value' => $applies_categories,
    ];

    $form['details'] = [
      '#type' => 'details',
      '#title' => $this->t('Category settings'),
      '#open' => TRUE,
    ];

    $form['details'][$this->categoriesField] = [
      '#type' => 'textfield',
      '#title' => $this->t('Category field'),
      '#size' => '60',
      '#description' => $this->t('Enter machine name of field you are used for product categories.'),
      '#default_value' => $applies_categoriesField,
      '#name' => $this->categoriesField,
    ];

    $form[$this->includeLastSegment] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Include last segment'),
      '#description' => $this->t("Check to include product title as a last segment of breadcrumbs."),
      '#default_value' => $applies_last_segment,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $settings = $this->configFactory->getEditable($this->moduleSettings);
    $values = $form_state->cleanValues()->getValues();
    $values['categoriesField'] = $form_state->getUserInput()['categoriesField'];

    foreach ($values as $field_key => $field_value) {
      $settings->set($field_key, $field_value);
    }
    $settings->save();

    parent::submitForm($form, $form_state);
  }

}
