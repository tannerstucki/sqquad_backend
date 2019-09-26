<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

// Get All Tasks
$app->get('/api/tasks', function(Request $request, Response $response){
        $sql = "SELECT * FROM Task";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->query($sql);
            $tasks = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo json_encode($tasks);
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

// Get Single Task
$app->get('/api/tasks/{id}', function(Request $request, Response $response){
        $id = $request->getAttribute('id');
        
        $sql = "SELECT * FROM Task WHERE id = '$id'";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->query($sql);
            $task = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo json_encode($task);
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

// Get All Tasks for a Squad
$app->get('/api/tasks/squad/{squad_id}', function(Request $request, Response $response){
        $squad_id = $request->getAttribute('squad_id');
        
        $sql = "SELECT * FROM Task WHERE squad_id = '$squad_id'";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->query($sql);
            $tasks = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo json_encode($tasks);
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

// Get All Tasks for a User
$app->get('/api/tasks/user/{user_id}', function(Request $request, Response $response){
        $user_id= $request->getAttribute('user_id');
        
        $sql = "SELECT * FROM Task WHERE user_id= '$user_id'";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->query($sql);
            $tasks = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo json_encode($tasks);
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

// Delete Task
$app->delete('/api/tasks/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $sql = "DELETE FROM Task WHERE id = '$id'";
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $db = null;
        echo '{"notice": {"text": "Task Deleted"}';
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

//Add Task
$app->post('/api/tasks', function(Request $request, Response $response){
        $id = uniqid('',true);
        $data = $request->getParam('data');
        $description = $request->getParam('description');
        $user_id = $request->getParam('user_id');
        $squad_id = $request->getParam('squad_id');
        $stage = "new";
        $expire_date = $request->getParam('expire_date');
                
        $sql = "INSERT INTO Task (id,data,description,user_id,squad_id,stage,expire_date) VALUES
        (:id,:data,:description,:user_id,:squad_id,:stage,:expire_date)";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->prepare($sql);

            $stmt->bindParam(':id',$id);
            $stmt->bindParam(':data',$data);
            $stmt->bindParam(':description',$description);
            $stmt->bindParam(':user_id',$user_id);
            $stmt->bindParam(':squad_id',$squad_id);
            $stmt->bindParam(':stage',$stage);
            $stmt->bindParam(':expire_date',$expire_date);

            $stmt->execute();
            echo '{"notice":{"text": "Task Added"}';
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

//Update Task
$app->put('/api/tasks/{id}', function(Request $request, Response $response){
        $id = $request->getAttribute('id');
        $data = $request->getParam('data');
        $description = $request->getParam('description');
        $user_id = $request->getParam('user_id');
        $squad_id = $request->getParam('squad_id');
        $stage = $request->getParam('stage');
        $expire_date= $request->getParam('expire_date');
        
        $sql = "UPDATE Task SET
                    data          = :data,
                    description   = :description,
                    user_id       = :user_id,
                    squad_id      = :squad_id,
                    stage         = :stage,
                    expire_date   = :expire_date
                WHERE id = '$id'";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->prepare($sql);

            $stmt->bindParam(':data',$data);
            $stmt->bindParam(':description',$description);
            $stmt->bindParam(':user_id',$user_id);
            $stmt->bindParam(':squad_id',$squad_id);
            $stmt->bindParam(':stage',$stage);
            $stmt->bindParam(':expire_date',$expire_date);

            $stmt->execute();
            echo '{"notice":{"text": "Task Updated"}';
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});