<?php
class Hole extends BaseModel {
  public $id, $courseId, $name, $par, $holenumber;

  public function __construct($attributes) {
    parent::__construct($attributes);
  }

  public static function find_by_course($courseId) {
    $query = DB::connection()->prepare('SELECT * FROM Hole WHERE courseid = :courseid ORDER BY holenumber');
    $query->execute(array('courseid' => $courseId));
    $rows = $query->fetchAll();
    $holes = array();
    foreach($rows as $row) {
      $holes[] = new Hole(array(
        'id' => $row['id'],
        'courseId' => $row['courseid'],
        'name' => $row['name'],
        'par' => $row['par'],
        'holenumber' => $row['holenumber']
      ));
    }
    return $holes;
  }

  public static function count_holes($courseId) {
    $query = DB::connection()->prepare('SELECT count(*) as count FROM Hole WHERE courseid = :courseid');
    $query->execute(array('courseid' => $courseId));
    $row = $query->fetch();
    return $row['count'];
  }

  public static function create_holes($n, $courseid) {
    for ($i=1; $i <= $n; $i++) {
      $query = DB::connection()->prepare('INSERT INTO Hole (courseid, name, holenumber) VALUES (:courseid, :name, :holenumber)');
      $query->execute(array('courseid' => $courseid, 'name' => "Väylä" . $i, 'holenumber' => $i));
    }
  }

  public static function find_by_course_and_holenumber($courseId, $holenumber) {
    $query = DB::connection()->prepare('SELECT * FROM Hole WHERE courseid = :courseid AND holenumber = :holenumber LIMIT 1');
    $query->execute(array('courseid' => $courseId, 'holenumber' => $holenumber));
    $row = $query->fetch();
    $hole = new Hole(array(
      'id' => $row['id'],
      'courseId' => $row['courseid'],
      'name' => $row['name'],
      'par' => $row['par'],
      'holenumber' => $row['holenumber']
    ));
    return $hole;
  }

  public function update() {
    $query = DB::connection()->prepare('UPDATE Hole SET name = :name, par = :par WHERE id = :id');
    $query->execute(array('name' => $this->name, 'par' => $this->par, 'id' => $this->id));
  }
}
