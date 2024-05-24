<?php
namespace Drupal\menu_reroute\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class RouteExistsConstraintValidator extends ConstraintValidator {
  public function validate($value, Constraint $constraint) {
    $route = $value->value;
    $path_validator = \Drupal::service('path.validator');
    $path_exists = $path_validator->getUrlIfValid($route);
  
    $dynamic_routes = \Drupal::entityTypeManager()->getStorage('dynamic_redirect_route')->loadByProperties([
      'route' => $route
    ]);
  
    if($dynamic_routes){
      $this->context->buildViolation($constraint->route_created)->addViolation();
    }
  
    if ($path_exists) {
      $this->context->buildViolation($constraint->path_exists)->addViolation();
    }
  }
}
