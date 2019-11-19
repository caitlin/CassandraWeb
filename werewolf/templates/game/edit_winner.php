<p><?php echo $instructions ?></p>
<form name='new_winner'>
    <select name='winner'>
        <option value=''></option>
        <?php foreach ( $winnerOptions as $winnerOption ) { ?>
            <option <?php if ( $winnerOption == $game['winner'] ) { echo 'selected="selected"'; } ?> value='<?php echo $winnerOption ?>'>
                <?php echo $winnerOption ?>
            </option>
        <?php } ?>
    </select>
    <input type='button' name='submit' value='submit' onClick='submit_winner()' />
</form>
