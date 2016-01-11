<?php
class Score extends BaseModel {
  public $id, $playerId, $playerName, $roundId, $round, $throws, $par, $holes, $scores;

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
        'roundId' => $row['roundid'],
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
        'roundId' => $row['roundid'],
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
      $playerScores[] = array('player' => $player, 'scores' => self::find_by_round_and_player($roundId, $player->id));
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
          'holenumber' => $row['holenumber'],
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
        'player' => $playerId,
        'roundId' => $row['roundid'],
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
        'roundId' => $row['roundid'],
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

  public static function saveRoundScores($params) {
    $holes = Hole::count_holes($params['course']);
    $players = $params['players'];
    $course = $params['course'];

  }

  public static function updateRoundScores($params) {
    $holes = Hole::count_holes($params['course']);
    $players = array();
    $score = null;
    for ($i=0; $i < $params['players']; $i++) {
      $split = explode('_', $params['player' . $i]);
      $players[] = array('player' => $split[0], 'score' => $split[1]);
      $score = new Score(array('id' => $split[1], 'playerId' => $split[0], 'roundId' => '3'));
    }
    foreach ($players as $player) {

    }

    $score->update();
  }

  public static function updatePlayerRound($player, $round, $scores) {
    foreach ($scores as $score) {

    }
  }

  public static function updateScoreHole($score, $hole, $result) {

  }

  public function save() {
    $query = DB::connection()->prepare('INSERT INTO Score (roundid, playerid) VALUES (:roundId, :playerId) RETURNING id');
    $query->execute(array('roundId' => $this->roundId, 'playerId' => $this->playerId));
    $row = $query->fetch();
    $this->id = $row['id'];
    $query = DB::connection()->prepare('INSERT INTO Score_Hole (holeid, scoreid, throws) VALUES (:holeId, :scoreId, :throws)');
    foreach ($this->scores as $holeScore) {
      $query->execute(array('throws' => $holeScore['throws'], 'holeId' => $holeScore['holeId'], 'scoreId' => $this->id));
    }
  }

  public function update() {
    $query = DB::connection()->prepare('UPDATE Score SET roundid = :roundId, playerid = :playerId WHERE id = :id');
    $query->execute(array('roundId' => $this->roundId, 'playerId' => $this->playerId, 'id' => $this->id));
    if($this->scores) {
      $round = Round::find($this->roundId);
      $numberOfHoles = Hole::count_holes($round->courseId);
      $scores = array_slice($this->scores, 0, $numberOfHoles);
      foreach ($scores as $holeScore) {
        $hole = Hole::find_by_course_and_holenumber($round->courseId, $holeScore['holenumber']);
        $query = DB::connection()->prepare('UPDATE Score_Hole SET throws = :throws WHERE holeid = :holeId AND scoreid = :scoreId');
        $exists = $query->execute(array('throws' => $holeScore['throws'], 'holeId' => $hole->id, 'scoreId' => $this->id));
        if(!$exists) {
          $query = DB::connection()->prepare('INSERT INTO Score_Hole (holeid, scoreid, throws) VALUES (:holeId, :scoreId, :throws)');
          $query->execute(array('throws' => $holeScore['throws'], 'holeId' => $holeScore['holeId'], 'scoreId' => $this->id));
        }
      }
    }
    $this->clean();
  }
  public function clean() {
    $courseId = Round::find($this->roundId)->courseId;
    $query = DB::connection()->prepare('DELETE FROM score_hole WHERE holeid IN (SELECT sh.holeid FROM score_hole sh JOIN score s ON s.id = sh.scoreid JOIN hole h ON h.id = sh.holeid WHERE s.id = 1 AND NOT h.courseid = :courseid) AND scoreid IN (SELECT sh.scoreid FROM score_hole sh JOIN score s ON s.id = sh.scoreid JOIN hole h ON h.id = sh.holeid WHERE s.id = 1 AND NOT h.courseid = :courseid)');
    $query->execute(array('courseid' => $courseId));
  }
}
