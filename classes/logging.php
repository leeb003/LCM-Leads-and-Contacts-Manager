<?php
/**
 *     Class to handle logging events for changes made to the program and insert them into the log table
**/

class Log extends DB
{
    // Properties
    private $userID;
    private $event;
    private $detail;
    private $dbPre;

    // Methods
    public function __construct($dbPre) {
        $this->dbPre = $dbPre;
        parent::__construct();
    }

    public function logEvent($userID, $event, $detail='None') {
        $sql = "select * from {$this->dbPre}users where id='$userID'";
        $userRow = parent::extQueryRowObj($sql);
        $userName = $userRow->first . ' ' . $userRow->last;
        $time = date('Y-m-d H:i:s');
        $vals = array(
            'userFirst' => $userRow->first,
            'userLast'  => $userRow->last,
            'event'     => $event,
            'detail'    => $detail,
            'eventTime' => "$time",
            'ipAddr'    => $_SERVER["REMOTE_ADDR"]
        );
        $insertID = parent::insert("{$this->dbPre}log", $vals);
        return $insertID;
    }
}
