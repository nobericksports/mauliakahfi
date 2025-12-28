<script>
    import { onMount, onDestroy } from "svelte";
    import { fade, scale } from "svelte/transition";

    // TERIMA DATA DARI PARENT (Wedding.svelte)
    let {
        galleryData = [],
        scrollProgress = $bindable(0),
        isLocked = $bindable(false),
    } = $props();
    // TERIMA DATA DARI PARENT (Wedding.svelte)

    let items = $state([]);
    let viewport = $state(null);
    let selectedImage = $state(null);
    let ticking = false;

    function update3D() {
        if (!ticking) {
            window.requestAnimationFrame(() => {
                if (!viewport) return;

                const vRect = viewport.getBoundingClientRect();
                const vCenter = vRect.top + vRect.height / 2;
                const scrollTop = viewport.scrollTop;
                const scrollH = viewport.scrollHeight - vRect.height;
                const progress = (scrollTop / scrollH) * 100;

                scrollProgress = progress;

                // Smart Lock
                const isAtTop = scrollTop <= 15;
                const isAtBottom = scrollTop >= scrollH - 15;
                isLocked = !isAtTop && !isAtBottom;

                items.forEach((el) => {
                    if (!el) return;
                    const rect = el.getBoundingClientRect();
                    const eCenter = rect.top + rect.height / 2;
                    const normalized = (eCenter - vCenter) / (vRect.height / 2);
                    const absNorm = Math.abs(normalized);

                    // 3D Effect (Agak dimundurin biar polaroidnya gak nabrak layar)
                    const z = Math.pow(1 - Math.min(1, absNorm), 2) * 300;
                    const scaleVal =
                        0.85 + Math.pow(1 - Math.min(1, absNorm), 2) * 0.15;
                    const opacity = 1 - absNorm * 1.5;

                    el.style.transform = `translateZ(${z}px) scale(${scaleVal})`;
                    el.style.opacity = opacity > 0 ? opacity : 0;
                    el.style.zIndex = Math.round((1 - absNorm) * 100);
                    el.style.pointerEvents = absNorm < 0.4 ? "auto" : "none";

                    if (window.innerWidth >= 768) {
                        el.style.filter = `blur(${absNorm * 10}px)`;
                    }
                });
                ticking = false;
            });
            ticking = true;
        }
    }

    onMount(() => {
        update3D();
        viewport.addEventListener("scroll", update3D, { passive: true });
        return () => {
            if (viewport) viewport.removeEventListener("scroll", update3D);
        };
    });
</script>

<div class="gallery-viewport" bind:this={viewport}>
    <section class="snap-section">
        <div
            class="header-content luxury-fade"
            style="opacity: {Math.max(
                0,
                1 - scrollProgress / 20,
            )}; transform: translateY({scrollProgress * 0.4}px)"
        >
            <p class="subtitle">CAPTURED MOMENTS</p>
            <h1 class="title">Our Gallery</h1>
            <div class="ornament">
                <div class="diamond"></div>
            </div>
            <p class="description">
                Cerita singkat dalam setiap bingkai. <br /> Momen berharga yang akan
                kami kenang selamanya.
            </p>

            <div
                class="scroll-instruction"
                style="opacity: {Math.max(0, 1 - scrollProgress / 10)}"
            >
                <span>SCROLL</span>
                <div class="mouse"><div class="wheel"></div></div>
            </div>
        </div>
    </section>

    {#each galleryData as photo, i}
        <section class="snap-section">
            <div class="gallery-item" bind:this={items[i]}>
                <button
                    class="polaroid-card"
                    onclick={() => (selectedImage = photo)}
                >
                    <div
                        class="photo-frame white-paper"
                        style="transform: rotate({i % 2 === 0
                            ? '-3deg'
                            : '3deg'});"
                    >
                        <div class="photo-wrapper">
                            <img
                                src={photo.path}
                                alt="Gallery Photo"
                                class="photo"
                                loading="lazy"
                            />
                        </div>
                        <div class="photo-caption">
                            <span class="caption-text">Moment #{i + 1}</span>
                        </div>
                    </div>
                </button>
            </div>
        </section>
    {/each}

    <section class="snap-section">
        <div
            class="footer-hint"
            style="opacity: {Math.max(0, (scrollProgress - 90) / 10)}"
        >
            <div class="ornament"><div class="diamond"></div></div>
            <p class="footer-msg">And many more memories to come...</p>
            <p class="next-label">SWIPE TO RSVP</p>
        </div>
    </section>
</div>

{#if selectedImage}
    <div
        class="lightbox"
        onclick={() => (selectedImage = null)}
        transition:fade={{ duration: 400 }}
    >
        <button class="lightbox-close">×</button>
        <div class="lightbox-content" onclick={(e) => e.stopPropagation()}>
            <div class="lightbox-frame white-paper">
                <img
                    src={selectedImage.url}
                    alt={selectedImage.caption}
                    class="lightbox-image"
                    transition:scale={{ duration: 500, start: 0.9, opacity: 0 }}
                />
                <p class="lightbox-caption dark-text">
                    {selectedImage.caption}
                </p>
            </div>
        </div>
    </div>
{/if}

<style>
    /* ... (CSS Font Import & Viewport Styles tetep sama) ... */
    @import url("https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600&family=Inter:wght@300;400&display=swap");
    .gallery-viewport {
        position: absolute;
        inset: 0;
        overflow-y: auto;
        overflow-x: hidden;
        scroll-snap-type: y mandatory;
        perspective: 1200px;
        scrollbar-width: none;
        -webkit-overflow-scrolling: touch;
        background: #0a0a0a;
    }
    .gallery-viewport::-webkit-scrollbar {
        display: none;
    }
    .snap-section {
        height: 100vh;
        width: 100%;
        scroll-snap-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        pointer-events: none;
    }

    /* Header Styling (Tetep Emas) */
    .header-content {
        text-align: center;
        color: white;
        padding: 2rem;
        pointer-events: auto;
    }
    .subtitle {
        font-size: 10px;
        letter-spacing: 0.5em;
        color: #d4af37;
        text-transform: uppercase;
        margin-bottom: 1rem;
    }
    .title {
        font-family: "Cormorant Garamond", serif;
        font-size: 3.5rem;
        font-weight: 300;
        line-height: 1;
        margin-bottom: 1.5rem;
        background: linear-gradient(to bottom, #fff, #a1a1aa);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .description {
        color: rgba(255, 255, 255, 0.5);
        font-size: 14px;
        font-style: italic;
        font-family: "Cormorant Garamond", serif;
        line-height: 1.7;
    }
    .ornament {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
        opacity: 0.6;
    }
    .ornament::before,
    .ornament::after {
        content: "";
        height: 1px;
        width: 40px;
        background: linear-gradient(to var(--dir, right), transparent, #d4af37);
    }
    .ornament::after {
        --dir: left;
    }
    .diamond {
        width: 6px;
        height: 6px;
        background: #d4af37;
        transform: rotate(45deg);
    }

    /* === PERUBAHAN UTAMA: WHITE POLAROID STYLE === */
    .gallery-item {
        width: 100%;
        display: flex;
        justify-content: center;
        will-change: transform, opacity;
        transform-style: preserve-3d;
    }

    .polaroid-card {
        background: none;
        border: none;
        padding: 0;
        cursor: pointer;
        width: 85%;
        max-width: 340px;
        position: relative;
        pointer-events: auto;
        /* Hapus transform-style di sini biar rotasi inline jalan */
    }

    .photo-frame.white-paper {
        background: #ffffff; /* Kertas Putih */
        padding: 14px;
        padding-bottom: 3.5rem; /* Space bawah lebih gede khas polaroid */
        border-radius: 4px;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5); /* Shadow tebel biar berasa 'ngangkat' */
        transition: transform 0.6s cubic-bezier(0.23, 1, 0.32, 1);
        /* Hapus rotasi CSS, kita pake inline style di HTML */
    }

    .photo-wrapper {
        width: 100%;
        aspect-ratio: 4/5;
        overflow: hidden;
        background: #f0f0f0;
    }
    .photo {
        width: 100%;
        height: 100%;
        object-fit: cover;
        filter: sepia(0.1); /* Sedikit efek vintage */
    }

    .photo-caption {
        margin-top: 1.2rem;
        text-align: center;
        font-family: "Cormorant Garamond", serif;
        font-size: 1.3rem;
        color: #222; /* Teks Gelap di atas putih */
        font-weight: 600;
        font-style: italic;
    }

    /* Lightbox (White Theme) */
    .lightbox {
        position: fixed;
        inset: 0;
        z-index: 10000;
        background: rgba(0, 0, 0, 0.95);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .lightbox-close {
        position: absolute;
        top: 1.5rem;
        right: 1.5rem;
        background: none;
        border: none;
        color: white;
        font-size: 2.5rem;
        cursor: pointer;
        opacity: 0.7;
    }
    .lightbox-frame.white-paper {
        background: #ffffff;
        padding: 14px;
        padding-bottom: 1rem;
        border-radius: 4px;
        max-width: 90vw;
    }
    .lightbox-image {
        max-height: 70vh;
        width: 100%;
        object-fit: contain;
        background: #f0f0f0;
    }
    .lightbox-caption.dark-text {
        color: #222;
        font-family: "Cormorant Garamond", serif;
        font-size: 1.5rem;
        font-style: italic;
        margin-top: 1rem;
        text-align: center;
        font-weight: 600;
    }

    /* ... (Scroll Instruction & Footer tetep sama) ... */
    .scroll-instruction {
        margin-top: 3rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
    }
    .scroll-instruction span {
        font-size: 8px;
        letter-spacing: 0.4em;
        color: rgba(255, 255, 255, 0.4);
        text-transform: uppercase;
    }
    .mouse {
        width: 22px;
        height: 36px;
        border: 1px solid rgba(212, 175, 55, 0.3);
        border-radius: 20px;
        position: relative;
    }
    .wheel {
        width: 2px;
        height: 7px;
        background: #d4af37;
        border-radius: 2px;
        position: absolute;
        top: 6px;
        left: 50%;
        transform: translateX(-50%);
        animation: scroll-wheel-anim 2s infinite;
    }
    @keyframes scroll-wheel-anim {
        0% {
            opacity: 0;
            transform: translate(-50%, 0);
        }
        30% {
            opacity: 1;
        }
        100% {
            opacity: 0;
            transform: translate(-50%, 18px);
        }
    }

    .footer-hint {
        text-align: center;
        color: white;
        pointer-events: auto;
    }
    .footer-msg {
        font-family: "Cormorant Garamond", serif;
        font-size: 1.4rem;
        font-style: italic;
        opacity: 0.7;
    }
    .next-label {
        font-size: 8px;
        letter-spacing: 0.3em;
        color: #d4af37;
        margin-top: 2rem;
        text-transform: uppercase;
    }
</style>
