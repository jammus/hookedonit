<?php

ini_set('display_errors', 'on');

require_once __DIR__ . '/../vendor/autoload.php'; 

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

$app = new Application(); 
$app['debug'] = true;

$votes = new Votes(__DIR__ . '/../data/votes.dat');

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));

$app->get('/', function (Application $app) use($votes) {
    $ranking = $votes->votesFor('indie');
    return $app['twig']->render('index.twig', array('votes' => $ranking));
});

$app->post('/vote', function (Request $request, Application $app) use($votes) {
    $genre = $request->get('genre');
    $id = $request->get('id');
    $title = $request->get('title');
    $uri = $request->get('uri');
    $ranking = $votes->vote($genre, $id, $title, $uri);
    return $app['twig']->render('votes.twig', array('votes' => $ranking));
});

$app->run();

class Votes
{
    private $filename;

    private $data;

    public function __construct($filename)
    {
        $this->filename = $filename;
        $this->data = unserialize(file_get_contents($this->filename));
    }

    public function vote($genre, $id, $title, $uri)
    {
        $this->data[$genre] ?: array();
        if ( ! isset($this->data[$genre][$id])) {
            $entry = array(
                'id' => $id,
                'title' => $title,
                'uri' => $uri,
                'votes' => 0
            );
            $this->data[$genre][$id] = $entry;
        }
        $this->data[$genre][$id]['votes']++;
        file_put_contents(__DIR__ . '/../data/votes.dat', serialize($this->data));
        return $this->votesFor($genre);
    }

    public function votesFor($genre)
    {
        $votes = $this->data[$genre];
        usort($votes, function($a, $b) {
            return ($a['votes'] < $b['votes']) ? 1 : -1;
        });
        return array_slice($votes, 0, 10);
    }
}
