<?php
/**
* Model class for round
*/
class Round extends BaseModel {
  public $id, $course, $courseName, $courseId, $scores, $played, $added, $addedBy, $best, $par, $average, $players;

  /**
  * Constructor for round.
  *
  * @param array $attributes Contains all the parameters to be stored for the object.
  *
  * @return Round Returns the newly created object.
  */
  public function __construct($attributes) {
    parent::__construct($attributes);
    $this->validators = array();
  }

  /**
  * Fetches all rounds from database.
  *
  * @return array Returns listing of all rounds in database.
  */
  public static function all() {
    $query = DB::connection()->prepare('SELECT r.id, c.name AS coursename, c.id AS courseid, r.played, r.added, p.name AS addedby FROM Round r LEFT JOIN Course c ON r.courseid = c.id LEFT JOIN Player p ON r.addedby = p.id ORDER BY r.played');
    $query->execute();
    $rows = $query->fetchAll();

    $rounds = array();

    foreach ($rows as $row) {

      $best = 0;
      $par = 0;
      $sum = 0;
      $scores = Score::find_by_round($row['id']);
      foreach ($scores as $score) {
        $par = $score->par;
        if ($best > $score->throws||$best == 0) {
          $best = $score->throws;
        }
        $sum += $score->throws;
      }
      $avg = $sum / count($scores);
      $rounds[] = new Round(array(
        'id' => $row['id'],
        'courseName' => $row['coursename'],
        'courseId' => $row['courseid'],
        'played' => $row['played'],
        'added' => $row['added'],
        'addedBy' => $row['addedby'],
        'scores' => $scores,
        'best' => $best,
        'par' => $par,
        'average' => $avg,
        'players' => count($scores)
      ));
    }

    return $rounds;
  }

  /**
  * Fetches one round from database.
  *
  * @param int $id Id of round to be fetched.
  *
  * @return Round Returns round if found. Otherwise returns null.
  */
  public static function find($id) {
    $query = DB::connection()->prepare('SELECT r.id, c.id AS course, c.name as coursename, r.played, r.added, p.id AS addedby FROM Round r LEFT JOIN Course c ON r.courseid = c.id LEFT JOIN Player p ON r.addedby = p.id WHERE r.id = :id LIMIT 1');
    $query->execute(array('id' => $id));
    $row = $query->fetch();

    if($row) {
      $best = 0;
      $par = 0;
      $sum = 0;
      $scores = Score::find_by_round($row['id']);
      foreach ($scores as $score) {
        $par = $score->par;
        if ($best > $score->throws||$best == 0) {
          $best = $score->throws;
        }
        $sum += $score->throws;
      }
      $avg = $sum / count($scores);
      $round = new Round(array(
        'id' => $row['id'],
        'played' => $row['played'],
        'added' => $row['added'],
        'addedBy' => $row['addedby'],
        'courseId' => $row['course'],
        'courseName' => $row['coursename'],
        'scores' => $scores,
        'best' => $best,
        'par' => $par,
        'average' => $avg,
        'players' => count($scores)
      ));
      return $round;
    }

    return null;
  }

  /**
  * Fetches all rounds played on selected course.
  *
  * @param int $courseId Id of selected course.
  *
  * @return array Returns listing of all rounds played on selected course.
  */
  public static function find_by_course($courseId) {
    $query = DB::connection()->prepare('SELECT r.id, c.name AS course, r.played, r.added, p.name AS addedby FROM Round r LEFT JOIN Course c ON r.courseid = c.id LEFT JOIN Player p ON r.addedby = p.id WHERE r.courseid = :courseId');
    $query->execute(array('courseId' => $courseId));
    $rows = $query->fetchAll();

    $rounds = array();
    foreach($rows as $row) {
      $best = 0;
      $par = 0;
      $sum = 0;
      $scores = Score::find_by_round($row['id']);
      foreach ($scores as $score) {
        $par = $score->par;
        if ($best > $score->throws||$best == 0) {
          $best = $score->throws;
        }
        $sum += $score->throws;
      }
      $avg = $sum / count($scores);
      $rounds[] = new Round(array(
        'id' => $row['id'],
        'course' => $row['course'],
        'played' => $row['played'],
        'added' => $row['added'],
        'addedBy' => $row['addedby'],
        'scores' => $scores,
        'best' => $best,
        'par' => $par,
        'average' => $avg,
        'players' => count($scores)
      ));
    }

    return $rounds;
  }

  /**
  * @return array Returns array of all scores in round.
  */
  public function getScores() {
    $scores = array();
    foreach ($this->scores as $score) {
      $scores[] = $score->throws;
    }
    return $scores;
  }

  /**
  * Saves round to database.
  */
  public function save(){
    $query = DB::connection()->prepare('INSERT INTO Round (courseid, played, addedby) VALUES (:courseId, :played, :addedBy) RETURNING id');
    $query->execute(array('courseId' => $this->courseId, 'played' => $this->played, 'addedBy' => $this->addedBy));
    $row = $query->fetch();
    $this->id = $row['id'];
  }

  /**
  * Updates round information into database.
  */
  public function update() {
    $query = DB::connection()->prepare('UPDATE Round SET courseid = :courseId, played = :played WHERE id = :id');
    return $query->execute(array('courseId' => $this->courseId, 'played' => $this->played, 'id' => $this->id));
  }

  /**
  * Deletes round from database.
  */
  public function destroy() {
    $query = DB::connection()->prepare('DELETE FROM Round WHERE id = :id');
    $query->execute(array('id' => $this->id));
  }
}
