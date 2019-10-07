<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");

// Get All Users
$app->get('/api/users', function(Request $request, Response $response){
        $sql = "SELECT * FROM User";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();
 
            $stmt = $db->query($sql);
            $users = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo json_encode($users);
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

// Validate User Password
$app->get('/api/users/validate', function(Request $request, Response $response){
    $headers = apache_request_headers();
    $email = $headers['email'];
    $password = $headers['password'];

    $sql = "SELECT * FROM User WHERE email = '$email' AND password = '$password'";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->query($sql);
            $user = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            if ($user != null)
               echo json_encode($user);
            else
               echo '[{"message" : "Sorry, this email and password do not match."}]';
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

// Get Single User
$app->get('/api/users/{id}', function(Request $request, Response $response){
        $id = $request->getAttribute('id');
        
        $sql = "SELECT * FROM User WHERE id = '$id'";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->query($sql);
            $user = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo json_encode($user);
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

// Get Single User by Name
$app->get('/api/users/name/{first_name}&{last_name}', function(Request $request, Response $response){
        $first_name = $request->getAttribute('first_name');
        $last_name = $request->getAttribute('last_name');
        
        $sql = "SELECT * FROM User 
WHERE first_name = '$first_name'
AND last_name = '$last_name'";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->query($sql);
            $user = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            if ($user != null)
               echo json_encode($user);
            else
               echo '[{"message" : "Sorry, we did not find any users with that name."}]';
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});

// Delete User
$app->delete('/api/users/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $sql = "DELETE FROM User WHERE id = '$id'";
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $db = null;
        echo '{"notice": {"text": "User Deleted"}';
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

//Add User
$app->post('/api/users', function(Request $request, Response $response){
        $id = uniqid('',true);
        $password = $request->getParam('password');
        $first_name = $request->getParam('first_name');
        $last_name = $request->getParam('last_name');
        $email = $request->getParam('email');
        $phone_number = $request->getParam('phone_number');
        $age = $request->getParam('age');
        $user_type = $request->getParam('user_type');
        $city = $request->getParam('city');
        $state = $request->getParam('state');
        $country = $request->getParam('country');
        $already_taken = 'false';

        $alreadyExistsSql = "SELECT * FROM User WHERE email = '$email'";
        
        $sql = "INSERT INTO User (id,password,first_name,last_name,email,phone_number,age,user_type,city,state,country) VALUES
        (:id,:password,:first_name,:last_name,:email,:phone_number,:age,:user_type,:city,:state,:country)";

//Check that the email isn't already taken
        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->prepare($alreadyExistsSql);

            $stmt->bindParam(':email',$email);

            $stmt = $db->query($alreadyExistsSql);
            $user = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            if ($user != null) {
               $already_taken = 'true';
               echo '[{"message" : "Sorry, that email is already taken."}]';
            }
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }

        if ($already_taken == 'false') {
           try{
               //Get DB Object
               $db = new db();
               // Connect
               $db = $db->connect();

               $stmt = $db->prepare($sql);

               $stmt->bindParam(':id',$id);
               $stmt->bindParam(':password',$password);
               $stmt->bindParam(':first_name',$first_name);
               $stmt->bindParam(':last_name',$last_name);
               $stmt->bindParam(':email',$email);
               $stmt->bindParam(':phone_number',$phone_number);
               $stmt->bindParam(':age',$age);
               $stmt->bindParam(':user_type',$user_type);
               $stmt->bindParam(':city',$city);
               $stmt->bindParam(':state',$state);
               $stmt->bindParam(':country',$country);

               $stmt->execute();
               echo '[{"confirmation" : "Welcome! Click the menu button to create a squad or check out your invites!", "id" : "'.$id.'"}]';
           }catch(PDOException $e){
               echo '{"error": {"text": '.$e->getMessage().'}';
           }
        }
});

//Update User
$app->put('/api/users/{id}', function(Request $request, Response $response){
        $id = $request->getAttribute('id');
        $password = $request->getParam('password');
        $first_name = $request->getParam('first_name');
        $last_name = $request->getParam('last_name');
        $email = $request->getParam('email');
        $phone_number = $request->getParam('phone_number');
        $age = $request->getParam('age');
        $user_type = $request->getParam('user_type');
        $city = $request->getParam('city');
        $state = $request->getParam('state');
        $country = $request->getParam('country');
        
        $sql = "UPDATE User SET
                    password      = :password,
                    first_name    = :first_name,
                    last_name     = :last_name,
                    email         = :email,
                    phone_number  = :phone_number,
                    age           = :age,
                    user_type     = :user_type,
                    city          = :city,
                    state         = :state,
                    country       = :country
                WHERE id = '$id'";

        try{
            //Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();

            $stmt = $db->prepare($sql);

            $stmt->bindParam(':password',$password);
            $stmt->bindParam(':first_name',$first_name);
            $stmt->bindParam(':last_name',$last_name);
            $stmt->bindParam(':email',$email);
            $stmt->bindParam(':phone_number',$phone_number);
            $stmt->bindParam(':age',$age);
            $stmt->bindParam(':user_type',$user_type);
            $stmt->bindParam(':city',$city);
            $stmt->bindParam(':state',$state);
            $stmt->bindParam(':country',$country);

            $stmt->execute();
            echo '{"notice":{"text": "User Updated"}';
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
});
