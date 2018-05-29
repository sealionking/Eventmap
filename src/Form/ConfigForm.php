<?php
/**
 * @file
 * Contains \Drupal\unccd_event_map\Form\ConfigForm.
 */
namespace Drupal\unccd_event_map\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;


class ConfigForm extends ConfigFormBase {
    /**
    * {@inheritdoc}
    */
    public function getFormId() {
        return 'event_config_form';
    }

    /** 
    * {@inheritdoc}
    */
    protected function getEditableConfigNames() {
        return [
            'unccd_event_map.config',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        $config = $this->config('unccd_event_map.config');

        $form['mapquestapikey'] = [
            '#type' => 'textfield',
            '#title' => t('MapQuest API Key:'),
            '#required' => TRUE,
            '#default_value' => $config->get('mapquestapikey'),
        ];

        return parent::buildForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {
        if (strlen($form_state->getValue('mapquestapikey')) < 5) {
            $form_state->setErrorByName('mapquestapikey', $this->t('Invalid Map Quest API Key'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {       
        // Retrieve the configuration
        $config = $this->configFactory->getEditable('unccd_event_map.config');
        $config->set('mapquestapikey', $form_state->getValue("mapquestapikey"));
        $config->save();

        drupal_set_message($this->t('Settings successful saved.'));
        
        parent::submitForm($form, $form_state);
    }
}
