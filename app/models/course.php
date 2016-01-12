<?php
/**
* Model class for courses.
*/
class Course extends BaseModel {

  public $id, $name, $address, $mapLink, $description, $added, $edited, $moderators, $best, $par, $rounds, $url;

  /**
  * Constructor for course.
  *
  * @param array $attributes Contains all the parameters to be stored for the object.
  *
  * @return Course Returns the newly created object.
  */
  public function __construct($attributes){
    parent::__construct($attributes);
    $this->validators = array('validate_name', 'validate_address', 'validate_maplink', 'validate_description', 'validate_url');
  }

  /**
  * Validations for name of course.
  */
  public function validate_name() {
    return $this->validate_string_length("Nimi", $this->name, 2, 100);
  }

  /**
  * Validations for address of course.
  */
  public function validate_address() {
    return $this->validate_string_length("Sijainti", $this->address, 0, 300);
  }

  /**
  * Validations for maplink of course.
  */
  public function validate_maplink() {
    return array_merge($this->validate_string_length("Karttalinkki", $this->mapLink, 0, 300), $this->validate_url_format("Karttalinkki", $this->mapLink));
  }

  /**
  * Validations for description of course.
  */
  public function validate_description() {
    return $this->validate_string_length("Kuvaus", $this->description, 0, 500);
  }

  /**
  * Validations for external url of course.
  */
  public function validate_url() {
    return array_merge($this->validate_string_length("Ulkoinen linkki", $this->url, 0, 300), $this->validate_url_format("Ulkoinen linkki", $this->url));
  }

  /**
  * Fetches all courses from database.
  *
  * @return array Returns a listing of all courses in database.
  */
  public static function all() {
    $query = DB::connection()->prepare('SELECT * FROM Course ORDER BY name');
    $query->execute();
    $rows = $query->fetchAll();
    $courses = array();

    foreach ($rows as $row) {
      $rounds = Round::find_by_course($row['id']);
      $moderators = Player::find_by_moderation($row['id']);
      $best = 0;
      $par = 0;

      foreach ($rounds as $round) {
        $scores = Score::find_by_round($round->id);
        foreach ($scores as $score) {
          $par = $score->par;
          if ($best > $score->throws||$best == 0) {
            $best = $score->throws;
          }
        }
      }

      $courses[] = new Course(array(
        'id' => $row['id'],
        'name' => $row['name'],
        'address' => $row['address'],
        'mapLink' => $row['maplink'],
        'description' => $row['description'],
        'added' => $row['added'],
        'edited' => $row['edited'],
        'rounds' => $rounds,
        'moderators' => $moderators,
        'best' => $best,
        'par' => $par,
        'url' => $row['url']
      ));
    }
    return $courses;
  }

  /**
  * Fetches a single course from database if found.
  *
  * @param int $id Id of course to be fetched.
  *
  * @return Course Course to be fetched or null if not found.
  */
  public static function find($id){
    $query = DB::connection()->prepare('SELECT * FROM Course WHERE id = :id LIMIT 1');
    $query->execute(array('id' => $id));
    $row = $query->fetch();

    if($row){
      $rounds = Round::find_by_course($row['id']);
      $moderators = Player::find_by_moderation($row['id']);
      $best = 0;
      $par = 0;

      foreach ($rounds as $round) {
        $scores = Score::find_by_round($round->id);
        foreach ($scores as $score) {
          $par = $score->par;
          if ($best > $score->throws||$best == 0) {
            $best = $score->throws;
          }
        }
      }

      $course = new Course(array(
        'id' => $row['id'],
        'name' => $row['name'],
        'address' => $row['address'],
        'mapLink' => $row['maplink'],
        'description' => $row['description'],
        'added' => $row['added'],
        'edited' => $row['edited'],
        'rounds' => $rounds,
        'moderators' => $moderators,
        'best' => $best,
        'par' => $par,
        'url' => $row['url']
      ));
      return $course;
    }
    return null;
  }

  /**
  * Fetches all courses moderated by selected player.
  *
  * @param int $id Id of selected player.
  *
  * @return array Array of all courses moderated by selected player.
  */
  public static function find_by_moderator($id) {
    $query = DB::connection()->prepare('SELECT c.* FROM Course c LEFT JOIN Course_moderators cm ON cm.courseid = c.id WHERE cm.playerid = :id');
    $query->execute(array('id' => $id));
    $rows = $query->fetchAll();

    $courses = array();

    foreach ($rows as $row) {
      $rounds = Round::find_by_course($row['id']);
      $moderators = Player::find_by_moderation($row['id']);
      $best = 0;
      $par = 0;

      foreach ($rounds as $round) {
        $scores = Score::find_by_round($round->id);
        foreach ($scores as $score) {
          $par = $score->par;
          if ($best > $score->throws||$best == 0) {
            $best = $score->throws;
          }
        }
      }

      $courses[] = new Course(array(
        'id' => $row['id'],
        'name' => $row['name'],
        'address' => $row['address'],
        'mapLink' => $row['maplink'],
        'description' => $row['description'],
        'added' => $row['added'],
        'edited' => $row['edited'],
        'rounds' => $rounds,
        'moderators' => $moderators,
        'best' => $best,
        'par' => $par,
        'url' => $row['url']
      ));
    }
    return $courses;
  }

  /**
  * Calculates average score for course.
  *
  * @return int Returns average score for course.
  */
  public function average() {
    $count = 0;
    $sum = 0;
    $rounds = Round::find_by_course($this->id);
    if(count($rounds)>0) {
      foreach ($rounds as $round) {
        $scores = $round->getScores();
        if(count($scores)>0) {
          foreach ($scores as $score) {
            $count++;
            $sum += $score->throws;
          }
        }
      }
    }
    $return = 0;
    if($count != 0) {
      $return = $sum/$count;
    }
    return $return;
  }

  /**
  * Counts number of holes on course.
  *
  * @return int Returns the number of holes.
  */
  public function holes() {
    return Hole::find_by_course($this->id);
  }

  /**
  * Saves course to database.
  */
  public function save(){
    $query = DB::connection()->prepare('INSERT INTO Course (name, address, maplink, url, description) VALUES (:name, :address, :maplink, :url, :description) RETURNING id');
    $query->execute(array('name' => $this->name, 'address' => $this->address, 'maplink' => $this->mapLink, 'url' => $this->url, 'description' => $this->description));
    $row = $query->fetch();
    $this->id = $row['id'];
  }

  /**
  * Updates course information into database.
  */
  public function update() {
    $query = DB::connection()->prepare('UPDATE Course SET name = :name, address = :address, maplink = :maplink, url = :url, description = :description, edited = now() WHERE id = :id');
    $query->execute(array('name' => $this->name, 'address' => $this->address, 'maplink' => $this->mapLink, 'url' => $this->url, 'description' => $this->description, 'id' => $this->id));
  }

  /**
  * Deletes course from database.
  */
  public function destroy() {
    $query = DB::connection()->prepare('DELETE FROM Course WHERE id = :id');
    $query->execute(array('id' => $this->id));
  }


}
