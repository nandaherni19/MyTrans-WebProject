@extends('layouts.admin')
@section('title', 'Kelola Laporan Transaksi')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/kelola-laporan-transaksi.css') }}">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
@endpush

@section('content')
        <div class="content-header">
            <div>
            <h1>Laporan Transaksi</h1>
            </div>
            <div class="header-actions"> 
            <button type="button" class="btn-primary">Kembali</button>
        </div>
        </div>
        
        <div class="main-scroll">
    <section class="laporan-wrapper">

        <div class="periode-box">
            <h3 class="section-mini-title">🗓 Periode</h3>

            <div class="periode-row">
                <div class="periode-group">
                    <label>Dari Tanggal</label>
                    <input type="text" value="17/03/2026">
                </div>

                <div class="periode-group">
                    <label>Sampai Tanggal</label>
                    <input type="text" value="18/03/2026">
                </div>

                <div class="periode-action">
                    <button type="button" class="btn-export" onclick="exportCSV()">⬇ Export CSV</button>
                </div>
            </div>
        </div>

        <div class="summary-grid">
            <div class="summary-card summary-blue">
                <p class="summary-icon">💰</p>
                <p class="summary-label">Total Pendapatan</p>
                <h3>Rp 6.600.000</h3>
                <span>5 transaksi selesai</span>
            </div>

            <div class="summary-card summary-green">
                <p class="summary-icon">💰</p>
                <p class="summary-label">Menunggu / Belum Selesai</p>
                <h3>Rp 6.600.000</h3>
                <span>5 transaksi selesai</span>
            </div>

            <div class="summary-card summary-orange">
                <p class="summary-icon">💰</p>
                <p class="summary-label">Total DP Diterima</p>
                <h3>Rp 6.600.000</h3>
                <span>5 transaksi selesai</span>
            </div>
        </div>

        <div class="riwayat-box">
            <h3 class="riwayat-title">Riwayat Transaksi</h3>

            <div class="table-wrapper">
                <table class="laporan-table">
                    <thead>
                        <tr>
                            <th>ID Booking</th>
                            <th>Pelanggan</th>
                            <th>Paket</th>
                            <th>Tanggal</th>
                            <th>Total Harga</th>
                            <th>Dibayar</th>
                            <th>Status</th>
                            <th>Metode</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>BK001</td>
                            <td>Asya Farasya</td>
                            <td>Wisata Pantai Watu Karung</td>
                            <td>17/03/2026</td>
                            <td>Rp 1.500.000</td>
                            <td>Rp 1.500.000</td>
                            <td>Lunas</td>
                            <td>QRIS</td>
                        </tr>
                        <tr>
                            <td>BK002</td>
                            <td>Asya Farasya</td>
                            <td>Wisata Gunung Bromo</td>
                            <td>20/03/2026</td>
                            <td>Rp 3.000.000</td>
                            <td>Rp 1.500.000</td>
                            <td>DP</td>
                            <td>QRIS</td>
                        </tr>
                        <tr>
                            <td>BK003</td>
                            <td>Asya Farasya</td>
                            <td>Wisata Jatim Park II</td>
                            <td>25/03/2026</td>
                            <td>Rp 3.500.000</td>
                            <td>Rp 3.500.000</td>
                            <td>Lunas</td>
                            <td>QRIS</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </section>
</div>


@endsection
@push('scripts')
<script>
// ===============================
// AUTO FORMAT TANGGAL (optional)
// ===============================
document.addEventListener('DOMContentLoaded', function () {
    console.log('Laporan Transaksi Loaded');

    // contoh: kasih efek focus input
    const inputs = document.querySelectorAll('.periode-group input');

    inputs.forEach(input => {
        input.addEventListener('focus', function () {
            this.style.border = '2px solid #2563eb';
        });

        input.addEventListener('blur', function () {
            this.style.border = '1px solid #ddd';
        });
    });
});


// ===============================
// EXPORT CSV (dummy dulu)
// ===============================
function exportCSV() {
    alert("Export CSV belum terhubung database 😄");
}


// ===============================
// FILTER PERIODE (dummy)
// ===============================
function filterPeriode() {
    const dari = document.querySelectorAll('.periode-group input')[0].value;
    const sampai = document.querySelectorAll('.periode-group input')[1].value;

    console.log("Filter dari:", dari);
    console.log("Filter sampai:", sampai);

    alert("Filter periode belum aktif (nanti sambung database)");
}
</script>
@endpush