<?php
/**
 * @file
 * Contains \Drupal\unccd_event_map\Form\EventForm.
 */
namespace Drupal\unccd_event_map\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

use Drupal\unccd_event_map\EventStorage;
use Drupal\unccd_event_map\Utils\Geocoder;

class EditEventForm extends FormBase {
    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'edit_event_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state, $id = null) {
        $event = EventStorage::loadById(['id' => $id]);
        if ($event == null) {
            drupal_set_message($this->t('Could not find event.'));
            return $this->redirect('event_map.event_admin.list');
        }

        $date = date_create($event->date);
        
        $form['title'] = [
            '#type' => 'textfield',
            '#title' => t('Title:'),
            '#required' => TRUE,
            '#default_value' => $event->title,
        ];
        $form['organisation'] = [
            '#type' => 'textfield',
            '#title' => t('Organisation:'),
            '#required' => TRUE,
            '#default_value' => $event->organisation,
        ];
        $form['url'] = [
            '#type' => 'textfield',
            '#title' => t('Event website:'),
            '#default_value' => $event->url,
        ];
        $form['email'] = [
            '#type' => 'textfield',
            '#title' => t('Contact Email:'),
            '#required' => TRUE,
            '#default_value' => $event->email,
        ];
        $form['date'] = [
            '#type' => 'date',
            '#title' => t('Date:'),
            '#required' => TRUE,
            '#default_value' => date_format($date, "Y-m-d"),
        ];
        $form['city'] = [
            '#type' => 'textfield',
            '#title' => t('City:'),
            '#required' => FALSE,
            '#default_value' => $event->city,
        ];
        $form['country'] = [
            '#type' => 'textfield',
            '#title' => t('Country:'),
            '#required' => TRUE,
            '#default_value' => $event->country,
        ];
        $form['description'] = [
            '#type' => 'textarea',
            '#title' => t('Description:'),
            '#required' => TRUE,
            '#default_value' => $event->description,
        ];
        $form['image'] = [
            '#type' => 'file',
            '#title' => t('Image:'),
            '#default_value' => FALSE,
        ];
        $form['latitude'] = [
            '#type' => 'textfield',
            '#title' => t('Latitude:'),
            '#required' => FALSE,
            '#default_value' => $event->latitude,
        ];
        $form['longitude'] = [
            '#type' => 'textfield',
            '#title' => t('Longitude:'),
            '#required' => FALSE,
            '#default_value' => $event->longitude,
        ];
        $form['approved'] = [
            '#type' => 'checkbox',
            '#title' => t('Approved'),
            '#required' => FALSE,
            '#default_value' => $event->approved,
        ];
        $form['id'] = [
            '#type' => 'hidden',
            '#required' => FALSE,
            '#value' => $event->id,
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
        // todo: check image size
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        if ($form_state->getValue('latitude') == 0 || $form_state->getValue('latitude') == null ||
            $form_state->getValue('longitude') == 0 || $form_state->getValue('longitude') == null) {
            $geocoder = new Geocoder();
            $coords = $geocoder->geocode($form_state->getValue('city'), $form_state->getValue('country'));
        } else {
            $coords['lat'] = $form_state->getValue('latitude');
            $coords['long'] = $form_state->getValue('longitude');
        }

        EventStorage::update([
            'id'  => $form_state->getValue('id'),
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
            'approved' => $form_state->getValue('approved')
        ]);

        drupal_set_message($this->t('Event sucessfully saved.'));
        $form_state->setRedirect('event_map.event_admin.list');
        return;
    }
}
