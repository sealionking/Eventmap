<?php
/**
 * @file
 * Contains \Drupal\unccd_event_map\Form\AddEventForm.
 */
namespace Drupal\unccd_event_map\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;

use Drupal\unccd_event_map\Utils\ContinentSpliter;
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
        $spliter = new ContinentSpliter;
        $countryOptions = $spliter->generateCountryOptionList();

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
        $form['organisation_url'] = [
            '#type' => 'textfield',
            '#title' => t('Organization website(s) (optional):'),
        ];
        $form['url'] = [
            '#type' => 'textfield',
            '#title' => t('Event website:'),
        ];
        $form['email'] = [
            '#type' => 'textfield',
            '#title' => t('Contact Email:'),
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
            '#type' => 'select',
            '#title' => t('Country:'),
            '#required' => TRUE,
            '#options' => $countryOptions,
        ];
        $form['description'] = [
            '#type' => 'text_format',
            '#title' => t('Description:'),
            '#required' => TRUE,
            '#default_value' => '<p></p>',
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
        $form['attachment_type'] = [
            '#type' => 'select',
            '#title' => t('Attachment type:'),
            '#options' => [
                'Event Flyer' => 'Event Flyer',
                'Report' => 'Report',
            ],
        ];
        $form['pdf'] = [
            '#type' => 'managed_file',
            '#title' => t('Event flyer PDF (optional):'),
            '#upload_location' => 'public://event-map/pdf/',
            '#multiple' => FALSE,
            '#upload_validators' => [
                'file_validate_extensions' => ['pdf'],
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
        if(!empty($form_state->getValue('image'))) {
            $image = $form_state->getValue('image');
            $file = File::load($image[0]);
            $file->setPermanent();
            $file->save();
            $image_url = $file->url();
        }

        // Handle the pdf upload
        if(!empty($form_state->getValue('pdf'))) {
            $pdf = $form_state->getValue('pdf');
            $file = File::load($pdf[0]);
            $file->setPermanent();
            $file->save();
            $pdf_url = $file->url();
        }
    
        // Save the event to the database
        $fields = [
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
            'approved' => $form_state->getValue('approved'),
            'attachment_type' => $form_state->getValue('attachment_type'),
        ];
        if(!empty($form_state->getValue('image'))) {
            $fields['image_id'] = $form_state->getValue('image')[0];
            $fields['image'] = $image_url;
        }
        if(!empty($form_state->getValue('pdf'))) {
            $fields['pdf_id'] = $form_state->getValue('pdf')[0];
            $fields['pdf'] = $pdf_url;
        }
        EventStorage::insert($fields);
        
        // Redirect to event list
        drupal_set_message($this->t('Event sucessfully created.'));
        $form_state->setRedirect('event_map.event_admin.list');
        return;
    }
}
