<?php
namespace Drupal\menu_reroute\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Provides a Custom Constraint.
 *
 * @Constraint(
 *   id = "RouteExistsConstraint",
 *   label = @Translation("Route check constraint", context = "Validation"),
 * )
 */
class RouteExistsConstraint extends Constraint {
  public $route_created = 'Redirect for route has already been created';
  public $path_exists = 'Path already exists, please provide a different path';
}
