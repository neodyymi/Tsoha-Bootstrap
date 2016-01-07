<?php
class RoundController extends BaseController {

  public static function list_all() {
    $rounds = Round::all();
    View::make('round/list.html', array('rounds' => $rounds));
  }
  public static function show($id) {
    $round = Round::find($id);
    $scores = Score::find_by_round2($id);

    View::make('round/show.html', array('round' => $round, 'scores' => $scores, 'holes' => Hole::count_holes($round->courseId)));
  }
  public static function create($courseId = null) {
    $player = self::get_user_logged_in();
    if(!$player) {
      View::make('player/login.html', array('error' => 'Vain kirjautuneet käyttäjät voivat lisätä kierroksia.'));
    } else {
      View::make('round/new.html', array('selected' => $courseId));
    }
  }
  public static function edit($id) {
    $player = self::get_user_logged_in();
    if(!$player) {
      View::make('player/login.html', array('error' => 'Vain kirjautuneet käyttäjät voivat muokata kierroksia.'));
    } else {
      $round = Round::find($id);
      View::make('round/edit.html', array('round' => $round));
    }
  }

  public static function store(){

    $player = self::get_user_logged_in();
    if(!$player) {
      View::make('player/login.html', array('error' => 'Vain kirjautuneet käyttäjät voivat lisätä ratoja.'));
    }
    $params = $_POST;
    $params['url'] = self::fix_url($params['url']);
    $params['mapLink'] = self::fix_url($params['mapLink']);

    $attributes = array(
      'name' => $params['name'],
      'courseid' => $params['courseid'],
      'username' => $params['username'],
      'password' => $params['password']
    );

    $round = new Round($attributes);
    $errors = $round->errors();

    if(count($errors) == 0) {
      $round->save();
      Redirect::to('/round/' . $round->id, array('message' => 'Kierros lisätty.'));
    } else {
      View::make('round/new.html', array('errors' => $errors, 'attributes' => $attributes));
    }
  }

  public static function update($id) {
    $params = $_POST;

    $attributes = array(
      'name' => $params['name'],
      'courseid' => $params['courseid'],
      'username' => $params['username'],
      'password' => $params['password']
    );
    $round = new Round($attributes);

    $errors = $round->errors();
    if(count($errors) == 0) {
      $round->update();
      Redirect::to('/round/' . $round->id, array('message' => 'Kierroksen tietoja muutettu.'));
    } else {
      $round = Round::find($id);
      View::make('round/edit.html', array('errors' => $errors, 'attributes' => $attributes, 'round' => $round));
    }
  }

  public static function destroy($id) {
    $round = new Round(array('id' => $id));
    $round->destroy();

    Redirect::to('/round', array('message' => 'Kierros poistettu.'));
  }
}
