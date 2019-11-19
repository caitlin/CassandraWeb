<form name='change_mod'>
    <select name='moderator[]' size='25' multiple>
        <?php foreach ( $users as $user_id=>$user_name ) { ?>
            <option <?php if (in_array($user_id, $game['moderator_ids'])) { echo 'selected="selected"'; } ?> value='<?php echo $user_id ?>'>
                <?php echo $user_name ?>
            </option>
        <?php } ?>
    </select>
    <input type=button value='submit' name='submit' onClick='submit_Moderators()' />
</form>