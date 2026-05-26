<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\LineArea;
use App\Models\User;
use App\Models\WorkshopPerson;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@ie-monitoring.local'],
            [
                'name' => 'Admin IE',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'position' => 'IE Admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'approver@ie.com'],
            [
                'name' => 'Yogi Pratama',
                'password' => Hash::make('password'),
                'role' => 'section_head',
                'position' => 'Section Head Industrial Engineering',
            ]
        );

        User::updateOrCreate(
            ['email' => 'divhead@ie.com'],
            [
                'name' => 'Isa Setyawan',
                'password' => Hash::make('password'),
                'role' => 'division_head',
                'position' => 'Division Head',
            ]
        );

        User::updateOrCreate(
            ['email' => 'direktur@ie.com'],
            [
                'name' => 'Dian Eka H',
                'password' => Hash::make('password'),
                'role' => 'director',
                'position' => 'Direktur',
            ]
        );

        $approvers = [
            ['name' => 'Budi Santoso', 'email' => 'budi.section@ie-monitoring.local', 'position' => 'Section Head'],
            ['name' => 'Sari Wulandari', 'email' => 'sari.department@ie-monitoring.local', 'position' => 'Department Head'],
            ['name' => 'Agus Pratama', 'email' => 'agus.division@ie-monitoring.local', 'position' => 'Division Head'],
            ['name' => 'Rina Marlina', 'email' => 'rina.section@ie-monitoring.local', 'position' => 'Section Head'],
            ['name' => 'Dedi Kurniawan', 'email' => 'dedi.department@ie-monitoring.local', 'position' => 'Department Head'],
            ['name' => 'Maya Lestari', 'email' => 'maya.division@ie-monitoring.local', 'position' => 'Division Head'],
            ['name' => 'Hendra Wijaya', 'email' => 'hendra.section@ie-monitoring.local', 'position' => 'Section Head'],
            ['name' => 'Novi Anggraini', 'email' => 'novi.department@ie-monitoring.local', 'position' => 'Department Head'],
            ['name' => 'Fajar Nugroho', 'email' => 'fajar.division@ie-monitoring.local', 'position' => 'Division Head'],
            ['name' => 'Lia Permata', 'email' => 'lia.section@ie-monitoring.local', 'position' => 'Section Head'],
        ];

        foreach ($approvers as $approver) {
            User::updateOrCreate(
                ['email' => $approver['email']],
                [
                    'name' => $approver['name'],
                    'password' => Hash::make('password'),
                    'role' => 'approver',
                    'position' => $approver['position'],
                ]
            );
        }

        $departments = [
            [
                'name' => 'Production',
                'code' => 'PRD',
                'description' => 'Department produksi',
                'status' => 'Active',
            ],
            [
                'name' => 'QA',
                'code' => 'QA',
                'description' => 'Department quality assurance',
                'status' => 'Active',
            ],
            [
                'name' => 'Maintenance',
                'code' => 'MTC',
                'description' => 'Department maintenance',
                'status' => 'Active',
            ],
            [
                'name' => 'Engineering',
                'code' => 'ENG',
                'description' => 'Department engineering',
                'status' => 'Active',
            ],
            [
                'name' => 'Warehouse',
                'code' => 'WH',
                'description' => 'Department warehouse',
                'status' => 'Active',
            ],
            [
                'name' => 'Office',
                'code' => 'OFF',
                'description' => 'Area office',
                'status' => 'Active',
            ],
            [
                'name' => 'Industrial Engineering',
                'code' => 'IE',
                'description' => 'Department industrial engineering',
                'status' => 'Active',
            ],
        ];

        foreach ($departments as $department) {
            Department::updateOrCreate(
                ['name' => $department['name']],
                [
                    'code' => $department['code'],
                    'description' => $department['description'],
                    'status' => $department['status'],
                ]
            );
        }

        $lineAreas = [
            [
                'name' => 'Welding Line 1',
                'code' => 'WL-01',
                'department' => 'Production',
                'description' => 'Area welding line 1',
                'status' => 'Active',
            ],
            [
                'name' => 'Assembly Line 1',
                'code' => 'AS-01',
                'department' => 'Production',
                'description' => 'Area assembly line 1',
                'status' => 'Active',
            ],
            [
                'name' => 'Painting Area',
                'code' => 'PT-01',
                'department' => 'Production',
                'description' => 'Area painting',
                'status' => 'Active',
            ],
            [
                'name' => 'Quality Check Area',
                'code' => 'QC-01',
                'department' => 'QA',
                'description' => 'Area quality check',
                'status' => 'Active',
            ],
            [
                'name' => 'Warehouse Material',
                'code' => 'WH-01',
                'department' => 'Warehouse',
                'description' => 'Area warehouse material',
                'status' => 'Active',
            ],
            [
                'name' => 'Maintenance Area',
                'code' => 'MTC-01',
                'department' => 'Maintenance',
                'description' => 'Area maintenance',
                'status' => 'Active',
            ],
            [
                'name' => 'Office Area',
                'code' => 'OFF-01',
                'department' => 'Office',
                'description' => 'Area office',
                'status' => 'Active',
            ],
            [
                'name' => 'IE Workshop',
                'code' => 'IE-WS',
                'department' => 'Industrial Engineering',
                'description' => 'Area workshop industrial engineering',
                'status' => 'Active',
            ],
            [
                'name' => 'Jig Area',
                'code' => 'JIG-01',
                'department' => 'Industrial Engineering',
                'description' => 'Area jig',
                'status' => 'Active',
            ],
            [
                'name' => 'Robot Welding Area',
                'code' => 'RBT-01',
                'department' => 'Production',
                'description' => 'Area robot welding',
                'status' => 'Active',
            ],
        ];

        foreach ($lineAreas as $lineArea) {
            LineArea::updateOrCreate(
                ['name' => $lineArea['name']],
                [
                    'code' => $lineArea['code'],
                    'department' => $lineArea['department'],
                    'description' => $lineArea['description'],
                    'status' => $lineArea['status'],
                ]
            );
        }

        foreach (WorkshopPerson::defaultNames() as $index => $name) {
            WorkshopPerson::updateOrCreate(
                ['name' => $name],
                [
                    'status' => 'Active',
                    'sort_order' => $index + 1,
                ]
            );
        }
    }
}
