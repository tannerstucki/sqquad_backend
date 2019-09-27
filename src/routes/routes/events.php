<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");

// Get All Events
$app->get('/api/events', function(Request $request, Response $response){
        $sql = "SELECT * FROM Event";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->query($sql);
            $events = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo json_encode($events);
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

// Get Single Event
$app->get('/api/events/{id}', function(Request $request, Response $response){
        $id = $request->getAttribute('id');
        
        $sql = "SELECT * FROM Event WHERE id = '$id'";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->query($sql);
            $event = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo json_encode($event);
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

// Get All Events for a Squad
$app->get('/api/events/squad/{squad_id}', function(Request $request, Response $response){
        $squad_id = $request->getAttribute('squad_id');
        
        $sql = "SELECT * FROM Event WHERE squad_id = '$squad_id'
ORDER BY start ASC, end ASC";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->query($sql);
            $event = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo json_encode($event);
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

// Get All Events for a Squad between two dates
$app->get('/api/events/squad_date/{squad_id}&{start}&{end}', function(Request $request, Response $response){
        $squad_id = $request->getAttribute('squad_id');
        $start = $request->getAttribute('start');
        $end = $request->getAttribute('end');
        
        $sql = "SELECT * 
FROM Event
WHERE start >= '$start'
AND end <= '$end'
AND squad_id = '$squad_id'
ORDER BY start ASC, end ASC";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->query($sql);
            $events = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo json_encode($events);
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

// Get All Events for a User
$app->get('/api/events/user/{user_id}', function(Request $request, Response $response){
        $user_id = $request->getAttribute('user_id');
        
        $sql = "SELECT Event.id AS event_id, Event.title, Event.description, Event.squad_id, Event.start, Event.end, UserSquad.user_id
FROM Event
INNER JOIN UserSquad ON UserSquad.squad_id = Event.squad_id
WHERE UserSquad.user_id = '$user_id'
ORDER BY start ASC, end ASC";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->query($sql);
            $events = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo json_encode($events);
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

// Get All Events for a User between two dates
$app->get('/api/events/user_date/{user_id}&{start}&{end}', function(Request $request, Response $response){
        $user_id = $request->getAttribute('user_id');
        $start = $request->getAttribute('start');
        $end = $request->getAttribute('end');
        
        $sql = "SELECT Event.id AS event_id, Event.title, Event.description, Event.squad_id, Event.start, Event.end, UserSquad.user_id
FROM Event
INNER JOIN UserSquad ON UserSquad.squad_id = Event.squad_id
WHERE start >= '$start'
AND end <= '$end'
AND UserSquad.user_id = '$user_id'
ORDER BY start ASC, end ASC";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->query($sql);
            $events = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo json_encode($events);
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

// Delete Event
$app->delete('/api/events/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $sql = "DELETE FROM Event WHERE id = '$id'";
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $db = null;
        echo '{"notice": {"text": "Event Deleted"}';
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

//Add Event
$app->post('/api/events', function(Request $request, Response $response){
        $id = uniqid('',true);
        $title = $request->getParam('title');
        $description = $request->getParam('description');
        $squad_id = $request->getParam('squad_id');
        $start = $request->getParam('start');
        $end = $request->getParam('end');
        
        $sql = "INSERT INTO Event (id,title,description,squad_id,start,end) VALUES
        (:id,:title,:description,:squad_id,:start,:end)";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->prepare($sql);

            $stmt->bindParam(':id',$id);
            $stmt->bindParam(':title',$title);
            $stmt->bindParam(':description',$description);
            $stmt->bindParam(':squad_id',$squad_id);
            $stmt->bindParam(':start',$start);
            $stmt->bindParam(':end',$end);

            $stmt->execute();
            echo '{"notice":{"text": "Event Added"}';
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

//Update Event
$app->put('/api/events/{id}', function(Request $request, Response $response){
        $id = $request->getAttribute('id');
        $title = $request->getParam('title');
        $description = $request->getParam('description');
        $squad_id = $request->getParam('squad_id');
        $start = $request->getParam('start');
        $end = $request->getParam('end');
        
        $sql = "UPDATE Event SET
                    title         = :title,
                    description   = :description,
                    squad_id      = :squad_id,
                    start         = :start,
                    end           = :end
                WHERE id = '$id'";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->prepare($sql);

            $stmt->bindParam(':title',$title);
            $stmt->bindParam(':description',$description);
            $stmt->bindParam(':squad_id',$squad_id);
            $stmt->bindParam(':start',$start);
            $stmt->bindParam(':end',$end);

            $stmt->execute();
            echo '{"notice":{"text": "Event Updated"}';
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});