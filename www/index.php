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
    $id = $request->get('id');
    $title = $request->get('title');
    $uri = $request->get('uri');
    $votes = array();
    if ( ! isset($votes[$id])) {
        $entry = array(
            'id' => $id,
            'title' => $title,
            'uri' => $uri,
            'votes' => 0
        );
        $votes[$id] = $entry;
    }
    $votes[$id]['votes']++;
    return $app['twig']->render('votes.twig', array('votes' => $votes));
});

$app->run();
