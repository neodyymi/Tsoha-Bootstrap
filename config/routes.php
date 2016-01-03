<?php

  $routes->get('/', function() {
    CourseController::list_all();
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

## Course
  $routes->get('/course', function() {
    CourseController::list_all();
  });

  $routes->post('/course', function() {
    CourseController::store();
  });

  $routes->get('/course/new', function() {
    CourseController::create();
  });

  $routes->get('/course/:id', function($id) {
    CourseController::show($id);
  });

  $routes->get('/course/:id/edit', function($id) {
    CourseController::edit($id);
  });

  $routes->post('/course/:id/edit', function($id) {
    CourseController::update($id);
  });

  $routes->post('/course/:id/destroy', function($id) {
    CourseController::destroy($id);
  });

## User / Player / Login
  $routes->get('/login', function(){
    PlayerController::login();
  });

  $routes->post('/login', function(){
    PlayerController::handle_login();
  });

  $routes->get('/player', function() {
    PlayerController::list_all();
  });
