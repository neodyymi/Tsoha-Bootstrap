<?php
class Moderator {

  public static function find_by_course($courseId) {
    $query = DB::connection()->prepare('SELECT * FROM Course_moderators WHERE courseid = :courseid');
    $query->execute(array('courseid' => $courseId));
    $rows = $query->fetchAll();

    $players = array();
    foreach($rows as $row) {
      $players[] = Player::find($row['playerid']);
    }
    return $players;
  }

  public static function add_as_moderator($courseId, $playerId) {
    $query = DB::connection()->prepare('INSERT INTO Course_moderators (courseid, playerid) VALUES (:courseid, :playerid)');
    $query->execute(array('courseid' => $courseId, 'playerid' => $playerId));
  }
}
