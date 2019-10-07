<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");

// Get All Invites
$app->get('/api/invites', function(Request $request, Response $response){
        $sql = "SELECT * FROM Invite";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->query($sql);
            $invites = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo json_encode($invites);
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

// Get All Invites for a User
$app->get('/api/invites/user/{user_id}', function(Request $request, Response $response){
        $user_id = $request->getAttribute('user_id');

        $sql = "SELECT Invite.id, Invite.sender_id, Invite.acceptor_id, Invite.squad_id, Squad.name AS 'squad_name', Invite.invite_type, Invite.acceptor_email, Invite.status FROM Invite
INNER JOIN Squad
ON Invite.squad_id = Squad.id
WHERE acceptor_id = '$user_id'";

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
               echo '[{"message" : "Sorry, you have no invites."}]';
        }catch(PDOException $e){
            echo '{"error": {"technical": '.$e->getMessage().', "text": "Sorry, you currently have no squads. Create or join one to collab!"}';
        }
});

// Get All Invites for a User using Email
$app->get('/api/invites/email/{email}', function(Request $request, Response $response){
        $email = $request->getAttribute('email');

        $sql = "SELECT Invite.id, Invite.sender_id, Invite.acceptor_id, Invite.squad_id, Squad.name AS 'squad_name', Invite.invite_type, Invite.acceptor_email, Invite.status FROM Invite
INNER JOIN Squad
ON Invite.squad_id = Squad.id
WHERE acceptor_email = '$email'
ORDER BY Invite.id DESC";

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
               echo '[{"message" : "Sorry, you have no invites."}]';
        }catch(PDOException $e){
            echo '{"error": {"technical": '.$e->getMessage().', "text": "Sorry, you currently have no squads. Create or join one to collab!"}';
        }
});

// Get Single Invite
$app->get('/api/invites/{id}', function(Request $request, Response $response){
        $id = $request->getAttribute('id');
        
        $sql = "SELECT * FROM Invite WHERE id = '$id'";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->query($sql);
            $invite = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo json_encode($invite);
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

// Delete Invite
$app->delete('/api/invites/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $sql = "DELETE FROM Invite WHERE id = '$id'";
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $db = null;
        echo '{"notice": {"text": "Invite Deleted"}';
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

//Add Invite
$app->post('/api/invites', function(Request $request, Response $response){
        $id = uniqid('',true);
        $sender_id= $request->getParam('sender_id');
        $acceptor_id = $request->getParam('acceptor_id');
        $squad_id= $request->getParam('squad_id');
        $new = $request->getParam('status');
        $acceptor_email = $request->getParam('acceptor_email');
        $invite_type = $request->getParam('invite_type');
        /*if ($acceptor_id == null){
           $invite_type = 'external';
        } 
        else {
           $invite_type = 'internal';
        }*/
        $sql_gather_info = "SELECT User.first_name, User.last_name, User.email, Squad.name as squad_name
FROM User
INNER JOIN UserSquad ON UserSquad.user_id = User.id
INNER JOIN Squad ON Squad.id = UserSquad.squad_id
WHERE User.id = '$sender_id'
AND Squad.id = '$squad_id'
UNION
SELECT User.first_name, User.last_name, User.email, null
FROM User
WHERE User.id = '$acceptor_id'";
        
        $sql = "INSERT INTO Invite (`id`,`sender_id`,`acceptor_id`,`squad_id`,invite_type,acceptor_email,status) VALUES
        (:id,:sender_id,:acceptor_id,:squad_id,:invite_type,:acceptor_email,:new)";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->prepare($sql);

            $stmt->bindParam(':id',$id);
            $stmt->bindParam(':sender_id',$sender_id);
            $stmt->bindParam(':acceptor_id',$acceptor_id);
            $stmt->bindParam(':squad_id',$squad_id);
            $stmt->bindParam(':invite_type',$invite_type);
            $stmt->bindParam(':acceptor_email',$acceptor_email);
            $stmt->bindParam(':new',$new);

            $stmt->execute();
            echo '[{"confirmation" : "Invite Sent", "id" : "'.$id.'"}]';
            //echo '{"notice":{"text": "Invite Added"}';

            $stmt_gather = $db->query($sql_gather_info);
            $gather = $stmt_gather->fetchAll(PDO::FETCH_OBJ);
            $gather_json = json_encode($gather);
            $db = null;
            //echo '{"notice":{"text": "Sender data gathered"}';
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }

        $json_output = json_decode($gather_json, true);
        $destination_email = $json_output[1]['email'];
        $acceptor_first = $json_output[1]['first_name'];
        $sender_first = $json_output[0]['first_name'];
        $sender_last = $json_output[0]['last_name'];
        $squad_name = $json_output[0]['squad_name'];

        if ($invite_type == "internal"){
           $email_text = "Hi $acceptor_first, you have received an invite from $sender_first $sender_last to join the $squad_name squad. Click on this link to respond to the invite!";
        }
        else {
           $destination_email = $acceptor_email;
           $email_text = "Hi there! You have received an invite from $sender_first $sender_last to join the team $squad_name on Squad. We noticed that you don't have a Squad account yet though. Click on this link to create your free account and join your squad!";
        }
        $subject = "$sender_first sent you an invite!";
        $headers = "From: virtualmanager@squad.com"; //change this to squad's email address

        mail($destination_email,$subject,$email_text,$headers);
        //echo '{"notice":{"text": "Email has been sent"}';    
});

//Update Invite
$app->put('/api/invites/{id}', function(Request $request, Response $response){
        $id = $request->getAttribute('id');
        $sender_id= $request->getParam('sender_id');
        $acceptor_id = $request->getParam('acceptor_id');
        $squad_id= $request->getParam('squad_id');
        $invite_type = $request->getParam('invite_type');
        $acceptor_email = $request->getParam('acceptor_email');
        $status = $request->getParam('status');
        
        $sql = "UPDATE Invite SET
                    sender_id     = :sender_id,
                    acceptor_id   = :acceptor_id,
                    squad_id      = :squad_id, 
                    invite_type   = :invite_type,
                    acceptor_email= :acceptor_email,
                    status        = :status
                WHERE id = '$id'";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->prepare($sql);

            $stmt->bindParam(':sender_id',$sender_id);
            $stmt->bindParam(':acceptor_id',$acceptor_id);
            $stmt->bindParam(':squad_id',$squad_id);
            $stmt->bindParam(':invite_type',$invite_type);
            $stmt->bindParam(':acceptor_email',$acceptor_email);
            $stmt->bindParam(':status',$status);

            $stmt->execute();
            if ($status == 'accepted'){
               echo '[{"confirmation" : "Invite accepted", "id" : "'.$id.'"}]';
            }
            else if ($status == 'declined'){
               echo '[{"confirmation" : "Invite declined", "id" : "'.$id.'"}]';
            }
            else {
               echo '[{"confirmation" : "Invite updated", "id" : "'.$id.'"}]';
            }
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});