<p><?php echo $instructions ?></p>
<form name='change_maxp'>
    <input type='text' name='max_players' value='<?php echo $game['max_players'] ?>' />
    <input type='button' name='submit' value='submit' onClick='submit_maxplayers()' />
</form>