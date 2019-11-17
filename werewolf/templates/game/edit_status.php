<p><?php echo $instructions ?></p>
<form name='new_status'>
    <select name='status'>
        <?php foreach ( $statusOptions as $statusOption ) { ?>
            <option <?php if ( $statusOption == $game['status'] ) { echo 'selected="selected"'; } ?> value='<?php echo $statusOption ?>'>
                <?php echo $statusOption ?>
            </option>
            <?php if ( $statusOption == $game['status'] ) { break; } ?>
        <?php } ?>
    </select>
    <br />
    <select name='phase'>
        <?php foreach ( $phaseOptions as $phaseOption ) { ?>
            <option <?php if ( $phaseOption == $game['phase'] ) { echo 'selected="selected"'; } ?> value='<?php echo $phaseOption ?>'>
                <?php echo $phaseOption ?>
            </option>
        <?php } ?>
    </select>
    <input type='text' size='2' name='day' value='<?php echo $game['day'] ?>' />
    <input type='button' name='submit' value='submit' onClick='submit_status()' />
</form>
