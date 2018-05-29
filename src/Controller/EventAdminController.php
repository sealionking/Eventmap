<?php

namespace Drupal\unccd_event_map\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\unccd_event_map\EventStorage;

/**
 * The admin panel pages to approve and edit events.
 */
class EventAdminController extends ControllerBase {

    /**
     * Lists the events
     *
     * @return array A render array as expected by the renderer.
     */
    public function eventList() {
        $headers = [
            $this->t('ID'),
            $this->t('Title'),
            $this->t('Organisation'),
            $this->t('City'),
            $this->t('Country'),
            $this->t('Operations'),
        ];

        $rows = [];
        $events = EventStorage::loadAll();

        foreach ($events as $event) {
            $row['id'] = [
                'data' => $event->id,
                'class' => 'table-filter-text-source',
            ];
            $row['title'] = [
                'data' => $event->title,
                'class' => 'table-filter-text-source',
            ];
            $row['organisation'] = [
                'data' => $event->organisation,
                'class' => 'table-filter-text-source',
            ];
            $row['city'] = [
                'data' => $event->city,
                'class' => 'table-filter-text-source',
            ];
            $row['country'] = [
                'data' => $event->country,
                'class' => 'table-filter-text-source',
            ];
            $row['operations']['data'] = [
                '#type' => 'operations',
                '#links' => [
                    'approve' => [
                        'title' => $this->t('Approve'),
                        'url' => Url::fromRoute('event_map.event_admin_approve', ['id' => $event->id]),
                    ],
                    // 'edit' => [
                    //     'title' => $this->t('Edit'),
                    //     'url' => Url::fromRoute('event_map.content'),//, ['id' => $event->id]),
                    // ],
                ],
            ];

            $rows[$event->id] = $row;
        }

        $output['services'] = [
            '#type' => 'table',
            '#header' => $headers,
            '#rows' => $rows,
            '#empty' => $this->t('No events found.'),
            '#sticky' => TRUE,
        ];

        return $output;
    }

    /**
     * Approves an event.
     * Redirects back to the list
     */
    public function approveEvent($id) {
        // Approve the event
        EventStorage::update([ "id" => $id, "approved" => 1 ]);

        // Return the user to the list of events with a confirmation message
        drupal_set_message(t('Event approved'), 'status', TRUE);
        return $this->redirect('event_map.event_admin_list');
    }
}
