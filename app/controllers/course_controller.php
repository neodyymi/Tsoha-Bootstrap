<?php
class CourseController extends BaseController{

  public static function list_all() {
    $courses = Course::all();
    View::make('course/list.html', array('courses' => $courses));
  }
  public static function show($id) {
    $course = Course::find($id);
    View::make('course/show.html', array('course' => $course));
  }
  public static function create() {
    $player = self::get_user_logged_in();
    if(!$player) {
      View::make('player/login.html', array('error' => 'Vain kirjautuneet käyttäjät voivat lisätä ratoja.'));
    } else {
      View::make('course/new.html');
    }
  }
  public static function edit($id) {
    $player = self::get_user_logged_in();
    if(!$player) {
      View::make('player/login.html', array('error' => 'Vain kirjautuneet käyttäjät voivat muokata ratoja.'));
    } else {
      $course = Course::find($id);
      View::make('course/edit.html', array('course' => $course));
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
      'description' => $params['description'],
      'address' => $params['address'],
      'mapLink' => $params['mapLink'],
      'url' => $params['url']
    );

    $course = new Course($attributes);
    $errors = $course->errors();

    if(count($errors) == 0) {
      $course->save();
      Redirect::to('/course/' . $course->id, array('message' => 'Rata lisätty.'));
    } else {
      View::make('course/new.html', array('errors' => $errors, 'attributes' => $attributes));
    }
  }

  public static function update($id) {
    $params = $_POST;
    $course = new Course(array(
      'name' => $params['name'],
      'description' => $params['description'],
      'address' => $params['address'],
      'mapLink' => $params['mapLink'],
      'url' => $params['url'],
      'id' => $id
    ));

    $course->update();

    Redirect::to('/course/' . $course->id, array('message' => 'Radan tietoja muutettu.'));
  }

  public static function destroy($id) {
    $course = new Course(array('id' => $id));
    $course->destroy();

    Redirect::to('/course', array('message' => 'Rata on poistettu onnistuneesti.'));
  }
}
