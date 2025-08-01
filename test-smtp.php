<?php

require __DIR__.'/vendor/autoload.php';

use Swift_SmtpTransport as SmtpTransport;
use Swift_Mailer as Mailer;
use Swift_Message as Message;

// SMTP Configuration
$smtpHost = 'smtp.hostinger.com';
$smtpPort = 465;
$smtpEncryption = 'ssl';
$smtpUsername = 'noreply@equitycircle.cloud';
$smtpPassword = 'EquityCircle2025!@';

// Email details
$fromEmail = 'noreply@equitycircle.cloud';
$fromName = 'SCLR';
$toEmail = 'ahmadmujtabap70@gmail.com';
$subject = 'Test Email via SMTP';
$messageText = 'This is a test email sent directly via SMTP at ' . date('Y-m-d H:i:s');

// Debug output
echo "=== SMTP Email Test ===\n";
echo "From: $fromName <$fromEmail>\n";
echo "To: $toEmail\n";
echo "Subject: $subject\n";
echo "SMTP Server: $smtpHost:$smtpPort\n";
echo "Encryption: $smtpEncryption\n\n";

try {
    // Create the Transport
    $transport = (new SmtpTransport($smtpHost, $smtpPort, $smtpEncryption))
        ->setUsername($smtpUsername)
        ->setPassword($smtpPassword);

    // Disable SSL verification (for testing only)
    $transport->setStreamOptions([
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
    ]);

    // Create the Mailer using your created Transport
    $mailer = new Mailer($transport);

    // Create a message
    $message = (new Message($subject))
        ->setFrom([$fromEmail => $fromName])
        ->setTo([$toEmail => 'Test User'])
        ->setBody($messageText);

    echo "Sending email...\n";
    
    // Send the message
    $result = $mailer->send($message);
    
    if ($result > 0) {
        echo "SUCCESS: Email sent to $result recipient(s)\n";
    } else {
        echo "ERROR: Failed to send email. No recipients received the message.\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    if ($e->getPrevious()) {
        echo "DETAILS: " . $e->getPrevious()->getMessage() . "\n";
    }
    
    // Additional debug information
    if (isset($transport)) {
        echo "\nSMTP Debug Info:\n";
        echo "- Host: " . $transport->getHost() . "\n";
        echo "- Port: " . $transport->getPort() . "\n";
        echo "- Encryption: " . $transport->getEncryption() . "\n";
        echo "- Username: " . $transport->getUsername() . "\n";
    }
}

echo "\nTest completed.\n";
