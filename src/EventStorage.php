<?php

namespace Drupal\unccd_event_map;

/**
 * Class EventStorage.
 */
class EventStorage {

    /**
     * Insert a new event into the database.
     *
     * @param array $entry An array containing all the fields of the database record.
     * @return int The number of updated rows.
     */
    public static function insert(array $entry) {
        $return_value = NULL;
        $return_value = db_insert('unccd_event_map')
            ->fields($entry)
            ->execute();
        return $return_value;
    }

    /**
     * Update an event already in the databasse.
     *
     * @param array $entry An array containing all the fields of the item to be updated.
     * @return int The number of updated rows.
     */
    public static function update(array $entry) {
        $count = db_update('unccd_event_map')
            ->fields($entry)
            ->condition('id', $entry['id'])
            ->execute();
        return $count;
    }

    /**
     * Delete an event from the database.
     *
     * @param int $id The id of the event to delete
     * @see db_delete()
     */
    public static function delete($id) {
        db_delete('unccd_event_map')
            ->condition('id', $id)
            ->execute();
    }

    /**
     * Retrieve all the events in the database.
     *
     * @return object An object containing the loaded entries if found.
     */
    public static function loadAll() {
        $select = db_select('unccd_event_map', 'event_map');
        $select->fields('event_map');
        return $select->execute()->fetchAll();
    }
    
    /**
     * Retrieve all of the events matching provided conditions.
     *
     * @param array $entry An array containing all the fields used to search the entries in the table.
     * @return object An object containing the loaded entries if found.
     */
    public static function loadByCriteria(array $entry = []) {
        $select = db_select('unccd_event_map', 'event_map');
        $select->fields('event_map');

        // Add each field and value as a condition to this query.
        foreach ($entry as $field => $value) {
            $select->condition($field, $value);
        }

        // Return the result in object format.
        return $select->execute()->fetchAll();
    }

    /**
     * Returns only the approved events.
     *
     * Equivalent SQL query:
     * SELECT
     *  e.id, e.title, e.city, e.country, e.latitude, e.longitude, e.approved
     * FROM
     *  {unccd_event_map} e
     * WHERE
     *  e.approved = 1
     */
    public static function loadApproved() {
        $select = db_select('unccd_event_map', 'e');
        // Select these specific fields for the output.
        $select->addField('e', 'id');
        $select->addField('e', 'title');
        $select->addField('e', 'city');
        $select->addField('e', 'country');
        $select->addField('e', 'latitude');
        $select->addField('e', 'longitude');
        $select->condition('e.approved', 1);
        // $select->range(0, 50);

        $entries = $select->execute()->fetchAll(\PDO::FETCH_ASSOC);

        return $entries;
    }
}
