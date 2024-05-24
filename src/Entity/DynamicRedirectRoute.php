<?php declare(strict_types = 1);

namespace Drupal\menu_reroute\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\menu_reroute\DynamicRedirectRouteInterface;
use Drupal\user\EntityOwnerTrait;

/**
 * Defines the dynamic redirect route entity class.
 *
 * @ContentEntityType(
 *   id = "dynamic_redirect_route",
 *   label = @Translation("Dynamic redirect route"),
 *   label_collection = @Translation("Dynamic redirect routes"),
 *   label_singular = @Translation("dynamic redirect route"),
 *   label_plural = @Translation("dynamic redirect routes"),
 *   label_count = @PluralTranslation(
 *     singular = "@count dynamic redirect routes",
 *     plural = "@count dynamic redirect routes",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\menu_reroute\DynamicRedirectRouteListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "add" = "Drupal\menu_reroute\Form\DynamicRedirectRouteForm",
 *       "edit" = "Drupal\menu_reroute\Form\DynamicRedirectRouteForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *       "delete-multiple-confirm" = "Drupal\Core\Entity\Form\DeleteMultipleForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "dynamic_redirect_route",
 *   data_table = "dynamic_redirect_route_field_data",
 *   translatable = TRUE,
 *   admin_permission = "administer dynamic_redirect_route",
 *   entity_keys = {
 *     "id" = "id",
 *     "langcode" = "langcode",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *     "owner" = "uid",
 *   },
 *   links = {
 *     "collection" = "/admin/content/dynamic-redirect-route",
 *     "add-form" = "/dynamic-redirect-route/add",
 *     "canonical" = "/dynamic-redirect-route/{dynamic_redirect_route}",
 *     "edit-form" = "/dynamic-redirect-route/{dynamic_redirect_route}/edit",
 *     "delete-form" = "/dynamic-redirect-route/{dynamic_redirect_route}/delete",
 *     "delete-multiple-form" = "/admin/content/dynamic-redirect-route/delete-multiple",
 *   },
 *   field_ui_base_route = "entity.dynamic_redirect_route.settings",
 * )
 */
final class DynamicRedirectRoute extends ContentEntityBase implements DynamicRedirectRouteInterface {

  use EntityChangedTrait;
  use EntityOwnerTrait;

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage): void {
    parent::preSave($storage);
    if (!$this->getOwnerId()) {
      // If no owner has been set explicitly, make the anonymous user the owner.
      $this->setOwnerId(0);
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type): array {

    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['label'] = BaseFieldDefinition::create('string')
      ->setTranslatable(TRUE)
      ->setLabel(t('Label'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('view', TRUE);

      $fields['route'] = BaseFieldDefinition::create('string')
      ->setTranslatable(TRUE)
      ->setLabel(t('Route'))
      ->setRequired(TRUE)
      ->addConstraint('RouteExistsConstraint')
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('view', TRUE);

      // $fields['reroute'] = BaseFieldDefinition::create('entity_reference')
      // ->setTranslatable(TRUE)
      // ->setLabel(t('Reroute To'))
      // ->setSetting('target_type', 'node')
      // ->setDisplayOptions('form', [
      //   'type' => 'entity_reference_autocomplete',
      //   'settings' => [
      //     'match_operator' => 'CONTAINS',
      //     'size' => 60,
      //     'placeholder' => '',
      //   ],
      //   'weight' => 15,
      // ])
      // ->setDisplayOptions('view', [
      //   'label' => 'above',
      //   'type' => 'entity_reference_label',
      //   'weight' => 1,
      // ])
      // ->setDisplayConfigurable('form', TRUE)
      // ->setDisplayConfigurable('view', TRUE);

      $fields['reroute'] = BaseFieldDefinition::create('link')
      ->setLabel(t('Reroute To'))
      ->setDisplayOptions('form', [
        'type' => 'link_default',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);
      

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Status'))
      ->setDefaultValue(TRUE)
      ->setSetting('on_label', 'Enabled')
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'settings' => [
          'display_label' => FALSE,
        ],
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'boolean',
        'label' => 'above',
        'weight' => 0,
        'settings' => [
          'format' => 'enabled-disabled',
        ],
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setTranslatable(TRUE)
      ->setLabel(t('Author'))
      ->setSetting('target_type', 'user')
      ->setDefaultValueCallback(self::class . '::getDefaultEntityOwner')
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'placeholder' => '',
        ],
        'weight' => 15,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'author',
        'weight' => 15,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Authored on'))
      ->setTranslatable(TRUE)
      ->setDescription(t('The time that the dynamic redirect route was created.'))
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'timestamp',
        'weight' => 20,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'datetime_timestamp',
        'weight' => 20,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setTranslatable(TRUE)
      ->setDescription(t('The time that the dynamic redirect route was last edited.'));

    return $fields;
  }

}
