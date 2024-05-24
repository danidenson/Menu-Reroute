<?php
namespace Drupal\menu_reroute\Routing;
 
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
 
/**
* Class DynamicMOTMenu
*
* @package Drupal\menu_reroute\Routing
*/
class DynamicMOTMenu {
 
  /**
   * Dynamically generate the routes for the entity details.
   *
   * @return \Symfony\Component\Routing\RouteCollection
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function routes() {
    $collection = new RouteCollection();
    $dynamicEntity = \Drupal::entityTypeManager()->getStorage('dynamic_redirect_route')->loadByProperties([
      'status' => 1
    ]);
    foreach ($dynamicEntity as $route_entity) {
      $entity_path = $route_entity->route->value;
      $route = new Route(
          $entity_path,
          [
              '_title' => $route_entity->label(),
              '_controller' => '\Drupal\menu_reroute\Controller\MOTDynamicPathController::dynamicPathAction',
              'type' => 'mot_links',
          ],
          [
              '_permission' => 'access content'
          ],
      );
      $collection->add("entity.$entity_path.entity_details", $route);
    }
    return $collection;
  }
}