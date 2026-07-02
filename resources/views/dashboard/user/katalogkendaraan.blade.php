@extends('layouts.user')

@section('title', 'Katalog Kendaraan')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user/katalog-kendaraan.css') }}">
@endpush

@section('content')

{{-- HERO --}}
<section class="hero-title">
    <h1>Katalog Armada</h1>
    <p>Pilih kendaraan terbaik untuk perjalanan tak terlupakan Anda.</p>
</section>

<section class="catalog-wrapper">

    {{-- ========================
        SIDEBAR FILTER
    ======================== --}}
    <aside class="filter-sidebar">

        <h3>Filter Pencarian</h3>

        {{-- Tipe Kendaraan (chip) --}}
        <div class="filter-group">
            <label>Tipe Kendaraan</label>
            <div class="chip-group" id="typeFilter">
                <span class="chip active" data-val="">Semua</span>
                <span class="chip" data-val="bus">Bus Pariwisata</span>
                <span class="chip" data-val="hiace">Mini Bus / Hiace</span>
                <span class="chip" data-val="mobil">Mobil Keluarga</span>
            </div>
        </div>

        {{-- Kapasitas --}}
        <div class="filter-group">
            <label>Kapasitas Kursi</label>
            <select id="kapasitasFilter">
                <option value="">Semua Kapasitas</option>
                <option value="5">5+ Orang</option>
                <option value="10">10+ Orang</option>
                <option value="20">20+ Orang</option>
                <option value="40">40+ Orang</option>
            </select>
        </div>

        {{-- Rentang Harga --}}
        <div class="filter-group">
            <label>Rentang Harga (Rp)</label>
            <input
                type="range"
                id="priceRange"
                min="500000"
                max="10000000"
                step="100000"
                value="10000000">
            <div class="range-labels">
                <span>500rb</span>
                <span id="priceLabel">Maks</span>
            </div>
        </div>

        {{-- Fasilitas --}}
        <div class="filter-group">
            <label>Fasilitas</label>
            <div class="chip-group" id="facFilter">
                <span class="chip" data-val="ac">AC</span>
                <span class="chip" data-val="wifi">WiFi</span>
                <span class="chip" data-val="usb">USB</span>
                <span class="chip" data-val="tv">TV</span>
                <span class="chip" data-val="toilet">Toilet</span>
            </div>
        </div>

        {{-- Pencarian --}}
        <div class="filter-group">
            <label>Cari Kendaraan</label>
            <input
                type="text"
                id="searchKendaraan"
                placeholder="Nama kendaraan...">
        </div>

        {{-- Promo Banner --}}
        <div class="promo-banner">
            <p>Promo Akhir Tahun</p>
            <h4>Diskon 20% Untuk Sewa Bus Besar</h4>
            <a href="#" class="promo-btn">Cek Sekarang</a>
        </div>

    </aside>

    {{-- ========================
         KATALOG CONTENT
    ======================== --}}
    <div class="catalog-content">

        <div class="catalog-header">
            <h2 id="resultCount">Menampilkan {{ $kendaraans->count() }} kendaraan</h2>
            <select class="sort-select" id="sortSelect">
                <option value="default">Urutkan</option>
                <option value="harga-asc">Harga Terendah</option>
                <option value="harga-desc">Harga Tertinggi</option>
                <option value="kapasitas-desc">Kapasitas Terbesar</option>
            </select>
        </div>

        <div class="catalog-grid" id="katalogGrid">

            @forelse($kendaraans as $kendaraan)

            {{-- Fasilitas disimpan sebagai JSON di kolom `fasilitas` (array),
                contoh: ["ac","wifi","usb","tv"]
                Sesuaikan nama kolom dengan struktur tabel kamu. --}}

            <div class="catalog-card kendaraan-card"
                data-nama="{{ strtolower($kendaraan->nama_kendaraan) }}"
                data-jenis="{{ strtolower($kendaraan->jenis_kendaraan) }}"
                data-kapasitas="{{ $kendaraan->kapasitas }}"
                data-harga="{{ $kendaraan->harga_sewa }}"
                data-fasilitas="{{ is_array($kendaraan->fasilitas) ? implode(',', $kendaraan->fasilitas) : $kendaraan->fasilitas }}">

                {{-- IMAGE + BADGE --}}
                <div class="card-image">

                    <img
                        src="{{ $kendaraan->foto_kendaraan
                            ? asset('storage/' . $kendaraan->foto_kendaraan)
                            : asset('img/default.png') }}"
                        alt="{{ $kendaraan->nama_kendaraan }}">

                    <span class="badge-status
                        {{ $kendaraan->status_kendaraan === 'tersedia'
                            ? 'status-tersedia'
                            : ($kendaraan->status_kendaraan === 'jadwal_terbatas'
                                ? 'status-terbatas'
                                : 'status-tidak') }}">
                        {{ ucfirst(str_replace('_', ' ', $kendaraan->status_kendaraan)) }}
                    </span>

                    @if($kendaraan->label ?? false)
                        <span class="badge-premium">{{ $kendaraan->label }}</span>
                    @endif

                </div>

                {{-- BODY --}}
                <div class="catalog-body">

                    <div class="card-name">{{ $kendaraan->nama_kendaraan }}</div>

                    <p class="card-type">
                        🚘 {{ ucfirst($kendaraan->jenis_kendaraan) }}
                    </p>

                    {{-- Fasilitas Pill --}}
                    @if($kendaraan->fasilitas)
                    <div class="facility-row">
                        @php
                            $facLabels = ['ac'=>'❄️ AC','wifi'=>'📶 WiFi','usb'=>'🔌 USB','tv'=>'📺 TV','toilet'=>'🚽 Toilet'];
                            $facList = is_array($kendaraan->fasilitas)
                                ? $kendaraan->fasilitas
                                : explode(',', $kendaraan->fasilitas);
                        @endphp
                        @foreach(array_slice($facList, 0, 4) as $fac)
                            <span class="fac-icon">{{ $facLabels[trim($fac)] ?? ucfirst(trim($fac)) }}</span>
                        @endforeach
                    </div>
                    @endif

                    <p class="card-desc">
                        {{ \Illuminate\Support\Str::limit(
                            $kendaraan->deskripsi ??
                            'Kendaraan nyaman dan siap digunakan untuk perjalanan wisata maupun kebutuhan transportasi lainnya.',
                            100
                        ) }}
                    </p>

                    <p class="cap-row">
                        👥 Kapasitas: <span>{{ $kendaraan->kapasitas }} Orang</span>
                    </p>

                    <div class="price-section">
                        <small>Mulai dari</small>
                        <h4>Rp {{ number_format($kendaraan->harga_sewa, 0, ',', '.') }}</h4>
                        <p>/hari</p>
                    </div>

                    <div class="card-action">
                        <a
                            href="{{ route('dashboard.user.detailkendaraan', $kendaraan->id_kendaraan) }}"
                            class="btn-detail">
                            Lihat Detail
                        </a>
                        <a
                            href="{{ $kendaraan->status_kendaraan !== 'tidak_tersedia'
                                ? route('dashboard.user.booking-kendaraan', $kendaraan->id_kendaraan)
                                : '#' }}"
                            class="btn-booking {{ $kendaraan->status_kendaraan === 'tidak_tersedia' ? 'disabled' : '' }}">
                            {{ $kendaraan->status_kendaraan === 'tidak_tersedia' ? 'Tidak Tersedia' : 'Pesan Sekarang' }}
                        </a>
                    </div>

                </div>

            </div>

            @empty

            <div class="empty-box">
                <p style="font-size:18px;margin-bottom:8px">🔍</p>
                <h3>Belum ada kendaraan tersedia</h3>
            </div>

            @endforelse

        </div>

        {{-- Empty search message (ditampilkan oleh JS) --}}
        <p id="emptySearchMessage" style="display:none;text-align:center;padding:20px;color:#888;">
            Kendaraan tidak ditemukan.
        </p>

        {{-- Pagination Laravel (opsional, jika pakai paginate()) --}}
        @if(method_exists($kendaraans, 'links'))
        <div class="pagination-wrap">
            {{ $kendaraans->links() }}
        </div>
        @endif

    </div>

</section>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Elements ──────────────────────────────────
    const searchInput    = document.getElementById('searchKendaraan');
    const kapasitasFilter = document.getElementById('kapasitasFilter');
    const priceRange     = document.getElementById('priceRange');
    const priceLabel     = document.getElementById('priceLabel');
    const sortSelect     = document.getElementById('sortSelect');
    const typeFilterEl   = document.getElementById('typeFilter');
    const facFilterEl    = document.getElementById('facFilter');
    const cards          = document.querySelectorAll('.kendaraan-card');
    const emptyMsg       = document.getElementById('emptySearchMessage');
    const resultCount    = document.getElementById('resultCount');

    let selectedType = '';
    let selectedFac  = [];

    // ── Price range label ─────────────────────────
    priceRange.addEventListener('input', function () {
        const val = parseInt(this.value);
        if (val >= parseInt(this.max)) {
            priceLabel.textContent = 'Maks';
        } else {
            priceLabel.textContent = val >= 1000000
                ? (val / 1000000).toFixed(1).replace('.0', '') + 'jt'
                : (val / 1000) + 'rb';
        }
        filter();
    });

    // ── Type chip ────────────────────────────────
    typeFilterEl.querySelectorAll('.chip').forEach(chip => {
        chip.addEventListener('click', function () {
            typeFilterEl.querySelectorAll('.chip').forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            selectedType = this.dataset.val;
            filter();
        });
    });

    // ── Facility chip (multi-select) ─────────────
    facFilterEl.querySelectorAll('.chip').forEach(chip => {
        chip.addEventListener('click', function () {
            this.classList.toggle('active');
            const val = this.dataset.val;
            if (selectedFac.includes(val)) {
                selectedFac = selectedFac.filter(v => v !== val);
            } else {
                selectedFac.push(val);
            }
            filter();
        });
    });

    // ── Other listeners ──────────────────────────
    searchInput.addEventListener('input', filter);
    kapasitasFilter.addEventListener('change', filter);
    sortSelect.addEventListener('change', function () {
        sortAndRender();
    });

    // ── Main filter function ──────────────────────
    function filter() {
        const keyword  = searchInput.value.toLowerCase().trim();
        const kap      = kapasitasFilter.value ? parseInt(kapasitasFilter.value) : 0;
        const maxPrice = parseInt(priceRange.value);

        let visible = 0;

        cards.forEach(card => {
            const nama       = card.dataset.nama || '';
            const jenis      = card.dataset.jenis || '';
            const kapCard    = parseInt(card.dataset.kapasitas) || 0;
            const hargaCard  = parseInt(card.dataset.harga) || 0;
            const facCard    = (card.dataset.fasilitas || '').split(',').map(f => f.trim());

            const matchKeyword  = !keyword || nama.includes(keyword) || jenis.includes(keyword);
            const matchJenis    = !selectedType || jenis === selectedType;
            const matchKap      = !kap || kapCard >= kap;
            const matchPrice    = hargaCard <= maxPrice;
            const matchFac      = selectedFac.length === 0 || selectedFac.every(f => facCard.includes(f));

            if (matchKeyword && matchJenis && matchKap && matchPrice && matchFac) {
                card.style.display = '';
                visible++;
            } else {
                card.style.display = 'none';
            }
        });

        emptyMsg.style.display   = visible === 0 ? 'block' : 'none';
        resultCount.textContent  = `Menampilkan ${visible} kendaraan`;

        sortAndRender();
    }

    // ── Sort visible cards ────────────────────────
    function sortAndRender() {
        const grid    = document.getElementById('katalogGrid');
        const sort    = sortSelect.value;
        const visible = [...cards].filter(c => c.style.display !== 'none');

        if (sort === 'harga-asc') {
            visible.sort((a, b) => parseInt(a.dataset.harga) - parseInt(b.dataset.harga));
        } else if (sort === 'harga-desc') {
            visible.sort((a, b) => parseInt(b.dataset.harga) - parseInt(a.dataset.harga));
        } else if (sort === 'kapasitas-desc') {
            visible.sort((a, b) => parseInt(b.dataset.kapasitas) - parseInt(a.dataset.kapasitas));
        }

        // Re-append in sorted order
        visible.forEach(card => grid.appendChild(card));
    }

});
</script>
@endpush