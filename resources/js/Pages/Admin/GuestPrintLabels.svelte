<script>
    let { guests } = $props();

    // Fungsi untuk trigger dialog print browser
    const printNow = () => window.print();
</script>

<div class="screen-only-header no-print">
    <div class="header-content">
        <h1>Cetak Label Undangan (T&J 103)</h1>
        <p>
            Total: <strong>{guests.length}</strong> Sticker | Layout: 3 Kolom x 4
            Baris (12 per lembar)
        </p>
    </div>
    <div class="actions">
        <button onclick={printNow} class="print-btn">
            <svg
                class="w-5 h-5"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"
                />
            </svg>
            CETAK SEKARANG
        </button>
    </div>
</div>

<div class="printable-area">
    <div class="sticker-grid">
        {#each guests as guest}
            <div class="sticker-unit">
                <div class="qr-container">
                    {@html guest.qrcode}
                </div>
                <div class="info">
                    <p class="guest-name">{guest.name}</p>
                    <div class="divider"></div>
                    <p class="event-label">The Wedding of Maul & Kahfi</p>
                </div>
            </div>
        {/each}
    </div>
</div>

<style>
    /* =========================================
       GAYA TAMPILAN LAYAR (PREVIEW)
    ========================================= */
    :global(body) {
        background: #e2e8f0;
        margin: 0;
        font-family: "Inter", sans-serif;
    }

    .screen-only-header {
        position: sticky;
        top: 0;
        z-index: 100;
        background: #18181b;
        color: white;
        padding: 1rem 5%;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
    .header-content h1 {
        font-size: 1.2rem;
        margin: 0;
        color: #d4af37;
    }
    .header-content p {
        font-size: 0.8rem;
        margin: 0;
        opacity: 0.7;
    }

    .print-btn {
        background: #d4af37;
        color: #000;
        padding: 0.6rem 1.2rem;
        border-radius: 6px;
        font-weight: 800;
        border: none;
        cursor: pointer;
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    /* Kertas A4 di Layar */
    .printable-area {
        background: white;
        width: 210mm;
        padding: 2mm; /* Margin luar kertas */
        margin: 2rem auto;
        min-height: 297mm;
        box-shadow: 0 0 50px rgba(0, 0, 0, 0.2);
    }

    /* Grid Layout */
    .sticker-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr); /* 3 Kolom T&J 103 */
        column-gap: 2mm;
        row-gap: 0;
    }

    .sticker-unit {
        width: 64mm; /* Lebar standar T&J 103 */
        height: 32mm; /* Tinggi standar T&J 103 */
        border: 1px dashed #eee; /* Garis bantu potong (layar saja) */
        display: flex;
        align-items: center;
        padding: 0 10mm;
        box-sizing: border-box;
        overflow: hidden;
    }

    /* =========================================
       GAYA KHUSUS CETAK (PRINT)
    ========================================= */
    @media print {
        @page {
            size: A4 portrait;
            margin: 5mm; /* Sesuaikan dengan margin printer masing-masing */
        }

        .no-print,
        .screen-only-header {
            display: none !important;
        }

        :global(body) {
            background: white;
            margin: 0;
        }

        .printable-area {
            width: 100%;
            margin: 0;
            padding: 0;
            box-shadow: none;
        }

        .sticker-unit {
            border: none; /* Hilangkan garis pas diprint */
            page-break-inside: avoid;
        }

        /* QR Adjustments for Print */
        .qr-container :global(svg) {
            width: 22mm !important;
            height: 22mm !important;
        }
    }

    /* STYLING KONTEN */
    .qr-container {
        flex-shrink: 0;
        margin-right: 3mm;
    }
    .qr-container :global(svg) {
        width: 18mm;
        height: 18mm;
        display: block;
    }

    .info {
        text-align: left;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .guest-name {
        font-weight: 800;
        font-size: 11px;
        margin: 0;
        color: #000;
        text-transform: uppercase;
        line-height: 1.2;
    }

    .divider {
        width: 20px;
        height: 1px;
        background: #000;
        margin: 3px 0;
    }

    .event-label {
        font-size: 7px;
        color: #666;
        margin: 0;
        letter-spacing: 0.5px;
    }
</style>
