@props(['status'])

<span data-status="{{ $status->value }}" data-status-badge {{ $attributes->merge(['class' => 'inline-flex items-center gap-1 whitespace-nowrap rounded-[5px] bg-(--tone-soft) px-1.5 py-1 text-[10px] font-extrabold text-(--tone-ink)']) }}>
    <span class="size-[5px] rounded-full bg-(--tone)"></span>
    <span data-status-label>{{ $status->label() }}</span>
</span>
