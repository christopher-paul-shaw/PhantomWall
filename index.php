<?php
# Display PHP Errors
ini_set('display_errors', 1);
error_reporting(E_ALL);

# Include Configuration file for DB Credentials
include 'libs/config.php';

# Create Connection to Database using PD0
$db = new PDO("mysql:host={$db_host};dbname={$db_name};charset=utf8mb4", $db_user, $db_pass);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

class Wall {
    
    public function __construct ($db) {
        $this->db = $db;
    }
    
    public function data () {
        $stmt = $this->db->prepare("SELECT * FROM messages WHERE id > :id");
        $stmt->execute(array(':id' => 0));
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function insert ($message) {
        $stmt = $this->db->prepare("INSERT INTO messages (message) VALUES (:message)");
        $stmt->execute(['message' => $message]);
    }
    
    public function delete ($message) {
        $stmt = $this->db->prepare("DELETE FROM messages WHERE message LIKE :message");
        $stmt->execute(['message' => '%'.$message.'%']);   
    }
    
    public function reset () {
        $stmt = $this->db->exec("DELETE FROM messages");
    }
    
    public function colors ($id=false) {
        $colors = [1=>'#707070','#b8b8b8','#50000','#B00000','#009900','#006666'];
        return $id && isset($colors[$id]) ? $colors[$id] : $colors;
    }
    
}

$message = isset($_POST['message']) ? $_POST['message'] : false;
$delete = isset($_GET['delete']) ? $_GET['delete'] : false;
$reset = isset($_GET['reset']);

$app = new Wall($db);

switch (true) {
    case $message:
        $app->insert($message);
        $reload = true;
        break;
    case $reset:
        $app->reset();
        $reload = true;
        break;
    case $delete:
        $app->delete($delete);
        $reload = true;
        break;
}

if(isset($reload)){ 
    header("Location: ./");
    die();
}

$data = $app->data();
foreach ($data as $r) {
    $left = rand(100,1700);
    $top = rand(100,600);
    $color = $app->colors(rand(1,6));
    $font_size = rand(16,48);
    $messages[] = <<<HTML
    <div style="position: absolute; left: {$left}; top: {$top}; color: {$color}; font-size: {$font_size}pt;">
        {$r['message']}
    </div>
HTML;
}

?>
<html>
    <head>
        <title>Phantom Wall</title>
        <style>
           @import 'https://fonts.googleapis.com/css?family=Creepster';
           body {
               font-size: 10pt;
               font-family: Arial;
               background: #222;
               font-family: 'Creepster', cursive;
           }

           input {
               background: rgba(45, 44, 44, 0.6);
               color: rgba(255, 255, 255, 0.6);
               border: 0;
               padding: 10px;
               box-sizing: border-box;
               box-shadow: 0px 1px 14px 5px #BFBFBF;
               -webkit-box-shadow: 0px 1px 14px 5px #BFBFBF;
               -moz-box-shadow: 0px 1px 14px 5px #BFBFBF;
               -o-box-shadow: 0px 1px 14px 5px #BFBFBF;
               border-radius: 4px;
               -webkit-border-radius: 4px;
               -moz-border-radius: 4px; 
               margin: 10px;
           }
           
           input:hover {
               background: #BFBFBF;
               color: #111;
           }
        </style>
    </head>       
    <body>
        <form method="post">
            <input type="text" name="message" />
            <input type="submit" value="POST" />
        </form>
        <?= isset($messages) ? implode($messages) : false?>
    </body>
</html>