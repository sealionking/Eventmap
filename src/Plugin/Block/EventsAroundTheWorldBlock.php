<?php

namespace Drupal\unccd_event_map\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\unccd_event_map\EventStorage;
use Drupal\unccd_event_map\Utils\ContinentSpliter;

/**
 * Provides an event around the world Block.
 *
 * @Block(
 *   id = "events_around_the_world_block",
 *   admin_label = @Translation("Events around the world block"),
 *   category = @Translation("UNCCD"),
 * )
 */
class EventsAroundTheWorldBlock extends BlockBase {

    /**
     * {@inheritdoc}
     */
    public function build() {
        $events = EventStorage::loadApproved();

        $spliter = new ContinentSpliter;
        $events = $spliter->splitByContinent($events);

        return [
            '#theme' => 'events_around_the_world',
            '#events' => $events,
        ];
    }
}
