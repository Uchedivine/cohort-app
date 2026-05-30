<?php

// Run this file with: php setup-secretary.php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

echo "=== Secretary Account Setup ===\n\n";

// Check if roles exist
$secretaryRole = Role::where('name', 'secretary')->first();
if (!$secretaryRole) {
    echo "❌ Secretary role not found!\n";
    echo "Please run: php artisan db:seed --class=RoleSeeder\n";
    exit(1);
}

echo "✅ Secretary role exists\n";

// Check if secretary account exists
$secretary = User::where('email', 'secretary@cohortapp.com')->first();

if ($secretary) {
    echo "✅ Secretary account exists: {$secretary->email}\n";
    
    // Check if has role
    if ($secretary->hasRole('secretary')) {
        echo "✅ Has secretary role\n";
    } else {
        echo "⚠️  Missing secretary role, assigning...\n";
        $secretary->assignRole('secretary');
        echo "✅ Secretary role assigned\n";
    }
    
    // Reset password
    $secretary->password = Hash::make('Secretary@2024!');
    $secretary->save();
    echo "✅ Password reset to: Secretary@2024!\n";
    
} else {
    echo "⚠️  Secretary account not found, creating...\n";
    
    $secretary = User::create([
        'name' => 'Cohort Secretary',
        'email' => 'secretary@cohortapp.com',
        'password' => Hash::make('Secretary@2024!'),
        'email_verified_at' => now(),
    ]);
    
    $secretary->assignRole('secretary');
    
    echo "✅ Secretary account created\n";
}

echo "\n=== Setup Complete ===\n";
echo "Email: secretary@cohortapp.com\n";
echo "Password: Secretary@2024!\n";
echo "Login at: http://localhost:8000/login\n";

