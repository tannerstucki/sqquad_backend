<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

// Get All TimerResponses
$app->get('/api/timerresponses', function(Request $request, Response $response){
        $sql = "SELECT * FROM TimerResponse";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->query($sql);
            $timerresponses = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo json_encode($timerresponses);
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

// Get Single TimerResponse
$app->get('/api/timerresponses/{id}', function(Request $request, Response $response){
        $id = $request->getAttribute('id');
        
        $sql = "SELECT * FROM TimerResponse WHERE id = '$id'";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->query($sql);
            $timerresponse = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo json_encode($timerresponse);
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

// Delete TimerResponse
$app->delete('/api/timerresponses/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $sql = "DELETE FROM TimerResponse WHERE id = '$id'";
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $db = null;
        echo '{"notice": {"text": "TimerResponse Deleted"}';
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

//Add TimerResponse
$app->post('/api/timerresponses', function(Request $request, Response $response){
        $id = uniqid('',true);
        $timer_id = $request->getParam('timer_id');
        $date = $request->getParam('date');
        $time_interval = $request->getParam('time_interval');
        $suggested = $request->getParam('suggested');
        $votes = 0;
                
        $sql = "INSERT INTO TimerResponse (id,timer_id,date,time_interval,suggested,votes) VALUES
        (:id,:timer_id,:date,:time_interval,:suggested,:votes)";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->prepare($sql);

            $stmt->bindParam(':id',$id);
            $stmt->bindParam(':timer_id',$timer_id);
            $stmt->bindParam(':date',$date);
            $stmt->bindParam(':time_interval',$time_interval);
            $stmt->bindParam(':suggested',$suggested);
            $stmt->bindParam(':votes',$votes);

            $stmt->execute();
            echo '{"notice":{"text": "TimerResponse Added"}';
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

//Update TimerResponse
$app->put('/api/timerresponses/{id}', function(Request $request, Response $response){
        $id = $request->getAttribute('id');
        $timer_id = $request->getParam('timer_id');
        $date = $request->getParam('date');
        $time_interval = $request->getParam('time_interval');
        $suggested = $request->getParam('suggested');
        $votes = $request->getParam('votes');
        
        $sql = "UPDATE TimerResponse SET
                    timer_id            = :timer_id,
                    date                = :date,
                    time_interval       = :time_interval,
                    suggested           = :suggested,
                    votes               = :votes
                WHERE id = '$id'";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->prepare($sql);

            $stmt->bindParam(':timer_id',$timer_id);
            $stmt->bindParam(':date',$date);
            $stmt->bindParam(':time_interval',$time_interval);
            $stmt->bindParam(':suggested',$suggested);
            $stmt->bindParam(':votes',$votes);

            $stmt->execute();
            echo '{"notice":{"text": "TimerResponse Updated"}';
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});