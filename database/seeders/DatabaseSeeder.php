<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\AdminStore;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /** demo accounts from the original build + the seeded imperial workspace */
    public function run(AdminStore $store): void
    {
        $password = 'Emperor@2026';

        User::updateOrCreate(['email' => 'admin'], [
            'name' => 'مدير النظام الأعلى', 'password' => $password,
            'role' => User::STAFF, 'staff_role' => 'SUPER_ADMIN',
            'staff_tracks' => ['الإدارة العليا', 'الإشراف التربوي'],
            'recovery_email' => 'admin@emperor.edu.sa',
        ]);

        User::updateOrCreate(['email' => 'fahad.zahrani@emperor.edu.sa'], [
            'name' => 'أ. فهد الزهراني', 'password' => $password,
            'role' => User::STAFF, 'staff_role' => 'TEACHER',
            'staff_tracks' => ['التدريس', 'الإشراف على المحتوى'],
            'recovery_email' => 'fahad.zahrani@emperor.edu.sa',
        ]);

        foreach ([['student@emperor.sa', 'الطالب المتفوق'], ['demo@emperor.sa', 'طالب تجريبي']] as [$email, $name]) {
            $user = User::updateOrCreate(['email' => $email], [
                'name' => $name, 'password' => $password, 'role' => User::STUDENT,
            ]);
            if (! $user->simRecords()->exists()) {
                $user->seedDefaultPortal();
            }
        }

        $store->seed();
    }
}
