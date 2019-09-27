<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");

// Get All PollResponses
$app->get('/api/pollresponses', function(Request $request, Response $response){
        $sql = "SELECT * FROM PollResponse";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->query($sql);
            $pollresponses = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo json_encode($pollresponses);
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

// Get Single PollResponse
$app->get('/api/pollresponses/{id}', function(Request $request, Response $response){
        $id = $request->getAttribute('id');
        
        $sql = "SELECT * FROM PollResponse WHERE id = '$id'";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->query($sql);
            $pollresponse = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo json_encode($pollresponse);
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

// Delete PollResponse
$app->delete('/api/pollresponses/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $sql = "DELETE FROM PollResponse WHERE id = '$id'";
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $db = null;
        echo '{"notice": {"text": "PollResponse Deleted"}';
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

//Add PollResponse
$app->post('/api/pollresponses', function(Request $request, Response $response){
        $id = uniqid('',true);
        $data = $request->getParam('data');
        $poll_id = $request->getParam('poll_id');
        $votes = 0;
                
        $sql = "INSERT INTO PollResponse (id,data,poll_id,votes) VALUES
        (:id,:data,:poll_id,:votes)";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->prepare($sql);

            $stmt->bindParam(':id',$id);
            $stmt->bindParam(':data',$data);
            $stmt->bindParam(':poll_id',$poll_id);
            $stmt->bindParam(':votes',$votes);

            $stmt->execute();
            echo '{"notice":{"text": "PollResponse Added"}';
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

//Update PollResponse
$app->put('/api/pollresponses/{id}', function(Request $request, Response $response){
        $id = $request->getAttribute('id');
        $data = $request->getParam('data');
        $poll_id = $request->getParam('poll_id');
        $votes = $request->getParam('votes');
        
        $sql = "UPDATE PollResponse SET
                    data                 = :data,
                    poll_id              = :poll_id,
                    votes                = :votes
                WHERE id = '$id'";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->prepare($sql);

            $stmt->bindParam(':data',$data);
            $stmt->bindParam(':poll_id',$poll_id);
            $stmt->bindParam(':votes',$votes);

            $stmt->execute();
            echo '{"notice":{"text": "PollResponse Updated"}';
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});