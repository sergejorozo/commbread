<?php

namespace Drupal\commerce_breadcrumb;

/**
 * CommerceBreadcrumb module's constants.
 */
class CommerceBreadcrumbConstants {

  /**
   * Module's name.
   */
  const MODULE_NAME = 'commerce_breadcrumb';

  /**
   * Module's settings.
   */
  const MODULE_SETTINGS = 'commerce_breadcrumb.settings';

  /**
   * Flag for including categories in breadcrumbs trail.
   */
  const INCLUDE_CATEGORIES = 'include_categories';

  /**
   * Field machine name used for categories.
   */
  const CATEGORIES_FIELD = 'categories_field';

  /**
   * ID for field categories.
   */
  const CATEGORIES_FIELD_TAG_ID = 'categories-field';

  /**
   * Use product title as a last breadcrumb segment.
   */
  const INCLUDE_LAST_SEGMENT = 'include_last_segment';

  /**
   * First segment title.
   */
  const FIRST_SEGMENT_TITLE = 'Home';

}
