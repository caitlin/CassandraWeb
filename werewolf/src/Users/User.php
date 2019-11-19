<?php
class User
{
    // -------------------------------------------------------------------------
    // Setup
    // -------------------------------------------------------------------------

    public $id;

    public function __construct($id) {
        $this->id;
    }

    // -------------------------------------------------------------------------
    // Public static functions
    // -------------------------------------------------------------------------
    
    // Returns id and name
    public static function get_all() {
        $sql="Select id, name from Users where level != '0' order by name";
        $result = mysql_query($sql);
        $users = [];
        while ( $row = mysql_fetch_array($result) ) {
            $users[$row['id']] = $row['name'];
        }

        return $users;
    }


    // -------------------------------------------------------------------------
    // Public functions
    // -------------------------------------------------------------------------
    

}