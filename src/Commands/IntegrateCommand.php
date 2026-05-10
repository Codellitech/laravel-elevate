<?php

namespace Codellitech\Elevate\Commands;

use Illuminate\Console\Command;
use Codellitech\Elevate\Integrations\WhatsAppOTPIntegration;
use function Laravel\Prompts\select;

class IntegrateCommand extends Command
{
    protected $signature = 'elevate:integrate';
    protected $description = 'Inject enterprise-grade modules into your Laravel application.';

    protected array $actions_taken = [];

    public function handle()
    {
        $this->displayBranding();

        $module = $this->selectAction('Select the module to integrate:', [
            'whatsapp-otp' => 'WhatsApp OTP Authentication (Ready)',
            'rbac'         => 'Role Based Access Control (Coming Soon)',
            'stripe-saas'  => 'Stripe SaaS Subscription (Coming Soon)',
            'socialite'    => 'Socialite (Google/GitHub Logins) (Coming Soon)',
            'filament'     => 'Filament Admin Panel (Coming Soon)',
            'impersonate'  => 'User Impersonation & Activity Logs (Coming Soon)',
            'session-spy'  => 'User Session Replay (Clarity Style) (Coming Soon)',
            'notifications' => 'Toast & Popup Notifications (Coming Soon)',
            'pwa'          => 'Progressive Web App (PWA) Support (Coming Soon)',
            'seo-suite'    => 'Advanced SEO & Meta Suite (Coming Soon)',
        ]);

        if (str_contains($module, '(Coming Soon)') || $module !== 'whatsapp-otp') {
            $this->warn("The selected module is currently in development. Stay tuned for the next release!");
            return 0;
        }

        $integration = new WhatsAppOTPIntegration();
        $this->info("Integrating WhatsApp OTP...");
        
        if ($integration->install()) {
            $this->recordActions();
            $this->displayIntegrationReport();
            $this->displayNextSteps();
            $this->celebrate();
        }

        return 0;
    }

    protected function recordActions()
    {
        $this->actions_taken = [
            ['Migration', 'database/migrations/xxxx_create_whatsapp_otps_table.php', 'Created'],
            ['Controller', 'app/Http/Controllers/Auth/WhatsAppOTPController.php', 'Created'],
            ['Model', 'app/Models/WhatsAppOTP.php', 'Created'],
            ['Routes', 'routes/api.php', 'Updated'],
        ];
    }

    protected function displayIntegrationReport()
    {
        $this->line("\n" . str_repeat('━', 72));
        $this->line('                          INTEGRATION REPORT');
        $this->line(str_repeat('━', 72));
        $this->table(['Area', 'Target/Action', 'Status'], $this->actions_taken);
        $this->line('');
    }

    protected function displayNextSteps()
    {
        $this->info("👉 NEXT STEPS TO ACTIVATE:");
        $this->line("1. Run: php artisan migrate");
        $this->line("2. Add your WhatsApp API credentials to .env");
        $this->line("3. Review the routes in routes/api.php");
        $this->line("");
    }

    protected function celebrate()
    {
        $this->line(str_repeat('✧', 72));
        $this->line('   WOOHOO! THE MODULE HAS BEEN SUCCESSFULLY INTEGRATED! 🚀');
        $this->line('   Your application just got more powerful.');
        $this->line(str_repeat('✧', 72));
        $this->line('');
    }

    protected function displayBranding()
    {
        $this->line(str_repeat('━', 72));
        $this->line('               LARAVEL ELEVATE by Codelli Technologies');
        $this->line('                   Module Integration Engine');
        $this->line(str_repeat('━', 72));
        $this->line('');
    }

    protected function selectAction($message, $options)
    {
        return function_exists('Laravel\Prompts\select') ? select($message, $options) : $this->choice($message, array_values($options), array_key_first($options));
    }
}
