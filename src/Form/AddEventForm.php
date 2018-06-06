<?php
/**
 * @file
 * Contains \Drupal\unccd_event_map\Form\AddEventForm.
 */
namespace Drupal\unccd_event_map\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;

use Drupal\unccd_event_map\Utils\Geocoder;
use Drupal\unccd_event_map\EventStorage;

class AddEventForm extends FormBase {
    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'add_event_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state, $id = null) {        
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
            '#title' => t('Event website:'),
        ];
        $form['email'] = [
            '#type' => 'textfield',
            '#title' => t('Contact Email:'),
            '#required' => TRUE,
        ];
        $form['date'] = [
            '#type' => 'date',
            '#title' => t('Date:'),
            '#required' => TRUE,
        ];
        $form['city'] = [
            '#type' => 'textfield',
            '#title' => t('City:'),
            '#required' => FALSE,
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
        $form['approved'] = [
            '#type' => 'checkbox',
            '#title' => t('Approved'),
            '#required' => FALSE,
        ];
        $form['actions']['#type'] = 'actions';
        $form['actions']['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Save'),
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
            'approved' => $form_state->getValue('approved'),
        ];
        if(!empty($form_state->getValue('image'))) $fields['image'] = $file->url();
        EventStorage::insert($fields);
        
        // Redirect to event list
        drupal_set_message($this->t('Event sucessfully created.'));
        $form_state->setRedirect('event_map.event_admin.list');
        return;
    }
}
