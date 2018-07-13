<?php

namespace Drupal\unccd_event_map\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Form\FormStateInterface;

use Drupal\unccd_event_map\EventStorage;
use Drupal\unccd_event_map\Utils\ContinentSpliter;

/**
 * Provides an event around the world Block.
 *
 * @Block(
 *   id = "events_around_the_world_block",
 *   admin_label = @Translation("Events Around the World block"),
 *   category = @Translation("UNCCD"),
 * )
 */
class EventsAroundTheWorldBlock extends BlockBase implements BlockPluginInterface {

    /**
     * {@inheritdoc}
     */
    public function build() {
        // Retrieve year to display from configuration
        $config = $this->getConfiguration();
        $year = isset($config['year']) ? $config['year'] : '';
        
        // Get approved events
        $events = EventStorage::loadApprovedInYear($year);

        // Split the events by continent
        $spliter = new ContinentSpliter;
        $events = $spliter->splitByContinent($events);

        // Do not cache the block
        // Drupal Bug
        // @see https://www.drupal.org/node/2352009
        \Drupal::service('page_cache_kill_switch')->trigger();

        return [
            '#theme' => 'events_around_the_world',
            '#events' => $events,
            '#cache' => ['max-age' => 0],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function blockForm($form, FormStateInterface $form_state) {
        $form = parent::blockForm($form, $form_state);

        // Retrieve existing configuration for this block.
        $config = $this->getConfiguration();

        // Add a form field to the existing block configuration form to configure the year displayed
        $form['year'] = [
            '#type' => 'textfield',
            '#title' => t('Year'),
            '#default_value' => isset($config['year']) ? $config['year'] : '',
        ];
        
        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function blockSubmit($form, FormStateInterface $form_state) {
        // Save our custom settings when the form is submitted.
        $this->setConfigurationValue('year', $form_state->getValue('year'));
    }
}
