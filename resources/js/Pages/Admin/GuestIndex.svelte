<script>
    import { router } from "@inertiajs/svelte";
    import { onMount } from "svelte";

    // Sekarang guests adalah object pagination { data, links, total, dll }
    let { guests } = $props();
    let fileInput = $state(null);
    let isUploading = $state(false);
    let copyStatus = $state(null);

    // FIX: Paksa scroll hidup pas masuk halaman ini
    onMount(() => {
        document.documentElement.style.overflow = "auto";
        document.body.style.overflow = "auto";
        return () => {
            document.documentElement.style.overflow = "hidden";
            document.body.style.overflow = "hidden";
        };
    });

    function handleDeleteAll() {
        if (
            confirm(
                "⚠️ PERINGATAN KERAS: Semua data tamu bakal ilang dan ID balik ke 1. Yakin, Bre?",
            )
        ) {
            router.delete("/admin/guests-truncate");
        }
    }
    function handleImport() {
        if (!fileInput.files[0]) return alert("Pilih file dulu!");
        isUploading = true;
        const formData = new FormData();
        formData.append("file", fileInput.files[0]);
        router.post("/admin/guests/import", formData, {
            onSuccess: () => {
                isUploading = false;
                alert("Import Berhasil!");
            },
            onError: () => (isUploading = false),
        });
    }

    async function copyToClipboard(text, id) {
        if (navigator.clipboard && window.isSecureContext) {
            try {
                await navigator.clipboard.writeText(text);
                showSuccess(id);
                return;
            } catch (err) {}
        }
        const textArea = document.createElement("textarea");
        textArea.value = text;
        textArea.style.position = "fixed";
        textArea.style.left = "-9999px";
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        try {
            document.execCommand("copy");
            showSuccess(id);
        } catch (err) {
            alert("Gagal copy!");
        }
        document.body.removeChild(textArea);
    }

    function showSuccess(id) {
        copyStatus = id;
        setTimeout(() => (copyStatus = null), 2000);
    }

    function handleDelete(id, name) {
        if (!confirm(`Yakin mau hapus si ${name}?`)) return;
        router.delete(`/admin/guests/${id}`);
    }
</script>

<div class="admin-page">
    <div class="container">
        <header class="header">
            <div class="title-section">
                <h1>Guest Management</h1>
                <span class="count-tag">{guests.total} Tamu Terdaftar</span>
            </div>

            <div class="actions">
                <a
                    href="/admin/guests/print-labels"
                    target="_blank"
                    class="btn-print-all"
                >
                    PRINT SEMUA LABEL
                </a>
                <button class="btn-delete-all" onclick={handleDeleteAll}>
                    KOSONGKAN DATA
                </button>
                <div class="import-area">
                    <input
                        type="file"
                        bind:this={fileInput}
                        accept=".xlsx,.xls"
                        hidden
                        id="excel"
                    />
                    <label for="excel" class="label-file">
                        {fileInput?.files[0]?.name || "Pilih Excel..."}
                    </label>
                    <button class="btn-import" onclick={handleImport}>
                        {isUploading ? "..." : "IMPORT"}
                    </button>
                </div>
            </div>
        </header>

        <div class="card-table">
            <table class="table">
                <thead>
                    <tr>
                        <th width="80">QR</th>
                        <th>INFO TAMU</th>
                        <th>LINK</th>
                        <th class="text-right">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    {#each guests.data as guest}
                        <tr>
                            <td>
                                <div class="qr-wrap">
                                    {@html guest.qrcode}
                                </div>
                            </td>
                            <td>
                                <div class="guest-detail">
                                    <span class="name">{guest.name}</span>
                                    <span class="city">{guest.city || "-"}</span
                                    >
                                </div>
                            </td>
                            <td>
                                <code class="link-box">{guest.link}</code>
                            </td>
                            <td class="text-right">
                                <div class="btn-group">
                                    <button
                                        class="btn-sm"
                                        class:copied={copyStatus === guest.id}
                                        onclick={() =>
                                            copyToClipboard(
                                                guest.link,
                                                guest.id,
                                            )}
                                    >
                                        {copyStatus === guest.id
                                            ? "COPIED"
                                            : "COPY"}
                                    </button>
                                    <a
                                        href="/admin/guests/print-labels?id={guest.id}"
                                        target="_blank"
                                        class="btn-sm-outline"
                                    >
                                        PRINT
                                    </a>
                                    <button
                                        class="btn-sm btn-danger"
                                        onclick={() =>
                                            handleDelete(guest.id, guest.name)}
                                    >
                                        HAPUS
                                    </button>
                                </div>
                            </td>
                        </tr>
                    {/each}
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {#each guests.links as link}
                <button
                    class="page-btn"
                    class:active={link.active}
                    class:disabled={!link.url}
                    onclick={() => link.url && router.get(link.url)}
                >
                    {@html link.label}
                </button>
            {/each}
        </div>
    </div>
</div>

<style>
    .admin-page {
        background: #0f1115;
        min-height: 100vh;
        color: #e2e8f0;
        padding: 40px 20px;
        box-sizing: border-box;
    }
    .container {
        max-width: 1100px;
        margin: 0 auto;
    }
    .header {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-bottom: 30px;
        border-bottom: 1px solid #2d3748;
        padding-bottom: 20px;
    }
    h1 {
        font-family: serif;
        font-size: 2rem;
        margin: 0;
        color: #fff;
    }
    .count-tag {
        font-size: 11px;
        background: #2d3748;
        padding: 4px 10px;
        border-radius: 4px;
        color: #a0aec0;
    }
    .btn-print-all {
        background: #d4af37;
        color: #000;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 12px;
        text-decoration: none;
    }
    .import-area {
        background: #1a202c;
        padding: 4px;
        border-radius: 8px;
        display: flex;
        align-items: center;
    }
    .label-file {
        font-size: 11px;
        padding: 0 15px;
        color: #718096;
        max-width: 150px;
        overflow: hidden;
        cursor: pointer;
    }
    .btn-import {
        background: #fff;
        border: none;
        padding: 6px 15px;
        border-radius: 6px;
        font-weight: 700;
        cursor: pointer;
    }

    .card-table {
        background: #1a202c;
        border-radius: 12px;
        border: 1px solid #2d3748;
        overflow: hidden;
    }
    .table {
        width: 100%;
        border-collapse: collapse;
    }
    th {
        background: #2d3748;
        padding: 15px 20px;
        text-align: left;
        font-size: 11px;
        color: #a0aec0;
    }
    td {
        padding: 15px 20px;
        border-bottom: 1px solid #2d3748;
    }

    .qr-wrap {
        background: white;
        padding: 5px;
        border-radius: 6px;
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .qr-wrap :global(svg) {
        width: 100% !important;
        height: 100% !important;
    }

    .guest-detail {
        display: flex;
        flex-direction: column;
    }
    .name {
        font-weight: 600;
        color: #fff;
        font-size: 14px;
    }
    .city {
        font-size: 11px;
        color: #718096;
    }
    .link-box {
        background: rgba(212, 175, 55, 0.1);
        color: #d4af37;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 10px;
    }

    .btn-group {
        display: flex;
        gap: 6px;
        justify-content: flex-end;
    }
    .btn-sm {
        background: #2d3748;
        color: #fff;
        border: none;
        padding: 6px 12px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 10px;
        font-weight: 600;
    }
    .btn-sm.copied {
        background: #38a169;
    }
    .btn-danger {
        background: #450a0a;
        color: #f87171;
    }
    .btn-sm-outline {
        border: 1px solid #2d3748;
        color: #718096;
        text-decoration: none;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 10px;
    }

    .pagination {
        display: flex;
        justify-content: center;
        gap: 5px;
        margin-top: 30px;
    }
    .page-btn {
        background: #1a202c;
        border: 1px solid #2d3748;
        color: #a0aec0;
        padding: 8px 15px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 12px;
    }
    .page-btn.active {
        background: #d4af37;
        color: #000;
        border-color: #d4af37;
        font-weight: bold;
    }
    .page-btn.disabled {
        opacity: 0.3;
        cursor: not-allowed;
    }

    .text-right {
        text-align: right;
    }
    .btn-delete-all {
        background: #450a0a;
        color: #f87171;
        border: 1px solid #7f1d1d;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 12px;
        cursor: pointer;
        transition: 0.3s;
    }

    .btn-delete-all:hover {
        background: #7f1d1d;
        color: #fff;
    }
    .actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap; /* Biar gak hancur di layar kecil */
    }

    /* Grouping Buttons */
    .main-buttons {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    /* Style Dasar Tombol & Link (Standardized) */
    .btn-print-all,
    .btn-delete-all,
    .btn-import-submit,
    .file-picker {
        height: 40px;
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 0 16px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.05em;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        box-sizing: border-box;
        border: none;
    }

    /* 1. PRINT ALL (Gold Style) */
    .btn-print-all {
        background: #d4af37;
        color: #000;
    }
    .btn-print-all:hover {
        background: #fff;
        transform: translateY(-2px);
    }

    /* 2. DELETE ALL (Dark Red Style) */
    .btn-delete-all {
        background: #1a1a1a;
        color: #ef4444;
        border: 1px solid #450a0a;
    }
    .btn-delete-all:hover {
        background: #450a0a;
        color: #fff;
    }

    /* 3. IMPORT GROUP (Input + Button menyatu) */
    .import-group {
        display: flex;
        background: #18181b;
        border: 1px solid #27272a;
        border-radius: 10px;
        padding: 4px;
        align-items: center;
    }

    .file-picker {
        background: transparent;
        color: #71717a;
        padding-right: 20px;
        max-width: 180px;
    }
    .file-name {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .btn-import-submit {
        background: #fff;
        color: #000;
        height: 32px; /* Lebih kecil dikit biar fit di dalem border */
        padding: 0 15px;
    }
    .btn-import-submit:hover:not(:disabled) {
        background: #d4af37;
    }
    .btn-import-submit:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Responsif buat HP */
    @media (max-width: 768px) {
        .actions {
            flex-direction: column;
            align-items: stretch;
        }
        .main-buttons {
            flex-direction: column;
        }
        .import-group {
            width: 100%;
            justify-content: space-between;
        }
        .file-picker {
            max-width: none;
            flex: 1;
        }
    }
</style>
