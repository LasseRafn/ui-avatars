<?php

require_once __DIR__ . '/vendor/autoload.php';

header('Content-type: image/png');

$avatar = new LasseRafn\InitialAvatarGenerator\InitialAvatar();
$initials = (new LasseRafn\Initials\Initials)->generate($_GET['name'] ?? 'John Doe');

// todo get from cache

$image = $avatar->name($_GET['name'] ?? 'John Doe')
		->length($_GET['length'] ?? 2)
		->fontSize((double) ($_GET['font-size'] ?? 0.5))
		->size((int) ($_GET['size'] ?? 64))
		->background($_GET['background'] ?? '#000')
		->color($_GET['color'] ?? '#fff')
		->generate();

echo $image->stream('png', 100);
