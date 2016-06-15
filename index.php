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
        
        # Insert
        
    }
    
    public function delete ($id) {
        
        # Update deleted field
        
    }
    public function colors ($id=false) {
        $colors = [1=>'blue','red','green','yellow','grey','black'];
        return $id && isset($colors[$id]) ? $colors[$id] : $colors;
    }
    
}



$app = new Wall($db);
$data = $app->data();

foreach ($data as $r) {
    $left = rand(100,1000);
    $top = rand(100,1000);
    $color = $app->colors(rand(1,6));
    $font_size = rand(8,16);
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
           body {
               font-size: 10pt;
               font-family: Arial;
               background: #efefef;
           } 
        </style>
    </head>       
    <body>
        <?=implode($messages)?>
    </body>
</html>