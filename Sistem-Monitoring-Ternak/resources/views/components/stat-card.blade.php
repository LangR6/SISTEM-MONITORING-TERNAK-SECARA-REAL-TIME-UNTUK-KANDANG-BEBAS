@props(['label', 'value', 'sub', 'icon', 'tone'])

<article data-status="{{ $tone }}" class="flex min-h-[104px] items-start justify-between rounded-card border border-line bg-white p-[18px] shadow-card md:min-h-32 md:p-[22px]">
    <div class="flex flex-col">
        <span class="text-xs font-semibold text-muted">{{ $label }}</span>
        <strong class="mb-2 mt-2 text-[25px] leading-none tracking-tight text-navy-900 md:text-[29px]">{{ $value }}</strong>
        <small class="flex items-center gap-1.5 whitespace-nowrap text-[11px] text-[#8494a3]">
            <i class="size-1.5 rounded-full bg-(--tone)"></i>{{ $sub }}
        </small>
    </div>
    <span class="grid size-11 shrink-0 place-items-center rounded-[11px] bg-(--tone-soft) text-(--tone)">
        <x-icon :name="$icon" :size="24" />
    </span>
</article>
