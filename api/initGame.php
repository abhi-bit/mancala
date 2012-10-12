<?php

$memcache = new Memcache;
$memcache->connect('localhost', 11211);


if(isset($_GET["s"]))
{
	$sessionId = $_GET["s"];
	$g = $memcache->get($sessionId);
	if ($g["num_players"] != 2) { $g["active_player"]=0; }
	$g["num_players"] = 2;
}
else
{
	session_start();
	$sessionId = "MANKALA_".session_id();
	$g = array("session_id" => $sessionId, "board" => array(4,4,4,4,4,4,4,4,4,4,4,4), "jar" => array(0, 0), "active_player" => -1, "game_over" => 0, "num_players" => 1);
}

$memcache->set($sessionId,$g, 0);
echo json_encode($g);
?>

