<div class="flex min-h-[69px] flex-wrap items-center justify-end gap-[9px] border-t border-line bg-[#fafcfd] px-4 py-3.5 md:px-[22px]">
    @if (session('status'))
        <span role="status" data-flash class="mb-1 w-full text-[11px] font-bold text-inside min-[441px]:mb-0 min-[441px]:mr-auto min-[441px]:w-auto">✓ {{ session('status') }}</span>
    @endif
    <button type="reset" class="inline-flex min-h-[34px] items-center justify-center rounded-lg border border-[#d6e0e8] bg-white px-3.5 text-[11px] font-bold text-navy-700 hover:bg-[#f7f9fb]">Batal</button>
    <button type="submit" class="inline-flex min-h-[34px] items-center justify-center rounded-lg border border-orange bg-orange px-3.5 text-[11px] font-bold text-white hover:bg-orange-dark">Simpan Perubahan</button>
</div>
