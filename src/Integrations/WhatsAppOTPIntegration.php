<?php

namespace Codellitech\Elevate\Integrations;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class WhatsAppOTPIntegration
{
    public function install(): bool
    {
        // 1. Publish migration
        $this->createMigration();

        // 2. Create Controller
        $this->createController();

        // 3. Create Model
        $this->createModel();

        // 4. Update Routes
        $this->updateRoutes();

        return true;
    }

    protected function createMigration()
    {
        $timestamp = date('Y_m_d_His');
        $path = database_path("migrations/{$timestamp}_create_whatsapp_otps_table.php");
        
        $content = "<?php\n\nuse Illuminate\Database\Migrations\Migration;\nuse Illuminate\Database\Schema\Blueprint;\nuse Illuminate\Support\Facades\Schema;\n\nreturn new class extends Migration {\n    public function up()\n    {\n        Schema::create('whatsapp_otps', function (Blueprint \$table) {\n            \$table->id();\n            \$table->string('phone_number');\n            \$table->string('otp');\n            \$table->timestamp('expires_at');\n            \$table->timestamps();\n        });\n    }\n};";

        File::put($path, $content);
    }

    protected function createController()
    {
        $path = app_path('Http/Controllers/Auth/WhatsAppOTPController.php');
        File::ensureDirectoryExists(dirname($path));

        $content = "<?php\n\nnamespace App\Http\Controllers\Auth;\n\nuse App\Http\Controllers\Controller;\nuse Illuminate\Http\Request;\n\nclass WhatsAppOTPController extends Controller\n{\n    public function send(Request \$request)\n    {\n        // Logic to send OTP via WhatsApp\n    }\n\n    public function verify(Request \$request)\n    {\n        // Logic to verify OTP\n    }\n}";

        File::put($path, $content);
    }

    protected function createModel()
    {
        $path = app_path('Models/WhatsAppOTP.php');
        $content = "<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Model;\n\nclass WhatsAppOTP extends Model\n{\n    protected \$fillable = ['phone_number', 'otp', 'expires_at'];\n}";

        File::put($path, $content);
    }

    protected function updateRoutes()
    {
        $path = base_path('routes/api.php');
        $route = "\nRoute::post('auth/whatsapp/send', [App\Http\Controllers\Auth\WhatsAppOTPController::class, 'send']);\nRoute::post('auth/whatsapp/verify', [App\Http\Controllers\Auth\WhatsAppOTPController::class, 'verify']);\n";
        
        File::append($path, $route);
    }
}
