<?php

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header("Cache-Control: no-cache, must-revalidate" );
header("Pragma: no-cache" );

include_once "edit_game_functions.php";
include_once "php/bgg.php";
include_once "php/common.php";
require_once('src/Games/Game.php');
require_once('src/Users/User.php');

function render_view($file, $vars = []) {
    extract($vars);
    include dirname(__FILE__) . '/' . $file . '.php';
}

$cache = init_cache();

if ( ! isset($_REQUEST['q']) ) {
    clear_editSpace();
    exit;
}

$game_id = $_REQUEST['game_id'];
$game = Game::game_id($game_id);

// =============================================================================
// Handle each edit/update action
// =============================================================================

switch ( $_REQUEST['q'] ) {

    // =====================================================
    // GAME INFO
    // =====================================================

    // ---------------------------------
    // Moderators
    // ---------------------------------

    // Replace text with form to edit_moderators.
    case 'e_moderator':
        $instructions = "Select moderators.  Use Control to select more than one.";

        $moderators = $game->get_moderators();
        $users = User::get_all();

        render_view('templates/game/edit_moderators', [
            'instructions' => $instructions,
            'users' => $users,
            'game' => [
                'moderator_ids' => array_keys($moderators)
            ]
        ]);
    break;

    // Edit database with new Moderator list and return text to original.
    case 's_moderator':
        $mod_list = split(",", $_REQUEST['modlist']);

        $game->set_moderators($mod_list);
        $moderators = $game->get_moderators();
        $post_counts = $game->get_post_count_for_users(array_keys($moderators));
        $thread_id = $game->get_thread_id();

        $cache->clean('front-signup-' . $game_id);
        $cache->clean('front-signup-swf-' . $game_id);
        $cache->clean('front-signup-fast-' . $game_id);
        $cache->remove('games-signup-fast-list', 'front');
        $cache->remove('games-signup-swf-list', 'front');
        $cache->remove('games-signup-list', 'front');

        render_view('templates/game/show_moderators', [
            'game' => [
                'moderators' => $moderators,
                'post_counts' => $post_counts,
                'thread_id' => $thread_id
            ]
        ]);
    break;

    // ---------------------------------
    // Date
    // ---------------------------------

    // Replace text with form to edit_dates.
    case 'e_date':
        $instructions = "Edit the start and end dates.";

        $dates = $game->get_dates();

        render_view('templates/game/edit_dates', [
            'instructions' => $instructions,
            'game' => $dates
        ]);
    break;

    // Edit database with new Dates and return text to original.
    case 's_date':
        if ( !isset($_REQUEST['speed']) ) {
            $start_timestamp = $_REQUEST['sdate']." ".$_REQUEST['stime'];
            $end_timestamp = $_REQUEST['edate'];
            $swf = $_REQUEST['swf'];
            $game->set_dates($start_timestamp, $end_timestamp, $swf);
        }

        $cache->remove('games-signup-fast-list', 'front');
        $cache->remove('games-signup-swf-list', 'front');
        $cache->remove('games-signup-list', 'front');
        $cache->clean('front-signup-' . $game_id);
        $cache->clean('front-signup-swf-' . $game_id);
        $cache->clean('front-signup-fast-' . $game_id);

        $dates = $game->get_dates();

        render_view('templates/game/show_dates', [
            'instructions' => $instructions,
            'game' => $dates
        ]);
    break;

    // ---------------------------------
    // Description
    // ---------------------------------

    // Replace text with from to edit description.
    case 'e_description':
        $description = $game->get_description();
        
        render_view('templates/game/edit_description', [
            'instructions' => "Edit the game description.  To format your text you must use html.",
            'description' => $description
        ]);
    break;

    // Edit database with new Description return text to original.
    case 's_description':
        $description = $_REQUEST['desc'];

        $game->set_description($description);

        render_view('templates/game/show_description', [
            'description' => stripslashes($description)
        ]);
    break;

    // ---------------------------------
    // Status
    // ---------------------------------

    // Replace text with form to change Status.
    case 'e_status':
        $instructions = "Change the status of the game.  In-Progress means that the players can only see their own roles, and that nobody can see any of the comments below.  Once you set the game to 'Finished' then everyone will be able to see everything.  When you set a game to 'Finished' please don't forget to set the winner.  If you are using the Automatied vote tally system there should be no need to manually change the period or number.";

        $full_status = $game->get_status();
        $statusOptions = Game::field_options_for('status');
        $phaseOptions = Game::field_options_for('phase');

        render_view('templates/game/edit_status', [
            'instructions' => $instructions,
            'description' => $description,
            'statusOptions' => $statusOptions,
            'phaseOptions' => $phaseOptions,
            'game' => $full_status
        ]);
    break;

    // Edit database with new Status return text to original.
    case 's_status':
        $status = $_REQUEST['status'];
        $phase = $_REQUEST['phase'];
        $day = $_REQUEST['day'];

        $game->set_status($status, $phase, $day);

        if($status == 'In Progress') {
            $cache->remove('total-games', 'front');
            $cache->remove('games-in-progress-fast-list', 'front');
            $cache->remove('games-in-progress-list', 'front');
            $cache->remove('current-games', 'front');
            $cache->remove('games-signup-fast-list', 'front');
            $cache->remove('games-signup-swf-list', 'front');
            $cache->remove('games-signup-list', 'front');
            $cache->clean('front-signup-' . $game_id);
            $cache->clean('front-signup-swf-' . $game_id);
            $cache->clean('front-signup-fast-' . $game_id);
            } elseif($status == 'Finished') {
            $cache->remove('current-games', 'front');
            $cache->remove('games-in-progress-fast-list', 'front');
            $cache->remove('games-in-progress-list', 'front');
            $cache->remove('games-ended-list', 'front');
            $game->remove_from_physics_processing();
        }

        render_view('templates/game/show_status', [
            'game' => [
                'status' => $status,
                'phase' => $phase,
                'day' => $day
            ]
        ]);
    break;

    // ---------------------------------
    // Speed
    // ---------------------------------

    // Replace text with form to change speed.
    case 'e_speed':
        $instructions = "Change the speed of the game.";

        $speed = $game->get_speed();
        $speedOptions = Game::field_options_for('deadline_speed');

        render_view('templates/game/edit_speed', [
            'instructions' => $instructions,
            'speedOptions' => $speedOptions,
            'game' => [
                'speed' => $speed
            ]
        ]);
    break;

    // Edit database with new Speed, return text to original.
    case 's_speed':
        $speed = $_REQUEST['speed'];

        $game->set_speed($speed);

        $cache->remove('games-in-progress-fast-list', 'front');
        $cache->remove('games-in-progress-list', 'front');
        $cache->remove('games-signup-fast-list', 'front');
        $cache->remove('games-signup-swf-list', 'front');
        $cache->remove('games-signup-list', 'front');

        render_view('templates/game/show_speed', [
            'game' => [
                'speed' => $speed
            ]
        ]);
    break;

    // ---------------------------------
    // Deadlines
    // ---------------------------------

    // Replace text with form to change deadlines.
    case 'e_deadline':
        $instructions = "Change the deadlines of the game.";

        $deadlines = $game->get_deadlines();
        
        render_view('templates/game/edit_deadline', [
            'instructions' => $instructions,
            'game' => $deadlines
        ]);
    break;

    // Edit database with new Deadlines return text to original.
    case 's_deadline':
        
        if ( isset($_REQUEST['speed']) ) {
            // If we're changing the speed of a game, the deadlines need to be visually refreshed
            $deadlines = $game->get_deadlines();
            extract($deadlines);
            $speed = $_REQUEST['speed'];
        } else {
            // Otherwise, we're updating as a result of the edit deadlines form
            $dusk = $_REQUEST['dusk'];
            $dawn = $_REQUEST['dawn'];
            $day_length = $_REQUEST['day_length'];
            $night_length = $_REQUEST['night_length'];

            $game->set_deadlines($dusk, $dawn, $day_length, $night_length);
            $speed = $game->get_deadline_speed();
        }

        $cache->remove('games-in-progress-fast-list', 'front');
        $cache->remove('games-in-progress-list', 'front');
        $cache->remove('games-signup-fast-list', 'front');
        $cache->remove('games-signup-swf-list', 'front');
        $cache->remove('games-signup-list', 'front');
        $cache->clean('front-signup-' . $game_id);
        $cache->clean('front-signup-swf-' . $game_id);
        $cache->clean('front-signup-fast-' . $game_id);

        render_view('templates/game/show_deadline', [
            'game' => [
                'speed' => $speed,
                'dusk' => $dusk,
                'dawn' => $dawn,
                'day_length' => $day_length,
                'night_length' => $night_length
            ]
        ]);
    break;

    // ---------------------------------
    // Winner
    // ---------------------------------

    // Replace text with form to change Winner
    case 'e_winner':
        $instructions = "Change the winner of the game.  If an evil team one select evil.  If the good team won, select good,  If the game was neither good vs evil or had an individual winner then choose 'other'.";

        $winner = $game->get_winner();
        $winnerOptions = Game::WINNERS;

        render_view('templates/game/edit_winner', [
            'instructions' => $instructions,
            'winnerOptions' => $winnerOptions,
            'game' => [
                'winner' => $winner
            ]
        ]);
    break;

    // Edit database with new Winner return text to original.
    case 's_winner':
        $winner = $_REQUEST['winner'];
        
        $game->set_winner($winner);
        
        $cache->remove('evil-games', 'front');
        $cache->remove('good-games', 'front');
        $cache->remove('other-games', 'front');

        render_view('templates/game/show_winner', [
            'game' => [
                'winner' => $winner
            ]
        ]);
    break;

    // ---------------------------------
    // Title
    // ---------------------------------

    // Replace Title with form to change title
    case 'e_name':
        $instructions = "You can change the name of the game or sub-thread.";

        $title = $game->get_title();

        render_view('templates/game/edit_title', [
            'instructions' => $instructions,
            'game' => [
                'title' => $title
            ]
        ]);
    break;
    
    // Change name and replace text with new name
    case 's_name':
        $title = safe_html($_REQUEST['title']);
        $game->set_title($title);

        $cache->clean('front-signup-' . $game_id);
        $cache->clean('front-signup-swf-' . $game_id);
        $cache->clean('front-signup-fast-' . $game_id);
        $cache->remove('game-' . $game_id, 'front');
        
        $number = $game->get_number();

        render_view('templates/game/show_title', [
            'game' => [
                'title' => $title,
                'number' => $number
            ]
        ]);
    break;

    // ---------------------------------
    // Complexity
    // ---------------------------------

    case 'e_complex':
        $instructions = "Change the complexity of the game.";
        
        $complexity = $game->get_complexity();
        $complexityOptions = Game::COMPLEXITIES;

        render_view('templates/game/edit_complexity', [
            'instructions' => $instructions,
            'complexityOptions' => $complexityOptions,
            'game' => [
                'complexity' => $complexity
            ]
        ]);
    break;
        
    case 's_complex':
        $complexity = $_REQUEST['complex'];

        $game->set_complexity($complexity);

        $cache->clean('front-signup-' . $game_id);
        $cache->clean('front-signup-swf-' . $game_id);
        $cache->clean('front-signup-fast-' . $game_id);

        render_view('templates/game/show_complexity', [
            'game' => [
                'complexity' => $complexity
            ]
        ]);
    break;

    // ---------------------------------
    // Max Players
    // ---------------------------------

     // Show change Max Players Dialoge Box
     case 'e_maxplayers':
        $instructions = "Change Max number of players.  If you make this number less than or equal to the number of people currently signed up then no more people can sign up via Cassandra.";
        
        $max_players = $game->get_max_players();

        render_view('templates/game/edit_max_players', [
            'instructions' => $instructions,
            'game' => [
                'max_players' => $max_players
            ]
        ]);
    break;

    case 's_maxplayers':
        $max_players = $_REQUEST['max_players'];
        $game->set_max_players($max_players);

        $cache->clean('front-signup-' . $game_id);
        $cache->clean('front-signup-swf-' . $game_id);
        $cache->clean('front-signup-fast-' . $game_id);

        render_view('templates/game/show_max_players', [
            'game' => [
                'max_players' => $max_players
            ]
        ]);
    break;

    // ---------------------------------
    // Subthread
    // ---------------------------------

    # Replace text with form to add or delete sub-threads.
    case 'e_subthread':
        print "If your game has sub-threads associated with it, such as threads where team-member can discussthings.  Then you add them here.  Once you have added the BGG thead_id you can edit that 'game' page just as you are editing this one.<br /><br />";
        edit_subt($game_id);
    break;
    
    # Delete a Sub-Thread
    case 'd_subthread':
        $sql = sprintf("select id from Games where thread_id=%s",quote_smart($_REQUEST['thread_id']));
        $result = mysql_query($sql);
        $st_game_id = mysql_result($result,0,0);
        $sql = "delete from Games where id ='$st_game_id'";
        $result = mysql_query($sql);
        show_subt($game_id);
    break;
    
    # Add a Sub-Thread
    case 'a_subthread':
        $sql = sprintf("insert into Games (id, title, status, thread_id, parent_game_id) values ( NULL, 'Sub-Thread', 'Sub-Thread', %s, %s)",quote_smart($_REQUEST['thread_id']),quote_smart($game_id));
        $result = mysql_query($sql);
        $new_game_id = mysql_insert_id();
        $sql = sprintf("select user_id from Moderators where game_id=%s",quote_smart($game_id));
        $result = mysql_query($sql);
        while ( $mod = mysql_fetch_array($result) ) {
        $sql2 = "insert into Moderators (user_id, game_id) values ('".$mod['user_id']."', '$new_game_id')";
        $result2 = mysql_query($sql2);
        }
        show_subt($game_id);
    break;

    // ---------------------------------
    // Thread ID
    // ---------------------------------

    # Replace Thread ID with form to change thread id
    case 'e_thread':
        print "You can change the BGG thread_id.  This should only be done when changing a game from a sign-up thread to a game thread.<br /><br />";
        edit_thread($game_id);
    break;
    
    # Change thread_id and replace text with new id
    case 's_thread':
        $sql = sprintf("update Games set thread_id=%s where id=%s",quote_smart($_REQUEST['thread_id']),quote_smart($game_id));
        $result = mysql_query($sql);
        $cache->clean('front-signup-' . $game_id);
        $cache->clean('front-signup-swf-' . $game_id);
        $cache->clean('front-signup-fast-' . $game_id);
        $cache->remove('game-' . $game_id, 'front');
        $output = "<div onMouseOver='show_hint(\"Click to change BGG Thread id\")' onMouseOut='hide_hint()' onClick='edit_thread()'>".$_REQUEST['thread_id']."</div>";
        
        print $output;
    break;

    // =====================================================
    // PLAYER TABLE
    // =====================================================

    # Show Player edit dialog.
        case 'e_player':
        print "You can edit or delete a player.  If the player is being replaced after the game has started, please use the replace function instead of deleting.<br /><br />";
        edit_player($_REQUEST['uid'],$_REQUEST['row'],$game_id);
    break;

    # Delete a replacement.
    case 'd_replace':
        $sql = sprintf("delete from Replacements where user_id=%s and replace_id=%s and game_id=%s",quote_smart($_REQUEST['user_id']),quote_smart($_REQUEST['replace_id']),quote_smart($game_id));
        $result=mysql_query($sql);
        $sql = sprintf("select name from Users where id=%s",quote_smart($_REQUEST['user_id']));
        $result=mysql_query($sql);
        $name = mysql_result($result,0,0);
        print display_player($name,$_REQUEST['user_id'],$game_id);
    break;

    # Change Players details.
    case 's_player':
        $user_id = $_REQUEST['uid'];
        if ( $_REQUEST['rep_id'] != "0" ) {
        $sql = sprintf("insert into Replacements (user_id, game_id, replace_id, period, number) values ( %s, %s, %s, %s, %s )",quote_smart($user_id),quote_smart($game_id), quote_smart($_REQUEST['rep_id']), quote_smart($_REQUEST['rep_p']), quote_smart($_REQUEST['rep_n']));
        $result = mysql_query($sql);
        }
        if ( $_REQUEST['d_day'] == "" ) {
        $sql = sprintf("update Players set role_name=%s, role_id=%s, side=%s, death_phase=%s, death_day=NULL, mod_comment=%s, player_alias=%s, alias_color=%s where user_id=%s and game_id=%s",quote_smart($_REQUEST['r_name']), quote_smart($_REQUEST['r_id']), quote_smart($_REQUEST['side']), quote_smart($_REQUEST['d_phase']), quote_smart($_REQUEST['comment']), quote_smart($_REQUEST['player_alias']), quote_smart($_REQUEST['alias_color']), quote_smart($user_id), quote_smart($game_id));
        } else {
        $sql = sprintf("update Players set role_name=%s, role_id=%s, side=%s, death_phase=%s, death_day=%s, mod_comment=%s, player_alias=%s, alias_color=%s where user_id=%s and game_id=%s",quote_smart($_REQUEST['r_name']), quote_smart($_REQUEST['r_id']), quote_smart($_REQUEST['side']), quote_smart($_REQUEST['d_phase']), quote_smart($_REQUEST['d_day']), quote_smart($_REQUEST['comment']), quote_smart($_REQUEST['player_alias']), quote_smart($_REQUEST['alias_color']), quote_smart($user_id), quote_smart($game_id));
        }
        $result = mysql_query($sql);
        $sql = sprintf("update Players set need_replace=null where game_id=%s and user_id=%s",quote_smart($game_id),quote_smart($user_id));
        $result = mysql_query($sql);
        createPlayer_table(true,$game_id);
    break;

    # Show Add Player dialog.
    case 'a_player':
        print "You can add a player to your game.  If this player is a replacement player, please edit the player he is replacing rather than adding him/her here.<br /><b>Important:</b>If the player is listed in the autocomplete drop down, please select their name from that list, rather than just typing it in.  Only type in names that are not in the drop-down list, these should be newbies.  And make sure you spell the name correctly.";
        add_player($game_id);
    break;

    # Add New Player.
    case 'an_player':
        if ( $_REQUEST['s'] == "new" ) {
        // Check to see if the username is a banned palyer
        $sql = sprintf("select level from Users where name=%s",quote_smart($_REQUEST['user_id']));
        $result = mysql_query($sql);
        if(mysql_num_rows($result)==0) {
        $level = 3;
        }
        else {
        $level = mysql_result($result,0,0);
        }

        if ( $level == '0' ) {
        print "<span style='color:red;'>The player you just tried to add has been banned from Cassandra.  </span><br />\n";
        createPlayer_table(true,$game_id);
        break;
        } 
        // Check to see if the username is a valid BGG username
        print "<!--\n";
        $bgg_result = is_bgg_user($_REQUEST['user_id']);
        print "-->\n";
        if ( $bgg_result == "true" ) {
            $sql = sprintf("insert into Users (id, name) values ( NULL, %s ) ",quote_smart($_REQUEST['user_id']));
        $result = mysql_query($sql);
        $id = mysql_insert_id();
        } else {
        print "<span style='color:red;'>The player you just tried to add is not a valid BGG user.  You can only add BGG users.</span><br />\n";
        createPlayer_table(true,$game_id);
        break;
        }
        if ( $id == 0 ) {
            $sql = sprintf("select id from Users where name=%s",quote_smart($_REQUEST['user_id']));
            $result = mysql_query($sql);
        $id = mysql_result($result,0,0);
        }
        $_REQUEST['user_id'] = $id;
        }
        $sql = sprintf("insert into Players (user_id, game_id) values (%s, %s)",quote_smart($_REQUEST['user_id']),quote_smart($game_id));
        $result = mysql_query($sql);
        $cache->clean('front-signup-' . $game_id);
        $cache->clean('front-signup-swf-' . $game_id);
        $cache->clean('front-signup-fast-' . $game_id);
        print "<!--";
        edit_playerlist_post($game_id);
        print "-->\n";
        createPlayer_table(true,$game_id);
    break;

    # Delete a Player
    case 'd_player':
        $sql = sprintf("delete from Players where user_id=%s and game_id=%s",quote_smart($_REQUEST['user_id']),quote_smart($game_id));
        $result = mysql_query($sql);
        $cache->clean('front-signup-' . $game_id);
        $cache->clean('front-signup-swf-' . $game_id);
        $cache->clean('front-signup-fast-' . $game_id);
        edit_playerlist_post($game_id);
        createPlayer_table(true,$game_id);
    break;

    # Show Alias Name Edit Dialog
    case 'e_alias':
        print "Enter the alias to be used in voting for each of the players.  Do not use the same alias twice.<br /><br />";
        edit_alias($game_id);
    break;

    # Change Aliases for all players.
    case 's_alias':
        $sql = sprintf("select Users.id from Users, Players where Users.id=Players.user_id and game_id=%s order by name",quote_smart($game_id));
        $result = mysql_query($sql);
        $aliases = split(",", $_REQUEST['aliases']);
        $colors = split(",", $_REQUEST['colors']);
        $i = 0;
        while ( $user = mysql_fetch_array($result) ) {
        $sql2 = sprintf("update Players set player_alias=%s, alias_color=%s where user_id=%s and game_id=%s",quote_smart($aliases[$i]),quote_smart($colors[$i]),quote_smart($user['id']), quote_smart($game_id));
        $result2 = mysql_query($sql2);
        $i++;
        }
        createPlayer_table(true,$game_id);
    break;

    # Show Role Name Edit Dialog.
    case 'e_rolename':
        print "Enter the names of each of the players roles.  Please do not use comma's in the name.<br /><br />";
        edit_rolename($game_id);
    break;

    # Change Role Names for all players.
    case 's_rolename':
        $sql = sprintf("select Users.id from Users, Players where Users.id=Players.user_id and game_id=%s order by name",quote_smart($game_id));
        $result = mysql_query($sql);
        $rnames = split(",", $_REQUEST['rnames']);
        $i = 0;
        while ( $user = mysql_fetch_array($result) ) {
        $sql2 = sprintf("update Players set role_name=%s where user_id=%s and game_id=%s",quote_smart($rnames[$i]),quote_smart($user['id']), quote_smart($game_id));
        $result2 = mysql_query($sql2);
        $i++;
        }
        createPlayer_table(true,$game_id);
    break;

    # Show Role Type Edit Dialog.
    case 'e_roletype':
        print "Select the role type of each of the players.<br /><br />";
        edit_roletype($game_id);
    break;

    # Change the Role Type for each player.
    case 's_roletype':
        $sql = sprintf("select Users.id from Users, Players where Users.id=Players.user_id and game_id=%s order by name",quote_smart($game_id));
        $result = mysql_query($sql);
        $rtypes = split(",", $_REQUEST['rtypes']);
        $i = 0;
        while ( $user = mysql_fetch_array($result) ) {
        $sql2 = sprintf("update Players set role_id=%s where user_id=%s and game_id=%s",quote_smart($rtypes[$i]), $user['id'], quote_smart($game_id));
        $result2 = mysql_query($sql2);
        $i++;
        }
        createPlayer_table(true,$game_id);
    break;

    # Show Team Edit Dialog.
    case 'e_team':
        print "Select the team of each of the players.<br /><br />";
        edit_team($game_id);
    break;

    # Change the Teams for each player
    case 's_team':
        $sql = sprintf("select Users.id from Users, Players where Users.id=Players.user_id and game_id=%s order by name",quote_smart($game_id));
        $result = mysql_query($sql);
        $teams = split(",", $_REQUEST['teams']);
        $i = 0;
        while ( $user = mysql_fetch_array($result) ) {
        $sql2 = sprintf("update Players set side=%s  where user_id='".$user['id']."' and game_id=%s",quote_smart($teams[$i]), quote_smart($game_id));
        $result2 = mysql_query($sql2);
        $i++;
        }
        createPlayer_table(true,$game_id);
    break;

    # Show Comment Edit Dialog.
    case 'e_comments':
        print "Edit Player comments.  These won't be seen by others until you set the game status to finished.<br /><br />";
        edit_comment($game_id);
    break;
   
    case 'e_deaths':
        print "Record when each player died in the game.<br />";
        edit_deaths($game_id);
    break;

    case 's_deaths':
        $sql = sprintf("select Users.id from Users, Players where Users.id=Players.user_id and game_id=%s order by name",quote_smart($game_id));
        $result = mysql_query($sql);
        $phases = split(",", $_REQUEST['phases']);
        $days = split(",", $_REQUEST['days']);
        $i = 0;
        while ( $user = mysql_fetch_array($result) ) {
        if ( $days[$i] == "" ) { 
            $sql2 = sprintf("update Players set death_phase=%s, death_day=NULL  where user_id=%s and game_id=%s",quote_smart($phases[$i]), quote_smart($user['id']), quote_smart($game_id));
        } else {
            $sql2 = sprintf("update Players set death_phase=%s, death_day=%s  where user_id=%s and game_id=%s",quote_smart($phases[$i]), quote_smart($days[$i]), quote_smart($user['id']), quote_smart($game_id));
        }
        $result2 = mysql_query($sql2);
        $i++;
        }
        createPlayer_table(true,$game_id);
    break;

}
?>