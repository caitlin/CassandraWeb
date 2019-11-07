<a id='game_link' href="http://www.boardgamegeek.com/thread/<?=$game_thread_id;?>">Go to Game Thread</a> 
<?php if ( $status != "Sign-up" ) { ?>
    : <a href='http://www.boardgamegeek.com/article/<?=$latest_post_id;?>#<?=$latest_post_id;?>'>Last retrieved post</a>
<?php } ?>
<?php if ( $game['automod_id'] != "") { ?>
    <br />
    <a href='../automod/template/<?=$game['automod_id'];?>'>Automod Template #<?=$game['automod_id'];?></a>
<?php } ?>
<br />
<?php if ( $game['auto_vt'] != "No" ) { ?>
    <a href='<?=$posts;?>tally'>Vote Tally</a>
    : <a href='<?=$posts;?>tally_inverted'>Inverted Tally</a>
    : <a href='<?=$posts;?>votes'>Vote Log</a>
    : <a href='<?=$posts;?>votes/xml'>XML</a>
    : <a href='http://gamedecay.com/voteviewer.html?g=<?=$game_thread_id;?>'>VoteViewer</a>
    <br />
<?php } ?>
<?php if ( $chats > 0 ) { ?>
  <a href='<?= $posts ?>chat'>Cassandra Communication System</a><br />
<?php } ?>
<a href='javascript:pm_players()'>GeekMail Players</a><br />