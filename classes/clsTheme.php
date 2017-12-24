<?php
class clsTheme {

	static private $instance = NULL;
	private $config = "";
	private $db = "";
	private $parser = "";
    private $polls  = "";
    
	public static function getInstance() {
		if (self::$instance == NULL) {
			self::$instance = new clsTheme();
		}
		return self::$instance;
	}

	public function __construct() {
		$this->db = DB::getInstance();
        $this->config = clsCommon::getDomainConfig();
        $this->parser = new clsParser();
        $this->polls = clsPolls::getInstance();
        $this->session = clsSession::getInstance();
	}
	
	public function getFooterBlock() {
		return clsDynamicBlocks::getInstance()->getBlockById(FOOTER_BLOCK);
	}
    
    public function getHeaderMenuBlock() {
        return clsDynamicBlocks::getInstance()->getBlockById(HEADER_MENU_BLOCK);
    }
    
    public function getPoolBlock() {
        $content = "";
        if($this->session->isAuthorisedUserSession()) {
            
            $currentPoll = $this->polls->getLastPoll();

            if($currentPoll) {
                $questionId = 0;
                $questionDiv = '';
                $question = '';
                $poll = '';
                $i = 1;
                foreach($currentPoll as $k => $v) {
                    if ($questionId != $v['question_id']) {
                        if($questionId != 0) {
                            $this->parser->clear();
                            $this->parser->setVar('{I}', $i++);
                            $this->parser->setVar('{QID}', $questionId);
                            $this->parser->setVar('{ANSWER}', $questionDiv);
                            $this->parser->setVar('{QUESTION}', $question);
                            $this->parser->setBlockTemplate("poll_question.html");
                            $poll .= $this->parser->getResult();
                            $questionDiv = '';
                        }
                        $questionId = $v['question_id'];
                        $question = $v['question'];
                    }

                    $this->parser->clear();
                    $this->parser->setVar('{I}', $i);
                    $this->parser->setVar('{ANSWER_ID}', $v['answer_id']);
                    $this->parser->setVar('{ANSWER}', $v['answer']);
                    if(!empty($v['answer'])) {
                        $this->parser->setBlockTemplate("poll_answer_radio.html");
                    } else {
                        $this->parser->setBlockTemplate("poll_answer.html");
                    }
                    $questionDiv .= $this->parser->getResult();

                }

                $this->parser->clear();
                $this->parser->setVar('{I}', $i);
                $this->parser->setVar('{QID}', $questionId);
                $this->parser->setVar('{ANSWER}', $questionDiv);
                $this->parser->setVar('{QUESTION}', $question);
                $this->parser->setBlockTemplate("poll_question.html");
                $poll .= $this->parser->getResult();

                $this->parser->clear();
                $this->parser->setVar('{TITLE}', $currentPoll[0]['title']);
                $this->parser->setVar('{ID}', $currentPoll[0]['id']);
                $this->parser->setVar('{POLL}', $poll);
                $this->parser->setBlockTemplate("poll.html");
                $content .= $this->parser->getResult();
            }
        }
            
        return $content;
    }
    
}