<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>Mankala Demo</title>
<link rel="stylesheet" href="css/reset.css" />
<link rel="stylesheet" href="css/grid.css" />
<link rel="stylesheet" href="css/style.css" />
<link href='http://fonts.googleapis.com/css?family=Gruppo' rel='stylesheet' type='text/css'>
<script src="js/jquery-1.8.2.min.js"></script>
<script src="js/game.js"></script>
</head>
<body>
<h1>
  <a href="#">Mankala</a>
</h1>
<input type="hidden" name="player" id="player" value="-1" />
<h2 id="url"></h2>
<div class="container_12">
  <?php for ($i = 0; $i < 12; ++$i) { 
        $val = ($i > 5) ? $i - 6 : 11 - $i;
        echo sprintf('<div class="grid_2" id="board_%d"><p>60</p></div>', $val);
    }?>
<!--  <div class="grid_2"> <p>60</p></div>
  <div class="grid_2"> <p>60</p></div>
  <div class="grid_2"> <p>60</p></div>
  <div class="grid_2"> <p>60</p></div>
  <div class="grid_2"> <p>60</p></div>
  <div class="grid_2"> <p>60</p></div>
  <div class="grid_2"> <p>60</p></div> -->
</div>
<div class="image_0">
<img src="img/bowl.gif" height="150px" width="150px" class="jar_0"/>
<div class="score"></div>
</div>
<div class="image_1">
<img src="img/bowl.gif" height="150px" width="150px" class="jar_1"/>
<div class="score"></div>
</div>
<div id="status"></div>
</body>
</html>
