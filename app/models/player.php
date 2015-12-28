<?php
class Player extends BaseModel {
  public $id, $name, $courseId, $username, $password, $admin;

  public function __construct($attributes) {
    parent::__construct($attributes);
  }

  public static function all() {
    $query = DB::connection()->prepare('SELECT * FROM Player');
    $query->execute();
    $rows = $query->fetchAll();

    $players = array();

    foreach ($rows as $row) {
      $players[] = new Player(array(
        'id' => $row['id'],
        'name' => $row['name'],
        'courseId' => $row['courseid'],
        'username' => $row['username'],
        'password' => $row['password'],
        'admin' => $row['admin']
      ));
    }

    return $players;
  }

  public static function find($id) {
    $query = DB::connection()->prepare('SELECT * FROM Player WHERE id = :id LIMIT 1');
    $query->execute(array('id' => $id));
    $row = $query->fetch();

    if($row) {
      $player = new Player(array(
        'id' => $row['id'],
        'name' => $row['name'],
        'courseId' => $row['courseid'],
        'username' => $row['username'],
        'password' => $row['password'],
        'admin' => $row['admin']
      ));
      return $player;
    }

    return null;
  }

  public static function find_by_course($courseId) {
    $query = DB::connection()->prepare('SELECT p.* FROM Round r INNER JOIN Score s ON r.id = s.roundid LEFT JOIN Player p ON s.playerid = p.id WHERE r.courseid = :courseId');
    $query->execute(array('courseId' => $courseId));
    $row = $query->fetchAll();

    $players = array();

    foreach ($rows as $row) {
      $players[] = new Player(array(
        'id' => $row['id'],
        'name' => $row['name'],
        'courseId' => $row['courseid'],
        'username' => $row['username'],
        'password' => $row['password'],
        'admin' => $row['admin']
      ));
    }

    return $players;
  }

  public static function find_by_round($roundId) {
    $query = DB::connection()->prepare('SELECT p.* FROM Round r INNER JOIN Score s ON r.id = s.roundid LEFT JOIN Player p ON s.playerid = p.id WHERE r.id = :roundId');
    $query->execute(array('roundId' => $roundId));
    $row = $query->fetchAll();

    $players = array();

    foreach ($rows as $row) {
      $players[] = new Player(array(
        'id' => $row['id'],
        'name' => $row['name'],
        'courseId' => $row['courseid'],
        'username' => $row['username'],
        'password' => $row['password'],
        'admin' => $row['admin']
      ));
    }

    return $players;
  }

  public static function find_by_moderation($courseId) {
    $query = DB::connection()->prepare('SELECT p.* FROM Course_moderators c INNER JOIN Player p ON c.playerid = p.id WHERE c.courseid = :courseId');
    $query->execute(array('courseId' => $courseId));
    $rows = $query->fetchAll();

    $players = array();

    foreach ($rows as $row) {
      $players[] = new Player(array(
        'id' => $row['id'],
        'name' => $row['name'],
        'courseId' => $row['courseid'],
        'username' => $row['username'],
        'password' => $row['password'],
        'admin' => $row['admin']
      ));
    }
    return $players;
  }
  
}
