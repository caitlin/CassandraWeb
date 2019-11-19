<?php foreach ( $subthreads as $subthread ) { ?>
    <div onMouseOver='show_hint(\"Click to Edit Sub-Thread\")' onMouseOut='hide_hint()' onClick='edit_subt()'>
        <a href='/game/<?php echo $subthread['thread_id'] ?>'><?php echo $subthread['title'] ?></a><br />
    </div>
<?php } ?>