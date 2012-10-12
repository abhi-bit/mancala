<?php
$sessionId;
if(isset($_GET["s"]))
{
        $sessionId = $_GET["s"];
}
else
{
        exit();
}

$memcache = new Memcache;
$memcache->connect('localhost', 11211);
$gameState = $memcache->get($sessionId); 
echo json_encode($gameState);

?>
