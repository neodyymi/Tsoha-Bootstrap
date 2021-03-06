<?php
/**
 *  CourseController handles course related requests.
 */
class CourseController extends BaseController{

  /**
   * Lists all courses.
   */
  public static function list_all() {
    $courses = Course::all();
    View::make('course/list.html', array('courses' => $courses));
  }

  /**
   * Displays one course.
   *
   * @param int $id Id of course to be displayed.
   */
  public static function show($id) {
    $course = Course::find($id);
    View::make('course/show.html', array('course' => $course, 'is_moderator' => self::is_moderator($id)));
  }

  /**
   * Displays new course creation page.
   */
  public static function create() {
    $player = self::get_user_logged_in();
    if(!$player) {
      View::make('player/login.html', array('error' => 'Vain kirjautuneet käyttäjät voivat lisätä ratoja.'));
    } else {
      View::make('course/new.html');
    }
  }

  /**
   * Displays course edit page.
   *
   * @param int $id Id of course to be edited.
   */
  public static function edit($id) {
    $player = self::get_user_logged_in();
    if(!$player) {
      View::make('player/login.html', array('error' => 'Vain kirjautuneet käyttäjät voivat muokata ratoja.'));
    } else {
      $course = Course::find($id);
      $holes = Hole::find_by_course($id);
      View::make('course/edit.html', array('course' => $course, 'holes' => $holes));
    }
  }

  /**
   * Attempts to save new course and redirect to the newly created course.
   */
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
      Hole::create_holes($params['holes'], $course->id);
      Moderator::add_as_moderator($course->id, $player->id);
      Redirect::to('/course/' . $course->id . '/edit', array('message' => 'Rata lisätty, täydennä vielä väylien tiedot.'));
    } else {
      View::make('course/new.html', array('errors' => $errors, 'attributes' => $attributes));
    }
  }

  /**
   * Attempts to update edited course information and displays the edited course.
   *
   * @param int $id Id of course to be updated.
   */
  public static function update($id) {
    $player = self::get_user_logged_in();
    if(!$player) {
      View::make('player/login.html', array('error' => 'Vain kirjautuneet käyttäjät voivat muokata ratoja.'));
    }

    $params = $_POST;
    $params['url'] = self::fix_url($params['url']);
    $params['mapLink'] = self::fix_url($params['mapLink']);

    $attributes = array(
      'name' => $params['name'],
      'description' => $params['description'],
      'address' => $params['address'],
      'mapLink' => $params['mapLink'],
      'url' => $params['url'],
      'id' => $id
    );
    $course = new Course($attributes);
    $errors = $course->errors();

    if(count($errors) == 0) {
      $course->update();
      for ($i=1; $i <= Hole::count_holes($course->id); $i++) {
        $hole = Hole::find_by_course_and_holenumber($course->id, $i);
        $hole->name = $params[$i . '_name'];
        $hole->par = $params[$i . '_par'];
        $hole->update();
      }
      Redirect::to('/course/' . $course->id, array('message' => 'Radan tietoja muutettu.'));
    } else {
      View::make('course/edit.html', array('errors' => $errors, 'attributes' => $attributes));
    }
  }

  /**
   * Attempts to destroy selected course and displays course listing.
   *
   * @param int $id Id of course to be deleted.
   */
  public static function destroy($id) {
    $course = new Course(array('id' => $id));
    $course->destroy();

    Redirect::to('/course', array('message' => 'Rata on poistettu onnistuneesti.'));
  }
}
