<?php
class PlayerController extends BaseController{

  /**
   * Displays login page.
   */
  public static function login() {
    View::make('player/login.html');
  }

  /**
   * Checks inputted login information and, if successful, creates a session for logged in user.
   */
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

  /**
   * Redirects to a listing of current logged in user list of all played rounds.
   */
  public static function list_all() {
    // $player = self::get_user_logged_in();
    // $scores = Score::find_by_player($player->id);
    // View::make('player/show.html', array('player' => $player, 'scores' => $scores));
    Redirect::to('/player/' . self::get_user_logged_in()->id);
  }

  /**
   * Displays a listing of a user's list of all played rounds.
   *
   * @param int $id Id of player to be displayed.
   */
  public static function show($id) {
    $player = Player::find($id);
    $moderatorOf = Course::find_by_moderator($id);
    $course = Course::find($player->course);
    $scores = Score::find_by_player($player->id);
    foreach($scores as $score) {
      $score->round = Round::find($score->roundId);
    }
    // $rounds = Round::find_by_player($player->id);
    // Kint::dump($rounds);
    View::make('player/show.html', array('player' => $player, 'scores' => $scores, 'moderatorOf' => $moderatorOf, 'course' => $course));
  }

  /**
  * Attempts to logout current user destroying the assigned session.
  */
  public static function logout() {
    $_SESSION['user'] = null;
    Redirect::to('/login', array('message' => 'Olet kirjautunut ulos.'));
  }

  /**
  * Displays new user registeration page.
  */
  public static function create() {
    if(self::get_user_logged_in()) {
      Redirect::to('/player', array('message' => 'Olet jo kirjautunut sisään.'));
    } else {
      $courses = Course::all();
      View::make('player/new.html', array('courses' => $courses));
    }
  }

  /**
  * Displays edit user page for selected user.
  *
  * @param int $id Id of selected user.
  */
  public static function edit($id) {
    if(self::get_user_logged_in()->id != $id && !self::is_admin()) {
      Redirect::to('/', array('error' => 'Sinulla ei ole oikeuksia muokata tämän käyttäjän tietoja.'));
    } else {
      $player = Player::find($id);
      $courses = Course::all();
      View::make('player/edit.html', array('player' => $player, 'courses' => $courses));
    }
  }

  /**
  * Attempts to store a newly created user.
  */
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

  /**
  * Attempts to update an edited users information.
  *
  * @param int $id Id of edited user.
  */
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

  /**
  * Attempts to destroy selected user.
  *
  * @param int $id Id of user to be deleted.
  */
  public static function destroy($id) {
    $player = new Player(array('id' => $id));
    $player->destroy();

    Redirect::to('/', array('message' => 'Käyttäjä poistettu.'));
  }
}
