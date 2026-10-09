<?php

namespace Tests\Feature;

use App\Models\AdminRecord;
use App\Models\AuditLog;
use App\Models\ContactMessage;
use App\Models\NewsletterSubscriber;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PlatformTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    private function admin(): User
    {
        return User::where('email', 'admin')->first();
    }

    private function teacher(): User
    {
        return User::where('email', 'fahad.zahrani@emperor.edu.sa')->first();
    }

    private function student(): User
    {
        return User::where('email', 'student@emperor.sa')->first();
    }

    /* ------------------------------ public site ------------------------------ */

    public function test_home_renders_with_seo_and_dynamic_content(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('أتقن الرياضيات،')
            ->assertSee('عبدالله المطيري')               // testimonials from the workspace
            ->assertSee('application/ld+json', false)
            ->assertSee('<link rel="canonical" href="https://mathemperor.sa/"', false);
    }

    public function test_sitemap_and_robots(): void
    {
        $this->get('/sitemap.xml')->assertOk()->assertSee('<loc>https://mathemperor.sa/#faq</loc>', false);
        $this->get('/robots.txt')->assertOk()->assertSee('Disallow: /imperial/');
    }

    public function test_newsletter_and_contact_are_stored(): void
    {
        $this->postJson('/newsletter', ['email' => 'Fan@Example.com'])->assertOk();
        $this->postJson('/newsletter', ['email' => 'fan@example.com'])->assertOk();
        $this->assertSame(1, NewsletterSubscriber::count());
        $this->postJson('/newsletter', ['email' => 'nope'])->assertStatus(422);

        $this->postJson('/contact', ['name' => 'سالم', 'email' => 's@x.sa', 'subject' => 'شراكة مدرسية', 'message' => 'مرحبا'])
            ->assertOk()->assertJson(['team' => 'الشراكات المؤسسية']);
        $this->assertSame(1, ContactMessage::count());
    }

    /* ---------------------------------- auth ---------------------------------- */

    public function test_role_scoped_login(): void
    {
        $this->postJson('/login', ['identifier' => 'admin', 'password' => 'Emperor@2026', 'role' => 'STUDENT'])
            ->assertStatus(422)->assertJson(['ok' => false]);
        $this->assertGuest();

        $this->postJson('/login', ['identifier' => 'ADMIN', 'password' => 'Emperor@2026', 'role' => 'STAFF', 'remember' => true])
            ->assertOk()->assertJson(['ok' => true, 'redirect' => route('imperial.show')]);
        $this->assertAuthenticatedAs($this->admin());
    }

    public function test_guests_are_sent_home_with_the_right_portal(): void
    {
        $this->get('/student')->assertRedirect('/')->assertSessionHas('open', 'student');
        $this->get('/imperial')->assertRedirect('/')->assertSessionHas('open', 'imperial');
        $this->actingAs($this->student())->get('/imperial')->assertRedirect(route('student.portal'));
    }

    public function test_registration_validates_like_the_original_and_logs_in(): void
    {
        $base = ['name' => 'محمد بن علي بن صالح الحربي', 'phone' => '+966 55 123 4567', 'grade' => 'الصف الثالث الثانوي', 'email' => 'new@student.sa', 'password' => 'secret1'];

        $this->postJson('/register', ['name' => 'محمد علي'] + $base)->assertStatus(422)->assertJson(['field' => 'name']);
        $this->postJson('/register', ['phone' => '123'] + $base)->assertStatus(422)->assertJson(['field' => 'phone']);
        $this->postJson('/register', ['grade' => 'جامعي'] + $base)->assertStatus(422)->assertJson(['field' => 'grade']);
        $this->postJson('/register', ['email' => 'student@emperor.sa'] + $base)->assertStatus(422)->assertJson(['field' => 'email']);

        $this->postJson('/register', $base)->assertOk()->assertJson(['ok' => true]);
        $user = User::where('email', 'new@student.sa')->first();
        $this->assertSame('0551234567', $user->phone);
        $this->assertAuthenticatedAs($user);
        $this->assertSame(3, $user->simRecords()->count());
        $this->assertTrue(AdminRecord::where('dataset', 'students')->where('data->email', 'new@student.sa')->exists());
    }

    public function test_password_reset_requires_the_emailed_code(): void
    {
        Mail::fake();
        $this->postJson('/password/request', ['identifier' => 'admin', 'role' => 'STAFF'])
            ->assertOk()->assertJson(['maskedEmail' => 'a****@emperor.edu.sa']);
        $this->postJson('/password/request', ['identifier' => 'ghost', 'role' => 'STAFF'])
            ->assertOk()->assertJson(['maskedEmail' => null]);

        $this->postJson('/password/reset', ['identifier' => 'admin', 'role' => 'STAFF', 'code' => '000000', 'password' => 'NewPass#2027'])
            ->assertStatus(422);

        // the emailed code is only stored hashed — plant a known one under the same cache key
        \Illuminate\Support\Facades\Cache::put('pw-reset-code:'.$this->admin()->id, \Illuminate\Support\Facades\Hash::make('123456'), 60);
        $this->postJson('/password/reset', ['identifier' => 'admin', 'role' => 'STAFF', 'code' => '123456', 'password' => 'short'])
            ->assertStatus(422);
        $this->postJson('/password/reset', ['identifier' => 'admin', 'role' => 'STAFF', 'code' => '123456', 'password' => 'NewPass#2027'])
            ->assertOk();

        $this->postJson('/login', ['identifier' => 'admin', 'password' => 'NewPass#2027', 'role' => 'STAFF'])->assertOk();
    }

    /* -------------------------------- simulator -------------------------------- */

    public function test_simulator_scores_on_the_server_for_students_only(): void
    {
        $this->postJson('/simulator/attempts', ['answers' => [1, 0, 2]])->assertOk()->assertJson(['recorded' => false]);

        $s = $this->student();
        $before = $s->points;
        $this->actingAs($s)->postJson('/simulator/attempts', ['answers' => [1, 3, -1]])
            ->assertOk()->assertJson(['recorded' => true, 'correct' => 1, 'total' => 3]);
        $s->refresh();
        $this->assertSame($before + 25, $s->points);
        $this->assertSame(33, $s->simRecords()->first()->score);

        $this->actingAs($s)->get('/student')->assertOk()->assertSee('اختبار قصير — تجربة المحاكي');
    }

    /* ------------------------------ imperial: CRUD ------------------------------ */

    public function test_admin_crud_lifecycle_is_audited(): void
    {
        $this->actingAs($this->admin());

        $row = $this->postJson('/imperial/api/records/questions', ['module' => 'questions', 'row' => ['skill' => 'هندسة', 'status' => 'قيد المراجعة']])
            ->assertOk()->json('row');

        $this->patchJson("/imperial/api/records/questions/{$row['id']}", ['module' => 'questions', 'patch' => ['level' => 'صعب']])->assertOk();
        $this->postJson("/imperial/api/records/questions/{$row['id']}/transition", ['module' => 'questions', 'action' => 'publish'])->assertOk();
        $this->assertSame('منشور', AdminRecord::where('key', $row['id'])->first()->data['status']);

        $this->deleteJson("/imperial/api/records/questions/{$row['id']}", ['module' => 'questions', 'reason' => ''])->assertStatus(422);
        $this->deleteJson("/imperial/api/records/questions/{$row['id']}", ['module' => 'questions', 'reason' => 'تجربة'])->assertOk();
        $this->assertFalse(AdminRecord::where('key', $row['id'])->exists());

        $this->assertSame(['حذف', 'نشر', 'تعديل', 'تعديل', 'إضافة'], AuditLog::latest('id')->limit(5)->pluck('action')->all());
    }

    public function test_bulk_import_and_export(): void
    {
        $this->actingAs($this->admin());
        $this->postJson('/imperial/api/records/exams/bulk', ['module' => 'exams', 'keys' => ['ex-13', 'ex-14'], 'kind' => 'publish'])->assertOk();
        $this->assertSame('منشور', AdminRecord::where('key', 'ex-14')->first()->data['status']);

        $this->postJson('/imperial/api/records/teachers/import', ['module' => 'teachers', 'rows' => [['name' => 'أ. جديد', 'status' => 'معتمد', 'nested' => ['x']]]])->assertOk();
        $imported = AdminRecord::where('dataset', 'teachers')->orderBy('position')->first();
        $this->assertSame('أ. جديد', $imported->data['name']);
        $this->assertArrayNotHasKey('nested', $imported->data);

        $csv = $this->get('/imperial/api/records/orders/export?module=commerce&format=csv&ids=or-9141')->assertOk()->streamedContent();
        $this->assertStringContainsString('or-9141', $csv);
        $this->assertStringNotContainsString('or-9155', $csv);
    }

    public function test_module_cannot_touch_an_unrelated_dataset(): void
    {
        $this->actingAs($this->admin())
            ->postJson('/imperial/api/records/orders', ['module' => 'questions', 'row' => ['x' => 1]])
            ->assertStatus(422);
    }

    /* ------------------------------ imperial: RBAC ------------------------------ */

    public function test_teacher_permissions_are_enforced_server_side_and_logged(): void
    {
        $this->actingAs($this->teacher());

        $this->get('/imperial/questions')->assertOk()->assertDontSee('مصفوفة الصلاحيات');
        $this->get('/imperial/rbac')->assertOk()->assertSee('غير مصرح بالوصول');

        // teachers can create questions but not delete them
        $this->postJson('/imperial/api/records/questions', ['module' => 'questions', 'row' => ['skill' => 'جبر']])->assertOk();
        $this->deleteJson('/imperial/api/records/questions/q-4821', ['module' => 'questions', 'reason' => 'x'])->assertStatus(403);
        $this->postJson('/imperial/api/workspace/reset')->assertStatus(403);

        $this->assertTrue(AuditLog::where('action', 'منع صلاحية')->where('module', 'questions')->exists());
    }

    public function test_live_rbac_override_changes_the_teacher_view(): void
    {
        $this->actingAs($this->admin())->postJson('/imperial/api/rbac/toggle', ['role' => 'TEACHER', 'module' => 'questions'])
            ->assertOk()->assertJson(['allowed' => false]);

        $this->actingAs($this->teacher())->get('/imperial/questions')->assertSee('غير مصرح بالوصول');

        $this->actingAs($this->admin())->postJson('/imperial/api/rbac/toggle', ['role' => 'SUPER_ADMIN', 'module' => 'questions'])
            ->assertJson(['allowed' => true]);
    }

    /* ------------------------ imperial: cms · seo · marketing ------------------------ */

    public function test_cms_publish_updates_the_home_hero(): void
    {
        $this->actingAs($this->admin())->postJson('/imperial/api/cms', ['heroLine1' => 'سطر تجريبي', 'heroLine2Gold' => 'ذهبي', 'heroLine3' => 'أخير'])->assertOk();
        $this->get('/')->assertSee('سطر تجريبي');
    }

    public function test_seo_override_reaches_the_head(): void
    {
        $this->actingAs($this->admin())->postJson('/imperial/api/seo', ['page' => 'home', 'title' => 'عنوان مخصص', 'robots' => 'noindex,nofollow'])->assertOk();
        $this->get('/')->assertSee('<title>عنوان مخصص</title>', false)->assertSee('content="noindex, nofollow"', false);
    }

    public function test_marketing_rejects_invalid_publisher_ids_and_loads_valid_ones(): void
    {
        $this->actingAs($this->admin());
        $this->postJson('/imperial/api/marketing', ['publisherId' => 'ca-pub-1234'])->assertStatus(422);
        $this->postJson('/imperial/api/marketing', ['publisherId' => 'ca-pub-123456789012', 'adsenseEnabled' => true])->assertOk();
        $this->get('/')->assertSee('adsbygoogle.js?client=ca-pub-123456789012', false);
    }

    public function test_stats_toggle_hides_the_section(): void
    {
        $this->actingAs($this->admin());
        foreach (['stat-1', 'stat-2', 'stat-3', 'stat-4'] as $k) {
            $this->patchJson("/imperial/api/records/stats/{$k}", ['module' => 'stats', 'patch' => ['enabled' => false]])->assertOk();
        }
        $this->get('/')->assertDontSee('aria-label="إحصاءات المنصة"', false);
    }

    public function test_approval_decision(): void
    {
        $this->actingAs($this->admin());
        $this->postJson('/imperial/api/approvals/ap-1/decide', ['type' => 'reject'])->assertStatus(422);
        $this->postJson('/imperial/api/approvals/ap-1/decide', ['type' => 'approve'])->assertOk();
        $this->assertSame('معتمد ✅', AdminRecord::where('key', 'ap-1')->first()->data['status']);
    }
}
