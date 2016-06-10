<?php
require_once 'swift/swift_required.php';

$transport = Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, "ssl")
  ->setUsername('eac_system@school.cdgfss.edu.hk')
  ->setPassword('7fM5wm$mT[Z=T7+N');

$mailer = Swift_Mailer::newInstance($transport);

$message = Swift_Message::newInstance('Test Subject')
  ->setFrom(array('eac_system@school.cdgfss.edu.hk' => 'EAC System'))
  ->setTo(array('t15ys@school.cdgfss.edu.hk'))
  ->setBody('This is a test mail.');

$result = $mailer->send($message);
?>