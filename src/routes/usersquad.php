<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");

// Get All UserSquad
$app->get('/api/usersquad', function(Request $request, Response $response){
        $sql = "SELECT * FROM UserSquad";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->query($sql);
            $usersquad = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo json_encode($usersquad);
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

// Get Single UserSquad
$app->get('/api/usersquad/{id}', function(Request $request, Response $response){
        $id = $request->getAttribute('id');
        
        $sql = "SELECT * FROM UserSquad WHERE id = '$id'";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->query($sql);
            $usersquad = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo json_encode($usersquad);
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

// Delete UserSquad
$app->delete('/api/usersquad/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $sql = "DELETE FROM UserSquad WHERE id = '$id'";
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $db = null;
        echo '{"notice": {"text": "UserSquad Deleted"}';
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

//Add UserSquad
$app->post('/api/usersquad', function(Request $request, Response $response){
        $id = uniqid('',true);
        $user_id = $request->getParam('user_id');
        $squad_id = $request->getParam('squad_id');
        
        $sql = "INSERT INTO UserSquad (id,user_id,squad_id) VALUES
        (:id,:user_id,:squad_id)";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->prepare($sql);

            $stmt->bindParam(':id',$id);
            $stmt->bindParam(':user_id',$user_id);
            $stmt->bindParam(':squad_id',$squad_id);

            $stmt->execute();
            echo '{"notice":{"text": "UserSquad Added"}';
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

//Update UserSquad
$app->put('/api/usersquad/{id}', function(Request $request, Response $response){
        $id = $request->getAttribute('id');
        $user_id = $request->getParam('user_id');
        $squad_id = $request->getParam('squad_id');
        
        $sql = "UPDATE UserSquad SET
                    user_id       = :user_id,
                    squad_id      = :squad_id
                WHERE id = '$id'";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->prepare($sql);

            $stmt->bindParam(':user_id',$user_id);
            $stmt->bindParam(':squad_id',$squad_id);

            $stmt->execute();
            echo '{"notice":{"text": "UserSquad Updated"}';
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});