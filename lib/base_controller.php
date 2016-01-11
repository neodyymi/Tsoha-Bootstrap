<?php

  class BaseController{

    public static function get_user_logged_in(){
      // Toteuta kirjautuneen käyttäjän haku tähän
      if(isset($_SESSION['user'])) {
        $player_id = $_SESSION['user'];
        $player = Player::find($player_id);
        if($player == null) {
          $_SESSION = null;
        }
        return $player;
      }
      return null;
    }

    public static function is_admin() {
      $player = Player::find($_SESSION['user']);
      return $player->admin;
    }

    public static function get_is_admin() {
      return is_admin();
    }

    public static function is_moderator($course) {
      $moderators = Moderator::find_by_course($course);
      return in_array(self::get_user_logged_in()->id, $moderators);
    }

    public static function check_logged_in(){
      if(!isset($_SESSION['user'])) {
        Redirect::to('/login', array('message' => 'Kirjaudu ensin sisään!'));
      }
    }

    public static function fix_url($url){
      if ($url && false === strpos($url, '://')) {
      $url = 'http://' . $url;
      }
      return $url;
    }

  }
