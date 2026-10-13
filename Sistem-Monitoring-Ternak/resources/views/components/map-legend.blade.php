@props(['area' => 'Area Geofence'])

<div {{ $attributes->merge(['class' => 'flex min-h-[43px] flex-wrap items-center gap-x-5 gap-y-2 px-3.5 py-2.5 md:px-[18px]']) }}>
    @foreach ([['inside', 'Dalam Kandang'], ['outside', 'Keluar Kandang'], ['offline', 'Offline']] as [$status, $label])
        <span data-status="{{ $status }}" class="flex items-center gap-1.5 text-[11px] font-semibold text-[#6a7e8e]">
            <i class="size-2 rounded-full bg-(--tone)"></i>{{ $label }}
        </span>
    @endforeach
    <span class="flex items-center gap-1.5 text-[11px] font-semibold text-[#6a7e8e]">
        <i class="h-2 w-3.5 rounded-sm border border-inside bg-inside/20"></i>{{ $area }}
    </span>
    {{ $slot }}
</div>
