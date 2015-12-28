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
    View::make('course/new.html');
  }
  public static function edit($id) {
    $course = Course::find($id);
    View::make('course/edit.html', array('course' => $course, 'average' => $course->average()));
  }

  public static function store(){
    $params = $_POST;
    $course = new Course(array(
      'name' => $params['name'],
      'description' => $params['description'],
      'address' => $params['address'],
      'mapLink' => $params['mapLink'],
      'url' => $params['url']
    ));

    $course->save();

    Redirect::to('/course/' . $course->id, array('message' => 'Rata lisätty!'));
  }
}
