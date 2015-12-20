<?php

  $routes->get('/', function() {
    HelloWorldController::index();
  });

  $routes->get('/hiekkalaatikko', function() {
    HelloWorldController::sandbox();
  });

  $routes->get('/suunnitelmat/round' function() {
    HelloWorldController::round_list();
  });

  $routes->get('/suunnitelmat/round/1' function() {
    HelloWorldController::round_show();
  });

  $routes->get('/suunnitelmat/login' function() {
    HelloWorldController::login();
  });
