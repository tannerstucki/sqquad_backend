<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

// Get All UserMessage
$app->get('/api/usermessage', function(Request $request, Response $response){
        $sql = "SELECT * FROM UserMessage";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->query($sql);
            $usermessage = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo json_encode($usermessage);
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

// Get Single UserMessage
$app->get('/api/usermessage/{id}', function(Request $request, Response $response){
        $id = $request->getAttribute('id');
        
        $sql = "SELECT * FROM UserMessage WHERE id = '$id'";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->query($sql);
            $usermessage = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo json_encode($usermessage);
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

// Delete UserMessage
$app->delete('/api/usermessage/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $sql = "DELETE FROM UserMessage WHERE id = '$id'";
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $db = null;
        echo '{"notice": {"text": "UserMessage Deleted"}';
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

//Add UserMessage
$app->post('/api/usermessage', function(Request $request, Response $response){
        $id = uniqid('',true);
        $user_id = $request->getParam('user_id');
        $message_id = $request->getParam('message_id');
        
        $sql = "INSERT INTO UserMessage (id,user_id,message_id) VALUES
        (:id,:user_id,:message_id)";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->prepare($sql);

            $stmt->bindParam(':id',$id);
            $stmt->bindParam(':user_id',$user_id);
            $stmt->bindParam(':message_id',$message_id);

            $stmt->execute();
            echo '{"notice":{"text": "UserMessage Added"}';
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

//Update UserMessage
$app->put('/api/usermessage/{id}', function(Request $request, Response $response){
        $id = $request->getAttribute('id');
        $user_id = $request->getParam('user_id');
        $message_id = $request->getParam('message_id');
        
        $sql = "UPDATE UserMessage SET
                    user_id         = :user_id,
                    message_id      = :message_id
                WHERE id = '$id'";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->prepare($sql);

            $stmt->bindParam(':user_id',$user_id);
            $stmt->bindParam(':message_id',$message_id);

            $stmt->execute();
            echo '{"notice":{"text": "UserMessage Updated"}';
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});