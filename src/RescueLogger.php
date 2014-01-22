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
		$tlog = R::dispense('tlog');
		$tlog->task_id = $task_id;
		$tlog->level = $level;
		$tlog->message = $message;
		$id = R::store($tlog);
		return $id;
	}

}

?>