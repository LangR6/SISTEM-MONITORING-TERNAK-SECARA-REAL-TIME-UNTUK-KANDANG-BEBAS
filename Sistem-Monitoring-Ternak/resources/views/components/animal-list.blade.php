{{--
    Daftar ternak dengan:
    - Pencarian ID
    - Filter jenis: Semua / Sapi / Kambing
    - Filter perangkat: Semua / Nyala / Mati
    - Filter lokasi: Semua / Dalam Kandang / Di Luar Kandang

    Ditangani oleh resources/js/modules/animal-list.js

    mode="link"   -> setiap baris adalah tautan ke halaman Lokasi Ternak
    mode="select" -> setiap baris adalah tombol; memilih ternak di peta & panel detail
--}}

@props(['animals', 'selected' => null, 'limit' => null, 'filters' => true, 'mode' => 'select'])

<div data-animal-list @if ($limit) data-limit="{{ $limit }}" @endif>

    {{-- Pencarian --}}
    <div class="px-4 pb-1.5 pt-3">

        <label
            class="flex h-[38px] items-center gap-2 rounded-lg border border-line bg-[#f7f9fb] px-2.5 text-[#91a0ad] transition focus-within:border-[#efb17b] focus-within:shadow-[0_0_0_3px_rgba(242,138,50,0.1)]">

            <x-icon name="search" :size="15" />

            <input type="search" data-list-search aria-label="Cari ID ternak" placeholder="Cari ID ternak..."
                autocomplete="off"
                class="min-w-0 flex-1 appearance-none border-0 bg-transparent p-0 text-[11px] text-navy-900 outline-none placeholder:text-[#9cabb7] [&::-webkit-search-cancel-button]:hidden">

            <button type="button" data-list-clear hidden aria-label="Hapus pencarian"
                class="grid size-[21px] place-items-center rounded-[5px] bg-[#e9eef2] p-0 text-[#8293a0]">
                <x-icon name="x" :size="13" />
            </button>

        </label>

        @if ($filters)
            {{-- Filter Jenis --}}
            <div class="mt-2">
                <label for="animal-type-filter" class="mb-1.5 block text-[10px] font-bold text-[#738594]">
                    Jenis Ternak
                </label>

                <select id="animal-type-filter" data-type-filter
                    class="h-9 w-full rounded-lg border border-line bg-white px-3 text-[11px] font-semibold text-navy-900 outline-none transition focus:border-[#efb17b] focus:ring-2 focus:ring-[#f2a55f]/10">
                    <option value="Semua">Semua Jenis</option>
                    <option value="Sapi">Sapi</option>
                    <option value="Kambing">Kambing</option>
                </select>
            </div>

            {{-- Status + Lokasi --}}
            <div class="mt-2 grid grid-cols-2 gap-2">

                {{-- Filter Perangkat --}}
                <div>
                    <label for="animal-device-filter" class="mb-1.5 block text-[10px] font-bold text-[#738594]">
                        Status Perangkat
                    </label>

                    <select id="animal-device-filter" data-device-filter
                        class="h-9 w-full rounded-lg border border-line bg-white px-2.5 text-[11px] font-semibold text-navy-900 outline-none transition focus:border-[#efb17b] focus:ring-2 focus:ring-[#f2a55f]/10">
                        <option value="Semua">Semua</option>
                        <option value="Nyala">Nyala</option>
                        <option value="Mati">Mati</option>
                    </select>
                </div>

                {{-- Filter Lokasi --}}
                <div>
                    <label for="animal-location-filter" class="mb-1.5 block text-[10px] font-bold text-[#738594]">
                        Lokasi
                    </label>

                    <select id="animal-location-filter" data-location-filter
                        class="h-9 w-full rounded-lg border border-line bg-white px-2.5 text-[11px] font-semibold text-navy-900 outline-none transition focus:border-[#efb17b] focus:ring-2 focus:ring-[#f2a55f]/10">
                        <option value="Semua">Semua</option>
                        <option value="inside">Dalam Kandang</option>
                        <option value="outside">Di Luar</option>
                    </select>
                </div>

            </div>
        @endif
    </div>

    {{-- Daftar --}}
    <div data-list-items
        class="max-h-[390px] overflow-y-auto px-2.5 pb-2 pt-1 [scrollbar-color:#c7d1d9_transparent] [scrollbar-width:thin]">

        @foreach ($animals as $animal)
            @php
                $isLink = $mode === 'link';
                $tag = $isLink ? 'a' : 'button';

                $statusValue = $animal['status']->value;

                /*
                 * Status perangkat:
                 * Offline = Mati
                 * Selain Offline = Nyala
                 */
                $deviceStatus = $statusValue === 'offline' ? 'Mati' : 'Nyala';

                /*
                 * Status lokasi:
                 * inside / outside
                 *
                 * Offline tidak mengubah lokasi terakhir.
                 */
                $locationStatus = match ($statusValue) {
                    'inside' => 'inside',
                    'outside' => 'outside',
                    default => 'inside',
                };
            @endphp

            <{{ $tag }}
                @if ($isLink) href="{{ route('locations', ['ternak' => $animal['id']]) }}"
                @else
                    type="button" @endif
                data-animal-row data-id="{{ $animal['id'] }}" data-type="{{ $animal['type'] }}"
                data-status="{{ $statusValue }}" data-device="{{ $deviceStatus }}"
                data-location="{{ $locationStatus }}" @if (!$isLink && $selected === $animal['id']) data-selected="true" @endif
                @if ($limit && $loop->index >= $limit) hidden @endif
                class="grid w-full grid-cols-[36px_minmax(70px,1fr)_auto_14px] items-center gap-2.5 border-b border-[#edf1f4] px-2 py-3 text-left text-inherit last:border-b-0 hover:rounded-lg hover:bg-[#f6f9fb] data-[selected=true]:rounded-lg data-[selected=true]:bg-[#f6f9fb] data-[selected=true]:shadow-[inset_2px_0_0_var(--color-orange)]">

                {{-- Icon --}}
                <span class="grid size-[34px] place-items-center rounded-[9px] bg-(--tone-soft) text-(--tone)">
                    <x-icon :name="$animal['type'] === 'Sapi' ? 'cattle' : 'goat'" :size="20" />
                </span>

                {{-- Informasi ternak --}}
                <span class="flex min-w-0 flex-col">

                    <strong class="truncate text-xs text-navy-900">
                        {{ $animal['id'] }}
                    </strong>

                    <small class="mt-1 text-[11px] text-muted">
                        {{ $animal['type'] }}
                    </small>

                    {{-- Status lokasi --}}
                    <small
                        class="mt-1 flex items-center gap-1 text-[10px] font-semibold
                        {{ $locationStatus === 'outside' ? 'text-[#d66f18]' : 'text-[#168153]' }}">
                        <i
                            class="size-1.5 rounded-full
                            {{ $locationStatus === 'outside' ? 'bg-[#e67e22]' : 'bg-[#20a66a]' }}"></i>

                        {{ $locationStatus === 'outside' ? 'Di Luar Kandang' : 'Dalam Kandang' }}
                    </small>

                </span>

                {{-- Status --}}
                <span class="flex flex-col items-end gap-1">

                    {{-- Nyala / Mati --}}
                    <span
                        class="inline-flex items-center gap-1 rounded-md px-1.5 py-1 text-[9px] font-extrabold
                        {{ $deviceStatus === 'Nyala' ? 'bg-inside-soft text-[#168153]' : 'bg-offline-soft text-[#7b8b98]' }}">
                        <i
                            class="size-1.5 rounded-full
                            {{ $deviceStatus === 'Nyala' ? 'bg-[#20a66a]' : 'bg-[#8b9ba8]' }}"></i>

                        {{ $deviceStatus }}
                    </span>

                    <small class="text-[10px] text-muted">
                        Update: {{ $animal['time'] }}
                    </small>

                </span>

                <x-icon name="chevron" :size="16" class="text-[#a3b0bb]" />

                </{{ $tag }}>
        @endforeach

        {{-- Tidak ditemukan --}}
        <div data-list-empty hidden class="flex min-h-[190px] flex-col items-center justify-center p-5 text-center">
            <span class="grid size-[38px] place-items-center rounded-[10px] bg-offline-soft text-[#8b9ba8]">
                <x-icon name="search" :size="20" />
            </span>

            <strong class="mt-3 text-xs text-navy-900">
                Ternak tidak ditemukan
            </strong>

            <p class="mb-2.5 mt-1.5 text-[11px] text-muted">
                Coba kata kunci atau filter lainnya.
            </p>

            <button type="button" data-list-reset
                class="rounded-md bg-orange-soft px-2.5 py-1.5 text-[11px] font-extrabold text-orange">
                Reset filter
            </button>
        </div>

    </div>
</div>
