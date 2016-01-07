<?php
class Score extends BaseModel {
  public $id, $playerId, $playerName, $round, $throws, $par, $holes;

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
    $query = DB::connection()->prepare('SELECT s.id, s.roundid, s.playerid, p.name as name FROM Score s LEFT JOIN Player p ON s.playerid=p.id WHERE roundid = :roundId');
    $query->execute(array('roundId' => $roundId));
    $rows = $query->fetchAll();

    $scores = array();

    foreach ($rows as $row) {
      $tmp = Score::getScore($row['id']);
      $throws = $tmp['throws'];
      $par = $tmp['par'];
      $holes = self::find_by_round_and_player($roundId, $row['playerid']);
      $scores[] = new Score(array(
        'id' => $row['id'],
        'playerId' => $row['playerid'],
        'playerName' => $row['name'],
        'round' => $row['roundid'],
        'throws' => $throws,
        'par' => $par,
        'holes' => $holes
      ));
    }
    return $scores;
  }

  public static function find_by_round2($roundId) {
    $players = Player::find_by_round($roundId);
    $playerScores = array();
    foreach ($players as $player) {
      $playerScores[] = array('$player' => $player, 'scores' => self::find_by_round_and_player($roundId, $player->id));
    }
    return $playerScores;
  }

  public static function find_by_round_and_player($roundId, $playerId) {
    $query = DB::connection()->prepare('SELECT sh.throws, h.holenumber, h.par FROM Score_Hole sh JOIN Score s ON sh.scoreId = s.id JOIN Hole h on h.id = sh.holeId WHERE s.playerid = :playerId AND s.roundId = :roundId ORDER BY holenumber');
    $query->execute(array('roundId' => $roundId, 'playerId' => $playerId));
    $rows = $query->fetchAll();
    $scores = array();
    foreach ($rows as $row) {
      $scores = array_merge($scores, array(
        $row['holenumber'] => array(
          'throws' => $row['throws'],
          'par' => $row['par']
        )
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
