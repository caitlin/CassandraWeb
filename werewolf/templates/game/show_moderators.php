<?php
    $count = 0;
    foreach ( $moderators as $mod_id => $mod_name ) { 
        $post_count = $post_counts[$mod_id];
        if ( $count > 0 ) { echo ', '; }
?><?php echo get_player_page($mod_name) ?>
    <a href="/game/<?php echo $game['thread_id'] . '/' . $mod_name ?>">
    (<?php echo $post_count ?> <?php echo $post_count == 1 ? 'post' : 'posts' ?>)</a><?php 
        $count++;
    } 
?>