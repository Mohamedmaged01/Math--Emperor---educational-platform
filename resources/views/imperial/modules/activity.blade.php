<div class="space-y-4">
    <x-card class="border-primary-100 bg-primary-soft/50">
        <p class="text-small font-bold leading-relaxed text-foreground-2">
            <x-i name="mouse-pointer-click" class="inline size-4 text-primary" /> كل إجراء تتخذه في هذه المساحة — إضافة، تعديل، نشر، حذف، قرار، تصدير، استيراد، وحتى <span class="font-black">العمليات الممنوعة لصلاحيتك</span> — يُدوّن هنا لحظيًا باسمك ({{ $user->name }}). السجل قابل للتصفح والبحث وللتصدير.
        </p>
    </x-card>
    @include('imperial.modules.table')
</div>
