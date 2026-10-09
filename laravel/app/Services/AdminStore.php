<?php

namespace App\Services;

use App\Models\AdminRecord;
use App\Models\AdminSetting;
use App\Models\AuditLog;
use App\Support\AdminSeed;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Imperial workspace persistence — the server-side replacement for the
 * localStorage store. Every mutation writes an audit entry.
 */
class AdminStore
{
    public const DATASETS = [
        'students', 'teachers', 'questions', 'exams', 'contents', 'resources', 'orders',
        'notifications', 'security', 'ai', 'approvals', 'flags', 'stats', 'media', 'testimonials',
    ];

    /* ------------------------------ reads ------------------------------ */

    public function rows(string $dataset): array
    {
        if ($dataset === 'audit') {
            return $this->auditRows();
        }

        return AdminRecord::where('dataset', $dataset)
            ->orderBy('position')->orderBy('id')
            ->get()->map->toRow()->all();
    }

    public function all(): array
    {
        $out = [];
        foreach (self::DATASETS as $d) {
            $out[$d] = $this->rows($d);
        }

        return $out;
    }

    public function find(string $dataset, string $key): ?AdminRecord
    {
        return AdminRecord::where('dataset', $dataset)->where('key', $key)->first();
    }

    public function auditRows(int $limit = 500): array
    {
        return AuditLog::latest('id')->limit($limit)->get()->map->toRow()->all();
    }

    public function setting(string $key, mixed $default = []): mixed
    {
        $value = AdminSetting::find($key)?->value;
        if ($value === null) {
            $value = AdminSeed::settings()[$key] ?? $default;
        }

        return json_decode(json_encode($value), true) ?? $default;
    }

    public function putSetting(string $key, mixed $value): void
    {
        AdminSetting::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    /* ----------------------------- mutations ----------------------------- */

    public function insert(string $dataset, string $module, array $row): array
    {
        $key = (string) ($row['id'] ?? '') ?: $this->uid(substr($dataset, 0, 2));
        unset($row['id']);
        while ($this->find($dataset, $key)) {
            $key = $this->uid(substr($dataset, 0, 2));
        }

        $rec = AdminRecord::create([
            'dataset' => $dataset,
            'key' => $key,
            'data' => $row,
            'position' => $this->topPosition($dataset),
        ]);
        $this->audit('إضافة', $module, $key, $this->summarize($row));

        return $rec->toRow();
    }

    public function update(string $dataset, string $module, string $key, array $patch, bool $log = true): ?array
    {
        $rec = $this->find($dataset, $key);
        if (! $rec) {
            return null;
        }
        unset($patch['id']);
        $rec->data = array_merge($rec->data ?? [], $patch);
        $rec->save();
        if ($log) {
            $this->audit('تعديل', $module, $key, implode(', ', array_keys($patch)));
        }

        return $rec->toRow();
    }

    public function transition(string $dataset, string $module, string $key, string $actionLabel, string $status): void
    {
        $this->update($dataset, $module, $key, ['status' => $status]);
        $this->audit($actionLabel, $module, $key, "الحالة ← {$status}");
    }

    public function remove(string $dataset, string $module, string $key, string $reason): void
    {
        $rec = $this->find($dataset, $key);
        if (! $rec) {
            return;
        }
        $summary = $this->summarize($rec->data ?? []);
        $rec->delete();
        $this->audit('حذف', $module, $key, $reason ?: $summary);
    }

    public function bulk(string $dataset, string $module, array $keys, string $actionLabel, string $status): void
    {
        AdminRecord::where('dataset', $dataset)->whereIn('key', $keys)->get()->each(function (AdminRecord $r) use ($status) {
            $r->data = array_merge($r->data ?? [], ['status' => $status]);
            $r->save();
        });
        $this->audit("{$actionLabel} (جماعي ×".count($keys).')', $module, implode(',', $keys), "الحالة ← {$status}");
    }

    public function import(string $dataset, string $module, array $rows): void
    {
        DB::transaction(function () use ($dataset, $rows) {
            $top = $this->topPosition($dataset) - count($rows) + 1;
            foreach (array_values($rows) as $i => $row) {
                $key = (string) ($row['id'] ?? '') ?: $this->uid('im');
                unset($row['id']);
                while ($this->find($dataset, $key)) {
                    $key = $this->uid('im');
                }
                AdminRecord::create(['dataset' => $dataset, 'key' => $key, 'data' => $row, 'position' => $top + $i]);
            }
        });
        $this->note('استيراد', $module, count($rows).' سجل مستورد بعد المعاينة والتأكيد');
    }

    /** (re)populate datasets + settings with the seed workspace */
    public function seed(): void
    {
        DB::transaction(function () {
            AdminRecord::query()->delete();
            AdminSetting::query()->delete();
            foreach (AdminSeed::datasets() as $dataset => $rows) {
                foreach ($rows as $i => $row) {
                    $key = $row['id'];
                    unset($row['id']);
                    AdminRecord::create(['dataset' => $dataset, 'key' => $key, 'data' => $row, 'position' => $i]);
                }
            }
            foreach (AdminSeed::settings() as $key => $value) {
                $this->putSetting($key, $value);
            }
        });
    }

    public function resetAll(string $reason): void
    {
        AuditLog::query()->delete();
        $this->seed();
        $this->audit('إعادة تعيين المساحة', 'system', 'store', $reason);
    }

    /* ------------------------------- audit ------------------------------- */

    public function audit(string $action, string $module, string $entity, string $detail, string $result = 'success'): void
    {
        AuditLog::create([
            'uid' => $this->uid('au'),
            'actor' => $this->actor(),
            'action' => $action,
            'module' => $module,
            'entity' => $entity,
            'detail' => $detail,
            'result' => $result,
            'at' => now()->format('Y-m-d H:i'),
        ]);
    }

    public function note(string $action, string $module, string $detail, string $result = 'success'): void
    {
        $this->audit($action, $module, $detail, $detail, $result);
    }

    public function denied(string $module, string $action): void
    {
        $this->audit('منع صلاحية', $module, $action, 'محاولة إجراء خارج الصلاحيات — رُفضت', 'denied');
    }

    /* ------------------------------ helpers ------------------------------ */

    public function actor(): string
    {
        return auth()->user()?->name ?? 'غير معروف';
    }

    public function uid(string $prefix): string
    {
        return $prefix.'-'.Str::lower(Str::random(5));
    }

    private function topPosition(string $dataset): int
    {
        $min = AdminRecord::where('dataset', $dataset)->min('position');

        return $min === null ? 0 : ((int) $min) - 1;
    }

    private function summarize(array $row): string
    {
        foreach ($row as $k => $v) {
            if ($k !== 'id') {
                return is_scalar($v) ? (string) $v : '—';
            }
        }

        return '—';
    }
}
