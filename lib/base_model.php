<?php

  class BaseModel{
    // "protected"-attribuutti on käytössä vain luokan ja sen perivien luokkien sisällä
    protected $validators;

    public function __construct($attributes = null){
      // Käydään assosiaatiolistan avaimet läpi
      foreach($attributes as $attribute => $value){
        // Jos avaimen niminen attribuutti on olemassa...
        if(property_exists($this, $attribute)){
          // ... lisätään avaimen nimiseen attribuuttin siihen liittyvä arvo
          $this->{$attribute} = $value;
        }
      }
    }

    public function errors(){
      // Lisätään $errors muuttujaan kaikki virheilmoitukset taulukkona
      $errors = array();

      foreach($this->validators as $validator){
        // Kutsu validointimetodia tässä ja lisää sen palauttamat virheet errors-taulukkoon
        $errors = array_merge($errors, $this->{$validator}());
      }

      return $errors;
    }

    public function validate_string_length($field, $string, $min, $max) {
      $errors = array();
      if(!is_string($string)) {
        $errors[] = $field . " täytyy olla merkkijono.";
      }
      if(strlen($string) < $min && $min!= 0) {
        $errors[] = $field . " täytyy olla vähintään " . $min . " merkkiä pitkä.";
      }
      if(strlen($string) > $max && $max != 0) {
        $errors[] = $field . " saa olla korkeintaan " . $max . " merkkiä pitkä.";
      }
      return $errors;
    }

    public function validate_int($field, $int) {
      $errors = array();
      if(!is_int($int)) {
        $errors[] = $field . " täytyy olla kokonaisluku.";
      }
      return $errors;
    }

    public function validate_date($field, $date) {
      $errors = array();
      $date = date_create($date);
      if(!checkdate($date->format("n"), $date->format("d"), $date->format("y"))) {
        $errors[] = $field . " täytyy olla päivämäärä.";
      }
      return $errors;
    }

    public function validate_url_format($field, $url) {
      $errors = array();
      if($url && !filter_var($url, FILTER_VALIDATE_URL)) {
        $errors[] = $field . " on väärää muotoa.";
      }
      if($url && !(parse_url($url, PHP_URL_SCHEME) == 'http' || parse_url($url, PHP_URL_SCHEME) == 'https')) {
        $errors[] = $field . " hyväksyy vain http tai https protokollan osoitteen.";
      }

      return $errors;
    }

    public function validate_password($password, $verify, $newPassword) {
      $errors = array();
      if($password != $verify) {
        $errors[] = "Et toistanut samaa salasanaa kahteen kertaan.";
      }
      // if($newPassword) {
      //   $errors = array_merge(validate_string_length("Salasana", $password, 5, 255));
      // }
      // Tänne voisi lisätä myös salasanan kompleksisuuteen liittyviä ehtoja.
      // if(!preg_match("#[A-Z]+#",$password) {
      //   $errors[] = "Salasanan täytyy sisältää vähintään 1 iso kirjain."
      // }
      // if(!preg_match("#[a-z]+#",$password) {
      //   $errors[] = "Salasanan täytyy sisältää vähintään 1 pieni kirjain."
      // }

      return $errors;
    }
  }
