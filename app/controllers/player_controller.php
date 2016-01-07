<?php
class PlayerController extends BaseController{

  public static function login() {
    View::make('player/login.html');
  }

  public static function handle_login() {
    $params = $_POST;
    $player = Player::authenticate($params['username'], $params['password']);
    if(!$player) {
      View::make('player/login.html', array('error' => 'Väärä käyttäjätunnus tai salasana.', 'username' => $params['username']));
    } else {
      $_SESSION['user'] = $player->id;

      Redirect::to('/', array('message' => 'Kirjautunut käyttäjänä ' . $player->username));
    }
  }

  public static function list_all() {
    $player = self::get_user_logged_in();
    $scores = Score::find_by_player($player->id);
    View::make('player/show.html', array('scores' => $scores));
  }

  public static function logout() {
    $_SESSION['user'] = null;
    Redirect::to('/login', array('message' => 'Olet kirjautunut ulos.'));
  }

  public static function create() {
    if(self::get_user_logged_in()) {
      Redirect::to('/player', array('message' => 'Olet jo kirjautunut sisään.'));
    } else {
      $courses = Course::all();
      View::make('player/new.html', array('courses' => $courses));
    }
  }

  public static function edit($id) {
    if(self::get_user_logged_in()->id != $id && !self::is_admin()) {
      Redirect::to('/', array('error' => 'Sinulla ei ole oikeuksia muokata tämän käyttäjän tietoja.'));
    } else {
      $player = Player::find($id);
      $courses = Course::all();
      View::make('player/edit.html', array('player' => $player, 'courses' => $courses));
    }
  }

  public static function store(){
    $params = $_POST;
    if(!is_numeric($params['courseid'])) {
      $params['courseid'] = null;
    }
    $attributes = array(
      'name' => $params['name'],
      'courseid' => $params['courseid'],
      'username' => $params['username'],
      'password' => $params['new_password'],
      'verify' => $params['password_verify'],
      'newPassword' => true
    );

    $player = new Player($attributes);
    $errors = $player->errors();

    if(count($errors) == 0) {
      $player->save();
      Redirect::to('/player', array('message' => 'Käyttäjä lisätty.'));
    } else {
      View::make('player/new.html', array('errors' => $errors, 'attributes' => $attributes));
    }
  }

  public static function update($id) {
    $params = $_POST;
    if(!is_numeric($params['courseid'])) {
      $params['courseid'] = null;
    }
    $attributes = array(
      'id' => $id,
      'name' => $params['name'],
      'courseId' => $params['courseid'],
      'username' => $params['username'],
      'password' => $params['new_password'],
      'verify' => $params['password_verify'],
      'newPassword' => (strlen($params['new_password']) != 0)
    );
    $player = new Player($attributes);
    $errors = $player->errors();
    if(Player::authenticate(self::get_user_logged_in()->username, $params['password']) == null) {
      $player = Player::find($id);
      $courses = Course::all();
      View::make('player/edit.html', array('errors' => $errors, 'attributes' => $attributes, 'player' => $player, 'courses' => $courses, 'error' => 'Väärä salasana tämänhetkiselle käyttäjälle.'));
    }
    if(count($errors) == 0) {
      $player->update();
      Redirect::to('/player', array('message' => 'Käyttäjän tietoja muutettu.'));
    } else {
      $player = Player::find($id);
      $courses = Course::all();
      View::make('player/edit.html', array('errors' => $errors, 'attributes' => $attributes, 'player' => $player, 'courses' => $courses));
    }
  }

  public static function destroy($id) {
    $player = new Player(array('id' => $id));
    $player->destroy();

    Redirect::to('/', array('message' => 'Käyttäjä poistettu.'));
  }
}
