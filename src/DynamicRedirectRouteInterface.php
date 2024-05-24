<?php declare(strict_types = 1);

namespace Drupal\menu_reroute;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface defining a dynamic redirect route entity type.
 */
interface DynamicRedirectRouteInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

}
