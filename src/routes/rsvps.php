<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

// Get All Rsvps
$app->get('/api/rsvps', function(Request $request, Response $response){
        $sql = "SELECT * FROM Rsvp";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->query($sql);
            $rsvps = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo json_encode($rsvps);
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

// Get Single Rsvp
$app->get('/api/rsvps/{id}', function(Request $request, Response $response){
        $id = $request->getAttribute('id');
        
        $sql = "SELECT * FROM Rsvp WHERE id = '$id'";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->query($sql);
            $rsvp = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo json_encode($rsvp);
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

// Get All Rsvps for an Event
$app->get('/api/rsvps/event/{event_id}', function(Request $request, Response $response){
        $event_id = $request->getAttribute('event_id');
        
        $sql = "SELECT Rsvp.*, User.first_name, User.last_name
FROM Rsvp
INNER JOIN User
ON User.id = Rsvp.user_id
WHERE Rsvp.event_id = '$event_id'";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->query($sql);
            $rsvps = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo json_encode($rsvps);
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

// Delete Rsvp
$app->delete('/api/rsvps/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $sql = "DELETE FROM Rsvp WHERE id = '$id'";
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $db = null;
        echo '{"notice": {"text": "Rsvp Deleted"}';
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

//Add Rsvp
$app->post('/api/rsvps', function(Request $request, Response $response){
        $id = uniqid('',true);
        $event_id = $request->getParam('event_id');
        $user_id = $request->getParam('user_id');
        $status = $request->getParam('status');
        
        $sql = "INSERT INTO Rsvp (id,event_id,user_id,status) VALUES
        (:id,:event_id,:user_id,:status)";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->prepare($sql);

            $stmt->bindParam(':id',$id);
            $stmt->bindParam(':event_id',$event_id);
            $stmt->bindParam(':user_id',$user_id);
            $stmt->bindParam(':status',$status);

            $stmt->execute();
            echo '{"notice":{"text": "Rsvp Added"}';
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

//Update Rsvp
$app->put('/api/rsvps/{id}', function(Request $request, Response $response){
        $id = $request->getAttribute('id');
        $event_id= $request->getParam('event_id');
        $user_id = $request->getParam('user_id');
        $status = $request->getParam('status');
        
        $sql = "UPDATE Rsvp SET
                    event_id      = :event_id,
                    user_id       = :user_id,
                    status        = :status
                WHERE id = '$id'";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->prepare($sql);

            $stmt->bindParam(':event_id',$event_id);
            $stmt->bindParam(':user_id',$user_id);
            $stmt->bindParam(':status',$status);

            $stmt->execute();
            echo '{"notice":{"text": "Rsvp Updated"}';
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});