<script>
    import { onMount, onDestroy } from "svelte";

    let { scrollProgress = $bindable(0), isLocked = $bindable(false) } =
        $props();

    let items = $state([]);
    let viewport = $state(null);
    let ticking = false;

    const weddingDate = new Date("2026-05-24T08:00:00");
    let timeLeft = $state({ days: 0, hours: 0, minutes: 0, seconds: 0 });

    function updateCountdown() {
        const now = new Date();
        const diff = weddingDate - now;
        if (diff > 0) {
            timeLeft = {
                days: Math.floor(diff / (1000 * 60 * 60 * 24)),
                hours: Math.floor((diff / (1000 * 60 * 60)) % 24),
                minutes: Math.floor((diff / 1000 / 60) % 60),
                seconds: Math.floor((diff / 1000) % 60),
            };
        }
    }

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

                    // Logic 3D
                    // Di dalam function update3D() - tweak bagian scale & z
                    const z = Math.pow(1 - Math.min(1, absNorm), 2) * 150; // Kurangi dari 500 ke 350 biar gak terlalu maju
                    const scale =
                        0.8 + Math.pow(1 - Math.min(1, absNorm), 2) * 0.15; // Base scale 0.8, max 0.95
                    const opacity = 1 - absNorm * 1.5;

                    el.style.transform = `translateZ(${z}px) scale(${scale})`;
                    el.style.opacity = opacity > 0 ? opacity : 0;
                    el.style.zIndex = Math.round((1 - absNorm) * 100);

                    // KLIK FIX: Hanya kartu yang di tengah yang bisa diklik
                    el.style.pointerEvents = absNorm < 0.4 ? "auto" : "none";
                });
                ticking = false;
            });
            ticking = true;
        }
    }

    onMount(() => {
        updateCountdown();
        const timer = setInterval(updateCountdown, 1000);
        update3D();
        viewport.addEventListener("scroll", update3D, { passive: true });
        return () => {
            clearInterval(timer);
            if (viewport) viewport.removeEventListener("scroll", update3D);
        };
    });

    function openMaps(e, url) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        window.open(url, "_blank");
    }

    function handleButtonTouch(e) {
        e.stopPropagation();
    }
</script>

<div class="event-viewport" bind:this={viewport}>
    <section class="snap-section">
        <div
            class="header-content luxury-fade"
            style="opacity: {Math.max(
                0,
                1 - scrollProgress / 20,
            )}; transform: translateY({scrollProgress * 0.4}px)"
        >
            <p class="subtitle">COUNTING DOWN TO</p>
            <h1 class="title">The Big Day</h1>
            <div class="ornament">
                <div class="diamond"></div>
            </div>

            <div class="countdown-grid">
                {#each Object.entries(timeLeft) as [unit, value]}
                    <div class="countdown-box">
                        <span class="num"
                            >{value.toString().padStart(2, "0")}</span
                        >
                        <span class="unit">{unit}</span>
                    </div>
                    {#if unit !== "seconds"}<span class="sep">:</span>{/if}
                {/each}
            </div>

            <div
                class="scroll-instruction"
                style="opacity: {Math.max(0, 1 - scrollProgress / 10)}"
            >
                <span>SCROLL</span>
                <div class="mouse"><div class="wheel"></div></div>
            </div>
        </div>
    </section>

    <section class="snap-section">
        <div class="event-item" bind:this={items[0]}>
            <div class="glass-card">
                <p class="tag">AKAD NIKAH</p>
                <h3 class="card-title">The Solemnization</h3>
                <div class="details">
                    <p class="date">Sabtu, 24 Mei 2026</p>
                    <p class="time">08:00 — 09:00 WIB</p>
                    <div class="divider"></div>
                    <p class="loc">Mushola Al-Ikhlas</p>
                    <p class="addr">
                        Jl. Prabayaksa No.43, Kalijaga, Kota Cirebon
                    </p>
                    <button
                        class="map-btn"
                        onclick={(e) =>
                            openMaps(
                                e,
                                "https://maps.google.com/?q=Mushola+Al-Ikhlas+Cirebon",
                            )}
                        ontouchstart={handleButtonTouch}
                    >
                        OPEN GOOGLE MAPS
                    </button>
                </div>
            </div>
        </div>
    </section>

    <section class="snap-section">
        <div class="event-item" bind:this={items[1]}>
            <div class="glass-card">
                <p class="tag">RESEPSI</p>
                <h3 class="card-title">Wedding Reception</h3>
                <div class="details">
                    <p class="date">Sabtu, 24 Mei 2026</p>
                    <p class="time">11:00 — 13:00 WIB</p>
                    <div class="divider"></div>
                    <p class="loc">Wedding Hall | Tepian Rasa</p>
                    <p class="addr">
                        Jl. DR. Cipto Mangunkusumo No.53, Kota Cirebon
                    </p>
                    <button
                        class="map-btn"
                        onclick={(e) =>
                            openMaps(
                                e,
                                "https://maps.google.com/?q=Tepian+Rasa+Cirebon",
                            )}
                        ontouchstart={handleButtonTouch}
                    >
                        OPEN GOOGLE MAPS
                    </button>
                </div>
            </div>
        </div>
    </section>

    <section class="snap-section">
        <div
            class="dress-code-content"
            style="opacity: {Math.max(0, (scrollProgress - 80) / 20)}"
        >
            <p class="subtitle">DRESS CODE</p>
            <h2 class="dress-title">Formal / Traditional Attire</h2>
            <div class="ornament"><div class="diamond"></div></div>
            <p class="next-hint">SWIPE TO SEE GALLERY</p>
        </div>
    </section>
</div>

<style>
    .event-viewport {
        position: absolute;
        inset: 0;
        overflow-y: auto;
        overflow-x: hidden;
        scroll-snap-type: y mandatory;
        perspective: 1200px;
        scrollbar-width: none;
        -webkit-overflow-scrolling: touch;
        background: #0a0a0a;
        transform-style: preserve-3d;
    }
    .event-viewport::-webkit-scrollbar {
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
        pointer-events: none; /* Biar gak nutupin klik */
        transform-style: preserve-3d;
    }

    /* Luxury Typography (StorySection Sync) */
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

    /* Countdown Styling */
    .countdown-grid {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.8rem;
        margin-top: 2rem;
    }
    .countdown-box {
        display: flex;
        flex-direction: column;
        align-items: center;
        min-width: 60px;
    }
    .num {
        font-size: 2.2rem;
        color: white;
        font-weight: 200;
        font-family: "Cormorant Garamond", serif;
    }
    .unit {
        font-size: 8px;
        color: #d4af37;
        letter-spacing: 0.1em;
        text-transform: uppercase;
    }
    .sep {
        color: rgba(212, 175, 55, 0.4);
        font-size: 1.5rem;
        margin-bottom: 15px;
    }

    /* Card Styling */
    .event-item {
        width: 100%;
        display: flex;
        justify-content: center;
        will-change: transform, opacity;
        transform-style: preserve-3d;
    }
    /* Card Styling - Dibuat lebih slim */
    .glass-card {
        width: 82%; /* Sedikit lebih ramping */
        max-width: 340px; /* Batasin biar gak melebar banget di tablet/HP gede */
        padding: 1.8rem; /* Kurangi padding dari 2.5rem */
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(212, 175, 55, 0.15); /* Border emas lebih subtle */
        border-radius: 16px; /* Radius lebih modern */
        text-align: center;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        pointer-events: auto;
    }

    /* Typography - Disesuaikan ukurannya */
    .tag {
        font-size: 8px; /* Lebih kecil */
        letter-spacing: 0.3em;
        color: #d4af37;
        margin-bottom: 0.4rem;
    }

    .card-title {
        font-family: "Cormorant Garamond", serif;
        font-size: 1.7rem; /* Kurangi dari 2rem */
        color: white;
        margin-bottom: 1.2rem;
        font-weight: 400;
    }

    .date {
        color: white;
        font-size: 0.95rem; /* 15px-ish */
        font-weight: 400;
    }

    .time {
        color: rgba(255, 255, 255, 0.5);
        font-size: 0.75rem;
        margin-top: 4px;
    }

    .divider {
        width: 30px;
        height: 1px;
        background: #d4af37;
        margin: 1.2rem auto;
        opacity: 0.3;
    }

    .loc {
        font-size: 1rem;
        color: white;
        margin-bottom: 4px;
    }

    .addr {
        color: rgba(255, 255, 255, 0.4);
        font-size: 11px; /* Lebih mungil */
        line-height: 1.5;
        margin: 0.5rem 0 1.5rem;
        padding: 0 10px; /* Kasih space dikit di teks alamat */
    }

    /* Button juga dikecilin dikit biar proporsional */
    .map-btn {
        /* Warna Emas Solid biar kelihatan di background item */
        background: #d4af37;
        color: #000000; /* Teks item biar kontras parah */
        border: none;
        padding: 0.8rem 1.6rem;
        font-size: 10px;
        letter-spacing: 0.2em;
        font-weight: 700;
        border-radius: 50px;
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(212, 175, 55, 0.4);
        transition: all 0.3s ease;
        text-transform: uppercase;
        margin-top: 1rem;
        display: inline-block;
    }

    .map-btn:hover {
        background: #f1c40f; /* Lebih terang dikit pas hover */
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(212, 175, 55, 0.6);
    }

    .map-btn:active {
        transform: scale(0.95);
    }

    /* Dress Code */
    .dress-code-content {
        text-align: center;
        color: white;
        pointer-events: auto;
    }
    .dress-title {
        font-family: "Cormorant Garamond", serif;
        font-size: 1.8rem;
        font-style: italic;
        font-weight: 300;
        margin-top: 0.5rem;
    }
    .next-hint {
        font-size: 8px;
        letter-spacing: 0.2em;
        opacity: 0.3;
        margin-top: 1rem;
    }

    /* Mouse Scroll */
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

    @media (max-width: 768px) {
        .title {
            font-size: 2.5rem;
        }
        .glass-card {
            padding: 2rem 1.5rem;
        }
    }
    .tag,
    .label,
    .desc,
    .addr {
        font-family: "Inter", sans-serif; /* Biar bersih kelihatannya */
    }

    /* Tombol HARUS Sans-Serif biar tegas */
    .map-btn,
    .rsvp-btn,
    .submit-btn {
        font-family: "Inter", sans-serif;
        font-weight: 600;
    }
</style>
