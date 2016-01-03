<?php
class Score extends BaseModel {
  public $id, $player, $round, $throws, $par;

  public function __construct($attributes){
    parent::__construct($attributes);
  }

  public static function all() {
    $query = DB::connection()->prepare('SELECT * FROM Score');
    $query->execute();
    $rows = $query->fetchAll();

    $scores = array();

    foreach ($rows as $row) {
      $tmp = getScore($row['id']);
      $throws = $tmp['throws'];
      $par = $tmp['par'];

      $scores[] = new Score(array(
        'id' => $row['id'],
        'player' => $row['playerid'],
        'round' => $row['roundid'],
        'throws' => $throws,
        'par' => $par
      ));
    }
    return $scores;
  }

  public static function find_by_round($roundId) {
    $query = DB::connection()->prepare('SELECT * FROM Score WHERE roundid = :roundId');
    $query->execute(array('roundId' => $roundId));
    $rows = $query->fetchAll();

    $scores = array();

    foreach ($rows as $row) {
      $tmp = Score::getScore($row['id']);
      $throws = $tmp['throws'];
      $par = $tmp['par'];

      $scores[] = new Score(array(
        'id' => $row['id'],
        'player' => $row['playerid'],
        'round' => $row['roundid'],
        'throws' => $throws,
        'par' => $par
      ));
    }
    return $scores;
  }

  public static function find_by_player($playerId) {
    $query = DB::connection()->prepare('SELECT * FROM Score WHERE playerid = :playerId');
    $query->execute(array('playerId' => $playerId));
    $rows = $query->fetchAll();

    $scores = array();

    foreach ($rows as $row) {
      $tmp = Score::getScore($row['id']);
      $throws = $tmp['throws'];
      $par = $tmp['par'];

      $scores[] = new Score(array(
        'id' => $row['id'],
        'player' => Player::find($row['playerid']),
        'round' => Round::find($row['roundid']),
        'throws' => $throws,
        'par' => $par
      ));
    }
    return $scores;
  }

  public static function find($id) {
    $query = DB::connection()->prepare('SELECT * FROM Score WHERE id = :id LIMIT 1');
    $query->execute(array('id' => $id));
    $row = $query->fetch();

    if($row) {
      $tmp = Score::getScore($row['id']);
      $throws = $tmp['throws'];
      $par = $tmp['par'];

      $score = new Score(array(
        'id' => $row['id'],
        'player' => $row['playerid'],
        'round' => $row['roundid'],
        'throws' => $throws,
        'par' => $par
      ));

      return $score;
    }

    return null;

  }

  public static function getScore($id) {
    $query = DB::connection()->prepare('SELECT * FROM Score_Hole WHERE scoreid = :id');
    $query->execute(array('id' => $id));
    $rows = $query->fetchAll();

    $throws = 0;
    $par = 0;
    foreach ($rows as $row) {
      $throws += $row['throws'];
      $query_hole = DB::connection()->prepare('SELECT par FROM Hole WHERE id = :id AND inuse = TRUE LIMIT 1');
      $query_hole->execute(array('id' => $row['holeid']));
      $hole = $query_hole->fetch();
      if ($row) {
        $par += $hole['par'];
      } else {
        $par += 3; # Default par 3
      }
    }
    $return = array('par' => $par, 'throws' => $throws);
    return $return;
  }
}
