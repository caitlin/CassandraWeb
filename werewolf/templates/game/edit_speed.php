<p><?php echo $instructions ?></p>
<form name='new_speed'>
    <select name='speed'>
        <?php foreach ( $speedOptions as $speedOption ) { ?>
            <option <?php if ( $speedOption == $game['speed'] ) { echo 'selected="selected"'; } ?> value='<?php echo $speedOption ?>'>
                <?php echo $speedOption ?>
            </option>
        <?php } ?>
    </select>
    <br />
    <input type='button' name='submit' value='submit' onClick='submit_speed()' />
</form>