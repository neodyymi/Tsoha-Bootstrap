<?php
class PlayerController extends BaseController{

  public static function login() {
    View::make('player/login.html');
  }

  public static function handle_login() {
    $params = $_POST;
    $user = Player::authenticate($params['username'], $params['password']);
    if(!$user) {
      View::make('player/login.html', array('error' => 'Väärä käyttäjätunnus tai salasana.', 'username' => $params['username']));
    } else {
      $_SESSION['user'] = $user->id;

      Redirect::to('/', array('message' => 'Kirjautunut käyttäjänä ' . $user->username));
    }
  }

  public static function list_all() {
    $user = self::get_user_logged_in();
    if($user) {
      $scores = Score::find_by_player($user->id);
      View::make('player/show.html', array('scores' => $scores));
    } else {
      View::make('player/login.html', array('error' => 'Kirjaudu sisään nähdäksesi oman sivusi.'));
    }

  }
}
