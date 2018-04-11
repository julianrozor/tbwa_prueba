<?php

namespace Drupal\tbwa_newsletter;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\user\EntityOwnerInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface defining a Contact entity.
 *
 * We have this interface so we can join the other interfaces it extends.
 *
 * @ingroup tbwa_newsletter
 */
interface NewsletterInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

}
