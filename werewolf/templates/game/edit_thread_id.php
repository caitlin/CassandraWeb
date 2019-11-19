<p><?php echo $instructions ?></p>
<form name='new_thread'>
    <input type='text' name='thread' value='<?php echo $game['thread_id']; ?>' />
    <input type='button' name='submit' value='submit' onClick='submit_thread()' />
</form>