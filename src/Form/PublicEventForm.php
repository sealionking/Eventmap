<?php
/**
 * @file
 * Contains \Drupal\unccd_event_map\Form\PublicEventForm.
 */
namespace Drupal\unccd_event_map\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;

use Drupal\unccd_event_map\EventStorage;
use Drupal\unccd_event_map\Utils\Geocoder;

class PublicEventForm extends FormBase {
    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'event_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        $form['title'] = [
            '#type' => 'textfield',
            '#title' => t('Title:'),
            '#required' => TRUE,
        ];
        $form['organisation'] = [
            '#type' => 'textfield',
            '#title' => t('Organisation:'),
            '#required' => TRUE,
        ];
        $form['url'] = [
            '#type' => 'textfield',
            '#title' => t('Event website (optional):'),
        ];
        $form['email'] = [
            '#type' => 'textfield',
            '#title' => t('Contact Email:'),
            '#required' => TRUE,
            '#field_prefix' => t('<br>This email will not be published, and will only be used if we require more information from you about the event.'),
        ];
        $form['date'] = [
            '#type' => 'date',
            '#title' => t('Date:'),
            '#required' => TRUE,
            '#field_prefix' => '<br>',
        ];
        $form['city'] = [
            '#type' => 'textfield',
            '#title' => t('City:'),
        ];
        $form['country'] = [
            '#type' => 'textfield',
            '#title' => t('Country:'),
            '#required' => TRUE,
        ];
        $form['description'] = [
            '#type' => 'textarea',
            '#title' => t('Description:'),
            '#required' => TRUE,
        ];
        $form['image'] = [
            '#type' => 'managed_file',
            '#title' => t('Image (optional):'),
            '#upload_location' => 'public://event-map/images/',
            '#multiple' => FALSE,
            '#upload_validators' => [
                'file_validate_is_image' => [],
                'file_validate_extensions' => ['gif png jpg jpeg'],
                'file_validate_size' => [25600000]
            ],
        ];
        $form['actions']['#type'] = 'actions';
        $form['actions']['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Submit'),
            '#button_type' => 'primary',
        ];
        
        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {
        if (strlen($form_state->getValue('title')) < 3) {
            $form_state->setErrorByName('title', $this->t('Title is too short.'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        // Get the latitude and longitude of the event location
        $geocoder = new Geocoder();
        $coords = $geocoder->geocode($form_state->getValue('city'), $form_state->getValue('country'));

        // Handle the image upload
        $image = $form_state->getValue('image');
        $file = File::load($image[0]);
        $file->setPermanent();
        $file->save();
   
        // Save the event to the database
        $fields = [
            'title' => $form_state->getValue('title'),
            'organisation' => $form_state->getValue('organisation'),
            'url' => $form_state->getValue('url'),
            'email' => $form_state->getValue('email'),
            'city' => $form_state->getValue('city'),
            'country' => $form_state->getValue('country'),
            'date' => $form_state->getValue('date'),
            'description' => $form_state->getValue('description'),
            'latitude' => $coords['lat'],
            'longitude' => $coords['long'],
            'approved' => 0,
        ];
        if(!empty($form_state->getValue('image'))) $fields['image'] = $file->url();
        EventStorage::insert($fields);

        // Success message
        drupal_set_message($this->t('Your event has been submitted! It will appear on the map after it has been reviewed.'));
    }
}
