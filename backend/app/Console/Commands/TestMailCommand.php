<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:test {email? : Email address to send test email to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test email to verify MailHog integration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?? 'test@example.com';
        
        try {
            Mail::raw('This is a test email from Q-Potek Dashboard to verify MailHog integration.', function ($message) use ($email) {
                $message->to($email)
                        ->subject('Test Email - MailHog Integration')
                        ->from(config('mail.from.address'), config('mail.from.name'));
            });
            
            $this->info("✅ Test email sent successfully to: {$email}");
            $this->info("📧 Check MailHog at: http://localhost:8025");
            
        } catch (\Exception $e) {
            $this->error("❌ Failed to send test email: " . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
