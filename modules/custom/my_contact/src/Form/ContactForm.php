<?php

namespace Drupal\my_contact\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\oo_common\Helper\CommonHelper;

/**
 * Implements an site config form.
 */
class ContactForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'my_contact_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['my_contact.form'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['#attributes'] = array('method' => array('POST'), 'class' => 'contact-form');
    // The status messages that will contain any form errors.
    /*$form['status_messages'] = [
      '#type' => 'status_messages',
      '#weight' => -10,
    ];*/

    $form['fullname'] = array(
      '#type' => 'textfield',
      //'#title' => $this->t('Full name'),
      '#default_value' => '',
      '#maxlength' => 20,
      '#attributes' => array( 
        'class' => array('form-control'),
        'placeholder' => $this->t('Full name')
      )
    );

    $form['phone'] = array(
      '#type' => 'textfield',
      //'#title' => $this->t('Address'),
      '#default_value' => '',
      '#maxlength' => 255,
      '#attributes' => array( 
        'class' => array('form-control'),
        'placeholder' => $this->t('Phone')
      )
    );

    $form['email'] = array(
      '#type' => 'textfield',
      //'#title' => $this->t('Email'),
      '#default_value' => '',
      '#maxlength' => 100,
      '#attributes' => array( 
        'class' => array('form-control'),
        'placeholder' => $this->t('Email Address')
      )
    );

    $form['message'] = array(
      '#type' => 'textarea',
     // '#title' => $this->t('Message'),
      '#default_value' => '',
      '#maxlength' => 200,
      '#attributes' => array( 
        'class' => array('form-control'),
        'placeholder' => $this->t('Message')
      )
    );

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Send'),
      '#attributes' => array( 
        'class' => array('tg-btn')
      )
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    if (!$form_state->getValue('fullname')) {
      $form_state->setErrorByName('fullname', $this->t('Please enter your full name'));
    }
    if (!$form_state->getValue('email')) {
      $form_state->setErrorByName('email', $this->t('Please enter your email'));
    }
    else {
      if(!CommonHelper::func_is_valid_email($form_state->getValue('email'))) {
        $form_state->setErrorByName('email', $this->t('Your email is invalid'));
      }
    }
    if (!$form_state->getValue('phone')) {
      $form_state->setErrorByName('phone', $this->t('Please enter your phone'));
    }
    else {
      if(!CommonHelper::func_is_valid_phonenumber($form_state->getValue('phone'))) {
        $form_state->setErrorByName('phone', $this->t('Your phone number is invalid'));
      }
      if (strlen($form_state->getValue('phone')) < 9) {
        $form_state->setErrorByName('phone', $this->t('The phone number is too short. Please enter a full phone number'));
      }
    }
    if (!$form_state->getValue('message')) {
      $form_state->setErrorByName('message', $this->t('Please enter your message'));
    }
    drupal_get_messages();
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    
    drupal_set_message($this->t('Update data successfully', array()));
  }

}
