<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestEmail;
use Illuminate\Support\Facades\Log;

class TestEmailCommand extends Command
{
    protected $signature = 'test:email {email} {--queue}';
    protected $description = 'Test email sending functionality';

    public function handle()
    {
        $email = $this->argument('email');
        $useQueue = $this->option('queue');

        $this->info("Sending test email to: " . $email);
        $this->info("Using queue: " . ($useQueue ? 'Yes' : 'No'));

        try {
            $mail = new \App\Mail\TestEmail();
            
            if ($useQueue) {
                Mail::to($email)->queue($mail);
                $this->info('Email has been queued!');
            } else {
                Mail::to($email)->send($mail);
                $this->info('Email sent successfully!');
            }
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error sending email: ' . $e->getMessage());
            Log::error('Email sending failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
