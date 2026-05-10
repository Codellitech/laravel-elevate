<?php

namespace Codellitech\Elevate\Commands;

use Illuminate\Console\Command;
use Codellitech\Elevate\Integrations\WhatsAppOTPIntegration;

class IntegrateCommand extends Command
{
    protected $signature = 'elevate:integrate {module? : The module to integrate}';
    protected $description = 'Integrate enterprise features automatically.';

    public function handle(WhatsAppOTPIntegration $whatsapp)
    {
        $this->introAction('Elevate Integration Engine');

        $options = [
            'whatsapp-otp' => 'WhatsApp OTP Authentication',
            'rbac' => 'Role Based Access Control',
            'audit-trails' => 'Audit Trails & Activity Logs',
        ];

        $module = $this->argument('module');

        if (!$module) {
            if (function_exists('Laravel\Prompts\select')) {
                $module = \Laravel\Prompts\select('Which module would you like to integrate?', $options);
            } else {
                $module = $this->choice('Which module would you like to integrate?', $options, 'whatsapp-otp');
            }
        }

        if ($module === 'whatsapp-otp') {
            $this->spinAction(
                fn () => $whatsapp->install(),
                'Installing WhatsApp OTP module...'
            );
        }

        $this->outroAction("Integration of {$module} complete!");
    }

    protected function introAction(string $message)
    {
        if (function_exists('Laravel\Prompts\intro')) {
            \Laravel\Prompts\intro($message);
        } else {
            $this->info($message);
        }
    }

    protected function outroAction(string $message)
    {
        if (function_exists('Laravel\Prompts\outro')) {
            \Laravel\Prompts\outro($message);
        } else {
            $this->info($message);
        }
    }

    protected function spinAction(\Closure $callback, string $message)
    {
        if (function_exists('Laravel\Prompts\spin')) {
            return \Laravel\Prompts\spin($callback, $message);
        }
        $this->info($message);
        return $callback();
    }
}
