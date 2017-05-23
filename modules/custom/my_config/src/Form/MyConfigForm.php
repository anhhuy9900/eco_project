<?php 

namespace Drupal\my_config\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form handler for the MyConfig add and edit forms.
 */
class MyConfigForm extends EntityForm {

  /**
   * Constructs an MyConfigForm object.
   *
   * @param \Drupal\Core\Entity\Query\QueryFactory $entity_query
   *   The entity query.
   */
  public function __construct(QueryFactory $entity_query) {
    $this->entityQuery = $entity_query;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.query')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $my_config = $this->entity;

    $form['label'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $my_config->label(),
      '#description' => $this->t("Label for the MyConfig."),
      '#required' => TRUE,
    );
    $form['id'] = array(
      '#type' => 'machine_name',
      '#default_value' => $my_config->id(),
      '#machine_name' => array(
        'exists' => array($this, 'exist'),
      ),
      '#disabled' => !$my_config->isNew(),
    );

    // You will need additional form elements for your custom properties.
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $my_config = $this->entity;
    $status = $my_config->save();

    if ($status) {
      drupal_set_message($this->t('Saved the %label MyConfig.', array(
        '%label' => $my_config->label(),
      )));
    }
    else {
      drupal_set_message($this->t('The %label MyConfig was not saved.', array(
        '%label' => $my_config->label(),
      )));
    }

    $form_state->setRedirect('my_config.list');
  }

  /**
   * Helper function to check whether an MyConfig configuration entity exists.
   */
  public function exist($id) {
    $entity = $this->entityQuery->get('my_config')
      ->condition('id', $id)
      ->execute();
    return (bool) $entity;
  }

}