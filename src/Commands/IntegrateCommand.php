<?php

namespace Codellitech\Elevate\Commands;

use Illuminate\Console\Command;
use Codellitech\Elevate\Integrations\WhatsAppOTPIntegration;
use function Laravel\Prompts\select;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\outro;
use function Laravel\Prompts\spin;

class IntegrateCommand extends Command
{
    protected $signature = 'elevate:integrate {module? : The module to integrate}';
    protected $description = 'Integrate enterprise features automatically.';

    public function handle(WhatsAppOTPIntegration $whatsapp)
    {
        intro('Elevate Integration Engine');

        $module = $this->argument('module') ?: select(
            'Which module would you like to integrate?',
            [
                'whatsapp-otp' => 'WhatsApp OTP Authentication',
                'rbac' => 'Role Based Access Control',
                'audit-trails' => 'Audit Trails & Activity Logs',
            ]
        );

        if ($module === 'whatsapp-otp') {
            spin(
                fn () => $whatsapp->install(),
                'Installing WhatsApp OTP module...'
            );
        }

        outro("Integration of {$module} complete!");
    }
}
