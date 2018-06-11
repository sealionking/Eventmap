<?php
/**
 * @file
 * Contains \Drupal\unccd_event_map\Form\EditEventForm.
 */
namespace Drupal\unccd_event_map\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;

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
        $form['organisation_url'] = [
            '#type' => 'textfield',
            '#title' => t('Organization website(s) (optional):'),
        ];
        $form['url'] = [
            '#type' => 'textfield',
            '#title' => t('Event website:'),
            '#default_value' => $event->url,
        ];
        $form['email'] = [
            '#type' => 'textfield',
            '#title' => t('Contact Email:'),
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
            '#type' => 'text_format',
            '#title' => t('Description:'),
            '#required' => TRUE,
            '#default_value' => $event->description,
        ];
        $form['image'] = [
            '#type' => 'managed_file',
            '#title' => t('Image (optional):'),
            '#default_value' => [$event->image_id],
            '#upload_location' => 'public://event-map/images/',
            '#multiple' => FALSE,
            '#upload_validators' => [
                'file_validate_is_image' => [],
                'file_validate_extensions' => ['gif png jpg jpeg'],
                'file_validate_size' => [25600000]
            ],
        ];
        $form['pdf'] = [
            '#type' => 'managed_file',
            '#title' => t('Event flyer PDF (optional):'),
            '#default_value' => [$event->pdf_id],
            '#upload_location' => 'public://event-map/pdf/',
            '#multiple' => FALSE,
            '#upload_validators' => [
                'file_validate_extensions' => ['pdf'],
                'file_validate_size' => [25600000]
            ],
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
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        // Geocoding
        if ($form_state->getValue('latitude') == 0 || $form_state->getValue('latitude') == null ||
            $form_state->getValue('longitude') == 0 || $form_state->getValue('longitude') == null) {
            $geocoder = new Geocoder();
            $coords = $geocoder->geocode($form_state->getValue('city'), $form_state->getValue('country'));
        } else {
            $coords['lat'] = $form_state->getValue('latitude');
            $coords['long'] = $form_state->getValue('longitude');
        }

        // Handle the image upload
        if(!empty($form_state->getValue('image'))) {
            $image = $form_state->getValue('image');
            $file = File::load($image[0]);
            $file->setPermanent();
            $file->save();
            $image_url = $file->url();
        }

        // Handle the PDF upload
        if(!empty($form_state->getValue('pdf'))) {
            $pdf = $form_state->getValue('pdf');
            $file = File::load($pdf[0]);
            $file->setPermanent();
            $file->save();
            $pdf_url = $file->url();
        }
        
        // Update db entry
        $fields = [
            'id'  => $form_state->getValue('id'),
            'title' => $form_state->getValue('title'),
            'organisation' => $form_state->getValue('organisation'),
            'organisation_url' => $form_state->getValue('organisation_url'),
            'url' => $form_state->getValue('url'),
            'email' => $form_state->getValue('email'),
            'city' => $form_state->getValue('city'),
            'country' => $form_state->getValue('country'),
            'date' => $form_state->getValue('date'),
            'description' => $form_state->getValue('description')['value'],
            'latitude' => $coords['lat'],
            'longitude' => $coords['long'],
            'approved' => $form_state->getValue('approved')
        ];
        if(!empty($form_state->getValue('image'))) {
            $fields['image_id'] = $form_state->getValue('image')[0];
            $fields['image'] = $image_url;
        } else {
            $fields['image_id'] = null;
            $fields['image'] = null;
        }
        if(!empty($form_state->getValue('pdf'))) {
            $fields['pdf_id'] = $form_state->getValue('pdf')[0];
            $fields['pdf'] = $pdf_url;
        } else {
            $fields['pdf_id'] = null;
            $fields['pdf'] = null;
        }
        EventStorage::update($fields);

        // Redirect to event list
        drupal_set_message($this->t('Event sucessfully saved.'));
        $form_state->setRedirect('event_map.event_admin.list');
        return;
    }
}
