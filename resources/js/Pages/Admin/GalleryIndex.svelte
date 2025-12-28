<script>
    import { router } from "@inertiajs/svelte";
    import { fade, scale } from "svelte/transition";
    import { onMount } from "svelte";

    // Props dari Laravel GalleryController
    let { images } = $props();

    let files = $state([]);
    let previews = $state([]);
    let isUploading = $state(false);

    // Pastikan scroll aktif di admin
    onMount(() => {
        document.documentElement.style.overflow = "auto";
        document.body.style.overflow = "auto";
    });

    // Handle pilih file & bikin preview sementara
    function handleFileSelect(e) {
        const selectedFiles = Array.from(e.target.files);
        files = [...files, ...selectedFiles];

        selectedFiles.forEach((file) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                previews = [...previews, e.target.result];
            };
            reader.readAsDataURL(file);
        });
    }

    // Upload ke Laravel
    function uploadImages() {
        if (files.length === 0) return alert("Pilih foto dulu, Bre!");

        isUploading = true;
        const formData = new FormData();
        files.forEach((file, i) => {
            formData.append(`images[${i}]`, file);
        });

        router.post("/admin/gallery", formData, {
            onSuccess: () => {
                isUploading = false;
                files = [];
                previews = [];
                alert("Foto-foto cakep berhasil di-upload!");
            },
            onError: () => {
                isUploading = false;
                alert("Gagal upload, cek ukuran file (max 2MB per foto).");
            },
        });
    }

    function removePreview(index) {
        files = files.filter((_, i) => i !== index);
        previews = previews.filter((_, i) => i !== index);
    }

    function handleDelete(id) {
        if (!confirm("Hapus foto ini dari gallery?")) return;
        router.delete(`/admin/gallery/${id}`);
    }
</script>

<div class="admin-page">
    <div class="container">
        <header class="header">
            <div class="title-section">
                <h1>Photo Gallery</h1>
                <span class="count-tag">{images.length} Foto Tersimpan</span>
            </div>

            <div class="actions">
                <input
                    type="file"
                    id="photo-upload"
                    multiple
                    accept="image/*"
                    hidden
                    onchange={handleFileSelect}
                />
                <label for="photo-upload" class="btn-select">
                    + PILIH FOTO
                </label>

                {#if files.length > 0}
                    <button
                        class="btn-upload"
                        onclick={uploadImages}
                        disabled={isUploading}
                        in:scale
                    >
                        {isUploading
                            ? "UPLOADING..."
                            : `UPLOAD ${files.length} FOTO`}
                    </button>
                {/if}
            </div>
        </header>

        {#if previews.length > 0}
            <div class="preview-grid" in:fade>
                {#each previews as src, i}
                    <div class="preview-item">
                        <img {src} alt="Preview" />
                        <button
                            class="remove-btn"
                            onclick={() => removePreview(i)}>×</button
                        >
                    </div>
                {/each}
            </div>
            <div class="divider"></div>
        {/if}

        <div class="gallery-grid">
            {#each images as img (img.id)}
                <div class="gallery-item" in:fade>
                    <div class="image-wrapper">
                        <img src={img.path} alt={img.filename} loading="lazy" />
                        <div class="overlay">
                            <button
                                class="btn-delete"
                                onclick={() => handleDelete(img.id)}
                            >
                                <svg
                                    width="20"
                                    height="20"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path
                                        d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"
                                    ></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <p class="file-info">{img.filename}</p>
                </div>
            {:else}
                <div class="empty-state">
                    <p>
                        Gallery masih kosong, Bre. Upload foto prewed lo
                        sekarang!
                    </p>
                </div>
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

    .actions {
        display: flex;
        gap: 12px;
    }
    .btn-select {
        background: #2d3748;
        color: #fff;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 12px;
        cursor: pointer;
        border: 1px solid #3f4e64;
    }
    .btn-upload {
        background: #d4af37;
        color: #000;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 12px;
        border: none;
        cursor: pointer;
    }

    /* PREVIEW AREA */
    .preview-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: 15px;
        margin-bottom: 30px;
    }
    .preview-item {
        position: relative;
        aspect-ratio: 1/1;
        border: 2px solid #d4af37;
        border-radius: 8px;
        overflow: hidden;
    }
    .preview-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .remove-btn {
        position: absolute;
        top: 5px;
        right: 5px;
        background: rgba(255, 0, 0, 0.8);
        color: white;
        border: none;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        cursor: pointer;
        font-size: 12px;
    }
    .divider {
        height: 1px;
        background: #2d3748;
        margin-bottom: 30px;
    }

    /* GALLERY GRID */
    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 25px;
    }

    .gallery-item {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .image-wrapper {
        position: relative;
        aspect-ratio: 1/1;
        background: #1a202c;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #2d3748;
    }
    .image-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s;
    }

    .overlay {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: 0.3s;
    }
    .image-wrapper:hover .overlay {
        opacity: 1;
    }
    .image-wrapper:hover img {
        transform: scale(1.1);
    }

    .btn-delete {
        background: #ef4444;
        color: white;
        border: none;
        padding: 10px;
        border-radius: 50%;
        cursor: pointer;
        transition: 0.2s;
    }
    .btn-delete:hover {
        transform: scale(1.1);
        background: #dc2626;
    }

    .file-info {
        font-size: 10px;
        color: #718096;
        text-align: center;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .empty-state {
        grid-column: 1/-1;
        padding: 100px;
        text-align: center;
        color: #4a5568;
        border: 2px dashed #2d3748;
        border-radius: 20px;
    }
</style>
