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
    $data = unserialize(file_get_contents(__DIR__ . '/../data/votes.dat'));
    $data[$genre] ?: array();
    if ( ! isset($data[$genre][$id])) {
        $entry = array(
            'id' => $id,
            'title' => $title,
            'uri' => $uri,
            'votes' => 0
        );
        $data[$genre][$id] = $entry;
    }
    $data[$genre][$id]['votes']++;
    file_put_contents(__DIR__ . '/../data/votes.dat', serialize($data));
    $votes = usort($data[$genre], function($a, $b) {
        return ($a['votes'] < $b['votes']) ? 1 : -1;
    });
    return $app['twig']->render('votes.twig', array('votes' => $data[$genre]));
});

$app->run();
