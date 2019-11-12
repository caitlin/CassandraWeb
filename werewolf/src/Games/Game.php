<?php
class Game
{
    // -------------------------------------------------------------------------
    // Setup
    // -------------------------------------------------------------------------

    private $game;

    public function __construct($game_id) {
      $this->game = $this->find_by_id($game_id);
    }

    
    // -------------------------------------------------------------------------
    // Public functions
    // -------------------------------------------------------------------------
    
    public function id() {
        return $this->game['id'];
    }

    public function get_latest_post_id() {
        $sql = sprintf("select max(article_id) as a_id from Posts where game_id=%s",$this->id());
        $result = mysql_query($sql);
        if ( $result ) { 
            return mysql_result($result,0,0); 
        }
    }

    public function get_next_post_scan() {
        $format1 = '%i';
        $format2 = '%l';
        $sql = sprintf("select concat(date_format(if(minute>date_format(now(),'%s'),now(),date_add(now(),interval 1 hour)),'%s'),':',if(minute<10,concat('0',minute),minute)) as next from Post_collect_slots where game_id=%s",$format1,$format2,quote_smart($this->id()));
        $result = mysql_query($sql);
        if ( mysql_num_rows($result) > 0 ) {
            return  mysql_result($result,0,0);
        }
    }

    public function get_nonplayers_who_posted() {
        $game_id = $this->id();
        $sql = "select distinct Posts.user_id, name from Posts, Users where Posts.user_id=Users.id and Posts.game_id='".$game_id."' and Posts.user_id not in ( select user_id from Players where game_id='".$game_id."') and Posts.user_id not in ( select user_id from Moderators where game_id='".$game['id']."') and Posts.user_id not in ( select replace_id from Replacements where game_id='".$game_id."') order by name";
        $result = mysql_query($sql);
        
        $players = [];
        while ( $player_data = mysql_fetch_array($result) ) {
            $players[] = [
                'user_id' => $player_data['user_id'],
                'name' => $player_data['name'],
                'post_count' => $this->get_post_count_for_player($player_data['user_id']),
            ];
        }

        return $players;
    }

    public function get_wolfy_awards() {
        $sql = sprintf("select * from Wolfy_games, Wolfy_awards where Wolfy_games.award_id=Wolfy_awards.id and game_id=%s order by id, year", $this->id());
        $result = mysql_query($sql);

        $awards = [];
        while ( $award_data = mysql_fetch_array($result) ) {
            $awards[] = [
                'award_post' => $award_data['award_post'],
                'award' => $award_data['award'],
                'year' => $award_data['year']
            ];
        }

        return $awards;
    }
  

    // -------------------------------------------------------------------------
    // Protected functions
    // -------------------------------------------------------------------------

    private function find_by_id($game_thread_id) {
        $sql = "Select * from Games where thread_id=$game_thread_id";
        $result = mysql_query($sql);
        $game = mysql_fetch_array($result);
        return $game;
    }

    private function get_post_count_for_player($player_id) {
        $sql = "select count(*) from Posts where game_id='".$this->id()."' and user_id='".$player_id."'";
        $result = mysql_query($sql);
        
        return mysql_result($result,0,0);
    }

}
?>