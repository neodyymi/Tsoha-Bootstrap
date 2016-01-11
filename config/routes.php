<?php

  function check_logged_in() {
    BaseController::check_logged_in();
  }

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

  $routes->get('/course/:id/add', 'check_logged_in', function($id) {
    RoundController::create($id);
  });

  $routes->get('/course/:id/edit', 'check_logged_in', function($id) {
    CourseController::edit($id);
  });

  $routes->post('/course/:id/edit', 'check_logged_in', function($id) {
    CourseController::update($id);
  });

  $routes->post('/course/:id/destroy', 'check_logged_in', function($id) {
    CourseController::destroy($id);
  });

## Round
  $routes->get('/round', function() {
    RoundController::list_all();
  });

  $routes->post('/round', function() {
    RoundController::store();
  });

  $routes->get('/round/new', function() {
    RoundController::create();
  });

  $routes->post('/round/new', function() {
    RoundController::create();
  });

  $routes->get('/round/:id', function($id) {
    RoundController::show($id);
  });

  $routes->get('/round/:id/edit', function($id) {
    RoundController::edit($id);
  });

  $routes->post('/round/:id/edit', function($id) {
    RoundController::update($id);
  });

  $routes->post('/round/:id/destroy', function($id) {
    RoundController::destroy($id);
  });

## User / Player / Login
  $routes->get('/login', function(){
    PlayerController::login();
  });
  $routes->post('/login', function(){
    PlayerController::handle_login();
  });
  $routes->post('/logout', function(){
    PlayerController::logout();
  });
  $routes->get('/player', 'check_logged_in', function() {
    PlayerController::list_all();
  });
  $routes->get('/player/new', function() {
    PlayerController::create();
  });
  $routes->post('/player/new', function() {
    PlayerController::store();
  });
  $routes->get('/player/:id', function($id) {
    PlayerController::show($id);
  });
  $routes->get('/player/:id/edit', function($id) {
    PlayerController::edit($id);
  });
  $routes->post('/player/:id/edit', function($id) {
    PlayerController::update($id);
  });
  $routes->post('/player/:id/destroy', function($id) {
    PlayerController::destroy($id);
  });
