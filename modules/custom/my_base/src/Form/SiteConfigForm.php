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
      '#title'         => $this->t('Choose Banner File ( 1920 x 350 ) '),
      '#upload_location' => 'public://top_banner_common/',
      '#default_value' => $fid_top_banner_common,
      '#description'   => $this->t('Specify an image(s) to display.'),
      '#upload_validators' => array(
        'file_validate_extensions' => array('gif png jpg jpeg'),
        // Pass the maximum file size in bytes
        //'file_validate_size' => array(MAX_FILE_SIZE*1024*1024),
      ),
      '#validated' => TRUE
    );

    $form['eco_contact_address'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Contact Address'),
      '#default_value' => \Drupal::state()->get('eco_contact_address'),
      '#size' => 60,
      '#maxlength' => 255,
    );

    $form['eco_contact_phone'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Contact Phone'),
      '#default_value' => \Drupal::state()->get('eco_contact_phone'),
      '#size' => 20,
      '#maxlength' => 20,
    );

    $form['eco_contact_fax'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Contact Fax'),
      '#default_value' => \Drupal::state()->get('eco_contact_fax'),
      '#size' => 20,
      '#maxlength' => 20,
    );

    $form['eco_contact_email'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Contact Email'),
      '#default_value' => \Drupal::state()->get('eco_contact_email'),
      '#size' => 30,
      '#maxlength' => 100,
    );

    $form['eco_working_time'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Working time'),
      '#default_value' => \Drupal::state()->get('eco_working_time'),
      '#size' => 30,
      '#maxlength' => 100,
    );

    $form['eco_slogan_footer'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Slogan footer'),
      '#default_value' => \Drupal::state()->get('eco_slogan_footer'),
      '#size' => 30,
      '#maxlength' => 100,
    );

    $form['eco_fb_social'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Link Facebook social'),
      '#default_value' => \Drupal::state()->get('eco_fb_social'),
      '#size' => 50,
      '#maxlength' => 200,
    );

    $form['eco_google_social'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Link Google social'),
      '#default_value' => \Drupal::state()->get('eco_google_social'),
      '#size' => 50,
      '#maxlength' => 200,
    );

    $form['eco_twitter_social'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Link Twitter social'),
      '#default_value' => \Drupal::state()->get('eco_twitter_social'),
      '#size' => 50,
      '#maxlength' => 200,
    );

    $form['eco_footer_summary'] = array(
      '#type' => 'textarea',
      '#title' => $this->t('Footer Summary'),
      '#default_value' => \Drupal::state()->get('eco_footer_summary'),
      '#maxlength' => 300,
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
    $file_id = is_array($form_state->getValue('top_banner_common')) ? $form_state->getValue('top_banner_common')['fids'][0] : $form_state->getValue('top_banner_common');
    $file = file_load( $file_id );
    if($file ) {
      /* Set the status flag permanent of the file object */
      $file->setPermanent();
      /* Save the file in database */
      if($file->save()) {
        \Drupal::state()->set('top_banner_common', $file_id);
      }
    }
    
    \Drupal::state()->set('eco_contact_address', $form_state->getValue('eco_contact_address'));
    \Drupal::state()->set('eco_contact_phone', $form_state->getValue('eco_contact_phone'));
    \Drupal::state()->set('eco_contact_fax', $form_state->getValue('eco_contact_fax'));
    \Drupal::state()->set('eco_contact_email', $form_state->getValue('eco_contact_email'));
    \Drupal::state()->set('eco_working_time', $form_state->getValue('eco_working_time'));
    \Drupal::state()->set('eco_slogan_footer', $form_state->getValue('eco_slogan_footer'));
    \Drupal::state()->set('eco_footer_summary', $form_state->getValue('eco_footer_summary'));

    #\Drupal::state()->set('phone_number', $form_state->getValue('phone_number'));
    drupal_set_message($this->t('Cập nhật dữ liệu thành công', array()));
  }

}
