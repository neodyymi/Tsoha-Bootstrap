<?php

  class HelloWorldController extends BaseController{

    public static function index(){
      // make-metodi renderöi app/views-kansiossa sijaitsevia tiedostoja
   	 //  View::make('home.html');
     echo 'Tämä on etusivu!';
    }

    public static function sandbox(){
      // Testaa koodiasi täällä
      // echo 'Hello World!';
      View::make('helloworld.html');
    }

    public static function round_list() {
      View::make('suunnitelmat/round_list.html');
    }

    public static function round_show() {
      View::make('suunnitelmat/round_show.html');
    }

    public static function login() {
      View::make('suunnitelmat/login.html');
    }
    
    public static function round_edit() {
      View::make('suunnitelmat/round_edit.html');
    }

  }
