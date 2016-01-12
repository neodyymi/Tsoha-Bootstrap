<?php
/**
* Model class for players
*/
class Player extends BaseModel {
  public $id, $name, $courseId, $course, $username, $password, $moderator, $admin, $joined, $login, $verify, $newPassword;

  /**
  * Constructor for player.
  *
  * @param array $attributes Contains all the parameters to be stored for the object.
  *
  * @return Player Returns the newly created object.
  */
  public function __construct($attributes) {
    parent::__construct($attributes);
    $this->validators = array("validate_name", "validate_nickname", "validate_new_password");
  }

  /**
  * Validations for name of player
  */
  public function validate_name() {
    return $this->validate_string_length("Nimi", $this->name, 2, 50);
  }

  /**
  * Validations for nickname of player
  */
  public function validate_nickname() {
    return $this->validate_string_length("Käyttäjänimi", $this->name, 2, 15);
  }

  /**
  * Validations for password of player
  */
  public function validate_new_password() {
    return $this->validate_password($this->password, $this->verify, $this->newPassword);
  }

  /**
  * Fetches all players from database.
  *
  * @return array Returns listing of all players in database
  */
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

  /**
  * Fetches one player from databse.
  *
  * @param int $id Id of player to be fetched
  *
  * @return Player Returns player if found. Otherwise returns null.
  */
  public static function find($id) {
    $query = DB::connection()->prepare('SELECT * FROM Player WHERE id = :id LIMIT 1');
    $query->execute(array('id' => $id));
    $row = $query->fetch();

    if($row) {
      $player = new Player(array(
        'id' => $row['id'],
        'name' => $row['name'],
        'courseId' => $row['courseid'],
        'course' => $row['courseid'],
        'username' => $row['username'],
        'password' => $row['password'],
        'admin' => $row['admin'],
        'joined' => $row['joined'],
        'login' => $row['login']
      ));
      return $player;
    }

    return null;
  }

  /**
  * Fetches all players that played on a round.
  *
  * @param int $roundId Id of selected round.
  *
  * @return array Returns listing of all players that played on the selected round.
  */
  public static function find_by_round($roundId) {
    $query = DB::connection()->prepare('SELECT p.* FROM Round r INNER JOIN Score s ON r.id = s.roundid LEFT JOIN Player p ON s.playerid = p.id WHERE r.id = :roundId');
    $query->execute(array('roundId' => $roundId));
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

  /**
  * Fetches all players that moderate selected course.
  *
  * @param int $courseId Id of selected course.
  *
  * @return array Return listing of all players that moderate selected course.
  */
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

  /**
  * Checks players password.
  *
  * @param String $username Username of user attempting to authenticate.
  * @param String $password Password of user attempting to authenticate.
  *
  * @return Player Returns player if authentication was successful. Otherwise returns null.
  */
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

  /**
  * Saves player to database.
  */
  public function save(){
    $query = DB::connection()->prepare('INSERT INTO Player (name, username, password) VALUES (:name, :username, :password) RETURNING id');
    $query->execute(array('name' => $this->name, 'username' => $this->username, 'password' => $this->password));
    $row = $query->fetch();
    $this->id = $row['id'];
  }

  /**
  * Updates player's information into database.
  */
  public function update() {
    if($this->newPassword) {
      $query = DB::connection()->prepare('UPDATE Player SET name = :name, courseid = :courseid, username = :username, password = :password login = now() WHERE id = :id RETURNING login');
      $query->execute(array('name' => $this->name, 'courseid' => $this->courseId, 'username' => $this->username, 'password' => $this->password, 'id' => $this->id));
    } else {
      $query = DB::connection()->prepare('UPDATE Player SET name = :name, courseid = :courseid, username = :username, login = now() WHERE id = :id RETURNING login');
      $query->execute(array('name' => $this->name, 'courseid' => $this->courseId, 'username' => $this->username, 'id' => $this->id));
    }
    $row = $query->fetch();
    $this->login = $row['login'];
  }

  /**
  * Deletes user from database.
  */
  public function destroy() {
    $query = DB::connection()->prepare('DELETE FROM Player WHERE id = :id');
    $query->execute(array('id' => $this->id));
  }

}
