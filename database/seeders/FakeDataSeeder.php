<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Presentacione;
use App\Models\Producto;
use App\Models\Persona;
use App\Models\Cliente;
use App\Models\Proveedore;
use App\Models\Comprobante;
use App\Models\Compra;
use App\Models\Venta;
use App\Models\User;
use App\Models\Caja;
use App\Models\Inventario;
use App\Models\Kardex;
use App\Enums\TipoTransaccionEnum;
use App\Models\Ubicacione;
use App\Events\CreateCompraDetalleEvent;
use App\Events\CreateVentaDetalleEvent;
use App\Events\CreateVentaEvent;
use Faker\Factory as Faker;
use Carbon\Carbon;

class FakeDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('es_ES');

        // 1. Obtener al menos un usuario administrador
        $user = User::first();
        if (!$user) {
            $user = User::create([
                'name' => 'Hide', // Using default
                'email' => 'admin@admin.com',
                'password' => bcrypt('password'),
            ]);
        }

        \Illuminate\Support\Facades\Auth::login($user);

        // 2. Crear Caja Principal
        $caja = Caja::create([
            'nombre' => 'Caja Principal - Ficticia',
            'fecha_hora_apertura' => Carbon::now()->subDays(10),
            'saldo_inicial' => 150.00,
            'estado' => 1,
            'user_id' => $user->id,
        ]);

        // 3. Crear Categorías
        $categorias = [];
        $nombresCategorias = ['Laptops y Computadoras', 'Smartphones', 'Audio y Video', 'Accesorios', 'Gaming'];
        foreach ($nombresCategorias as $nom) {
            $carac = \App\Models\Caracteristica::create(['nombre' => $nom, 'descripcion' => 'Categoría de ' . $nom]);
            $categorias[] = Categoria::create(['caracteristica_id' => $carac->id]);
        }

        // 4. Crear Marcas
        $marcas = [];
        $nombresMarcas = ['Apple', 'Samsung', 'Lenovo', 'HP', 'Logitech', 'Sony'];
        foreach ($nombresMarcas as $nom) {
            $carac = \App\Models\Caracteristica::create(['nombre' => $nom, 'descripcion' => 'Productos de marca ' . $nom]);
            $marcas[] = Marca::create(['caracteristica_id' => $carac->id]);
        }

        // 5. Crear Presentaciones
        $presentaciones = [];
        $carac = \App\Models\Caracteristica::create(['nombre' => 'Unidad', 'descripcion' => 'Por Unidad']);
        $presentaciones[] = Presentacione::create(['caracteristica_id' => $carac->id, 'sigla' => 'UND']);
        
        $carac2 = \App\Models\Caracteristica::create(['nombre' => 'Caja', 'descripcion' => 'Por Caja']);
        $presentaciones[] = Presentacione::create(['caracteristica_id' => $carac2->id, 'sigla' => 'CJ']);

        // 6. Crear Productos
        $productos = [];
        $ubicacion = Ubicacione::first();
        
        for ($i = 1; $i <= 20; $i++) {
            $prod = Producto::create([
                'codigo' => str_pad($faker->unique()->randomNumber(8), 12, '0', STR_PAD_LEFT),
                'nombre' => 'Producto Ficticio ' . $faker->word() . ' ' . $i,
                'descripcion' => $faker->sentence(6),
                'precio' => $faker->randomFloat(2, 50, 2000),
                'estado' => 1,
                'marca_id' => $marcas[array_rand($marcas)]->id,
                'presentacione_id' => $presentaciones[0]->id,
                'categoria_id' => $categorias[array_rand($categorias)]->id,
            ]);
            $productos[] = $prod;
            
            $costo_unitario = $prod->precio * 0.7;
            
            (new Kardex())->crearRegistro([
                'producto_id' => $prod->id,
                'cantidad' => 0,
                'costo_unitario' => $costo_unitario,
            ], TipoTransaccionEnum::Apertura);

            Inventario::create([
                'producto_id' => $prod->id,
                'cantidad' => 0,
                'fecha_vencimiento' => null,
                'ubicacione_id' => $ubicacion->id
            ]);
        }

        // 7. Personas, Clientes y Proveedores
        $clientes = [];
        for ($i = 0; $i < 15; $i++) {
            $persona = Persona::create([
                'razon_social' => $faker->name,
                'direccion' => $faker->address,
                'telefono' => $faker->numerify('#########'),
                'tipo' => 'NATURAL',
                'email' => $faker->unique()->safeEmail,
                'documento_id' => 1, // DNI generally created by DocumentoSeeder
                'numero_documento' => $faker->numerify('########'),
            ]);
            $clientes[] = Cliente::create(['persona_id' => $persona->id]);
        }

        $proveedores = [];
        for ($i = 0; $i < 5; $i++) {
            $persona = Persona::create([
                'razon_social' => $faker->company,
                'direccion' => $faker->address,
                'telefono' => $faker->numerify('#########'),
                'tipo' => 'JURIDICA',
                'email' => $faker->unique()->companyEmail,
                'documento_id' => 2, // RUC generally
                'numero_documento' => $faker->numerify('###########'),
            ]);
            $proveedores[] = Proveedore::create(['persona_id' => $persona->id]);
        }

        $comprobanteFactura = Comprobante::where('tipo_comprobante', 'Factura')->first() ?? Comprobante::first();

        // 8. Crear Compras (Stock Inicial)
        foreach ($proveedores as $prov) {
            $numComprobante = $faker->unique()->numerify('F001-######');
            
            // Subtotal logic simplified
            $total = 0;
            $items = array_rand($productos, 3); // 3 random products
            
            $compra = Compra::create([
                'proveedore_id' => $prov->id,
                'comprobante_id' => $comprobanteFactura->id,
                'user_id' => $user->id,
                'numero_comprobante' => $numComprobante,
                'metodo_pago' => rand(0, 1) ? 'EFECTIVO' : 'TARJETA',
                'fecha_hora' => Carbon::now()->subDays(rand(1, 30)),
                'impuesto' => 18,
                'subtotal' => 0,
                'total' => 0 // calculated below
            ]);

            $totalCompra = 0;
            foreach ($items as $idx) {
                $prod = $productos[$idx];
                $cantidad = rand(10, 50);
                $precio_compra = $prod->precio * 0.7; // 30% margin
                
                $compra->productos()->attach($prod->id, [
                    'cantidad' => $cantidad,
                    'precio_compra' => $precio_compra,
                    'fecha_vencimiento' => Carbon::now()->addMonths(rand(6, 24))->format('Y-m-d')
                ]);

                $totalCompra += ($cantidad * $precio_compra);

                // Disparar evento para actualizar el Kardex y el Inventario
                event(new CreateCompraDetalleEvent($compra, $prod->id, $cantidad, $precio_compra, Carbon::now()->addMonths(6)->format('Y-m-d')));
            }

            $totalCompraConImpuesto = $totalCompra * 1.18;
            $compra->update(['subtotal' => $totalCompra, 'total' => $totalCompraConImpuesto]);
        }

        // 9. Crear Ventas
        for ($i = 0; $i < 20; $i++) {
            $cliente = $clientes[array_rand($clientes)];
            $numComprobante = 'B001-' . str_pad($i + 1, 6, '0', STR_PAD_LEFT);
            
            $venta = Venta::create([
                'cliente_id' => $cliente->id,
                'user_id' => $user->id,
                'caja_id' => $caja->id,
                'comprobante_id' => $comprobanteFactura->id,
                'numero_comprobante' => $numComprobante,
                'metodo_pago' => rand(0, 1) ? 'EFECTIVO' : 'TARJETA',
                'fecha_hora' => Carbon::now()->subDays(rand(0, 5)),
                'subtotal' => 0,
                'impuesto' => 0,
                'total' => 0,
                'monto_recibido' => 0,
                'vuelto_entregado' => 0,
            ]);

            $totalVenta = 0;
            $items = array_rand($productos, rand(1, 3));
            if (!is_array($items)) $items = [$items];

            foreach ($items as $idx) {
                $prod = $productos[$idx];
                $cantidad = rand(1, 3);
                $precio_venta = $prod->precio;
                
                $venta->productos()->attach($prod->id, [
                    'cantidad' => $cantidad,
                    'precio_venta' => $precio_venta
                ]);

                $totalVenta += ($cantidad * $precio_venta);

                // Disparar evento para actualizar inventario y kardex
                event(new CreateVentaDetalleEvent($venta, $prod->id, $cantidad, $precio_venta));
            }

            $totalVentaFinal = $totalVenta * 1.18;
            $venta->update([
                'subtotal' => $totalVenta,
                'impuesto' => $totalVentaFinal - $totalVenta,
                'total' => $totalVentaFinal,
                'monto_recibido' => ceil($totalVentaFinal / 10) * 10,
                'vuelto_entregado' => (ceil($totalVentaFinal / 10) * 10) - $totalVentaFinal
            ]);

            // Disparar evento de venta general (para registro en caja y movimientos)
            event(new CreateVentaEvent($venta));
        }
    }
}
