<!-- Table with Main Game Information -->
<table class='forum_table' border='0' >
    <?php // MODERATORS ?>
    <tr>
        <td>
            <div <?=$open_comment;?>onMouseOver='show_hint("Click to Edit Moderators")' onMouseOut='hide_hint()' onClick='edit_mod()' <?=$close_comment;?>><b>Moderator: </b></div>
        </td>
        <td id='mod_td'>
            <?php render_view('templates/game/show_moderators', [ 
                'moderators' => $moderators,
                'post_counts' => $post_counts,
                'game' => $gameAttributes
            ]) ?>
        </td>
    </tr>
    <?php // DATES ?>
    <?php if ( !$subthread ) { ?>
        <tr>
            <td>
                <div <?=$open_comment;?>onMouseOver='show_hint("Click to Edit Dates")' onMouseOut='hide_hint()' onClick='edit_dates()' <?=$close_comment;?>><b>Dates: </b></div>
            </td>
            <td>
                <table border='0' width='100%'>
                    <tr>
                        <td id='date_td'>
                            <?php render_view('templates/game/show_dates', [ 
                                'game' => $gameAttributes
                            ]) ?>
                        </td>
                        <td align='right'><? print add_game_link($game['id']); ?></td>
                    </tr>
                </table>
            </td>
        </tr>
    <?php } ?>
    <?php // STATUS ?>
    <tr>
        <td>
            <div <?=$open_comment;?>onMouseOver='show_hint("Click to Change Status")' onMouseOut='hide_hint()' onClick='edit_status()' <?=$close_comment;?>><b>Status: </b><div>
        </td>
        <td>
            <table width='100%' border='0'>
                <tr>
                    <td>
                        <table width='100%'>
                            <tr>
                                <td id='status_td'>
                                    <div <?=$open_comment;?>onMouseOver='show_hint("Click to Change Status")' onMouseOut='hide_hint()' onClick='edit_status()' <?=$close_comment;?>><?=$game['status'].' - '.$game['phase'].' '.$game['day']; ?>
                                    <?php
                                    if ( $subthread) {
                                        $sql = "Select title, thread_id from Games where id='".$game['parent_game_id']."'";
                                        $result = mysql_query($sql);
                                        $parent_game = mysql_fetch_array($result);
                                        print " of <a href='$game_page".$parent_game['thread_id']."'>".$parent_game['title']."</a>";
                                    }
                                    ?>
                                    </div>
                                </td>
                                <td align='right'>
                                    <?php if ( $game['status'] == "In Progress" ) { ?>
                                        Next Post Scan at <?= $gameObj->get_next_post_scan() ?>
                                    <?php } ?>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td align='right'>
                        <?php
                        if ( $game['status'] == "Sign-up" ) {
                        $sql1 = "select count(*) from Players where game_id='".$game['id']."'";
                        $result1 = mysql_query($sql1);
                        $count = mysql_result($result1,0,0);
                        if ( $count < $game['max_players'] && !$isplayer ) {
                            print "<a href='${here}sign_me_up.php?action=add&game_id=".$game['id']."'>Sign Me UP!!!</a>";
                        }
                        if ( $isplayer ) {
                            if ( $player_info['need_to_confirm'] == 1 ) {
                            print "<a href='${here}sign_me_up.php?action=confirm&game_id=".$game['id']."'>Confirm</a><br />";
                            }
                            print "<a href='${here}sign_me_up.php?action=remove&game_id=".$game['id']."'>Remove me</a>";
                        }
                        }
                        ?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <?php // SPEED ?>
    <tr>
        <td>
            <div <?=$open_comment;?>onMouseOver='show_hint("Click to Change Speed")' onMouseOut='hide_hint()' onClick='edit_speed()' <?=$close_comment;?>><b>Speed:</b></div>
        </td>
        <td id='speed_td'>
            <?php render_view('templates/game/show_speed', [ 
                'game' => $gameAttributes 
            ]) ?>
        </td>
    </tr>
    <?php // DEADLINES ?>
    <tr>
        <td>
            <div <?=$open_comment;?> onMouseOver='show_hint("Click to Change Deadlines")' onMouseOut='hide_hint()' onClick='edit_deadline()' <?=$close_comment;?>><b>Deadlines:</b></div>
        </td>
        <td id='deadline_td'>
            <?php render_view('templates/game/show_deadline', [ 
                'game' => $gameAttributes
            ]) ?>
        </td>
    </tr>
    <?php // LYNCH DAYS ?>
    <?php
    if ( $lynch != "" ) {
        $sql = sprintf("SELECT concat_ws(', ',if(sun, 'Sun', null),if(mon, 'Mon', null), if(tue, 'Tue', null), if(wed, 'Wed', null), if(thu, 'Thu', null), if(fri, 'Fri', null), if(sat, 'Sat', null)) as lynch_days from Auto_dusk where  game_id=%s",quote_smart($game['id']));
        $result = mysql_query($sql);
        if ( mysql_num_rows($result) == 1 ) {
            $lynch_days = mysql_result($result,0,0);
            print"<tr><td><b>Lynch Days:</b></td><td>$lynch_days</td></tr>\n";
        }
    }
    ?>
    <?php // MAX PLAYERS ?>
    <?php if ( $game['status'] == "Sign-up" ) { ?>
        <tr>
            <td>
                <div <?=$open_comment;?> onMouseOver='show_hint(\"Click to Change Max Players\")' onMouseOut='hide_hint()' onClick='edit_maxplayers()' <?=$close_comment;?>><b>Max Players:</b></div>
            </td>
            <td id='td_maxplayers'>
                <?php render_view('templates/game/show_max_players', [ 
                    'game' => $gameAttributes
                ]) ?>
            </td>
        </tr>
    <?php } ?>
    <?php // COMPLEXITY ?>
    <?php if ( !$subthread) { ?>
        <tr>
            <td>
                <div <?=$open_comment;?> onMouseOver='show_hint("Click to Change Complexity")' onMouseOut='hide_hint()' onClick='edit_complex()' <?=$close_comment;?>><b>Complexity:</b></div>
            </td>
            <td id='td_complex'>
                <?php render_view('templates/game/show_complexity', [ 
                    'game' => $gameAttributes
                ]) ?>
            </td>
        </tr>
    <?php } ?>
    <?php // WINNER ?>
    <?php 
    $finished = false;
    if ( $status == "Finished" || $edit) {
        $finished = true;
    ?>
        <tr>
            <td>
                <div  <?=$open_comment;?> onMouseOver='show_hint("Click to Change Winner")' onMouseOut='hide_hint()' onClick='edit_winner()' <?=$close_comment;?>><b>Winner:</b></div>
            </td>
            <td id='win_td'>
                <?php render_view('templates/game/show_winner', [ 
                    'game' => $gameAttributes
                ]) ?>
            </td>
        </tr>
    <?php } ?>
    <?php // THREAD ID ?>
    <?php if ( $edit ) { ?>
        <tr>
            <td>
                <div <?=$open_comment;?>onMouseOver='show_hint("Click to change BGG Thread id")' onMouseOut='hide_hint()' onClick='edit_thread()' <?=$close_comment;?>><b>BGG<br />Thread id:</b></div>
            </td>
            <td id='thread_td'>
                <div <?=$open_comment;?>onMouseOver='show_hint("Click to change BGG Thread id")' onMouseOut='hide_hint()' onClick='edit_thread()' <?=$close_comment;?>><?=$game['thread_id'];?></div>
            </td>
        </tr>
    <?php } ?>
    <?php // SUBTHREADS ?>
    <?php
    if ( ! $subthread) {
        $sql = "select count(*) from Games where parent_game_id='".$game['id']."'";
        $result = mysql_query($sql);
        $num = mysql_result($result,0,0);
        if ( $num > 0 || $edit) {
        ?>
            <tr>
                <td>
                    <div <?=$open_comment;?> onMouseOver='show_hint("Click to Add or Delete a Sub-Thread")' onMouseOut='hide_hint()' onClick='edit_subt()' <?=$close_comment;?>><b>Sub-Threads:</b></div>
                </td>
                <td id='subt_td'>
                    <?php render_view('templates/game/show_subthreads', [ 
                        'subthreads' => $subthreads
                    ]) ?>
                </td>
            </tr>
    <?php } } ?>
    <?php // DESCRIPTION ?>
    <tr>
        <td>
            <div <?=$open_comment;?>onMouseOver='show_hint("Click to change Description")' onMouseOut='hide_hint()' onclick='edit_desc()' <?=$close_comment;?>><b>Description:</b></div>
        </td>
        <td id='desc_td'>
            <?php render_view('templates/game/show_description', [ 
                'description' => $gameAttributes['description'] 
            ]) ?>
        </td>
    </tr>
</table>
<!-- End of Main Game Table -->