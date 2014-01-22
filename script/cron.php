#!/usr/bin/php
<?php

require '../vendor/autoload.php';
require '../config.php';
use RedBean_Facade as R;

$dsn      = 'mysql:host='.$config['db']['host'].';dbname='.$config['db']['database'];
$username = $config['db']['user'];
$password = $config['db']['password'];

use Monolog\Logger;
use Monolog\Handler\TestHandler;

// create a log channel
$log = new Logger('cron');
$handler = new TestHandler(Logger::WARNING);
$log->pushHandler($handler);

// add records to the log
//$log->addWarning('Foo');
//$log->addError('Bar');

R::setup($dsn,$username,$password);
R::freeze(true);

$task_list = R::find('task','status = ?', array(\Rescue\RequestStatus::QUEUE));
//$task_list = R::findAll('task');

foreach ($task_list as $task_id => $task) {
	
	$args = json_decode($task->arguments);

	$task->updated = R::isoDateTime();
	$task->status = \Rescue\RequestStatus::IN_PROGRESS;

	R::store($task);
	
	if( \Rescue\RequestType::SEARCH == $task->type ) {

		$socio = R::getRow('select * from asa where nome = ? and cognome = ? and datan = ? and nascita = ?',
			array($args->nome,$args->cognome,$args->datanascita,$args->luogonascita));
		//R::getRow('select * from page where title like ? limit 1', array('%Jazz%'));

		$codice_socio = $socio['csocio'];
		$email = $socio['numero'];

		// Create the message
		$message = Swift_Message::newInstance()

		  // Give the message a subject
		  ->setSubject('Estrazione Codice Censimento')

		  // Set the From address with an associative array (ovverride from gmail if you use gmail account.)
		  ->setFrom(array('webmaster@emiroagesci.it' => 'Webmaster Emiro Agesci'))

		  // Set the To addresses with an associative array
		  ->setTo( array($email => $args->nome.' '.$args->cognome) )

		  // Give it a body
		  ->setBody('Codice Censimento : '.$codice_socio)

		  // And optionally an alternative body
		  ->addPart('<p>Codice Censimento : <strong>'.$codice_socio.'</strong></p>', 'text/html');

		  // Optionally add any attachments
		  //->attach(Swift_Attachment::fromPath('my-document.pdf'));

		// To use the ArrayLogger
		$logger = new Swift_Plugins_Loggers_ArrayLogger();

		$smtpConfig = $config['smtp'];

		$transport = Swift_SmtpTransport::newInstance($smtpConfig['host'], $smtpConfig['port'], $smtpConfig['security'])
		->setUsername($smtpConfig['username'])
  		->setPassword($smtpConfig['password']);

		// Create the Mailer using your created Transport
		$mailer = Swift_Mailer::newInstance($transport);
		$mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($logger));

		// Pass a variable name to the send() method
		try {
			if (!$mailer->send($message, $failures))
			{
				//echo "Failures:";
				//print_r($failures);
				/*
				Failures:
				Array (
				  0 => receiver@bad-domain.org,
				  1 => other-receiver@bad-domain.org
				)
				*/
				$task->status = \Rescue\RequestStatus::FAILED;
				$task->result = "Fallito l'invio a ".json_decode($failures);
			} else {
				$task->status = \Rescue\RequestStatus::ELABORATED; 
				$task->result = "Inviato correttamente codice socio : $codice_socio a $email";
			}
		}
		catch(Swift_TransportException $e) {
			$message = $e->getMessage();
			$log->addError($message);
			$log->addError($e->getTraceAsString());
			$task->result = "Fallito l'invio, errore nel trasporto";
			$task->status = \Rescue\RequestStatus::FAILED; 
		}

		R::store($task);

		$log_records = $handler->getRecords();
		/*
			{
			  "log_mail": "++ Starting Swift_SmtpTransport\n<< 220 mx.google.com ESMTP o47sm29825570eem.21 - gsmtp\r\n\n>> EHLO [127.0.0.1]\r\n\n<< 250-mx.google.com at your service, [79.32.23.147]\r\n250-SIZE 35882577\r\n250-8BITMIME\r\n250-AUTH LOGIN PLAIN XOAUTH XOAUTH2 PLAIN-CLIENTTOKEN\r\n250-ENHANCEDSTATUSCODES\r\n250 CHUNKING\r\n\n>> AUTH LOGIN\r\n\n<< 334 VXNlcm5hbWU6\r\n\n>> b3JzZXR0b0BnbWFpbC5jb20=\r\n\n<< 334 UGFzc3dvcmQ6\r\n\n>> MCRyaSQ=\r\n\n<< 535-5.7.8 Username and Password not accepted. Learn more at\r\n535 5.7.8 http://support.google.com/mail/bin/answer.py?answer=14257 o47sm29825570eem.21 - gsmtp\r\n\n!! Expected response code 235 but got code \"535\", with message \"535-5.7.8 Username and Password not accepted. Learn more at\r\n535 5.7.8 http://support.google.com/mail/bin/answer.py?answer=14257 o47sm29825570eem.21 - gsmtp\r\n\"\n>> RSET\r\n\n<< 250 2.1.5 Flushed o47sm29825570eem.21 - gsmtp\r\n\n>> AUTH PLAIN b3JzZXR0b0BnbWFpbC5jb20Ab3JzZXR0b0BnbWFpbC5jb20AMCRyaSQ=\r\n\n<< 535-5.7.8 Username and Password not accepted. Learn more at\r\n535 5.7.8 http://support.google.com/mail/bin/answer.py?answer=14257 o47sm29825570eem.21 - gsmtp\r\n\n!! Expected response code 235 but got code \"535\", with message \"535-5.7.8 Username and Password not accepted. Learn more at\r\n535 5.7.8 http://support.google.com/mail/bin/answer.py?answer=14257 o47sm29825570eem.21 - gsmtp\r\n\"\n>> RSET\r\n\n<< 250 2.1.5 Flushed o47sm29825570eem.21 - gsmtp\r\n",
			  "log_records": [
			    {
			      "message": "Failed to authenticate on SMTP server with username \"orsetto@gmail.com\" using 2 possible authenticators",
			      "context": [],
			      "level": 400,
			      "level_name": "ERROR",
			      "channel": "cron",
			      "datetime": {
			        "date": "2014-01-22 18:52:53",
			        "timezone_type": 3,
			        "timezone": "Europe/Rome"
			      },
			      "extra": [],
			      "formatted": "[2014-01-22 18:52:53] cron.ERROR: Failed to authenticate on SMTP server with username \"orsetto@gmail.com\" using 2 possible authenticators [] []\n"
			    }
			  ]
			}
		*/
		foreach ($log_records as $record) {
			$tlog = R::dispense('tlog');
			$tlog->task_id = $task_id;
			$tlog->level = $record['level'];
			$tlog->message = $record['formatted'];
			$id = R::store($tlog);	
		}

		$tlog = R::dispense('tlog');
		$tlog->task_id = $task_id;
		$tlog->level = Logger::INFO;
		$tlog->message = $logger->dump();
		$id = R::store($tlog);	

	}

}


?>