<!DOCTYPE html>
<html>
<head>
    <title>Test Email</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #f4f4f4; padding: 20px; text-align: center; }
        .content { padding: 20px; }
        .footer { margin-top: 20px; padding: 10px; text-align: center; font-size: 12px; color: #666; }
        .config { background-color: #f9f9f9; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .success { color: #28a745; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Test Email from {{ config('app.name') }}</h1>
        </div>
        
        <div class="content">
            <p>Hello,</p>
            <p>This is a test email sent from your Laravel application.</p>
            
            <div class="config">
                <h3>Email Configuration:</h3>
                <ul>
                    <li><strong>Mailer:</strong> {{ $config['mail.mailer'] }}</li>
                    <li><strong>Host:</strong> {{ $config['mail.host'] }}</li>
                    <li><strong>Port:</strong> {{ $config['mail.port'] }}</li>
                    <li><strong>Encryption:</strong> {{ $config['mail.encryption'] ?? 'None' }}</li>
                    <li><strong>From Address:</strong> {{ $config['mail.from.address'] }}</li>
                    <li><strong>From Name:</strong> {{ $config['mail.from.name'] }}</li>
                    <li><strong>Environment:</strong> {{ $config['app.env'] }}</li>
                    <li><strong>App URL:</strong> {{ $config['app.url'] }}</li>
                </ul>
            </div>
            
            <p class="success">✓ Email sent successfully at {{ $time }}</p>
        </div>
        
        <div class="footer">
            <p>This is an automated message, please do not reply.</p>
        </div>
    </div>
</body>
</html>
