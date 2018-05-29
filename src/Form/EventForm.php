<?php
/**
 * @file
 * Contains \Drupal\unccd_event_map\Form\EventForm.
 */
namespace Drupal\unccd_event_map\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

use Drupal\unccd_event_map\EventStorage;
use Drupal\unccd_event_map\Geocoder;

class EventForm extends FormBase {
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
        ];
        $form['date'] = [
            '#type' => 'date',
            '#title' => t('Date:'),
            '#required' => TRUE,
        ];
        $form['city'] = [
            '#type' => 'textfield',
            '#title' => t('City:'),
            '#required' => TRUE,
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
            '#type' => 'file',
            '#title' => t('Image (optional):'),
            '#required' => FALSE,
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
        // todo: check image size
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $geocoder = new Geocoder();
        $coords = $geocoder->geocode($form_state->getValue('city'), $form_state->getValue('country'));

        EventStorage::insert([
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
        ]);

        drupal_set_message($this->t('Your event has been submitted! It will appear on the map after it has been reviewed.'));
    }
}
