<?php

namespace Drupal\unccd_event_map\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\unccd_event_map\EventStorage;

/**
 * Provides an event map Block.
 *
 * @Block(
 *   id = "event_map_block",
 *   admin_label = @Translation("Event Map block"),
 *   category = @Translation("UNCCD"),
 * )
 */
class EventMapBlock extends BlockBase {

    /**
     * {@inheritdoc}
     */
    public function build() {
        $events = EventStorage::loadApproved();
        return [
            '#theme' => 'map_view',
            '#events' => $events,
        ];
    }
}
