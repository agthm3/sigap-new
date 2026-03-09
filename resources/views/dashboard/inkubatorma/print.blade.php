<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan SIGAP Inkubatorma</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

  <style>
    :root {
      --crimson:    #7a2222;
      --crimson-dk: #5c1a1a;
      --crimson-lt: #f5eaea;
      --ink:        #1f2937;
      --ink-2:      #374151;
      --slate:      #6b7280;
      --mist:       #f9fafb;
      --border:     #d1d5db;
      --white:      #ffffff;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: Arial, Helvetica, sans-serif;
      color: var(--ink);
      background: var(--white);
      font-size: 12px;
      line-height: 1.6;
    }

    /* ── PAGE WRAPPER ── */
    .page {
      max-width: 960px;
      margin: 0 auto;
      padding: 48px 48px 64px;
    }

    /* ── PRINT BUTTON ── */
    .no-print {
      margin-bottom: 28px;
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .btn-print {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 10px 22px;
      background: var(--crimson);
      color: var(--white);
      font-family: 'DM Sans', sans-serif;
      font-size: 12px;
      font-weight: 600;
      letter-spacing: 0.04em;
      text-transform: uppercase;
      border: none;
      cursor: pointer;
      border-radius: 4px;
      transition: background 0.2s;
    }
    .btn-print:hover { background: var(--crimson-dk); }
    .btn-print svg { flex-shrink: 0; }

    /* ── HEADER ── */
    .report-header {
      position: relative;
      border-top: 4px solid var(--crimson);
      padding-top: 28px;
      margin-bottom: 36px;
    }

    .header-kicker {
      font-size: 10px;
      letter-spacing: 0.16em;
      text-transform: uppercase;
      color: var(--crimson);
      margin-bottom: 10px;
    }

    .header-title {
      font-size: 22px;
      font-weight: 700;
      color: var(--ink);
      line-height: 1.15;
      max-width: 640px;
    }

    .header-subtitle {
      margin-top: 6px;
      font-size: 13px;
      color: var(--slate);
      font-weight: 500;
    }

    .header-meta {
      display: flex;
      gap: 32px;
      margin-top: 20px;
      padding-top: 18px;
      border-top: 1px solid var(--border);
    }

    .meta-item { }
    .meta-label {
      font-size: 10px;
      text-transform: uppercase;
      letter-spacing: 0.12em;
      color: var(--slate);
      font-weight: 600;
    }
    .meta-value {
      font-size: 12px;
      color: var(--ink-2);
      font-weight: 500;
      margin-top: 2px;
    }

    .header-logo-area {
      position: absolute;
      top: 0;
      right: 0;
      padding-top: 28px;
      text-align: right;
    }

    .header-logo-text {
      font-size: 22px;
      font-weight: 700;
      color: var(--crimson);
      letter-spacing: -0.01em;
    }
    .header-logo-sub {
      font-size: 10px;
      color: var(--slate);
      letter-spacing: 0.1em;
      text-transform: uppercase;
      margin-top: 2px;
    }

    /* ── DIVIDER ── */
    .divider {
      height: 1px;
      background: var(--border);
      margin: 32px 0;
    }

    /* ── SECTION ── */
    .section { margin-bottom: 36px; }

    .section-header {
      display: flex;
      align-items: baseline;
      gap: 12px;
      margin-bottom: 16px;
    }

    .section-tag {
      font-size: 9px;
      font-weight: 500;
      letter-spacing: 0.14em;
      text-transform: uppercase;
      color: var(--white);
      background: var(--crimson);
      padding: 3px 8px;
      border-radius: 2px;
      flex-shrink: 0;
    }

    .section-title {
      font-size: 14px;
      font-weight: 700;
      color: var(--ink);
    }

    /* ── STAT CARDS ── */
    .stat-row {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 16px;
    }

    .stat-card {
      border: 1px solid var(--border);
      border-top: 3px solid var(--crimson);
      padding: 20px 22px 18px;
      background: var(--white);
      position: relative;
      overflow: hidden;
    }

    .stat-card::after {
      content: '';
      position: absolute;
      bottom: 0; right: 0;
      width: 60px; height: 60px;
      background: var(--crimson-lt);
      border-radius: 60px 0 0 0;
      opacity: 0.5;
    }

    .stat-label {
      font-size: 10px;
      text-transform: uppercase;
      letter-spacing: 0.14em;
      font-weight: 600;
      color: var(--slate);
    }

    .stat-value {
      font-size: 40px;
      font-weight: 700;
      color: var(--crimson);
      line-height: 1;
      margin-top: 8px;
    }

    .stat-hint {
      font-size: 10px;
      color: var(--slate);
      margin-top: 6px;
    }

    /* ── CHART LAYOUT ── */
    .chart-duo {
      display: grid;
      grid-template-columns: 3fr 2fr;
      gap: 16px;
      margin-bottom: 16px;
    }

    .chart-card {
      border: 1px solid var(--border);
      padding: 18px 20px;
      background: var(--white);
    }

    .chart-label {
      font-size: 10px;
      letter-spacing: 0.1em;
      text-transform: uppercase;
      color: var(--slate);
      margin-bottom: 4px;
    }

    .chart-title {
      font-size: 13px;
      font-weight: 600;
      color: var(--ink-2);
      margin-bottom: 4px;
    }

    .chart-note {
      font-size: 10px;
      color: var(--slate);
      margin-bottom: 14px;
    }

    .chart-box-lg  { height: 240px; }
    .chart-box-md  { height: 240px; }
    .chart-box-full { height: 260px; }

    canvas { width: 100% !important; height: 100% !important; display: block; }

    /* ── TABLES ── */
    .data-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 11.5px;
    }

    .data-table thead tr {
      background: var(--ink);
      color: var(--white);
    }

    .data-table th {
      padding: 10px 12px;
      text-align: left;
      font-weight: 600;
      font-size: 10px;
      letter-spacing: 0.08em;
      text-transform: uppercase;
    }

    .data-table td {
      padding: 9px 12px;
      border-bottom: 1px solid var(--border);
      vertical-align: top;
      color: var(--ink-2);
    }

    .data-table tbody tr:nth-child(even) td {
      background: var(--mist);
    }

    .data-table tbody tr:last-child td {
      border-bottom: 2px solid var(--ink);
    }

    .data-table .muted { color: var(--slate); font-style: italic; }

    /* status badges */
    .badge {
      display: inline-block;
      padding: 2px 8px;
      border-radius: 2px;
      font-size: 10px;
      font-weight: 600;
      letter-spacing: 0.04em;
    }
    .badge-menunggu  { background: #fef3c7; color: #92400e; }
    .badge-terjadwal { background: #dbeafe; color: #1e40af; }
    .badge-selesai   { background: #d1fae5; color: #065f46; }

    /* ── NUMBERED INDEX ── */
    .row-num {
      font-size: 10px;
      color: var(--slate);
    }

    /* ── FOOTER ── */
    .report-footer {
      margin-top: 48px;
      padding-top: 16px;
      border-top: 1px solid var(--border);
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
    }

    .footer-left {
      font-size: 10px;
      color: var(--slate);
      line-height: 1.8;
    }

    .footer-right {
      text-align: right;
      font-size: 10px;
      color: var(--slate);
    }

    .footer-brand {
      font-size: 14px;
      font-weight: 700;
      color: var(--crimson);
    }

    /* hide fixed footer on screen */
    .print-footer { display: none; }

    /* ── PRINT ── */
    @media print {
      @page {
        size: A4 portrait;
        margin: 14mm 14mm 24mm;
      }

      body { font-size: 11px; }

      .no-print { display: none !important; }
      .page { padding: 0; max-width: 100%; }

      .section,
      .chart-card,
      .stat-card {
        break-inside: avoid;
        page-break-inside: avoid;
      }

      .chart-duo { grid-template-columns: 3fr 2fr; }
      .chart-box-lg, .chart-box-md { height: 200px; }
      .chart-box-full { height: 220px; }

      .data-table { page-break-inside: auto; }
      .data-table tr { page-break-inside: avoid; page-break-after: auto; }
      .data-table thead { display: table-header-group; }

      .header-title { font-size: 20px; }
      .stat-value { font-size: 32px; }

      /* Hide in-page footer, show fixed one */
      .report-footer { display: none !important; }

      .print-footer {
        display: flex !important;
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 8px 0;
        border-top: 1px solid #d1d5db;
        background: #fff;
        justify-content: space-between;
        align-items: flex-end;
        font-size: 10px;
        color: #6b7280;
        line-height: 1.8;
      }

      .print-footer .pf-brand {
        font-size: 13px;
        font-weight: 700;
        color: #7a2222;
      }

      .print-footer .pf-right {
        text-align: right;
      }
    }
  </style>
</head>
<body>
  @php
    $lineLabels = $line['labels'] ?? [];
    $lineValues = $line['values'] ?? [];

    $pieLabels = collect($pieLayanan ?? [])->pluck('label')->values()->all();
    $pieValues = collect($pieLayanan ?? [])->pluck('total')->map(fn($v)=>(int)$v)->values()->all();

    $opdLabels = collect($opdCounts ?? [])->pluck('label')->values()->all();
    $opdValues = collect($opdCounts ?? [])->pluck('total')->map(fn($v)=>(int)$v)->values()->all();

    $countMenunggu  = (int) ($ringkasanStatus['Menunggu'] ?? 0);
    $countTerjadwal = (int) ($ringkasanStatus['Terjadwal'] ?? 0);
    $countSelesai   = (int) ($ringkasanStatus['Selesai'] ?? 0);
  @endphp

  <div class="page">

    <!-- PRINT BUTTON -->
    <div class="no-print">
      <button class="btn-print" onclick="window.print()">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <path d="M6 9V2h12v7"/><rect x="6" y="17" width="12" height="5" rx="1"/>
          <path d="M6 13H4a2 2 0 0 0-2 2v4h4v-4h12v4h4v-4a2 2 0 0 0-2-2h-2"/>
          <circle cx="18" cy="13" r="1" fill="currentColor"/>
        </svg>
        Cetak Laporan
      </button>
    </div>

    <!-- HEADER -->
    <header class="report-header">
      <div class="header-logo-area">
        <div class="header-logo-text">SIGAP</div>
        <div class="header-logo-sub">BRIDA Kota Makassar</div>
      </div>

      <div class="header-kicker">Laporan Resmi &nbsp;/&nbsp; Inkubatorma</div>
      <h1 class="header-title">Dashboard SIGAP Inkubatorma</h1>
      <div class="header-subtitle">Badan Riset dan Inovasi Daerah Kota Makassar</div>

      <div class="header-meta">
        <div class="meta-item">
          <div class="meta-label">Periode</div>
          <div class="meta-value">{{ $periodeLabel }}</div>
        </div>
        <div class="meta-item">
          <div class="meta-label">Dicetak Pada</div>
          <div class="meta-value">{{ $printedAt }}</div>
        </div>
        <div class="meta-item">
          <div class="meta-label">Total Pengajuan</div>
          <div class="meta-value">{{ $rows->count() }} pengajuan</div>
        </div>
      </div>
    </header>

    <!-- STAT CARDS -->
    <section class="section">
      <div class="section-header">
        <span class="section-tag">01</span>
        <span class="section-title">Ringkasan Status Pengajuan</span>
      </div>

      <div class="stat-row">
        <div class="stat-card">
          <div class="stat-label">Menunggu</div>
          <div class="stat-value">{{ $countMenunggu }}</div>
          <div class="stat-hint">Belum dijadwalkan</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Terjadwal</div>
          <div class="stat-value">{{ $countTerjadwal }}</div>
          <div class="stat-hint">Sudah ada jadwal konsultasi</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Selesai</div>
          <div class="stat-value">{{ $countSelesai }}</div>
          <div class="stat-hint">Konsultasi telah diselesaikan</div>
        </div>
      </div>
    </section>

    <!-- CHARTS -->
    <section class="section">
      <div class="section-header">
        <span class="section-tag">02</span>
        <span class="section-title">Visualisasi Data</span>
      </div>

      <div class="chart-duo">
        <div class="chart-card">
          <div class="chart-label">Tren Waktu</div>
          <div class="chart-title">Grafik Jumlah Pengajuan</div>
          <div class="chart-note">
            @if(($period ?? 'overall') === 'monthly')
              Distribusi jumlah pengajuan per hari pada bulan terpilih.
            @elseif(($period ?? 'overall') === 'yearly')
              Distribusi jumlah pengajuan per bulan pada tahun terpilih.
            @else
              Tren jumlah pengajuan sepanjang waktu.
            @endif
          </div>
          <div class="chart-box-lg">
            <canvas id="chartSubmissions"></canvas>
          </div>
        </div>

        <div class="chart-card">
          <div class="chart-label">Komposisi</div>
          <div class="chart-title">Persebaran Layanan</div>
          <div class="chart-note">Proporsi tiap jenis layanan yang diajukan.</div>
          <div class="chart-box-md">
            <canvas id="chartLayanan"></canvas>
          </div>
        </div>
      </div>

      <div class="chart-card">
        <div class="chart-label">Distribusi</div>
        <div class="chart-title">Persebaran OPD Pengaju</div>
        <div class="chart-note">Jumlah pengajuan berdasarkan OPD / unit yang mengajukan.</div>
        <div class="chart-box-full">
          <canvas id="chartOpd"></canvas>
        </div>
      </div>
    </section>

    <!-- LAYANAN TABLE -->
    <section class="section">
      <div class="section-header">
        <span class="section-tag">03</span>
        <span class="section-title">Persebaran Layanan</span>
      </div>

      <table class="data-table">
        <thead>
          <tr>
            <th style="width: 75%;">Layanan</th>
            <th style="width: 25%;">Jumlah Pengajuan</th>
          </tr>
        </thead>
        <tbody>
          @forelse($layananCounts as $item)
            <tr>
              <td>{{ $item['label'] }}</td>
              <td><strong>{{ $item['total'] }}</strong></td>
            </tr>
          @empty
            <tr><td colspan="2" class="muted">Belum ada data.</td></tr>
          @endforelse
        </tbody>
      </table>
    </section>

    <!-- OPD TABLE -->
    <section class="section">
      <div class="section-header">
        <span class="section-tag">04</span>
        <span class="section-title">Persebaran OPD Pengaju</span>
      </div>

      <table class="data-table">
        <thead>
          <tr>
            <th style="width: 75%;">OPD / Unit</th>
            <th style="width: 25%;">Jumlah Pengajuan</th>
          </tr>
        </thead>
        <tbody>
          @forelse($opdCounts as $item)
            <tr>
              <td>{{ $item['label'] }}</td>
              <td><strong>{{ $item['total'] }}</strong></td>
            </tr>
          @empty
            <tr><td colspan="2" class="muted">Belum ada data.</td></tr>
          @endforelse
        </tbody>
      </table>
    </section>

    <!-- SUBMISSIONS TABLE -->
    <section class="section">
      <div class="section-header">
        <span class="section-tag">05</span>
        <span class="section-title">Daftar Pengajuan</span>
      </div>

      <table class="data-table">
        <thead>
          <tr>
            <th style="width: 4%;">No</th>
            <th style="width: 20%;">Judul Konsultasi</th>
            <th style="width: 14%;">Pengaju</th>
            <th style="width: 15%;">OPD / Unit</th>
            <th style="width: 19%;">Layanan</th>
            <th style="width: 10%;">Status</th>
            <th style="width: 18%;">Tanggal Diajukan</th>
          </tr>
        </thead>
        <tbody>
          @forelse($rows as $i => $row)
            @php
              $layananKey   = (string) ($row->layanan_id ?? '');
              $layananLabel = $layananOptions[$layananKey] ?? '—';
              if ($layananKey === 'lainnya' && !empty($row->layanan_lainnya)) {
                $layananLabel = ($layananOptions['lainnya'] ?? 'Lainnya') . ' · ' . $row->layanan_lainnya;
              }
              $statusClass = match(strtolower($row->status ?? '')) {
                'menunggu'  => 'badge-menunggu',
                'terjadwal' => 'badge-terjadwal',
                'selesai'   => 'badge-selesai',
                default     => ''
              };
            @endphp
            <tr>
              <td class="row-num">{{ $i + 1 }}</td>
              <td>{{ $row->judul_konsultasi ?? '—' }}</td>
              <td>{{ $row->nama_pengaju ?? '—' }}</td>
              <td>{{ $row->opd_unit ?? '—' }}</td>
              <td>{{ $layananLabel }}</td>
              <td>
                <span class="badge {{ $statusClass }}">{{ $row->status ?? '—' }}</span>
              </td>
              <td style="font-size:10px; color:var(--slate)">
                {{ $row->created_at ? \Carbon\Carbon::parse($row->created_at)->timezone('Asia/Makassar')->translatedFormat('d M Y H:i') . ' WITA' : '—' }}
              </td>
            </tr>
          @empty
            <tr><td colspan="7" class="muted">Belum ada data pengajuan.</td></tr>
          @endforelse
        </tbody>
      </table>
    </section>

    <!-- FOOTER -->
    <footer class="report-footer">
      <div class="footer-left">
        <div>Dokumen ini dicetak secara otomatis dari Sistem SIGAP BRIDA.</div>
        <div>Dicetak pada: {{ $printedAt }}</div>
        <div>Periode: {{ $periodeLabel }}</div>
      </div>
      <div class="footer-right">
        <div class="footer-brand">SIGAP</div>
        <div>Badan Riset dan Inovasi Daerah</div>
        <div>Kota Makassar</div>
      </div>
    </footer>

  </div><!-- end .page -->

  <!-- FIXED FOOTER (visible only when printing) -->
  <div class="print-footer">
    <div class="pf-left">
      <div>Dokumen ini dicetak secara otomatis dari Sistem SIGAP BRIDA.</div>
      <div>Dicetak pada: {{ $printedAt }}</div>
      <div>Periode: {{ $periodeLabel }}</div>
    </div>
    <div class="pf-right">
      <div class="pf-brand">SIGAP</div>
      <div>Badan Riset dan Inovasi Daerah</div>
      <div>Kota Makassar</div>
    </div>
  </div>

  <script>
    (function () {
      const lineLabels = @json($lineLabels);
      const lineValues = @json($lineValues);
      const pieLabels  = @json($pieLabels);
      const pieValues  = @json($pieValues);
      const opdLabels  = @json($opdLabels);
      const opdValues  = @json($opdValues);

      const palette = [
        '#7a2222','#a14a4a','#d08a8a','#b8860b','#6b7280',
        '#9ca3af','#0f766e','#1d4ed8','#7c3aed','#059669'
      ];

      // — Line Chart —
      const lineCtx = document.getElementById('chartSubmissions');
      if (lineCtx) {
        new Chart(lineCtx, {
          type: 'line',
          data: {
            labels: lineLabels,
            datasets: [{
              label: 'Pengajuan',
              data: lineValues,
              tension: 0.4,
              fill: true,
              borderColor: '#7a2222',
              borderWidth: 2,
              backgroundColor: 'rgba(122,34,34,0.08)',
              pointRadius: 3.5,
              pointHoverRadius: 5,
              pointBackgroundColor: '#ffffff',
              pointBorderColor: '#7a2222',
              pointBorderWidth: 2
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: false,
            plugins: { legend: { display: false } },
            scales: {
              x: {
                grid: { color: 'rgba(0,0,0,0.05)' },
                ticks: { color: '#5c5a5a', font: { size: 10 } }
              },
              y: {
                beginAtZero: true,
                grid: { color: 'rgba(0,0,0,0.06)' },
                ticks: { color: '#5c5a5a', precision: 0, font: { size: 10 } }
              }
            }
          }
        });
      }

      // — Doughnut Chart —
      const pieCtx = document.getElementById('chartLayanan');
      if (pieCtx && pieLabels.length) {
        new Chart(pieCtx, {
          type: 'doughnut',
          data: {
            labels: pieLabels,
            datasets: [{
              data: pieValues,
              backgroundColor: pieValues.map((_, i) => palette[i % palette.length]),
              borderWidth: 2,
              borderColor: '#ffffff',
              hoverOffset: 6
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: false,
            cutout: '62%',
            plugins: {
              legend: {
                position: 'bottom',
                labels: {
                  boxWidth: 10,
                  boxHeight: 10,
                  padding: 10,
                  color: '#2d2b2b',
                  font: { size: 10 }
                }
              }
            }
          }
        });
      }

      // — Bar Chart —
      const opdCtx = document.getElementById('chartOpd');
      if (opdCtx && opdLabels.length) {
        new Chart(opdCtx, {
          type: 'bar',
          data: {
            labels: opdLabels,
            datasets: [{
              label: 'Jumlah Pengajuan',
              data: opdValues,
              backgroundColor: opdValues.map((_, i) =>
                i === 0 ? 'rgba(122,34,34,0.85)' : 'rgba(122,34,34,0.2)'
              ),
              borderColor: '#7a2222',
              borderWidth: 1,
              borderRadius: 4,
              maxBarThickness: 40
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: false,
            plugins: { legend: { display: false } },
            scales: {
              x: {
                grid: { display: false },
                ticks: {
                  color: '#5c5a5a',
                  font: { size: 10 },
                  callback: function(value) {
                    const label = this.getLabelForValue(value) || '';
                    return label.length > 16 ? label.slice(0, 16) + '…' : label;
                  }
                }
              },
              y: {
                beginAtZero: true,
                grid: { color: 'rgba(0,0,0,0.06)' },
                ticks: { color: '#5c5a5a', precision: 0, font: { size: 10 } }
              }
            }
          }
        });
      }
    })();
  </script>
</body>
</html>