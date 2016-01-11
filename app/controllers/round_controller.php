<?php
class RoundController extends BaseController {

  public static function list_all() {
    $rounds = Round::all();
    View::make('round/list.html', array('rounds' => $rounds));
  }
  public static function show($id) {
    $round = Round::find($id);
    $course = Course::find($round->courseId);
    $holes = Hole::count_holes($round->courseId);
    $scores = Score::find_by_round2($id);

    View::make('round/show.html', array('round' => $round, 'scores' => $scores, 'holes' => $holes, 'course' => $course));
  }
  public static function create($courseId = null) {
    $player = self::get_user_logged_in();
    if(!$player) {
      View::make('player/login.html', array('error' => 'Vain kirjautuneet käyttäjät voivat lisätä kierroksia.'));
    } else {
      if($_POST) {
        $player = self::get_user_logged_in();
        $players = Player::all();
        $params = $_POST;
        $course = Course::find($params['course']);
        $courses = Course::all();
        $holes = Hole::find_by_course($course->id);
        $played = $params['played'];
        View::make('round/new_continued.html', array('numberOfPlayers' => $params['numberOfPlayers'], 'players' => $players, 'course' => $course, 'holes' => $holes, 'courses' => $courses, 'played' => $played));
      } else {
        $courses = Course::all();
        View::make('round/new.html', array('selected' => $courseId, 'courses' => $courses));
      }
    }
  }


  public static function edit($id) {
    $player = self::get_user_logged_in();
    if(!$player) {
      View::make('player/login.html', array('error' => 'Vain kirjautuneet käyttäjät voivat muokata kierroksia.'));
    } else {
      $round = Round::find($id);
      $course = Course::find($round->courseId);
      $courses = Course::all();
      $holes = Hole::find_by_course($course->id);
      $scores = Score::find_by_round($id);
      $players = Player::all();
      View::make('round/edit.html', array('numberOfPlayers' => sizeof($scores), 'round' => $round, 'scores' => $scores, 'holes' => $holes, 'course' => $course, 'courses' => $courses, 'players' => $players));
    }
  }

  public static function store(){

    $player = self::get_user_logged_in();
    if(!$player) {
      View::make('player/login.html', array('error' => 'Vain kirjautuneet käyttäjät voivat lisätä ratoja.'));
    }
    $params = $_POST;
    if($params['numberOfPlayers'] != $params['numberOfPlayers_orig'] or $params['course'] != $params['course_orig']) {
      $playerScores = array();
      $numberOfHoles = Hole::count_holes($params['course_orig']);
      for ($i=1; $i <= $params['numberOfPlayers_orig']; $i++) {
        $holeScores = array();
        for ($j=1; $j <= $numberOfHoles; $j++) {
          $holeScores[] = $params['p' . $i . '_h' . $j];
        }
        $playerScores[] = array('player' => Player::find($params['player_' . $i]), 'holes' => $holeScores);
      }
      $players = Player::all();
      $course = Course::find($params['course']);
      $courses = Course::all();
      $holes = Hole::find_by_course($course->id);
      $played = $params['played'];
      View::make('round/new_continued.html', array('numberOfPlayers' => $params['numberOfPlayers'], 'players' => $players, 'course' => $course, 'holes' => $holes, 'courses' => $courses, 'playerScores' => $playerScores, 'played' => $played));
    } else {
      $attributes = array(
        'courseId' => $params['course'],
        'played' => $params['played'],
        'addedBy' => self::get_user_logged_in()->id
      );

      $playerScores = array();
      $numberOfHoles = Hole::count_holes($params['course_orig']);
      $round = new Round($attributes);
      $errors = $round->errors();

      if(count($errors) == 0) {
        $round->save();
        for ($i=1; $i <= $params['numberOfPlayers_orig']; $i++) {
          $holeScores = array();
          for ($j=1; $j <= $numberOfHoles; $j++) {
            $hole = Hole::find_by_course_and_holenumber($params['course_orig'], $j);
            $holeScores[] = array('throws' => $params['p' . $i . '_h' . $j], 'holeId' => $hole->id);
          }
          $score = new Score(array('playerId' => $params['player_' . $i], 'roundId' => $round->id, 'scores' => $holeScores));
          $score->save();
        }
        Redirect::to('/round/' . $round->id, array('message' => 'Kierros lisätty.'));
      } else {
        View::make('round/new.html', array('errors' => $errors, 'attributes' => $attributes));
      }
    }
  }

  public static function update($id) {
    $player = self::get_user_logged_in();
    if(!$player) {
      View::make('player/login.html', array('error' => 'Vain kirjautuneet käyttäjät voivat muokata ratoja.'));
    }
    $params = $_POST;

    $players = Player::all();
    $course = Course::find($params['course']);
    $courses = Course::all();
    $played = $params['played'];
    $round = Round::find($id);
    $holes = Hole::find_by_course($course->id);
    $scores = Score::find_by_round($id);
    $numberOfPlayers = sizeof($scores);
    if($params['course'] != $params['course_orig']) {
      $playerScores = array();
      $numberOfHoles = Hole::count_holes($params['course_orig']);
      for ($i=1; $i <= $numberOfPlayers; $i++) {
        $holeScores = array();
        for ($j=1; $j <= $numberOfHoles; $j++) {
          $holeScores[] = $params['p' . $i . '_h' . $j];
        }
        $playerScores[] = array('player' => Player::find($params['player_' . $i]), 'holes' => $holeScores);
      }
      View::make('round/edit.html', array('numberOfPlayers' => $numberOfPlayers, 'players' => $players, 'course' => $course, 'holes' => $holes, 'courses' => $courses, 'playerScores' => $playerScores,'round' => $round, 'scores' => $scores, 'played' => $played));
    } else {
      $attributes = array(
        'courseId' => $params['course'],
        'played' => $params['played']
      );

      $playerScores = array();
      $numberOfHoles = Hole::count_holes($params['course_orig']);

      $round = new Round(array_merge(array("id" => $round->id), $attributes));
      $errors = $round->errors();

      if(count($errors) == 0) {
        // Kint::dump($round);
        // return;
        $round->update();
        // if($ret) {
        //   echo "joo".$ret;
        //   Kint::dump($round);
        // } else {
        //   echo "ei".$ret;
        //   Kint::dump($round);
        // }
        // Kint::dump($params);
        // return;
        for ($i=1; $i <= $numberOfPlayers; $i++) {
          $holeScores = array();
          $scoreId = $params['score_' . $i];
          for ($j=1; $j <= $numberOfHoles; $j++) {
            $hole = Hole::find_by_course_and_holenumber($params['course_orig'], $j);
            $holeScores[] = array('throws' => $params['p' . $i . '_h' . $j], 'holeId' => $hole->id, 'holenumber' => $j);
          }
          $score = new Score(array('id' => $scoreId, 'playerId' => $params['player_' . $i], 'roundId' => $round->id, 'scores' => $holeScores));

          $score->update();

        }
        Redirect::to('/round/' . $round->id, array('message' => 'Kierrosta muokattu.'));
      } else {
        View::make('round/new.html', array('errors' => $errors, 'attributes' => $attributes));
      }
    }
  }

  public static function destroy($id) {
    $round = new Round(array('id' => $id));
    $round->destroy();

    Redirect::to('/round', array('message' => 'Kierros poistettu.'));
  }
}
