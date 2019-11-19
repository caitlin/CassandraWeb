<p><?php echo $instructions ?></p>
<?php foreach ( $subthreads as $subthread ) { ?>
    <?php echo $subthread['title']." - ".$subthread['thread_id'] ?> <a href='javascript:delete_subt("<?php echo $subthread['thread_id'] ?>")'>delete</a> <br />
<?php } ?>
<form name='new_subt'>
    <input type='text' name='tid' />
    <a href='javascript:add_subt()'>Add a Sub-Thread</a>
    <br />
</form
