<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");

// Get All Messages
$app->get('/api/messages', function(Request $request, Response $response){
        $sql = "SELECT * FROM Message";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->query($sql);
            $messages = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo json_encode($messages);
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

// All Messages Page (organized according to sender and date)
$app->get('/api/messages/page', function(Request $request, Response $response){
        $headers = apache_request_headers();
        $user_id = $headers['user_id'];

        $sql = "(SELECT Message.*, CONCAT(User.first_name, ' ', User.last_name) AS name, Squad.name as squad_name
FROM Message
INNER JOIN (
    SELECT MAX(date_time) AS date_time, squad_id
    FROM Message
    WHERE message_type = 'squad'
	GROUP BY squad_id) AS join_temp
ON Message.squad_id = join_temp.squad_id
AND Message.date_time = join_temp.date_time
INNER JOIN User on User.id = Message.sender_id
INNER JOIN Squad on Squad.id = Message.squad_id
WHERE Message.id IN (SELECT message_id FROM UserMessage WHERE user_id = '$user_id') 
AND message_type = 'squad'
GROUP BY Message.squad_id)
UNION
(SELECT Message.*, CONCAT(User.first_name, ' ', User.last_name) AS name, Message.squad_id
FROM Message
INNER JOIN (
    SELECT MAX(date_time) AS date_time, sender_id
    FROM Message
    WHERE message_type = 'user'
	GROUP BY sender_id) AS join_temp
ON Message.sender_id = join_temp.sender_id
AND Message.date_time = join_temp.date_time
INNER JOIN User on User.id = Message.sender_id
WHERE Message.id IN (SELECT message_id FROM UserMessage WHERE user_id = '$user_id') 
AND message_type = 'user'
GROUP BY Message.sender_id)
ORDER BY date_time DESC";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->query($sql);
            $messages = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo json_encode($messages);
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

// All Messages Between User and Group/Other User(organized according to date)
$app->get('/api/messages/conversation', function(Request $request, Response $response){
        $headers = apache_request_headers();
        $user_id = $headers['user_id'];
        $sender_id = $headers['sender_id'];
        $message_type = $headers['message_type'];
        $squad_id = $headers['squad_id'];

        if ($message_type == 'user')
           $sql = "(SELECT Message.*, CONCAT(User.first_name, ' ', User.last_name) AS name
FROM Message 
INNER JOIN User ON User.id = Message.sender_id 
WHERE Message.id IN (SELECT message_id FROM UserMessage WHERE user_id = '$user_id') 
AND sender_id = '$sender_id'
AND message_type = 'user')
UNION
(SELECT Message.*, CONCAT(User.first_name, ' ', User.last_name) AS name
FROM Message 
INNER JOIN User ON User.id = Message.sender_id 
WHERE Message.id IN (SELECT message_id FROM UserMessage WHERE user_id = '$sender_id') 
AND sender_id = '$user_id'
AND message_type = 'user')
ORDER BY date_time DESC";
        else
           $sql = "SELECT Message.*, CONCAT(User.first_name, ' ', User.last_name) AS name, Squad.name AS squad_name
FROM Message 
INNER JOIN Squad ON Squad.id = Message.squad_id
INNER JOIN User ON User.id = Message.sender_id
WHERE Message.id IN (SELECT message_id FROM UserMessage WHERE user_id = '$user_id') 
AND squad_id = '$squad_id'
AND message_type = 'squad'
ORDER BY date_time DESC";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->query($sql);
            $messages = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo json_encode($messages);
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

// Get Single Message
$app->get('/api/messages/{id}', function(Request $request, Response $response){
        $id = $request->getAttribute('id');
        
        $sql = "SELECT * FROM Message WHERE id = '$id'";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->query($sql);
            $message = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo json_encode($message);
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

// Delete Message
$app->delete('/api/messages/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $sql = "DELETE FROM Message WHERE id = '$id'";
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $db = null;
        echo '{"notice": {"text": "Message Deleted"}';
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

//Send a Message
$app->post('/api/messages', function(Request $request, Response $response){
        $message_id = uniqid('',true);
        $user_message_id = uniqid('',true);
        $recipient_id = $request->getParam('recipient_id');
        $data = $request->getParam('data');
        $seen = $request->getParam('seen');
        $sender_id = $request->getParam('sender_id');
        $message_type = $request->getParam('message_type');
        $squad_id = $request->getParam('squad_id');
        
        $sql_message = "INSERT INTO Message (id,data,seen,sender_id,message_type,squad_id) VALUES
        (:message_id,:data,:seen,:sender_id,:message_type,:squad_id)";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->prepare($sql_message);

            $stmt->bindParam(':message_id',$message_id);
            $stmt->bindParam(':data',$data);
            $stmt->bindParam(':seen',$seen);
            $stmt->bindParam(':sender_id',$sender_id);
            $stmt->bindParam(':message_type',$message_type);
            $stmt->bindParam(':squad_id',$squad_id);

            $stmt->execute();
            echo '{"notice":{"text": "Message Sent"}';
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }

        if ($message_type == "squad"){
           $ch = curl_init();
           curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
           curl_setopt($ch, CURLOPT_URL, "http://sqquad.x10host.com/api/squads/members");
           curl_setopt($ch, CURLOPT_HTTPHEADER, array('squad_id: '.$squad_id));
           $result = json_decode(curl_exec($ch),true);
           $ids = array_column($result, 'id');
           curl_close($ch);
           var_dump(json_decode($output, true));
           $sql_user_message = "INSERT INTO UserMessage (id, user_id, message_id) VALUES ";

           $last_element = end($ids);
           foreach ($ids as &$value) {
           $current_user_message_id = uniqid('',true);
              if ($value != $last_element)
                 $sql_user_message .= "('$current_user_message_id', '$value', '$message_id'),";
              else
                 $sql_user_message .= "('$current_user_message_id', '$value', '$message_id')";
           }
           
           try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->prepare($sql_user_message);

            $stmt->execute();
            echo '{"notice":{"text": "Message received by every member of the squad."}';
           }catch(PDOException $e){
               echo '{"error": {"text": '.$e->getMessage().'}';
           }
        }
        else {
              $sql_user_message = "INSERT INTO UserMessage (id,user_id,message_id) VALUES
        (:user_message_id,:recipient_id,:message_id)";

           try{
               //Get DB Object
               $db = new db();
               // Connect
               $db = $db->connect();

               $stmt = $db->prepare($sql_user_message);

               $stmt->bindParam(':user_message_id',$user_message_id);
               $stmt->bindParam(':recipient_id',$recipient_id);
               $stmt->bindParam(':message_id',$message_id);

               $stmt->execute();
               echo '{"notice":{"text": "Message Received"}';
           }catch(PDOException $e){
               echo '{"error": {"text": '.$e->getMessage().'}';
           }
        }   
});

//Update Message
$app->put('/api/messages/{id}', function(Request $request, Response $response){
        $id = $request->getAttribute('id');
        $data = $request->getParam('data');
        $seen = $request->getParam('seen');
        
        $sql = "UPDATE Message SET
                    data    = :data,
                    seen    = :seen
                WHERE id = '$id'";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->prepare($sql);

            $stmt->bindParam(':data',$data);
            $stmt->bindParam(':seen',$seen);

            $stmt->execute();
            echo '{"notice":{"text": "Message Updated"}';
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});