<?php

require_once __DIR__ . '/vendor/autoload.php';

header('Content-type: image/png');

$avatar = new LasseRafn\InitialAvatarGenerator\InitialAvatar();


$name = $_GET['name'] ?? 'John Doe';
$length = (int) ( $_GET['length'] ?? 2 );
$size = (int) ( $_GET['size'] ?? 64 );
$fontSize = (double) ($_GET['font-size'] ?? 0.5);
$background = $_GET['background'] ?? '#000';
$color = $_GET['color'] ?? '#fff';


$initials = (new LasseRafn\Initials\Initials)->generate($name);

// todo get from cache

$image = $avatar->name($name)
		->length($length)
		->fontSize($fontSize)
		->size($size)
		->background($background)
		->color($color)
		->generate();

echo $image->stream('png', 100);
