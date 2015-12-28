<?php

  $routes->get('/', function() {
    HelloWorldController::index();
  });

  $routes->get('/hiekkalaatikko', function() {
    HelloWorldController::sandbox();
  });

  $routes->get('/suunnitelmat/round', function() {
    HelloWorldController::round_list();
  });

  $routes->get('/suunnitelmat/round/1', function() {
    HelloWorldController::round_show();
  });

  $routes->get('/suunnitelmat/round/edit/1', function() {
    HelloWorldController::round_edit();
  });

  $routes->get('/suunnitelmat/login', function() {
    HelloWorldController::login();
  });

  $routes->get('/course', function() {
    CourseController::list_all();
  });

  $routes->post('/course', function() {
    CourseController::store();
  });

  $routes->get('/course/new', function() {
    CourseController::create();
  });

  $routes->get('/course/:id/edit', function($id) {
    CourseController::edit($id);
  });

  $routes->get('/course/:id', function($id) {
    CourseController::show($id);
  });
