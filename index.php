<?php

ini_set('display_errors',1);
error_reporting(E_ALL);

require_once 'vendor/autoload.php';

$github = new \VOLL\Github('AUTH_KEY');

$repo = $github->getRepo('volldigital', 'sezkir');

$count = $github->getRepoCodeCount($repo);

var_dump($count);
