<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

// Get All Timers
$app->get('/api/timers', function(Request $request, Response $response){
        $sql = "SELECT * FROM Timer";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->query($sql);
            $timers = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo json_encode($timers);
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

// Get Single Timer
$app->get('/api/timers/{id}', function(Request $request, Response $response){
        $id = $request->getAttribute('id');
        
        $sql = "SELECT * FROM Timer WHERE id = '$id'";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->query($sql);
            $timer = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo json_encode($timer);
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

// Get All Timers for a Squad
$app->get('/api/timers/squad/{id}', function(Request $request, Response $response){
        $id = $request->getAttribute('id');
        
        $sql = "SELECT * 
FROM Timer
WHERE Timer.squad_id = '$id'
ORDER BY Timer.id DESC";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->query($sql);
            $timer = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo json_encode($timer);
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

// Get All Timers for a User
$app->get('/api/timers/user/{id}', function(Request $request, Response $response){
        $id = $request->getAttribute('id');
        
        $sql = "SELECT Timer.id AS timer_id, Timer.title, Timer.description, Timer.squad_id, UserSquad.user_id
FROM Timer
INNER JOIN UserSquad ON UserSquad.squad_id = Timer.squad_id
WHERE UserSquad.user_id = '$id'
ORDER BY Timer.id DESC";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->query($sql);
            $timer = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo json_encode($timer);
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

// Timer Selection Page
$app->get('/api/timers/selection/{id}', function(Request $request, Response $response){
        $id = $request->getAttribute('id');
        
        $sql = "SELECT *
FROM Timer
INNER JOIN TimerResponse
ON TimerResponse.timer_id = Timer.id
WHERE Timer.id = '$id'  
ORDER BY TimerResponse.date ASC, TimerResponse.time_interval ASC";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->query($sql);
            $timer = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo json_encode($timer);
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

// Timer Results Page
$app->get('/api/timers/result/{id}', function(Request $request, Response $response){
        $id = $request->getAttribute('id');
        
        $sql = "SELECT *, (SELECT COUNT(*) FROM UserSquad WHERE UserSquad.squad_id = (SELECT Timer.squad_id)) AS total_members
FROM Timer
INNER JOIN TimerResponse
ON TimerResponse.timer_id = Timer.id
WHERE Timer.id = 1
ORDER BY TimerResponse.votes DESC, TimerResponse.date ASC, TimerResponse.time_interval ASC";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->query($sql);
            $timer = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo json_encode($timer);
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

// Delete Timer
$app->delete('/api/timers/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $sql = "DELETE FROM Timer WHERE id = '$id'";
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $db = null;
        echo '{"notice": {"text": "Timer Deleted"}';
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

//Add Timer
$app->post('/api/timers', function(Request $request, Response $response){
        $id = uniqid('',true);
        $title = $request->getParam('title');
        $description = $request->getParam('description');
        $squad_id = $request->getParam('squad_id');
        
        $sql = "INSERT INTO Timer (id,title,description,squad_id) VALUES
        (:id,:title,:description,:squad_id)";

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

            $stmt->execute();
            echo '{"notice":{"text": "Timer Added"}';
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

//Update Timer
$app->put('/api/timers/{id}', function(Request $request, Response $response){
        $id = $request->getAttribute('id');
        $title = $request->getParam('title');
        $description = $request->getParam('description');
        $squad_id = $request->getParam('squad_id');
        
        $sql = "UPDATE Timer SET
                    title       = :title,
                    description = :description,
                    squad_id    = :squad_id
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

            $stmt->execute();
            echo '{"notice":{"text": "Timer Updated"}';
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});