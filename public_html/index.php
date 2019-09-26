<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';
require '../src/config/db.php';

//Routes
require '../src/routes/squads.php';
require '../src/routes/users.php';
require '../src/routes/usersquad.php';
require '../src/routes/messages.php';
require '../src/routes/usermessage.php';
require '../src/routes/poll.php';
require '../src/routes/pollresponse.php';
require '../src/routes/tasks.php';
require '../src/routes/timer.php';
require '../src/routes/timerresponse.php';
require '../src/routes/tokens.php';
require '../src/routes/invites.php';
require '../src/routes/events.php';
require '../src/routes/rsvps.php';

$app->run();