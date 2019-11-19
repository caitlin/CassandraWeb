<p><?php echo $instructions ?></p>
<form name='new_title'>
    <input type='text' name='title' value='<?php echo $game['title'] ?>' />
    <input type='button' name='submit' value='submit' onClick='submit_name()'/>
</form>