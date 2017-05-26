<?php

namespace Drupal\my_base\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements an site config form.
 */
class SiteConfigForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'site_config_forms';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['my_base.site_config'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $fid_top_banner_common = \Drupal::state()->get('top_banner_common') ? [\Drupal::state()->get('top_banner_common')] : 0;
    $form['top_banner_common'] = array(
      '#type'          => 'managed_file',
      '#title'         => $this->t('Choose Banner File'),
      '#upload_location' => 'public://top_banner_common/',
      '#default_value' => $fid_top_banner_common,
      '#description'   => $this->t('Specify an image(s) to display.'),
      '#upload_validators' => array(
        'file_validate_extensions' => array('gif png jpg jpeg'),
        // Pass the maximum file size in bytes
        //'file_validate_size' => array(MAX_FILE_SIZE*1024*1024),
      ),
    );

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#button_type' => 'primary',
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    /*if (strlen($form_state->getValue('phone_number')) < 3) {
      $form_state->setErrorByName('phone_number', $this->t('The phone number is too short. Please enter a full phone number.'));
    }*/
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $file_id = is_array($form_state->getValue('top_banner_common')) ? $form_state->getValue('top_banner_common')[0] : $form_state->getValue('top_banner_common');
    $file = file_load( $file_id );
    /* Set the status flag permanent of the file object */
    $file->setPermanent();
    /* Save the file in database */
    if($file->save()) {
      \Drupal::state()->set('top_banner_common', $file_id);
    }



    #\Drupal::state()->set('phone_number', $form_state->getValue('phone_number'));
    drupal_set_message($this->t('Cập nhật dữ liệu thành công', array()));
  }

}
