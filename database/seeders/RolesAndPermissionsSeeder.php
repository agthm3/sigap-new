<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // daftar izin granular – kamu bebas namain sesuai kebutuhan
        $perms = [
            'inovasi.view','inovasi.create','inovasi.edit','inovasi.delete',
            'evidence.fill','evidence.review',
            'pegawai.manage','config.evidence',
        ];

        foreach ($perms as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
        }

        // roles
        $admin      = Role::firstOrCreate(['name'=>'admin','guard_name'=>'web']);
        $inovator   = Role::firstOrCreate(['name'=>'inovator','guard_name'=>'web']);
        $verificator= Role::firstOrCreate(['name'=>'verificator','guard_name'=>'web']);
        $employee   = Role::firstOrCreate(['name'=>'employee','guard_name'=>'web']);
        $researcher = Role::firstOrCreate(['name'=>'researcher','guard_name'=>'web']);
        $user       = Role::firstOrCreate(['name'=>'user','guard_name'=>'web']); // umum

        // mapping izin → role (contoh, silakan sesuaikan)
        $admin->givePermissionTo($perms);

        $inovator->givePermissionTo([
            'inovasi.view','inovasi.create','inovasi.edit','evidence.fill'
        ]);

        $verificator->givePermissionTo([
            'inovasi.view','evidence.review'
        ]);

        $employee->givePermissionTo(['inovasi.view']);
        $researcher->givePermissionTo(['inovasi.view']);
        $user->givePermissionTo(['inovasi.view']);
    }
}
