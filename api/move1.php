<?php

 function move($gameState, $boxNumber)
 {
    $board = $gameState["board"];
    $activePlayer = $gameState["active_player"];
    $jars = $gameState["jar"];
    
    //Not enough players in the game to make move
    if($gameState["num_players"] < 2 )
    {
        return $gameState;
    }
    
    //range  0 - 11
    if($boxNumber > 11 || $boxNumber<0)
     {
        return $gameState;
     }
     
     $currentBoxIndex = $activePlayer * 6;
     $boxOffset = $boxNumber - $currentBoxIndex;
     //range 0 - 5 for the current player Turn
     if($boxOffset > 5 || $boxOffset < 0)
     {
        return $gameState;
     }
     
     //if empty-box is clicked
     $numSeeds = $board[$boxNumber];
     if($numSeeds == 0)
     {
        return $gameState;
     }
     
     $isJarInvolved = 0;
     if($boxOffset + $numSeeds >= 6)
     {
        $isJarInvolved = 1;
     }     
     
     //update board
     $gameState["board"][$boxNumber]=0;    

     $boxPos=1;     
     for($boxPos = 1; $boxPos <= $numSeeds-$isJarInvolved; $boxPos++)
     {
        $boxId = ($boxNumber + $boxPos) % 12;
        $gameState["board"][$boxId]++;
     }
     
     //If last position is on activePlayer side & it is an empty slot
    $lastBoxId = ($boxNumber + $numSeeds-$isJarInvolved) % 12;
    $lastBoxOffset = $lastBoxId - $currentBoxIndex;
    error_log("lastBoxId = " . $lastBoxId);
    if($lastBoxOffset<6 && ($numSeeds!=$isJarInvolved))
    {
        if($gameState["board"][$lastBoxId] == 1)
        {
          //Step 1: On activePlayer side
          $gameState["board"][$lastBoxId] = 0;
          $gameState["jar"][$activePlayer]++;
          
          //Step 2: Capture on opposite side
          $oppBoxId = 11-$lastBoxId;
          $gameState["jar"][$activePlayer] = $gameState["jar"][$activePlayer]+$gameState["board"][$oppBoxId];
          $gameState["board"][$oppBoxId] = 0;
        
        }    
     }
     // Need to update jars per the move.. 
     if($isJarInvolved == 1)
     {
         $gameState["jar"][$activePlayer] ++;
     }
     
      //update gameOver
      //check for P0 player
     $isGameOverP0 = 1;
     $isGameOverP1 = 1;
     for($i = 0; $i<6; $i++)
      {
        if( $gameState["board"][$i] != 0)
        {
          $isGameOverP0 = 0;
          break;
        }
      }
      if($isGameOverP0 ==0)
      {
        for($i = 6; $i<12; $i++)
        {
          if( $gameState["board"][$i] != 0)
          {
            $isGameOverP1 = 0;
            break;
          }
        }
      }
      
      if($isGameOverP0 == 1 || $isGameOverP1==1)
      {
        $gameState["game_over"] = 1;
      }
      else
      {
        $gameState["game_over"] = 0;
      }
     
     //Update activePlayer
     if($boxOffset + $numSeeds != 6)
     {
        $gameState["active_player"] = ($gameState["active_player"]+1) % 2;
     }
 
      return $gameState;
 }

$sessionId;
if(isset($_GET["s"]))
{
        $sessionId = $_GET["s"];
}
else
{
        exit();
}

$box;
if(isset($_GET["box"]))
{
        $box = $_GET["box"];
}
else
{
        exit();
}

$memcache = new Memcache;
$memcache->connect('localhost', 11211);
$gameState = $memcache->get($sessionId);
$gameState = move($gameState,$box);
$memcache->set($sessionId,$gameState);
echo json_encode($gameState);

?>
