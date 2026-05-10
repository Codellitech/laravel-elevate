<?php

namespace Codellitech\Elevate\Commands;

use Illuminate\Console\Command;
use Codellitech\Elevate\Integrations\WhatsAppOTPIntegration;
use function Laravel\Prompts\select;
use function Laravel\Prompts\spin;

class IntegrateCommand extends Command
{
    protected $signature = 'elevate:integrate';
    protected $description = 'Integrate enterprise modules into your Laravel application.';

    public function handle()
    {
        $module = $this->selectAction('Select the module to integrate', [
            'whatsapp-otp' => 'WhatsApp OTP Authentication',
            'rbac' => 'Role Based Access Control (Coming Soon)',
        ]);

        if ($module === 'whatsapp-otp') {
            $this->spinAction('Integrating WhatsApp OTP...', function () {
                $integration = app(WhatsAppOTPIntegration::class);
                $integration->install();
            });

            $this->info('WhatsApp OTP integrated successfully!');
        }

        return 0;
    }

    protected function selectAction($message, $options)
    {
        if (function_exists('Laravel\Prompts\select')) {
            return select($message, $options);
        } else {
            return $this->choice($message, array_values($options), array_key_first($options));
        }
    }

    protected function spinAction($message, $callback)
    {
        if (function_exists('Laravel\Prompts\spin')) {
            return spin($callback, $message);
        } else {
            $this->info($message . ' (please wait)');
            return $callback();
        }
    }
}
