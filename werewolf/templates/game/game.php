<html>
<head>
<title><?=$game['title'];?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel='stylesheet' type='text/css' href='/assets/css/application.css'>
<link rel='stylesheet' type='text/css' href='/assets/css/hint.css'>
<script language='javascript'>
    <!--
    var thread_id = '<?=$game_thread_id;?>'
    var game_id = '<?=$game['id'];?>'
    var myURL = '<?=$_SERVER['REQUEST_URI'];?>'
    var currentStatus = '<?=$game['status'];?>'

    var xmlHttp
    var element
    if ( myURL == "/game/"+thread_id) {
    var dir = "../"
    } else {
    var dir = ""
    }

    function close(element) {
    document.getElementById(element).style.visibility='hidden'
    }

    function pm_players() {
        element="PM_div"
        document.getElementById(element).style.visibility='visible'
        var url=dir+"pm_players.php?game_id="+game_id
        xmlHttp=GetXmlHttpObject(stateChanged)
        xmlHttp.open("GET", url , false)
        xmlHttp.send(null)
    }

    function go_replace(user_id, action) {
    sure = confirm("Are you sure?")
    if ( sure ) {
        location.href="/replace.php?user_id="+user_id+"&game_id="+game_id+"&action="+action
    }
    }

    //-->
</script>
<script src='/assets/js/hint.js'></script>
<?php
print time_dropdown_js();
$open_comment = "><!--";
$close_comment = "--";
if ( $edit )  {
    ?>
    <script src='/assets/js/color_picker.js'></script>
    <script src='<?=$here;?>edit_game.js'></script>
    <script src='<?=$here;?>mod_control.js'></script>
    <script src='/assets/js/validation.js'></script>
    <?php
    $open_comment = "";
    $close_comment = "";
}
?>
<script src='/assets/js/ajax.js'></script>
<script src='https://unpkg.com/micromodal/dist/micromodal.min.js' />
</head>
<body>
    <?php display_menu(); ?>
    <div class='content'>

        <h1>
            <div id='name_span' <?=$open_comment;?> onMouseOver='show_hint("Click to Change Name")' onMouseOut='hide_hint()' onClick='edit_name()' <?=$close_comment;?>>
                <?php if ( $game['number'] != "" ) { echo $game['number'].") "; } ?>
                <?= $game['title'] ?>
            </div>
        </h1>
        <div id='divDescription' class='clDescriptionCont'>
        <!--Empty Div used for hint popup-->
        </div>

        <div class='game-details'>
            <div class='game-details--info'>
                <?php require_once 'templates/game/_info.php' ?>
            </div>
            
            <div class='game-details--edit'>
            <?php require_once 'templates/game/_edit.php' ?>
            </div>
        </div>

        <?php require_once 'templates/game/_actions.php' ?>

        <div style='position:absolute; visibility:hidden; background-color:white; border:1px solid black;' id='PM_div'></div>

        <div id='player_table'>
            <?php createPlayer_table($edit,$game['id']); ?>
        </div>

        <?php if ( count($wolfy_awards) > 0 ) { ?>
            <table class='forum_table'>
                <tr><th>Wolfy Awards</th></tr>
                <?php foreach ( $wolfy_awards as $award ) { ?>
                    <tr><td><a href="http://www.boardgamegeek.com/article/<?php $award['award_post']."#".$award['award_post'] ?>"><?php echo $award['award'] ?> (<?php echo $award['year'] ?>)</a></td></tr>
                <?php } ?>
            </table>
        <?php } ?>

        <?php if (count($nonplayers_who_posted) > 0 ) { ?>
        <br />
        Non-Players who posted
        <br />
        <table class='forum_table'>
            <?php foreach ( $nonplayers_who_posted as $player ) { ?>
                <tr><td><?php get_player_page($player['name']) ?><a href='<?php echo $posts . $player['name'] ?>'>(<?php $player['post_count'] ?>)</a></td></tr>
            <?php } ?>
        </table>
        <?php } ?>

        <br />
        <table>
            <tr>
                <td><h3>Player Time Zone Chart</h3></td>
                <td><?php print timezone_changer(); ?></td>
            </tr>
        </table>
        <div id='tz_div'><?php print timezone_chart("",$game['id']); ?></div>
        <?php timezone_js(); ?>

        <script language='javascript'>
        setHint()
        </script>
        <?php print player_autocomplete_js("new_p"); ?>