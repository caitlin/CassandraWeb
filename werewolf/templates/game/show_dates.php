<?php
$content = $game['start_date']." to ".$game['end_date'];
if ( $game['status'] == "Sign-up" ) {
    if ( $game['deadline_speed'] == "Fast" ) { $content = $game['start_date']." ".$game['start_time']." to ".$game['end_date']; }
    if ( $game['swf'] == "Yes" ) {  $content = "Starts When Full"; }
}
?>
<div onMouseOver='show_hint("Click to Edit Dates")' onMouseOut='hide_hint()' onClick='edit_dates()'><?php echo $content ?></div>