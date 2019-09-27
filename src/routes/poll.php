<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");

// Get All Polls
$app->get('/api/polls', function(Request $request, Response $response){
        $sql = "SELECT * FROM Poll";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->query($sql);
            $polls = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo json_encode($polls);
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

// Get Single Poll
$app->get('/api/polls/{id}', function(Request $request, Response $response){
        $id = $request->getAttribute('id');
        
        $sql = "SELECT * FROM Poll WHERE id = '$id'";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->query($sql);
            $poll = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo json_encode($poll);
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

// Get Poll and its Responses
$app->get('/api/polls/responses/{id}', function(Request $request, Response $response){
        $id = $request->getAttribute('id');
        
        $sql = "SELECT Poll.id as poll_id, Poll.data as poll_data, Poll.squad_id, Poll.status, PollResponse.id as poll_response_id, PollResponse.data as poll_response_data, PollResponse.votes, ROUND((PollResponse.votes / (Select SUM(PollResponse.votes) From PollResponse WHERE PollResponse.poll_id = '$id')* 100), 0) as percent
FROM Poll
INNER JOIN PollResponse
ON PollResponse.poll_id = Poll.id
WHERE Poll.id = '$id'";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->query($sql);
            $poll = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo json_encode($poll);
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

// Get All Polls for a Squad
$app->get('/api/polls/squad/{id}', function(Request $request, Response $response){
        $id = $request->getAttribute('id');
        
        $sql = "SELECT *
FROM Poll
WHERE Poll.squad_id = '$id'";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->query($sql);
            $poll = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo json_encode($poll);
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

// Get All Polls for a User
$app->get('/api/polls/user/{id}', function(Request $request, Response $response){
        $id = $request->getAttribute('id');
        
        $sql = "SELECT Poll.id AS poll_id, Poll.data, Poll.squad_id, Poll.status, UserSquad.user_id
FROM Poll
INNER JOIN UserSquad ON UserSquad.squad_id = Poll.squad_id
WHERE UserSquad.user_id = '$id'";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->query($sql);
            $poll = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo json_encode($poll);
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

// Delete Poll
$app->delete('/api/polls/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $sql = "DELETE FROM Poll WHERE id = '$id'";
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $db = null;
        echo '{"notice": {"text": "Poll Deleted"}';
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

//Add Poll
$app->post('/api/polls', function(Request $request, Response $response){
        $id = uniqid('',true);
        $data = $request->getParam('data');
        $squad_id = $request->getParam('squad_id');
        $status = $request->getParam('status');
        
        $sql = "INSERT INTO Poll (id,data,squad_id,status) VALUES
        (:id,:data,:squad_id,:status)";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->prepare($sql);

            $stmt->bindParam(':id',$id);
            $stmt->bindParam(':data',$data);
            $stmt->bindParam(':squad_id',$squad_id);
            $stmt->bindParam(':status',$status);

            $stmt->execute();
            echo '{"notice":{"text": "Poll Added"}';
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

//Update Poll
$app->put('/api/polls/{id}', function(Request $request, Response $response){
        $id = $request->getAttribute('id');
        $data = $request->getParam('data');
        $squad_id = $request->getParam('squad_id');
        $status = $request->getParam('status');
        
        $sql = "UPDATE Poll SET
                    data        = :data,
                    squad_id    = :squad_id,
                    status      = :status
                WHERE id = '$id'";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->prepare($sql);

            $stmt->bindParam(':data',$data);
            $stmt->bindParam(':squad_id',$squad_id);
            $stmt->bindParam(':status',$status);

            $stmt->execute();
            echo '{"notice":{"text": "Poll Updated"}';
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});