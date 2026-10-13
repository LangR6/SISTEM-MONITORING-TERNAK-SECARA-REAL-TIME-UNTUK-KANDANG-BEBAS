<footer class="mt-[18px] flex min-h-[66px] flex-col items-start gap-2.5 px-[3px] pb-[18px] text-muted sm:flex-row sm:items-center sm:justify-between sm:gap-0 sm:pb-0">
    <div class="flex flex-col">
        <strong class="text-[11px] text-[#536b7f]">Terakhir diperbarui: {{ \App\Support\LivestockRepository::LAST_SYNC }}</strong>
        <span class="mt-1 text-[10px]">Update otomatis setiap 15 menit</span>
    </div>
    <span class="flex items-center gap-[7px] rounded-[7px] bg-inside-soft px-2.5 py-[7px] text-[11px] font-bold text-[#19875a]">
        <i class="size-1.5 rounded-full bg-inside shadow-[0_0_0_3px_rgba(32,166,106,0.12)]"></i>
        <x-icon name="gateway" :size="16" /> Gateway Online
    </span>
</footer>
