<?php

namespace Drupal\commerce_breadcrumb;

use Drupal\Core\Breadcrumb\Breadcrumb;
use Drupal\Core\Breadcrumb\BreadcrumbBuilderInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\path_alias\AliasManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Controller\TitleResolverInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Primary implementation for the Commerce Breadcrumb builder.
 */
class CommerceBreadcrumbBuilder implements BreadcrumbBuilderInterface {

  use StringTranslationTrait;

  /**
   * Breadcrumb config object.
   *
   * @var \Drupal\Core\Config\Config
   */
  protected $config;

  /**
   * The request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * The title resolver.
   *
   * @var \Drupal\Core\Controller\TitleResolverInterface
   */
  protected $titleResolver;


  /**
   * The title resolver.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The path alias interface.
   *
   * @var \Drupal\path_alias\AliasManagerInterface
   */
  protected $aliasManager;

  /**
   * Constructs the CommerceBreadcrumbBuilder.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory service.
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   The request stack.
   * @param \Drupal\Core\Controller\TitleResolverInterface $title_resolver
   *   The title resolver.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\path_alias\AliasManagerInterface $alias_manager
   *   The path alias.
   */
  public function __construct(ConfigFactoryInterface $config_factory,
                              RequestStack $request_stack,
                              TitleResolverInterface $title_resolver,
                              EntityTypeManagerInterface $entity_type_manager,
                              AliasManagerInterface $alias_manager) {
    $this->config = $config_factory->get(CommerceBreadcrumbConstants::MODULE_SETTINGS);
    $this->requestStack = $request_stack;
    $this->titleResolver = $title_resolver;
    $this->entityTypeManager = $entity_type_manager;
    $this->aliasManager = $alias_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function applies(RouteMatchInterface $route_match) {
    $p = $route_match->getRouteName() == 'entity.commerce_product.canonical';
    return $route_match->getRouteName() == 'entity.commerce_product.canonical';
  }

  /**
   * {@inheritdoc}
   */
  public function build(RouteMatchInterface $route_match) {
    $breadcrumb = new Breadcrumb();
    // Add first segment as Home.
    $links[] = Link::createFromRoute(t(CommerceBreadcrumbConstants::FIRST_SEGMENT_TITLE), '<front>');
    // Add second segment as Product category.
    if ($this->config->get(CommerceBreadcrumbConstants::INCLUDE_CATEGORIES)) {
      $field_category = $this->config->get(CommerceBreadcrumbConstants::CATEGORIES_FIELD);
      /** @var \Drupal\commerce_product\Entity\ProductInterface $product */
      $product = $route_match->getParameter('commerce_product');
      /** @var \Drupal\taxonomy\TermInterface $category */
      $category = $product->get($field_category)->first()->getValue();
      if ($category) {
        $category = $this->entityTypeManager->getStorage('taxonomy_term')->load($category['target_id']);
        /** @var \Drupal\path_alias\AliasManager $alias */
        $alias = $this->aliasManager->getAliasByPath('/taxonomy/term/' . $category->id());
        $links[] = Link::fromTextAndUrl($category->label(), Url::fromUserInput($alias));
      }
    }
    // Add last segment as Product title.
    if ($this->config->get(CommerceBreadcrumbConstants::INCLUDE_LAST_SEGMENT)) {
      $title = $this->titleResolver->getTitle($this->requestStack->getCurrentRequest(), $route_match->getRouteObject());
      $links[] = Link::createFromRoute($title, '<none>');
    }

    $breadcrumb->addCacheContexts(['route']);
    return $breadcrumb->setLinks($links);
  }

}
