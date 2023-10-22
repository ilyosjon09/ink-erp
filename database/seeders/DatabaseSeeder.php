<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\PaperProp;
use App\Models\PaperType;
use App\Models\Service;
use App\Models\ServicePrice;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->permissions();

        // $superadmin = Role::query()->where('name', 'Суперадмин')->first();
        // $superadmin->syncPermissions(Permission::all());
    }
    protected function permissions(): void
    {
        Permission::destroy(Permission::all(['id'])->pluck('id')->toArray());
        Permission::query()->insert([
            [
                'name' => 'order.create',
                'guard_name' => 'web'
            ],
            [
                'name' => 'order.edit',
                'guard_name' => 'web'
            ],
            [
                'name' => 'order.view',
                'guard_name' => 'web'
            ],
            [
                'name' => 'order.delete',
                'guard_name' => 'web'
            ],
            [
                'name' => 'order.accept',
                'guard_name' => 'web'
            ],
            [
                'name' => 'order.mark-as-printed',
                'guard_name' => 'web'
            ],
            [
                'name' => 'order.mark-as-processed',
                'guard_name' => 'web'
            ],
            [
                'name' => 'user.create',
                'guard_name' => 'web'
            ],
            [
                'name' => 'user.edit',
                'guard_name' => 'web'
            ],
            [
                'name' => 'user.view',
                'guard_name' => 'web'
            ],
            [
                'name' => 'user.delete',
                'guard_name' => 'web'
            ],
            [
                'name' => 'role.create',
                'guard_name' => 'web'
            ],
            [
                'name' => 'role.edit',
                'guard_name' => 'web'
            ],
            [
                'name' => 'role.view',
                'guard_name' => 'web'
            ],
            [
                'name' => 'role.delete',
                'guard_name' => 'web'
            ],
            [
                'name' => 'dictionary.view',
                'guard_name' => 'web'
            ],
            [
                'name' => 'print_shop.view',
                'guard_name' => 'web'
            ],
            [
                'name' => 'post_print_shop.view',
                'guard_name' => 'web'
            ],
            [
                'name' => 'warehouse.view',
                'guard_name' => 'web'
            ],
            [
                'name' => 'cash_office.view',
                'guard_name' => 'web'
            ],
            [
                'name' => 'reports.view',
                'guard_name' => 'web'
            ],
        ]);

        Role::destroy(Role::all('id')->pluck('id')->toArray());
        Role::create([
            'name' => 'Суперадмин',
            'guard_name' => 'web',
        ]);
        Role::create([
            'name' => 'Менеджер',
            'guard_name' => 'web',
        ]);
        $superadmin = Role::query()->where('name', 'Суперадмин')->first();

        $superadmin->syncPermissions(Permission::all());
    }
    protected function servicePrices(): void
    {
        $servicePrices = [
            [
                'service_id' => 1,
                'print_type' => '4+0',
                'price_before_1k' => 200_000,
                'price_after_1k' => 200,
                'calc_method' => 0,
                'price' => 200_000,
            ],
            [
                'service_id' => 1,
                'print_type' => '4+4',
                'price_before_1k' => 350_000,
                'price_after_1k' => 350,
                'calc_method' => 0,
                'price' => 200_000,
            ],
        ];

        ServicePrice::query()->insert($servicePrices);
    }

    protected function paperTypes(): void
    {
        $paperTypes = [
            ['name' => 'Картон'],
            ['name' => 'Самоклей'],
            ['name' => 'Меловка'],
            ['name' => 'Офсет'],
            ['name' => 'Дв. Картон'],
            ['name' => 'Пергамент ОГ'],
        ];
        PaperType::insert($paperTypes);

        $paperProps = [
            // Karton
            ['paper_type_id' => 1,    'grammage' => 190, 'size' => '35*50', 'price' => 685],
            ['paper_type_id' => 1,    'grammage' => 190, 'size' => '23*50', 'price' => 455],
            ['paper_type_id' => 1,    'grammage' => 210, 'size' => '35*50', 'price' => 750],
            ['paper_type_id' => 1,    'grammage' => 210, 'size' => '23*50', 'price' => 500],
            ['paper_type_id' => 1,    'grammage' => 230, 'size' => '35*50', 'price' => 810],
            ['paper_type_id' => 1,    'grammage' => 230, 'size' => '23*50', 'price' => 540],
            ['paper_type_id' => 1,    'grammage' => 250, 'size' => '35*50', 'price' => 875],
            ['paper_type_id' => 1,    'grammage' => 250, 'size' => '23*50', 'price' => 585],
            ['paper_type_id' => 1,    'grammage' => 270, 'size' => '35*50', 'price' => 950],
            ['paper_type_id' => 1,    'grammage' => 270, 'size' => '23*50', 'price' => 640],
            ['paper_type_id' => 1,    'grammage' => 300, 'size' => '35*50', 'price' => 1050],
            ['paper_type_id' => 1,    'grammage' => 300, 'size' => '23*50', 'price' => 700],
            ['paper_type_id' => 1,    'grammage' => 350, 'size' => '35*50', 'price' => 1265],
            ['paper_type_id' => 1,    'grammage' => 350, 'size' => '23*50', 'price' => 850],
            ['paper_type_id' => 1,    'grammage' => 400, 'size' => '35*50', 'price' => 1450],
            ['paper_type_id' => 1,    'grammage' => 400, 'size' => '23*50', 'price' => 1000],
        ];
        PaperProp::insert($paperProps);
    }

    protected function services(): void
    {
        $services = [
            ['name' => 'Печать'],
            ['name' => 'Лак'],
            ['name' => 'Лак офсет'],
            ['name' => 'Ламинация Мат'],
            ['name' => 'Ламинация Глянц.'],
            ['name' => 'Выбороч. Лак'],
            ['name' => 'Тигель'],
            ['name' => 'Резка'],
            ['name' => 'Тиснение'],
            ['name' => 'Склейка'],
        ];
        Service::insert($services);
    }
}
