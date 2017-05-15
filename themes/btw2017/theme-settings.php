<?php

function btw2017_form_system_theme_settings_alter(
  &$form,
  \Drupal\Core\Form\FormStateInterface &$form_state,
  $form_id = NULL
) {
  // Work-around for a core bug affecting admin themes. See issue #943212.
  if (isset($form_id)) {
    return;
  }

  $form['footer_contact'] = array(
    '#type' => 'textfield',
    '#title' => t('Contact Link'),
    '#default_value' => theme_get_setting('footer_contact'),
    '#description' => t("Contact Link"),
  );

  $form['footer_facebook_link'] = array(
    '#type' => 'textfield',
    '#title' => t('Facebook'),
    '#default_value' => theme_get_setting('footer_facebook_link'),
    '#description' => t("Facebook fanpage link"),
  );
  $form['footer_twitter_link'] = array(
    '#type' => 'textfield',
    '#title' => t('Twitter'),
    '#default_value' => theme_get_setting('footer_twitter_link'),
    '#description' => t("Twitter link"),
  );
  $form['footer_instagram_link'] = array(
    '#type' => 'textfield',
    '#title' => t('Instagram'),
    '#default_value' => theme_get_setting('footer_instagram_link'),
    '#description' => t("Instagram"),
  );
  $form['footer_copyright'] = array(
    '#type' => 'textfield',
    '#title' => t('Copyright'),
    '#default_value' => theme_get_setting('footer_copyright'),
    '#description' => t("Copyright"),
  );

  $form['page_destination_list_title'] = array(
    '#type' => 'textfield',
    '#title' => t('Destination List Title'),
    '#default_value' => theme_get_setting('page_destination_list_title'),
    '#description' => t("Destination List Title"),
  );

  /*$form['page_article_banner'] = array(
      '#title' => t('Article Banner'),
      '#type' => 'managed_file',
      '#description' => t('The uploaded image will be displayed on this page using the image style chosen below.'),
      '#default_value' => theme_get_setting('page_article_banner'),
      '#upload_location' => 'public://page/',
      '#required' => FALSE,
//      '#theme'	=>	'advimagearray_thumb_upload',
  );

  $form['page_article_title'] = array(
      '#type' => 'textfield',
      '#title' => t('Article Title'),
      '#default_value' => theme_get_setting('page_article_title'),
      '#description' => t("Article Title"),
  );*/
}