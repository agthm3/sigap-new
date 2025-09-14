<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // pastikan cache permission bersih
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // pastikan role 'admin' ada
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $email = 'giga.makkasau@gmail.com';

        // generate username unik dari email (bagian sebelum '@')
        $base = Str::of($email)->before('@')->lower()->replaceMatches('/[^a-z0-9._]+/', '.');
        $username = (string) $base;
        $i = 1;
        while (User::where('username', $username)->exists()) {
            $username = "{$base}.{$i}";
            $i++;
        }

        // password default bisa diatur via .env (ADMIN_DEFAULT_PASSWORD), fallback: 'admin12345'
        $plainPassword = env('ADMIN_DEFAULT_PASSWORD', 'admin12345');

        // buat / ambil usernya
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name'              => 'Administrator',
                'username'          => $username,
                'password'          => Hash::make($plainPassword),
                'status'            => 'active',
                'unit'              => 'Administrator',
                'email_verified_at' => now(),
            ]
        );

        // assign role admin (gunakan Spatie)
        if (! $user->hasRole('admin')) {
            $user->assignRole($adminRole);
        }

        $this->command?->info("Admin user siap: {$email} | password: {$plainPassword}");
    }
}
