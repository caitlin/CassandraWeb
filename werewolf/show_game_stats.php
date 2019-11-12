<?php

include "php/accesscontrol.php";
include_once "php/db.php";
include_once "php/common.php";
include_once "timezone_functions.php";
include_once "edit_game_functions.php";
include_once "google_calendar_functions.php";
include_once "menu.php";
include_once "autocomplete.php";

#checkLevel($level,1);

$game_thread_id = $_REQUEST['thread_id'];
$here = "/";
$pagename = "show_game_stats.php";
$posts = "${here}game/$game_thread_id/";
$player = "${here}player/";
$game_page = "${here}game/";


if ( $game_thread_id == "" ) {
?>
<html>
<head>
<script language='javascript'>
<!--
window.history.back();
//-->
</script>
</head>
<body>
Please hit your browsers back button.
</body>
</html>
<?php
exit;
}

# Get Game info
$sql = "Select * from Games where thread_id=$game_thread_id";
$result = mysql_query($sql);
$game = mysql_fetch_array($result);
if ( mysql_num_rows($result) != 1 ) { $game['id'] = 0; }

$status = $game['status'];
$subthread = false;
if ( $status == "Sub-Thread" ) {
  $sql = "Select `status` from Games where id=".$game['parent_game_id'];
  $result = mysql_query($sql);
  $status = mysql_result($result,0,0);
  $subthread = true;
}
$sql = sprintf("select count(*) from Chat_rooms where game_id=%s",quote_smart($game['id']));
$result = mysql_query($sql);
$chats = mysql_result($result,0,0);

# Find out if the person viewing is the moderator.
$moderator = is_moderator($uid,$game['id']);

# Find out if the person viewing is a player.
$sql = "Select * from Players, Games where Players.game_id=Games.id and user_id=$uid and thread_id=$game_thread_id";
$result=mysql_query($sql);
$row_count = mysql_num_rows($result);
$isplayer = false;
if ( $row_count == 1 ) $isplayer = true;
$player_info = mysql_fetch_array($result);

# Find out if person viewing should have edit abilities.
$edit = false;
if ( $moderator || (($level == 1 || $level == 2)  && $status == 'Finished') ) {
  $edit = true;
}

if ( $game['id'] == "" || $game['id'] == 0 )  {$game['title'] = "Invalid Game";}

// Necessary functions (for ajax use in agent)
function edit_dialog($user_id,$game_id,$original_id) {
  $output = "<form>\n";
  if ( $user_id == $original_id ) {
    $table = "Players";
    $field = "user_comment";
    $id = "user_id";
  } else {
    $table = "Replacements";
    $field = "rep_comment";
    $id = "replace_id";
  }
  $sql = sprintf("select %s from %s where %s=%s and game_id=%s",$field,$table,$id,quote_smart($user_id),quote_smart($game_id));
  $result = mysql_query($sql);
  $comment = mysql_result($result,0,0);
  $output .= "<textarea id='new_comment' name='new_comment' style='width:100%; height:80px;'>$comment</textarea><br />";
  $output .= "<input type='button' name='submit' value='submit' onclick='submit_comment()' /> ";
  $output .= "<input type='button' name='cancel' value='cancel' onclick='clear_edit()' />";
  $output .= "</form>\n";

  return $output;
}

function update_comment($user_id,$game_id,$original_id,$comment){
  if ( $user_id == $original_id ) {
    $table = "Players";
    $field = "user_comment";
    $id = "user_id";
  } else {
    $table = "Replacements";
    $field = "rep_comment";
    $id = "replace_id";
  }
  $comment = stripslashes($comment);
  $comment = safe_html($comment);
  $sql = sprintf("update %s set %s=%s where %s=%s and game_id=%s",$table,$field,quote_smart($comment),$id,quote_smart($user_id),quote_smart($game_id));
  $result = mysql_query($sql);

  return $comment;
}


// CONTROLLER
require_once('src/Games/Game.php');
$gameObj = new Game($game_thread_id);
$nonplayers_who_posted = $gameObj->get_nonplayers_who_posted();
$latest_post_id = $gameObj->get_latest_post_id();
$wolfy_awards = $gameObj->get_wolfy_awards();


// RENDER VIEW
require_once 'templates/game/game.php';
?>