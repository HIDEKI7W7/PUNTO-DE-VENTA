<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    /**
     * Crea o actualiza el usuario administrador con credenciales conocidas.
     */
    public function run(): void
    {
        // Correr seeders base si no existen datos previos
        if (Permission::count() === 0) {
            $this->call(DocumentoSeeder::class);
            $this->call(ComprobanteSeeder::class);
            $this->call(PermissionSeeder::class);
            $this->call(UbicacioneSeeder::class);
            $this->call(MonedaSeeder::class);
        }

        // Crear rol administrador si no existe
        $rol = Role::firstOrCreate(['name' => 'administrador']);
        $permisos = Permission::pluck('id', 'id')->all();
        $rol->syncPermissions($permisos);

        // Crear o actualizar usuario admin
        $user = User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name'     => 'Administrador',
                'password' => Hash::make('12345678'),
            ]
        );

        $user->syncRoles(['administrador']);

        // Crear empresa si no existe
        if (\App\Models\Empresa::count() === 0) {
            \App\Models\Empresa::create([
                'nombre'               => 'EL PUNTO',
                'propietario'          => 'EL PUNTO',
                'ruc'                  => '1089674538',
                'porcentaje_impuesto'  => '15',
                'abreviatura_impuesto' => 'IGV',
                'direccion'            => 'Av. Los Pinos n°789',
                'moneda_id'            => 1,
            ]);
        } else {
            // Actualizar nombre de empresa existente
            \App\Models\Empresa::query()->update(['nombre' => 'EL PUNTO']);
        }
    }
}
