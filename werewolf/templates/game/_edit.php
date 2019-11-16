<?php
if ( $edit ) {
  if ( $moderator && ($status != "Finished") ) {
?>

<div id='control_table'>
<table class='forum_table' width='100%'>
<tr><th> 
Moderator Controls 
</th></tr>
<tr><td align='center'>
<?php
# Controls while the game is in Signup or In Progress
if ( $status != "Finished" ) { 
  print "<div><a href='javascript:rand_assign()'>Randomly Assign Roles</a></div>\n"; 
  print "<div><a href='javascript:delete_game()'>Remove this game from the Cassandra Database</a></div>\n";
  print "<div><a href='${here}configure_physics.php?game_id=".$game['id']."'>Activate/Configure Physics System</a></div>\n"; 
}
# Controls while the game is In Progress
if ( $status == "In Progress" ) {
  if ( $game['auto_vt'] == "No" ) { 

    print "<div><a href='javascript:activate_vt()'>Activate Auto Vote Tally</a></div>\n";
  } else {
    print "<div><a href='javascript:retrieve_vt()'>Force Vote Tally Post</a></div>\n";
    if ( $game['vote_by_alias'] == "No" ) {
      #print "<div><a href='javascript:activate_aliases()'>Require Voting by Aliases</a></div>\n";
    }
  }
  print "<div><a href='${here}configure_chat.php?game_id=".$game['id']."'>Activate/Configure Game Communications System</a></div>\n"; 
  if ( $chats > 0 ) {
    print "<div><a href='javascript:activate_goa()'>Activate/Modify Game Order Assistant</a></div>\n";
  }
  if ( $game['missing_hr'] > 0 ) {
    print "<div><a href='javascript:activate_mpw()'>Missing Player Warning: ".$game['missing_hr']."hrs</a></div>";
  } else {
    print "<div><a href='javascript:activate_mpw()'>Activate Missing Player Warning System</a></div>";
  }
  print "<div><a href='javascript:activate_al()'>Activate/Modify Alias Settings</a></div>\n";
  #print "<div><a href='javascript:activate_ad()'>Activate/Modify Auto Dusk</a></div>\n";
}
# Controls for the game in sign-up or In progress
if ( $status == "Sign-up" || $status == "In Progress" ) {
  print "<div><a href='javascript:random_selector()'>Random Selector Tool</a></div>\n";
  $sql_player_list = sprintf("select name from Players, Players_all, Users where Players.user_id=Players_all.original_id and Players.game_id=Players_all.game_id and Players_all.user_id=Users.id and Players.game_id=%s order by name",quote_smart($game['id']));
  $result_player_list = mysql_query($sql_player_list);
?>
<script language='javascript'>
//<!--
<?php
$list = "";
$count = 0;
while ( $player = mysql_fetch_array($result_player_list) ) {
  if ( $list != "" ) { $list .= ", "; }
  $list .= '"'.$player['name'].'"';
  $count ++;
}
 print "var Player_list = new Array($list)\n";
?>

function random_selector() {
  myelement = document.getElementById("control_space")
  myelement.style.visibility='visible'
  myelement.innerHTML = "<form><table class='forum_table'>"
  myelement.innerHTML += "<tr><td>Choose:</td><td><input type='text' size='2' id='rand_count' value='1' /></td></tr>"
  myelement.innerHTML += "<tr><td><input type='checkbox' onClick=select_all() id='all' /></td><td>Select All</td></tr>"
  for(var i=0; i < <?=$count;?>; i++) {
    myelement.innerHTML += "<tr><td><input type='checkbox' id='"+Player_list[i]+"' /></td><td>"+Player_list[i]+"</td></tr>\n"
  }
  myelement.innerHTML += "<tr><td colspan='2'><input type='button' value='Submit' onClick='select_random()' /></td></tr>\n"
  myelement.innerHTML += "</table>"
  myelement.innerHTML += "<span align='right'><a href='javascript:close(\"control_space\")'>[close]</a></span>\n"
  myelement.innerHTML += "</forum>"
}

function select_all() {
  if ( document.getElementById('all').checked ) {
    for(var i=0; i < <?=$count;?>; i++) {
	  document.getElementById(Player_list[i]).checked = true
	}
  } else {
    for(var i=0; i < <?=$count;?>; i++) {
	  document.getElementById(Player_list[i]).checked = false
	}
  }
}

function select_random() {
  var rand_list = new Array()
  c = 0;
  for(var i=0; i < <?=$count;?>; i++) {
    if ( document.getElementById(Player_list[i]).checked ) {
     rand_list[c] = Player_list[i]
	 c++
	}
  }
  num = document.getElementById('rand_count').value
  if ( num <= c ) { 
    var r_list = new Array()
    for(var i=0; i < num; i++ ) {
      r = Math.floor(Math.random()*c)
    	while ( in_array(r,r_list) ) {
        r = Math.floor(Math.random()*c)
    	}
  	  r_list[i] = r
    }
  }
  myelement = document.getElementById("control_space")
  myelement.style.visibility='visible'
  myelement.innerHTML = ""
  if ( num <= c ) {
    myelement.innerHTML += "<ul>"
    for(var i=0; i<num; i++ ) {
        myelement.innerHTML += "<li>"+rand_list[r_list[i]]+"</li>"
    }
	myelement.innerHTML += "</ul>";
  } else {
    myelement.innerHTML += "Not enough players<br />selected to provide<br />results.";
  }
    myelement.innerHTML += "<br /><span align='right'><a href='javascript:close(\"control_space\")'>[close]</a></span>"
}

function in_array(mystring,myarray) {
  for (var i=0; i< myarray.length; i++ ) {
    if ( mystring == myarray[i] ) {
      return true;
	}
  }
  return false;
}
//-->
</script>

<?php
}
?>
<div style='position:absolute; visibility:hidden; background-color:white; border:1px solid black;' id='control_space'></div>
</td></tr>
</table></div>
<br />
<?php

  }
?>
<div class="modal micromodal-slide" id="modal-edit-game" aria-hidden="true">
  <div class="modal__overlay" tabindex="-1" data-micromodal-close>
    <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-edit-game-title">
      <header class="modal__header">
        <h2 class="modal__title" id="modal-edit-game-title">
          Edit
        </h2>
        <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
      </header>
      <main class="modal__content" id="modal-edit-game-content">

        <img id='busy' style='visibility:hidden' src='/assets/images/ajax_busy.gif' />
        <div id='edit_space'><?php clear_editSpace(); ?></div>

      </main>
      <footer class="modal__footer">
        <button class="modal__btn modal__btn-primary">Continue</button>
        <button class="modal__btn" data-micromodal-close aria-label="Close this dialog window">Close</button>
      </footer>
    </div>
  </div>
</div>
<?php
}
# Create a place for the Player to make their own comments about the game.
if ( $isplayer && $status != "Sign-up") { 
  $sql_player_comment = sprintf("select user_comment, original_id from Games, Players, Players_all where Games.id=Players_all.game_id and Games.id=Players.game_id and Players_all.original_id=Players.user_id and Players_all.game_id=Players.game_id and Players_all.user_id=%s and Players_all.game_id=%s",quote_smart($uid),quote_smart($game['id']));
  $result_player_comment = mysql_query($sql_player_comment);
  $player_comment = mysql_result($result_player_comment,0,0); 
  $player_original_id = mysql_result($result_player_comment,0,1);
?>
<script language='javascript'>
<!--
var user_id = "<?=$uid;?>";
var game_id = "<?=$game['id'];?>";
var original_id = "<?=$player_original_id;?>";
var myDiv = "";
var myComment = "";
var myForm = "";

function edit_player_comment() {
  myComment = document.getElementById('player_comment');
  myForm = document.getElementById('form_player_comment');
  myDiv = myForm
  agent.call('','edit_dialog','update_div',user_id,game_id,original_id);
}

function update_div (str) {
  myComment.style.visibility = "hidden";
  myComment.style.position = "absolute";
  myComment.innerHTML = "";
  myForm.style.visibility = "hidden";
  myForm.style.position = "absolute";
  myForm.innerHTML = "";
  myDiv.style.visibility = "visible";
  myDiv.style.position = "static";
  myDiv.innerHTML = str;
}

function submit_comment() {
  comment = document.getElementById('new_comment').value;
  myComment = document.getElementById('player_comment');
  myForm = document.getElementById('form_player_comment');
  myDiv = myComment
  agent.call('','update_comment','update_div',user_id,game_id,original_id,comment);
}
function clear_edit() {
  comment = document.getElementById('new_comment').value;
  myComment = document.getElementById('player_comment');
  myForm = document.getElementById('form_player_comment');
  myDiv = myComment
  if (comment == "" ) { comment = "&nbsp;" }
  update_div(comment)
}
 //-->
</script>
<br />
<table class='forum_table' width='100%'>
<tr><th>My Comments</th></tr>
<tr><td align='center'>The Comments you write here will be publically viewable on <a href="<?="$player$username/games_played";?>">your game page</a> after the game is finished. </td></tr>
<tr><td><div id='player_comment' onMouseOver='show_hint("Click to Edit your Comment")' onMouseOut='hide_hint()' onClick='edit_player_comment()' style='visibility:visible; position:static;'><?=$player_comment;?>&nbsp;</div>
<div id='form_player_comment' style='visibiliy:hidden; position:absolute;'></div>
</td></tr>
</table>
<?php
}
?>


