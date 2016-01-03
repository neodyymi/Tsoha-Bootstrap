<?php
class Player extends BaseModel {
  public $id, $name, $courseId, $course, $username, $password, $moderator, $admin, $joined, $login;

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

    $courses = Course::find_by_moderator($id);

    if($row) {
      $player = new Player(array(
        'id' => $row['id'],
        'name' => $row['name'],
        'courseId' => $row['courseid'],
        'course' => Course::find($row['courseid']),
        'username' => $row['username'],
        'password' => $row['password'],
        'moderator' => $courses,
        'admin' => $row['admin'],
        'joined' => $row['joined'],
        'login' => $row['login']
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

  public static function authenticate($username, $password) {
    $query = DB::connection()->prepare('SELECT * FROM Player WHERE username = :username LIMIT 1');
    $query->execute(array('username' => $username));
    $row = $query->fetch();
##    if($row && password_verify($password, $row['password'])) { ## Usersin php-versio ei tuekaan tätä.. :(
      if($row && $password == $row['password']) {
      $player = new Player(array(
        'id' => $row['id'],
        'name' => $row['name'],
        'courseId' => $row['courseid'],
        'username' => $row['username'],
        'password' => $row['password'],
        'admin' => $row['admin'],
        'joined' => $row['joined']
      ));
      $player->update();
      return $player;
    } else {
      return null;
    }
  }

  public function save(){
    $query = DB::connection()->prepare('INSERT INTO Player (name, username, password) VALUES (:name, :username, :password) RETURNING id');
    $query->execute(array('name' => $this->name, 'username' => $this->username, 'password' => $this->password));
    $row = $query->fetch();
    $this->id = $row['id'];
  }

  public function update() {
    $query = DB::connection()->prepare('UPDATE Player SET name = :name, courseid = :courseid, username = :username, login = now() WHERE id = :id RETURNING login');
    $query->execute(array('name' => $this->name, 'courseid' => $this->courseId, 'username' => $this->username, 'id' => $this->id));
    $row = $query->fetch();
    $this->login = $row['login'];
  }

  public function destroy() {
    $query = DB::connection()->prepare('DELETE FROM Player WHERE id = :id');
    $query->execute(array('id' => $this->id));
  }

}
