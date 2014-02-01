<?php

namespace Rescue;

use RedBean_Facade as R;
use Monolog\Logger;

class RescueLogger
{
    public static function addHistory($srcUrl,$srcParam,$message,$creatorId = 0)
    {
        $history = R::dispense('history');
        $history->recall_url = $srcUrl;
        $history->recall_param = $srcParam;
        $history->description = $message;
        $history->raddress = \BitPrepared\Security\IpIdentifier::get_ip_address();
        $history->created_by = $creatorId;
        $history->created_at = R::isoDateTime();
        $id = R::store($history);

        return $id;
    }

    public static function taskLog($task_id,$level,$message,$creatorId = 0,$creationDate = "")
    {
        $level_text = "";
        if ( "" == $creationDate ) $creationDate = R::isoDateTime();
        if ( is_numeric($level) ) {
            switch ($level) {
                case Logger::EMERGENCY 	:
                    $level_text = "EMERGENCY";
                    break;
                case Logger::ALERT 		:
                    $level_text = "ALERT";
                    break;
                case Logger::CRITICAL		:
                    $level_text = "CRITICAL";
                    break;
                case Logger::ERROR		:
                    $level_text = "ERROR";
                    break;
                case Logger::WARNING			:
                    $level_text = "WARNING";
                    break;
                case Logger::NOTICE		:
                    $level_text = "NOTICE";
                    break;
                case Logger::INFO			:
                    $level_text = "INFO";
                    break;
                case Logger::DEBUG		:
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
        $tlog->created = $creationDate;
        $tlog->author = $creatorId;
        $id = R::store($tlog);

        return $id;
    }

}
