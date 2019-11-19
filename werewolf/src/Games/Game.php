<?php
class Game
{
    // -------------------------------------------------------------------------
    // Setup
    // -------------------------------------------------------------------------
    const ALLOWED_FIELDS_FOR_OPTIONS = ['status', 'phase', 'deadline_speed'];
    const WINNERS = ['Evil','Good','Other'];
    const COMPLEXITIES = ['Newbie','Low','Medium','High','Extreme'];

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

    public function get_complexity() {
        $sql = sprintf("select complex from Games where id=%s",quote_smart($this->id));
        $result = mysql_query($sql);
        
        return mysql_result($result,0,0);
    }

    public function get_dates() {
        $date_format = "'%Y-%m-%d'";
        $time_format = "'%H:%i'";
        $sql = sprintf("select date_format(start_date, %s) as start_date, date_format(start_date, %s) as start_time, date_format(end_date, %s) as end_date, swf, status, deadline_speed from Games where id=%s", $date_format,$time_format,$date_format,quote_smart($this->id));
        $result = mysql_query($sql);

        return mysql_fetch_array($result);
    }

    // Deadline info has 5 parts: dusk, dawn, day, night, speed
    public function get_deadlines() {
        $sql = sprintf("select lynch_time, na_deadline, day_length, night_length, deadline_speed from Games where id=%s",quote_smart($this->id));
        $result = mysql_query($sql);

        $dusk = mysql_result($result,0,0);
        $dawn = mysql_result($result,0,1);
        $day_length = mysql_result($result,0,2);
        $night_length = mysql_result($result,0,3);
        $speed = mysql_result($result,0,4);

        return [
            'dusk' => $dusk,
            'dawn' => $da,
            'day_length' => $day_length,
            'night_length' => $night_length,
            'speed' => $speed
        ];
    }

    public function get_description() {
        $sql = sprintf("select description from Games where id=%s",quote_smart($this->id));
        $result = mysql_query($sql);

        if ( $result ) { 
            return mysql_result($result,0,0); 
        }
    }

    public function get_max_players() {
        $sql = sprintf("select max_players from Games where id=%s",quote_smart($this->id));
        $result = mysql_query($sql);

        return mysql_result($result,0,0);
    }

    public function get_number() {
        $sql = "select number from Games where id='$this->id'";
        $result = mysql_query($sql);
        
        return mysql_result($result,0,0);
    }

    public function get_speed() {
        $sql=sprintf("select deadline_speed from Games where id=%s",quote_smart($this->id));
        $result = mysql_query($sql);

        if ( $result ) { 
            return mysql_result($result,0,0); 
        }
    }

    // Status has three parts: status, phase, day
    public function get_status() {
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

    public function get_thread_id() {
        $sql = sprintf("SELECT thread_id 
                        FROM Games 
                        WHERE id=%s",quote_smart($this->id));
        $result = mysql_query($sql);

        return mysql_result($result,0,0);
    }

    public function get_title() {
        $sql = sprintf("select title from Games where id=%s",quote_smart($this->id));
        $result = mysql_query($sql);

        return mysql_result($result,0,0);
    }

    public function get_winner() {
        $sql=sprintf("select winner from Games where id=%s",quote_smart($this->id));
        $result = mysql_query($sql);

        if ( $result ) { 
            return mysql_result($result,0,0); 
        }
    }

    // Setters

    public function set_complexity($complexity) {
        $sql = sprintf("update Games set complex=%s where id=%s",quote_smart($complexity),quote_smart($this->id));
        
        return mysql_query($sql);
    }

    public function set_dates($start_timestamp, $end_timestamp, $swf) {
        $sql = sprintf("UPDATE Games SET start_date=%s, end_date=%s, swf=%s WHERE id=%s",
                        quote_smart($start_timestamp),quote_smart($end_timestamp),quote_smart($swf),quote_smart($this->id));

        return mysql_query($sql);
    }

    public function set_description($description) {
        $new_description = safe_html($description,"<a>");
        $sql = sprintf("update Games set description=%s where id=%s",quote_smart($new_description),quote_smart($this->id));
        $result = mysql_query($sql);

        return mysql_query($sql); 
    }

    public function set_deadlines($dusk, $dawn, $day_length, $night_length) {
        if ( $dusk == "" ) {
            $dusk_value = 'null';
        } else {
            $dusk_value = quote_smart($dusk);
        }
        if ( $dawn == "" ) {
            $dawn_value = 'null';
        } else {
            $dawn_value = quote_smart($dawn);
        }
        
        $sql = sprintf("UPDATE Games SET `lynch_time`=%s, `na_deadline`=%s, `day_length`=%s, `night_length`=%s WHERE id=%s",$dusk_value,$dawn_value,quote_smart($day_length),quote_smart($night_length),quote_smart($this->id));
        
        return mysql_query($sql);
    }

    public function set_max_players($max_players) {
        $sql = sprintf("update Games set max_players=%s where id=%s",quote_smart($max_players),quote_smart($this->id));
        return mysql_query($sql);
    }

    public function set_speed($speed) {
        $sql = sprintf("update Games set deadline_speed=%s where id=%s",quote_smart($speed),quote_smart($this->id));

        return mysql_query($sql);
    }

    public function set_status($status, $phase, $day) {
        $sql = sprintf("update Games set `status`=%s, phase=%s, day=%s where id=%s",quote_smart($status),quote_smart($phase),quote_smart($day),quote_smart($this->id));
        
        return mysql_query($sql);
    }

    public function set_thread_id($thread_id) {
        $sql = sprintf("UPDATE Games SET thread_id=%s WHERE id=%s",quote_smart($thread_id),quote_smart($this->id));
        return mysql_query($sql);
    }

    public function set_title($title) {
        $sql = sprintf("update Games set title=%s where id=%s",quote_smart($title),quote_smart($this->id));

        return mysql_query($sql);
    }

    public function set_winner($winner) {
        $sql = sprintf("update Games set winner=%s where id=%s",quote_smart($winner),quote_smart($this->id));

        return mysql_query($sql);
    }

    // Associations

    public function get_moderators() {
        $sql = sprintf("SELECT Moderators.user_id AS id, Users.name AS name FROM Users, Moderators WHERE Moderators.user_id=Users.id AND Moderators.game_id=%s ORDER BY name",quote_smart($this->id));
        $result = mysql_query($sql);
        $ids = [];
        while ( $row = mysql_fetch_array($result) ) {
            $ids[$row['id']]=$row['name'];
        }

        return $ids;
    }

    public function get_moderator_ids() {
        $sql = sprintf("SELECT user_id FROM Moderators WHERE game_id=%s",quote_smart($this->id));
        $result = mysql_query($sql);
        $ids = [];
        while ( $row = mysql_fetch_array($result) ) {
            $id[] = $row['user_id'];
        }

        return $ids;
    }

    public function create_moderator($user_id) {
        $sql = "insert into Moderators (user_id, game_id) values ('".$user_id."', '$this->id')";
        $result = mysql_query($sql);

        return mysql_insert_id();
    }

    public function update_moderators($ids) {
        $newidlist = $ids;
        sort($newidlist);
        $oldidlist = [];

        $sql = sprintf("select user_id from Games, Moderators where Games.id = Moderators.game_id and Games.id=%s",quote_smart($this->id));
        $result = mysql_query($sql);
        while ( $row = mysql_fetch_array($result) ) {
            $oldidlist[] = $row['user_id'];
        }

        # Find Id's that need to be added.
        foreach ( $newidlist as $newid ) {
            $found = false;
            foreach ( $oldidlist as $oldid ) {
                if ( $newid == $oldid ) $found = true;
            }
            if ( ! $found ) $addlist[] = $newid;
        }

        # Add Id's that need to be added.
        if ( $addlist[0] != "" ) {
            foreach ( $addlist as $id ) {
                $sql = sprintf("insert into Moderators ( user_id, game_id ) values ( %s, %s )",quote_smart($id),quote_smart($this->id));
                $result = mysql_query($sql);
            }
        }

        # Find id's that need to be deleted.
        foreach ( $oldidlist as $oldid ) {
            $found = false;
            foreach ( $newidlist as $newid ) {
                if ( $newid == $oldid ) $found = true;
            }
            if ( ! $found ) $dellist[] = $oldid;
        }

        # Delete id's that need to be deleted.
        if ( $dellist[0] != "" ) {
            foreach ( $dellist as $id ) {
                $sql = sprintf("delete from Moderators where user_id=%s and game_id=%s",quote_smart($id),quote_smart($this->id));
                $result = mysql_query($sql);
            }
        }
        return true;
    }

    public function get_subthreads() {
        $sql = sprintf("SELECT * FROM Games WHERE parent_game_id=%s",quote_smart($this->id));
        $result = mysql_query($sql);
        $subthreads = [];
        while ( $row = mysql_fetch_array($result) ) {
            $subthreads[] =  $row;
        }

        return $subthreads;
    }

    public function create_subthread($thread_id) {
        $sql = sprintf("INSERT INTO Games (id, title, status, thread_id, parent_game_id) 
                        VALUES ( NULL, 'Sub-Thread', 'Sub-Thread', %s, %s)",
                        quote_smart($thread_id),quote_smart($this->id));
        $result = mysql_query($sql);
        
        return mysql_insert_id();
    }

    public function destroy_subthread($thread_id) {
        $sql = sprintf("select id from Games where thread_id=%s",quote_smart($thread_id));
        $result = mysql_query($sql);
        $subthread_game_id = mysql_result($result,0,0);

        $sql = "delete from Games where id ='$subthread_game_id'";
        
        return mysql_query($sql);
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
  
    // This query takes an array of user ids
    public function get_post_count_for_users($ids) {
        $game_id = $this->id;
        $user_ids = join(',',$ids);
        $sql = "SELECT user_id, count(*) AS post_count
                FROM Posts 
                WHERE game_id='$game_id' 
                AND user_id IN ($user_ids) GROUP BY user_id";
        $result = mysql_query($sql);
        $counts = array_fill_keys($ids, 0);
        while ( $row = mysql_fetch_array($result) ) {
            $counts[$row['user_id']] = $row['post_count'];
        }
        
        return $counts;
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