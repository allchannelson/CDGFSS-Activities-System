<?php
  require_once 'submit_mail.php';
  
  $asdf = new cdgfss_mail();
  $asdf->sendMailWithAttachment('t15ys@school.cdgfss.edu.hk', 7);
  // $asdf->TEST_sendMailXLSXAttachment('t15ys@school.cdgfss.edu.hk',1);
  // $asdf->TEST_sendMailPlainTextAttachment('t15ys@school.cdgfss.edu.hk',1);
?>