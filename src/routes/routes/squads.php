<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");

$app = new \Slim\App;

// Get All Squads
$app->get('/api/squads', function(Request $request, Response $response){
        $sql = "SELECT * FROM Squad";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->query($sql);
            $squads = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo json_encode($squads);
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

// Get All Squads for a User
$app->get('/api/squads/user/{user_id}', function(Request $request, Response $response){
        $user_id = $request->getAttribute('user_id');

        $sql = "SELECT * FROM Squad WHERE id IN (SELECT squad_id FROM UserSquad WHERE user_id = '$user_id')";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->query($sql);
            $squads = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            if ($squads != null)
               echo json_encode($squads);
            else
               echo '{"error": {"text": "Sorry, you currently have no squads. Create or join one to collab!"}';
        }catch(PDOException $e){
            echo '{"error": {"technical": '.$e->getMessage().', "text": "Sorry, you currently have no squads. Create or join one to collab!"}';
        }
});

// Get All Members of a Squad
$app->get('/api/squads/members/{squad_id}', function(Request $request, Response $response){
        //$headers = apache_request_headers();
        //$squad_id = $headers['squad_id'];
        $squad_id = $request->getAttribute('squad_id');

        $sql = "SELECT * FROM User WHERE id IN (SELECT user_id FROM UserSquad WHERE squad_id = '$squad_id')";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->query($sql);
            $squads = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            if ($squads != null)
               echo json_encode($squads);
            else
               echo '{"error": {"text": "Sorry, this squad has no members. Invite members to collab!"}';
        }catch(PDOException $e){
            echo '{"error": {"technical": '.$e->getMessage().', "text": "Sorry, this squad has no members. Invite members to collab!"}';
        }
});

// Get Single Squad
$app->get('/api/squads/{id}', function(Request $request, Response $response){
        $id = $request->getAttribute('id');
        
        $sql = "SELECT * FROM Squad WHERE id = '$id'";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->query($sql);
            $squad = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo json_encode($squad);
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

// Delete Squad
$app->delete('/api/squads/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $sql = "DELETE FROM Squad WHERE id = '$id'";
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $db = null;
        echo '{"notice": {"text": "Squad Deleted"}';
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

//Create Squad
$app->post('/api/squads', function(Request $request, Response $response){
        $squad_id = uniqid('',true);
        $user_squad_id = uniqid('',true);
        $name = $request->getParam('name');
        $description = $request->getParam('description');
        $city = $request->getParam('city');
        $state = $request->getParam('state');
        $country = $request->getParam('country');
        $organizer_id = $request->getParam('organizer_id');
                
        $sql_squad = "INSERT INTO Squad (id,name,description,city,state,country,organizer_id) VALUES
        (:squad_id,:name,:description,:city,:state,:country,:organizer_id)";

        $sql_user_squad = "INSERT INTO `UserSquad`(`id`, `user_id`, `squad_id`) VALUES (:user_squad_id,:organizer_id,:squad_id)";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->prepare($sql_squad);

            $stmt->bindParam(':squad_id',$squad_id);
            $stmt->bindParam(':name',$name);
            $stmt->bindParam(':description',$description);
            $stmt->bindParam(':city',$city);
            $stmt->bindParam(':state',$state);
            $stmt->bindParam(':country',$country);
            $stmt->bindParam(':organizer_id',$organizer_id);

            $stmt->execute();
            echo '{"notice":{"text": "Squad successfully created."}';
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->prepare($sql_user_squad);

            $stmt->bindParam(':user_squad_id',$user_squad_id);
            $stmt->bindParam(':organizer_id',$organizer_id);
            $stmt->bindParam(':squad_id',$squad_id);

            $stmt->execute();
            echo '{"notice":{"text": "The organizer has been added to the squad."}';
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

//Join Squad
$app->post('/api/squads/join', function(Request $request, Response $response){
        $user_squad_id = uniqid('',true);
        $squad_id = $request->getParam('squad_id');
        $user_id = $request->getParam('user_id');

        $sql = "INSERT INTO `UserSquad`(`id`, `user_id`, `squad_id`) VALUES (:user_squad_id,:user_id,:squad_id)";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->prepare($sql);

            $stmt->bindParam(':user_squad_id',$user_squad_id);
            $stmt->bindParam(':user_id',$user_id);
            $stmt->bindParam(':squad_id',$squad_id);

            $stmt->execute();
            echo '{"notice":{"text": "Successfully joined squad."}';
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

//Update Squad
$app->put('/api/squads/{id}', function(Request $request, Response $response){
        $id = $request->getAttribute('id');
        $name = $request->getParam('name');
        $description = $request->getParam('description');
        $city = $request->getParam('city');
        $state = $request->getParam('state');
        $country = $request->getParam('country');
        $organizer_id = $request->getParam('organizer_id');
        
        $sql = "UPDATE Squad SET
                    name          = :name,
                    description   = :description,
                    city          = :city,
                    state         = :state,
                    country       = :country,
                    organizer_id  = :organizer_id
                WHERE id = '$id'";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->prepare($sql);

            $stmt->bindParam(':name',$name);
            $stmt->bindParam(':description',$description);
            $stmt->bindParam(':city',$city);
            $stmt->bindParam(':state',$state);
            $stmt->bindParam(':country',$country);
            $stmt->bindParam(':organizer_id',$organizer_id);

            $stmt->execute();
            echo '{"notice":{"text": "Squad Updated"}';
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});