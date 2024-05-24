<?php

namespace Drupal\menu_reroute\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;


/**
 * Controller for custom route.
 */
class MOTDynamicPathController extends ControllerBase {

  /**
   * The current route match service.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * Constructs a new MOTDynamicPathController object.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The current route match service.
   */
  public function __construct(RouteMatchInterface $route_match) {
    $this->routeMatch = $route_match;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_route_match')
    );
  }

  public function dynamicPathAction() {
    $routePath = $this->routeMatch->getRouteObject()->getPath();
    $dynamicEntity = \Drupal::entityTypeManager()->getStorage('dynamic_redirect_route')->loadByProperties([
        'route' => $routePath
    ]);
    if($dynamicEntity){
        $routeEntity = reset($dynamicEntity);
        $path = $routeEntity->reroute->first()->getUrl()->toString();
        $response = new RedirectResponse($path);
        $response->send();
    }else{
        $path = \Drupal\Core\Url::fromRoute('<front>')->toString();
        $response = new RedirectResponse($path);
        $response->send();
    }
    
  }
}
