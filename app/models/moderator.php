<?php
class Moderator {

  /**
  * Fetches moderators for selected course.
  *
  * @param int $courseId Id of selected course.
  *
  * @return array Returns all players moderating selected course.
  */
  public static function find_by_course($courseId) {
    $query = DB::connection()->prepare('SELECT * FROM Course_moderators WHERE courseid = :courseid');
    $query->execute(array('courseid' => $courseId));
    $rows = $query->fetchAll();

    $players = array();
    foreach($rows as $row) {
      $players[] = $row['playerid'];
    }
    return $players;
  }

  /**
  * Adds user as moderator for selected course.
  *
  * @param int $courseId Id of selected course.
  * @param int $playerId Id of player to be added as moderator.
  */
  public static function add_as_moderator($courseId, $playerId) {
    $query = DB::connection()->prepare('INSERT INTO Course_moderators (courseid, playerid) VALUES (:courseid, :playerid)');
    $query->execute(array('courseid' => $courseId, 'playerid' => $playerId));
  }
}
