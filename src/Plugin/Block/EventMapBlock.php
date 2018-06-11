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

        // Do not cache the block
        // Drupal Bug
        // @see https://www.drupal.org/node/2352009
        \Drupal::service('page_cache_kill_switch')->trigger();

        return [
            '#theme' => 'map_view',
            '#events' => $events,
            '#cache' => ['max-age' => 0],
        ];
    }
}
