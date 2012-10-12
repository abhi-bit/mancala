<?php

 function move($gameState, $boxNumber)
 {
    $board = $gameState->board;
    $activePlayer = $gameState->activePlayer;
    $jars = $gameState->jars;
    
    //Not enough players in the game to make move
    if($gameState->numPlayers < 4 )
    {
        return $gameState;
    }
    
    //range  0 - 23
    if($boxNumber > 23 || $boxNumber<0)
     {
        return null;
     }
     
     $currentBoxIndex = $activePlayer * 6;
     $boxOffset = $boxNumber - $currentBoxIndex;
     //range 0 - 5 for the current player Turn
     if($boxOffset > 5 || $boxOffset < 0)
     {
        return null;
     }
     
     //if empty-box is clicked
     $numSeeds = $board[$boxNumber];
     if($numSeeds == 0)
     {
        return null;
     }
     
     $isJarInvolved = 0;
     if($boxOffset + $numSeeds >= 6)
     {
        $isJarInvolved = 1;
     }     
     
     //update board
     $boxPos;     
     for($boxPos = 1; $boxPos <= $numSeeds-$isJarInvolved; $boxPos++)
     {
        $boxId = ($boxNumber + $boxPos) % 24;
        $board[$boxId]++;
     }
     
     //If last position is a capture
    if($isJarInvolved == 0)
    {
        $boxId = ($boxNumber + $boxPos) % 24;
        if($board[$boxId] == 1)
        {
          //Step 1: On activePlayer side
          $board[$boxId] = 0;
          $jars[$activePlayer]++;
          
          //Step 2: Capture on opposite side
          if($activePlayer ==0 || $activePlayer==2)
          {
            $oppBoxId = 17-$boxId;
          }
          else
          {
            $oppBoxId = 29-$boxId;
          }
          $jars[$activePlayer] = $board[$oppBoxId];
          $board[$oppBoxId] = 0;
        
        }
    }     
     
     // Need to update jars per the move.. 
     if($isJarInvovled == 1)
     {
         $jars[$activePlayer] ++;
     }
     

     
     //Update activePlayer
     if($boxOffset + $numSeeds != 6)
     {
        $gameState->activePlayer = ($gameState->activePlayer + 1) % 4;
     }
     
      //update gameOver
     $isGameOver = true;
     for($i = 0; $i< 24; $i++)
     {
        if($board[$i] != 0)
         {
            $isGameOver = false;
            break;
         }
     }
     $gameState->gameOver = $isGameOver;
 
      return $gameState;
 }


?>