<?php

namespace Drupal\tbwa_newsletter\Entity\Controller;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Routing\UrlGeneratorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a list controller for tbwa_newsletter entity.
 *
 * @ingroup tbwa_newsletter
 */
class NewsletterListBuilder extends EntityListBuilder {

  /**
   * The url generator.
   *
   * @var \Drupal\Core\Routing\UrlGeneratorInterface
   */
  protected $urlGenerator;

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('entity.manager')->getStorage($entity_type->id()),
      $container->get('url_generator')
    );
  }

  /**
   * Constructs a new NewsletterListBuilder object.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The entity storage class.
   * @param \Drupal\Core\Routing\UrlGeneratorInterface $url_generator
   *   The url generator.
   */
  public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage, UrlGeneratorInterface $url_generator) {
    parent::__construct($entity_type, $storage);
    $this->urlGenerator = $url_generator;
  }

  /**
   * {@inheritdoc}
   *
   * We override ::render() so that we can add our own content above the table.
   * parent::render() is where EntityListBuilder creates the table using our
   * buildHeader() and buildRow() implementations.
   */
  public function render() {
    // $build['description'] = array(
    //   '#markup' => $this->t('Content Entity Example implements a Newsletters model. These contacts are fieldable entities. You can manage the fields on the <a href="@adminlink">Newsletters admin page</a>.', array(
    //     '@adminlink' => $this->urlGenerator->generateFromRoute('tbwa_newsletter.contact_settings'),
    //   )),
    // );
    $build['table'] = parent::render();
    return $build;
  }

  /**
   * {@inheritdoc}
   *
   * Building the header and content lines for the contact list.
   *
   * Calling the parent::buildHeader() adds a column for the possible actions
   * and inserts the 'edit' and 'delete' links as defined for the entity type.
   */
  public function buildHeader() {
    $header['id'] = $this->t('Id Newsletter');
    $header['first_name'] = $this->t('First Name');
    $header['last_name'] = $this->t('last Name');
    $header['age'] = $this->t('age');
    $header['street_address'] = $this->t('Street Address');
    $header['country'] = $this->t('Country');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\tbwa_newsletter\Entity\Newsletter */
    $row['id'] = $entity->id();
    $row['first_name'] = $entity->first_name->value;
    $row['last_name'] = $entity->last_name->value;
    $row['age'] = $entity->age->value;
    $row['street_address'] = $entity->street_address->value;
    $row['country'] = $entity->country->value;
    return $row + parent::buildRow($entity);
  }

}
