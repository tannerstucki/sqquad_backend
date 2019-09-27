<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");

// Get All Tokens
$app->get('/api/tokens', function(Request $request, Response $response){
        $sql = "SELECT * FROM Token";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->query($sql);
            $tokens = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo json_encode($tokens);
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

// Get Single Token
$app->get('/api/tokens/{id}', function(Request $request, Response $response){
        $id = $request->getAttribute('id');
        
        $sql = "SELECT * FROM Token WHERE id = '$id'";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->query($sql);
            $token = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo json_encode($token);
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

// Delete Token
$app->delete('/api/tokens/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $sql = "DELETE FROM Token WHERE id = '$id'";
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $db = null;
        echo '{"notice": {"text": "Token Deleted"}';
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

//Add Token
$app->post('/api/tokens', function(Request $request, Response $response){
        $id = uniqid('',true);
        $user_id = $request->getParam('user_id');
        $live = $request->getParam('live');
        
        $sql = "INSERT INTO Token (id,user_id,live) VALUES
        (:id,:user_id,:live)";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->prepare($sql);

            $stmt->bindParam(':id',$id);
            $stmt->bindParam(':user_id',$user_id);
            $stmt->bindParam(':live',$live);

            $stmt->execute();
            echo '{"notice":{"text": "Token Added"}';
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

//Update Token
$app->put('/api/tokens/{id}', function(Request $request, Response $response){
        $id = $request->getAttribute('id');
        $user_id = $request->getParam('user_id');
        $live = $request->getParam('live');
        
        $sql = "UPDATE Token SET
                    user_id       = :user_id,
                    live          = :live
                WHERE id = '$id'";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->prepare($sql);

            $stmt->bindParam(':user_id',$user_id);
            $stmt->bindParam(':live',$live);

            $stmt->execute();
            echo '{"notice":{"text": "Token Updated"}';
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});