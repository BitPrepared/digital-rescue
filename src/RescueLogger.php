<?php

namespace Rescue;

use RedBean_Facade as R;

class RescueLogger {

	public static function addHistory($srcUrl,$srcParam,$message,$creatorId = 0) {
		$history = R::dispense('history');
		$history->recall_url = $srcUrl;
		$history->recall_param = $srcParam; 
		$history->description = $message;
		$history->created_by = $creatorId;
		$history->created_at = R::isoDateTime();
		$id = R::store($history);
		return $id;
	}
	

	public static function taskLog($task_id,$level,$message) {

		$level_text = $level;
		if ( is_numeric($level) ) {
			switch ($level) {
				case \Slim\Log::EMERGENCY 	:
					$level_text = "EMERGENCY";
					break;
				case \Slim\Log::ALERT 		:
					$level_text = "ALERT";
					break;
				case \Slim\Log::CRITICAL		:
					$level_text = "CRITICAL";
					break;
				case \Slim\Log::ERROR		:
					$level_text = "ERROR";
					break;
				case \Slim\Log::WARN			:
					$level_text = "WARNING";
					break;
				case \Slim\Log::NOTICE		:
					$level_text = "NOTICE";
					break;
				case \Slim\Log::INFO			:
					$level_text = "INFO";
					break;
				case \Slim\Log::DEBUG		:
					$level_text = "DEBUG";
					break;
				default:	
					$level_text = "WARNING";
					break;
			}
		}

		$tlog = R::dispense('tlog');
		$tlog->task_id = $task_id;
		$tlog->level = $level_text;
		$tlog->message = $message;
		$id = R::store($tlog);
		return $id;
	}

}

?>