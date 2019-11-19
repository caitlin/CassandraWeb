<?php
    list($dusk,$lmin,$x) = split(":",$game['dusk']);
    list($dawn,$nmin,$x) = split(":",$game['dawn']);
    list($day_length,$dlmin,$x) = split(":",$game['day_length']);
    list($night_length,$nlmin,$x) = split(":",$game['night_length']);
?>
<div onMouseOver='show_hint(\"Click to Change Deadlines\")' onMouseOut='hide_hint()' onClick='edit_deadline()'>
    <?php if ( $game['deadline_speed'] == "Standard" ) { ?>
        <?php if ( $dusk != "" ) { ?>
            Dusk: <?php echo time_24($dusk,$lmin) ?> BGG<br />
        <?php } ?>
        <?php if ( $dawn != "" ) { ?>
            Dawn: <?php echo time_24($dawn,$nmin) ?> BGG
        <?php } ?>
    <?php } else { ?>
        Day Length: <?php echo "$day_length:$dlmin" ?> <br />
        Night Length: <?php echo "$night_length:$nlmin" ?>
    <?php } ?>
</div>