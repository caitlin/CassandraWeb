<form name='new_deadline'>
    <table>
        <?php if ( $game['speed'] == "Standard" ) { ?>
            <tr><td>Dusk:</td><td> <?php echo time_dropdown('dusk',$game['dusk'],false,false) ?></td></tr>
            <tr><td>Dawn:</td><td> <?php echo time_dropdown('dawn',$game['dawn'],false,false) ?></td></tr>
            <input type='hidden' name='day_length' value='<?php echo $game['day_length'] ?>' />
            <input type='hidden' name='night_length' value='<?php echo $game['night_length'] ?>' />
        <?php } else { ?>
            <tr><td>Day Length:</td><td> <?php echo time_dropdown('day_length',$game['day_length'],true,false) ?></td></tr>
            <tr><td>Night Length:</td><td> <?php echo time_dropdown('night_length',$game['night_length'],true,false) ?></td></tr>
            <input type='hidden' name='dusk' value='<?php echo $game['dusk'] ?>' />
            <input type='hidden' name='dawn' value='<?php echo $game['dawn'] ?>' />
        <?php } ?>
        <tr><td colspan='2' align='center'><input type='button' name='submit' value='submit' onClick='submit_deadline()' /></td></tr>
    </table>
</form>