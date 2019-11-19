<p><?php echo $instructions ?></p>
<form name='edit_date'>
    <?php if ( $game['status'] == "Sign-up") { ?>
        <input type='checkbox' name='swf' value='No'<?php if ($game['swf'] == 'Yes') { echo " checked='checked'"; } ?>/> Starts when full<br />
    <?php } else { ?>
        <input type='hidden' name='swf' value='<?php echo $game['swf'] ?>' />
    <?php } ?>
    <input type=text name='start_date' value='<?php echo $game['start_date'] ?>' />
    <?php if ( $game['deadline_speed'] == "Fast" ) { ?>
        <?php echo time_dropdown('start_time',$game['start_time'],false,false); ?>
    <?php } else { ?>
        <input type='hidden' name='start_time' value='00:00' />
    <?php } ?>
    to
    <input type=text name='end_date' value='<?php echo $game['end_date'] ?>' />
    <br />
    <input type='button' name='submit' value='submit' onClick='submit_dates()'/>
</form>