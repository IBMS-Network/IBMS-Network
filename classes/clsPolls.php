<?php

class clsPolls {

    static private $instance = NULL;
    private $db = "";
    private $session = "";

    public static function getInstance() {
        if (self::$instance == NULL) {
            self::$instance = new clsPolls();
        }
        return self::$instance;
    }

    public function clsPolls() {
        $this->db = DB::getInstance();
        $this->session = clsSession::getInstance();
    }

 
    /**
     * Get last poll
     * 
     */
    public function getLastPoll() {
        
        $sql = "SELECT p.*, pq.text as question, pa.text as answer,
                pq.id as question_id, pa.id as answer_id
                FROM polls p
                JOIN pollsquestions pq
                    ON p.id = pq.poll_id AND pq.status = 1
                LEFT JOIN pollsanswers pa
                    ON pq.id = pa.question_id AND pa.status = 1
                WHERE p.id = (SELECT id FROM polls ORDER BY id DESC LIMIT 1)";

        $res = $this->db->getAll($sql, $sqlArr);
        if($res) {
            return $res;
        }
        
        return false;
    }
    
    public function addPoll($pollId, $data) {
        
        foreach($data as $k => $v) {
            if($this->session->isAuthorisedUserSession()) {
                $sql = "INSERT INTO pollsusers(poll_id, question_id, answer_id, user_id, user_additional)
                        VALUES (?, ?, ?, ?, ?)";
                $userId = $this->session->getUserIdSession();
                if($v['type'] == 'radio') {
                    $sqlArr = array($pollId, $v['question'], $v['answer'], $userId, '');
                } else {
                    $sqlArr = array($pollId, $v['question'],  0, $userId, $v['answer'],);
                }

                $res = $this->db->Execute($sql, $sqlArr);
            }
        }
        
		return true;
    }
}