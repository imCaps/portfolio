<?php namespace Core\Model;

/**
 * Point events model
 * @author Alexandr Shumilow
 */
class PointEvent {

    public function __construct() {}

    /**
     * Checking athlete existing in database.
     * @param $chipId - to athlete attached chip
     * @return bool
     */
    public static function checkAthlete($chipId) {
        return (boolean) \Core\Registry::get('db')
            ->queryRow("SELECT * FROM athletes WHERE chip_id = :chipId LIMIT 1", [':chipId' => (int)$chipId]);
    }

    /**
     * Checking point existing in database.
     * @param $pointId - timing point id
     * @return bool
     */
    public static function checkPoint($pointId) {
        return (boolean) \Core\Registry::get('db')
            ->queryRow("SELECT * FROM points WHERE id = :pointId LIMIT 1", [':pointId' => $pointId]);
    }

    /**
     * Registering timing point reaching in database.
     * @param $chipId - to athlete attached chip
     * @param $pointId - timing point id
     * @return bool
     */
    public static function registerEvent($chipId, $pointId) {
        return (boolean) \Core\Registry::get('db')
            ->exec("INSERT INTO events(chip_id, point_id, event_time) VALUES(:chipId, :pointId, NOW())",
                [':chipId' => $chipId, ':pointId' => $pointId]);
    }

    /**
     * Getting events from database.
     * @return mixed
     */
    public static function getEvents() {
        return \Core\Registry::get('db')
            ->queryAll("SELECT events.id, events.point_id, events.event_time, athletes.start_number, athletes.full_name
                        FROM events INNER JOIN athletes ON athletes.chip_id = events.chip_id
                        WHERE events.event_time = (
                        SELECT MAX(ev2.event_time) FROM events ev2 WHERE events.chip_id = ev2.chip_id)
                        ORDER BY events.event_time DESC;");
    }
}