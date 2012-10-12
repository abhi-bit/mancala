var me = -1;
var gs;
var _timer;
var count_down;
var extra_turn = 0;

function displayTimer() {
    $('<div id="timer"></div>').insertBefore('.container_12');
    var c = 10;
    count_down = setInterval(function() {
        $('#timer').html(c);
        c -= 1;
        if (c < 1) {
            c = 0;
            clearInterval(count_down);
            $('#timer').hide();
            $('#updates').html('Times up!').fadeIn('slow', function() {
                $(this).fadeOut(3000);
            });
        }
    }, 1000);
}

$(document).ready(function() {
    setupJars();
    setupEventHandlers();
    var qs = location.search.replace(/\?/g, '');
    $.ajax({
        url: 'http://mankala.zc2.zynga.com/api/initGame.php',
        data: qs,
        success: function(result) {
            console.log(result);
            gs = JSON.parse(result);        
            console.log("Game State JSON = " + gs);
            initBoard(qs,gs);
	// setup a ugly poller
            _timer = setInterval(function() {$.ajax({
                url: 'http://mankala.zc2.zynga.com/api/getState.php',
                data: {s: gs.session_id},
                success: function(result) {
                    // find the game state from server
                        gs = JSON.parse(result);
                        renderBoard(gs);
                        if (me != -1 && (gs.active_player == me)) {
                            renderBoard(gs);
//                            console.log("rendering board via setInterval");
                        }
                    },
                })}, 1500);
        }
    });

});


function setupEventHandlers() {
    $('.grid_2').click(function(e) {
        e.preventDefault();
        console.log($(this).attr('id'));
        if (gs.active_player == -1) {
            console.log("Please wait, game not yet ready");
            return;
        }
        if (gs.active_player != me) {
            console.log("Sorry, not your turn");
            return;
        }
        board_pos = $(this).attr('id').split('_')[1];
        if (me == 1 && (board_pos > 5 && board_pos < 12)) {
            // ajax request
            $.ajax({
                url: 'http://mankala.zc2.zynga.com/api/move.php',
                data: {s: gs.session_id, box: board_pos, extra_turn: extra_turn},
                success: function(result) {
                    gs = JSON.parse(result);
                    renderBoard(gs);
                    if (gs.active_player == me) {
                        // got an extra turn
                        $('#updates').html("Got an extra Turn!");
                        $('#updates').fadeIn();
                    } else {
                        $('#updates').hide();
                    }
                }
            });
            if (extra_turn == 1) { extra_turn = 0; }
            return;
        }
        if (me == 0 && (board_pos >= 0 && board_pos < 6)) {
            // ajax request
            $.ajax({
                url: 'http://mankala.zc2.zynga.com/api/move.php',
                data: {s: gs.session_id, box: board_pos, extra_turn: extra_turn},
                success: function(result) {
                    gs = JSON.parse(result);
                    renderBoard(gs);
                    if (gs.active_player == me) {
                        // got an extra turn
                        $('#updates').html("Got an extra Turn!");
                        $('#updates').fadeIn();
                    } else {
                        $('#updates').hide();
                    }
                }
            });
            if (extra_turn == 1) { extra_turn = 0; }

            return;
        }
        console.log("invalid move by player");
    });
}
/* result: GameStatus 
   displays GameBoard and updates Player information */

function setupJars() {
    var top = $('.container_12').offset().top;
    var left = $('.container_12').offset().left;
    var right = $('.container_12').width() + left;

    $('.image_1').css({
        top: top,
        left: left - 150,
        position: 'fixed',
    });

    $('.image_0').css({
        top: top,
        left: right,
        position: 'fixed'
    });
}

function gameOver(gameState) {
    // clear the interval, no longer kill my server
    clearInterval(_timer);
    $('body').append('<div class="gameover"></div>');
    // calculate score
    var player_0 = 0, player_1 = 0;
    for (var i = 0; i < 6; ++i) {
        player_0 += gameState.board[i];
        player_1 += gameState.board[i+6];
    }
    player_0 += gameState.jar[0];
    player_1 += gameState.jar[1];
    var msg;
    if (player_0 > player_1) {
        if (me == 0) { msg = "You won!<br>" ; } else { msg = "You lost! <br>" ;}
    } else {
        if (me == 1) { msg = "You won!<br>" ; } else { msg = "You lost! <br>" ; }
    }
    if (me == 0) {
        msg += "Your score " + player_0 + "<br>";
        msg +=  "Opponent score " + player_1 + "<br>";
    } else {
        msg += "Your score " + player_1 + "<br>";
        msg += "Opponent score " + player_0 + "<br>";
    }
    $('.gameover').html(msg).hide();
    var gleft = parseInt(($(window).width() - $('.gameover').outerWidth())/2);
    var gtop = parseInt(($(window).height() - $('.gameover').outerHeight())/2);
    $('.gameover').css({
        top: gtop,
        left: gleft
    });
    $('.gameover').fadeIn();
    $('#status').remove();
    $('#updates').remove();
    $('.grid_2').off('click');
    $('.grid_2 p').unbind('mouseenter mouseleave');
}

function renderBoard(result) {
    me = parseInt($('#player').val());
    var board = result.board;
    var obj;
    for (i = 0; i < board.length; ++i) {
        obj = '#board_' + i;
        $(obj).find('p').html(board[i]);
	if (board[i] == 0) {
		$(obj).find('p').css('background-image', 'none');
	} else if (board[i] == 1) 
		$(obj).find('p').css('background-image', 'url("/mankala/img/pits.gif")');
    }
    if (gs.num_players == 2 && ($('#url').css('visibility') == "visible"))
        $('#url').css('visibility', 'hidden');
    if (gs.active_player == me) {
        $('#status').html('Your Turn');
    } else if(gs.active_player != -1) {
        $('#status').html('Player ' + gs.active_player + ' Turn');
        console.log("bool = " + gs.active_player == me);
    } 
        
    // display jar scores
    $('.jar_0').next().html(gs.jar[0]);
    $('.jar_1').next().html(gs.jar[1]);

    if (gs.game_over == 1) {
        gameOver(result);
    }
}

function initBoard(qs, result) {
    me = result.num_players - 1;
    $('#player').val(me);
    var game_url = $('#url');
    console.log(typeof(result));
    if (qs) {
        $(game_url).html(location.href);
    } else {
        $(game_url).html(location.href + '?s=' + result.session_id);
    }
    if (me == 0) 
	for (var i = 6; i < 12; ++i)  {
	var ele = $('.grid_2 p')[i];
    $(ele).hover(
        function() { $(this).addClass('hover'); },
        function() { $(this).removeClass('hover'); }
    );
   }
    if (me == 1) 
	for (var i = 0; i < 6; ++i)  {
	var ele = $('.grid_2 p')[i];
    $(ele).hover(
        function() { $(this).addClass('hover'); },
        function() { $(this).removeClass('hover'); }
    );
   }
    renderBoard(result);
    // hide the URL if all players are in
    if (result.num_players == 2) {
        $('#url').css('visibility', 'hidden');
        $('#status').html('Player ' + result.active_player + ' Turn')
    // Update whose turn is next
    } else {
        $('#status').html('Waiting for peers to join');
    }
    $('body').append('<div id="updates">Got an extra turn!</div>');
    $('body').append('<div id="powerups"><a href="" id="skip"><img src="img/Skip.png" height="45px" width="50px"/></a><a href="" id="extra"><img  src="img/extra.jpg" height="45px" width="50px"></a></div>');
    $('#updates').hide();
    $('#skip').click(function(e) {
        e.preventDefault();
        if (gs.active_player != me) return;
        $.ajax({
            url: 'http://mankala.zc2.zynga.com/api/move.php',
            data: {s: gs.session_id, box: 0, skip: 1},
            success: function(result) {
                gs = JSON.parse(result);
                $('#updates').html('Powerup Skip Turn!').hide();
                $('#updates').fadeIn('slow', function() {
                    $(this).fadeOut(3000);
                });
            }
        });

    });
    $('#extra').click(function(e) {
        e.preventDefault();
        if (gs.active_player != me) return;
        extra_turn = 1;
        $('#updates').html('Powerup Extra Turn!').hide();
        $('#updates').fadeIn('slow', function() {
            $(this).fadeOut(3000);
        })
    });
//    $('#powerups').hide();
}
