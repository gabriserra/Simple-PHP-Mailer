<?php

    // show errors
    ini_set( 'display_errors', 1 );
    error_reporting( E_ALL );

    // sender & recipient address
    $from = "hello@donkeykonglivorno.com";
    $to = "gabriele_serra@hotmail.it";

    // mail content
    $subject = "PHP Mailer Test";
    $message = "<b>If you can read this line your server can send emails.<b>";
    
    // mail headers
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-type: text/html; charset=iso-8859-1';
    $headers[] = 'From: ' . $from;
    
    if(mail($to,$subject,$message, implode("\r\n", $headers)))
        echo "Mail sent. Check your inbox folder";
    else
        echo "Problem sending emails."
?>