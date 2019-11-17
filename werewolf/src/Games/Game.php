<?php
class Game
{
    // -------------------------------------------------------------------------
    // Setup
    // -------------------------------------------------------------------------
    const ALLOWED_FIELDS_FOR_OPTIONS = ['status', 'phase'];

    public $id;
    private $thread_id;

    public function __construct() {
    }

    public static function thread_id($id) {
        $game = new self();
        $game->id = self::find_by_thread_id($id)['id'];
        return $game;
    }

    public static function game_id($id) {
        $game = new self();
        $game->id = $id;
        return $game;
    }

    // -------------------------------------------------------------------------
    // Public static functions
    // -------------------------------------------------------------------------
    
    // This method gets possible values for a specific field
    // in the Games database
    // It only accepts a from a certain set of fields 
    public static function field_options_for($field) {
        if (!in_array($field, self::ALLOWED_FIELDS_FOR_OPTIONS)) {
            throw new Exception('Invalid field provided to Game::field_options_for');
        }

        $sql="show columns from Games where field='$field'";
        $result=mysql_query($sql);
        $options = [];
        while ($row=mysql_fetch_row($result)) {
            foreach(explode("','",substr($row[1],6,-2)) as $v) {
                $options[] = $v;
            }
    }

        return $options;
    }
    
    // -------------------------------------------------------------------------
    // Public functions
    // -------------------------------------------------------------------------
    
    // Getters

    public function get_description() {
        $sql = sprintf("select description from Games where id=%s",quote_smart($this->id));
        $result = mysql_query($sql);

        if ( $result ) { 
            return mysql_result($result,0,0); 
        }
    }

    // Status has three parts: status, phase, day
    public function get_full_status() {
        $sql=sprintf("select status, phase, day from Games where id=%s",quote_smart($this->id));
        $result = mysql_query($sql);

        if ( $result ) { 
            return [
                'status' => mysql_result($result,0,0),
                'phase' => mysql_result($result,0,1),
                'day' => mysql_result($result,0,2)
            ];
        }
    }

    // Setters

    public function set_description($description) {
        $new_description = safe_html($description,"<a>");
        $sql = sprintf("update Games set description=%s where id=%s",quote_smart($new_description),quote_smart($this->id));
        $result = mysql_query($sql);

        return $new_description; 
    }

    public function set_full_status($status, $phase, $day) {
        $sql = sprintf("update Games set `status`=%s, phase=%s, day=%s where id=%s",quote_smart($status),quote_smart($phase),quote_smart($day),quote_smart($this->id));
        
        return  mysql_query($sql);
    }

    // Queries

    public function get_latest_post_id() {
        $sql = sprintf("select max(article_id) as a_id from Posts where game_id=%s",$this->id);
        $result = mysql_query($sql);
        if ( $result ) { 
            return mysql_result($result,0,0); 
        }
    }

    public function get_next_post_scan() {
        $format1 = '%i';
        $format2 = '%l';
        $sql = sprintf("select concat(date_format(if(minute>date_format(now(),'%s'),now(),date_add(now(),interval 1 hour)),'%s'),':',if(minute<10,concat('0',minute),minute)) as next from Post_collect_slots where game_id=%s",$format1,$format2,quote_smart($this->id));
        $result = mysql_query($sql);
        if ( mysql_num_rows($result) > 0 ) {
            return  mysql_result($result,0,0);
        }
    }

    public function get_nonplayers_who_posted() {
        $game_id = $this->id;
        $sql = "select distinct Posts.user_id, name from Posts, Users where Posts.user_id=Users.id and Posts.game_id='".$game_id."' and Posts.user_id not in ( select user_id from Players where game_id='".$game_id."') and Posts.user_id not in ( select user_id from Moderators where game_id='".$game_id."') and Posts.user_id not in ( select replace_id from Replacements where game_id='".$game_id."') order by name";
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
        $sql = sprintf("select * from Wolfy_games, Wolfy_awards where Wolfy_games.award_id=Wolfy_awards.id and game_id=%s order by id, year", $this->id);
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
  

    // Other functions

    public function remove_from_physics_processing() {
        $sql = sprintf("DELETE FROM Physics_processing 
                        WHERE game_id=%s", quote_smart($this->id));
        $result = mysql_query($sql);

        return $result;
    }

    // -------------------------------------------------------------------------
    // Protected functions
    // -------------------------------------------------------------------------

    private function find_by_thread_id($id) {
        $sql = "Select * from Games where thread_id=$id";
        $result = mysql_query($sql);
        $game = mysql_fetch_array($result);
        return $game;
    }

    private function find_by_game_id($id) {
        $sql = "Select * from Games where id=$id";
        $result = mysql_query($sql);
        $game = mysql_fetch_array($result);
        return $game;
    }

    private function get_post_count_for_player($player_id) {
        $sql = "select count(*) from Posts where game_id='".$this->id."' and user_id='".$player_id."'";
        $result = mysql_query($sql);
        
        return mysql_result($result,0,0);
    }

}
?>