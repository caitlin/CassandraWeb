<p><?php echo $instructions ?></p>
<form name='comp_form'>
    <select name='complex'>
        <option value='' />
        <?php foreach ( $complexityOptions as $complexity ) { ?>
            <option <?php if ($game['complexity'] == $complexity) { echo 'selected="selected"'; } ?> value='<?php echo $complexity ?>'>
                <?php echo $complexity ?>
            </option>
        <?php } ?>
    </select>
    <input type='button' name='submit' value='submit' onClick='submit_complex()' />
</form>