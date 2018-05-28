<?php
/**
 * @file
 * Contains \Drupal\unccd_event_map\Controller\MapController.
 */
namespace Drupal\unccd_event_map\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\unccd_event_map\EventStorage;

class MapController extends ControllerBase {
    /**
    * {@inheritdoc}
    */
    protected function getModuleName() {
        return 'unccd_event_map';
    }

    public function content() {
        $events = EventStorage::loadApproved();
        return [
            '#theme' => 'map_view',
            '#events' => $events,
        ];
    }

    public function form() {
        $form = \Drupal::formBuilder()->getForm('Drupal\unccd_event_map\Form\EventForm');
        return [
            '#theme' => 'form_view',
            '#form' => $form
        ];
    }
}
