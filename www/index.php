<?php

ini_set('display_errors', 'on');

require_once __DIR__ . '/../vendor/autoload.php'; 

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

$app = new Application(); 
$app['debug'] = true;

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));

$app->get('/', function (Application $app) {
    return $app['twig']->render('index.twig');
});

$app->post('/vote', function (Request $request, Application $app) {
    $genre = $request->get('genre');
    $track = $request->get('track');
    return $app->json(array($genre, $track));
});

$app->run();
