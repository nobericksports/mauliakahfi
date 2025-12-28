<script>
    import { onMount, onDestroy } from "svelte";

    let { scrollProgress = $bindable(0), isLocked = $bindable(false) } =
        $props();

    let items = $state([]);
    let viewport = $state(null);
    let quoteElement = $state(null);
    let ticking = false; // Untuk throttling

    // Fungsi Update 3D dengan Throttling (requestAnimationFrame)
    function update3D() {
        if (!ticking) {
            window.requestAnimationFrame(() => {
                if (!viewport) return;

                const vRect = viewport.getBoundingClientRect();
                const vCenter = vRect.top + vRect.height / 2;
                const scrollH = viewport.scrollHeight - viewport.clientHeight;
                const progress = (viewport.scrollTop / scrollH) * 100;

                scrollProgress = progress;
                isLocked = progress < 98;

                // Update timeline items
                items.forEach((el) => {
                    if (!el) return;
                    const rect = el.getBoundingClientRect();
                    const eCenter = rect.top + rect.height / 2;
                    const normalized = (eCenter - vCenter) / (vRect.height / 2);

                    const absNorm = Math.abs(normalized);
                    const isMobile = window.innerWidth < 768;

                    // Logic Animasi
                    const z = Math.pow(1 - Math.min(1, absNorm), 2) * 600;
                    const scale =
                        0.75 + Math.pow(1 - Math.min(1, absNorm), 2) * 0.25;
                    const opacity = 1 - absNorm * 1.8;

                    // OPTIMASI: Matikan blur di mobile agar tidak lag
                    const blur = isMobile ? 0 : absNorm * 12;

                    el.style.transform = `translateZ(${z}px) scale(${scale})`;
                    el.style.opacity = opacity > 0 ? opacity : 0;
                    if (!isMobile) el.style.filter = `blur(${blur}px)`;
                    el.style.zIndex = Math.round((1 - absNorm) * 100);
                });

                if (quoteElement) {
                    const quoteProgress = Math.max(0, (progress - 85) / 15);
                    quoteElement.style.opacity = quoteProgress;
                    quoteElement.style.transform = `translateY(${(1 - quoteProgress) * 30}px)`;
                }

                ticking = false;
            });
            ticking = true;
        }
    }

    onMount(() => {
        update3D();
        viewport.addEventListener("scroll", update3D, { passive: true });
    });

    onDestroy(() => {
        if (viewport) viewport.removeEventListener("scroll", update3D);
    });

    const timeline = [
        {
            year: "2020",
            title: "First Met",
            description:
                "Pertama kali bertemu di acara gathering kantor. Tidak pernah menyangka bahwa pertemuan singkat itu akan menjadi awal dari cerita indah kami.",
        },
        {
            year: "2021",
            title: "First Date",
            description:
                "Kencan pertama kami di kafe favorit. Obrolan yang tidak pernah habis membuat kami sadar bahwa ini lebih dari sekadar kebetulan.",
        },
        {
            year: "2023",
            title: "The Proposal",
            description:
                "Di tempat pertama kami bertemu, dia berlutut dan mengajukan pertanyaan yang telah lama ditunggu. Tentu saja jawabannya adalah yes!",
        },
        {
            year: "2025",
            title: "Our Wedding",
            description:
                "Akhirnya hari yang ditunggu-tunggu tiba. Kami akan memulai babak baru dalam hidup kami, bersama-sama selamanya.",
        },
    ];
</script>

<div
    class="story-viewport"
    bind:this={viewport}
    role="region"
    aria-label="Our Love Story Timeline"
>
    <section class="snap-section">
        <div
            class="header-content luxury-fade"
            style="opacity: {Math.max(
                0,
                1 - scrollProgress / 20,
            )}; transform: translateY({scrollProgress * 0.5}px)"
        >
            <div class="header-inner">
                <p class="subtitle">THE JOURNEY OF US</p>
                <h1 class="title">Our Love Story</h1>
                <div class="ornament">
                    <div class="diamond"></div>
                </div>
                <p class="description">
                    Setiap cerita cinta memiliki awalnya sendiri. <br />
                    Ini adalah perjalanan kami dari dua orang asing menjadi soulmates.
                </p>
            </div>

            <div
                class="scroll-instruction"
                style="opacity: {Math.max(0, 1 - scrollProgress / 10)}"
            >
                <span>SCROLL</span>
                <div class="mouse">
                    <div class="wheel"></div>
                </div>
            </div>
        </div>
    </section>

    {#each timeline as item, i}
        <section class="snap-section">
            <div class="timeline-item" bind:this={items[i]}>
                <div class="glass-card">
                    <div class="card-glow"></div>
                    <div class="content">
                        <span class="year">{item.year}</span>
                        <h3 class="card-title">{item.title}</h3>
                        <p class="desc">{item.description}</p>
                    </div>
                </div>
            </div>
        </section>
    {/each}

    <section class="snap-section">
        <div class="footer-content" bind:this={quoteElement}>
            <div class="quote-container">
                <div class="quote-mark">"</div>
                <p class="quote-text">
                    Love is not about how many days, months, or years you have
                    been together. It's all about how much you love tiap hari.
                </p>
                <div class="quote-author">— Unknown</div>
            </div>
        </div>
    </section>
</div>

<style>
    /* FIX: @import harus di baris paling atas! */
    @import url("https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500&family=Inter:wght@300;400&display=swap");

    .story-viewport {
        position: absolute;
        inset: 0;
        overflow-y: auto;
        overflow-x: hidden;
        scroll-snap-type: y mandatory;
        perspective: 1200px;
        perspective-origin: center center;
        scrollbar-width: none;
        -webkit-overflow-scrolling: touch;
    }

    .story-viewport::-webkit-scrollbar {
        display: none;
    }

    .snap-section {
        height: 100vh;
        width: 100%;
        scroll-snap-align: center;
        scroll-snap-stop: always;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    /* Optimasi Animasi: Gunakan will-change */
    .timeline-item {
        width: 100%;
        display: flex;
        justify-content: center;
        transform-style: preserve-3d;
        will-change: transform, opacity;
        transition: transform 0.4s cubic-bezier(0.23, 1, 0.32, 1);
    }

    .glass-card {
        width: 85%;
        max-width: 450px;
        padding: 2.5rem;
        background: rgba(255, 255, 255, 0.04);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 24px;
        box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.5);
        position: relative;
    }

    /* Header & Text Styles */
    .header-content {
        text-align: center;
        color: white;
        padding: 2rem;
        max-width: 600px;
    }
    .subtitle {
        letter-spacing: 0.5em;
        font-size: 10px;
        opacity: 0.5;
        text-transform: uppercase;
    }
    .title {
        font-family: "Cormorant Garamond", serif;
        font-size: 3rem;
        font-weight: 300;
    }
    .year {
        color: #d4af37;
        font-weight: 600;
        letter-spacing: 0.3em;
        font-size: 11px;
        display: block;
        margin-bottom: 0.75rem;
    }
    .card-title {
        color: white;
        font-family: "Cormorant Garamond", serif;
        font-size: 2rem;
        font-weight: 400;
    }
    .desc {
        color: rgba(255, 255, 255, 0.65);
        font-size: 14px;
        font-weight: 300;
    }

    /* Quote Styles */
    .footer-content {
        text-align: center;
        padding: 2rem;
        will-change: opacity, transform;
    }
    .quote-container {
        max-width: 600px;
        margin: 0 auto;
        padding: 3rem 2rem;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    .quote-text {
        color: rgba(255, 255, 255, 0.8);
        font-family: "Cormorant Garamond", serif;
        font-style: italic;
        font-size: 1.25rem;
        font-weight: 300;
    }

    /* Mobile optimization */
    @media (max-width: 768px) {
        .glass-card {
            backdrop-filter: none;
            background: rgba(20, 20, 20, 0.8);
        } /* Matikan backdrop-filter di mobile */
        .title {
            font-size: 2.5rem;
        }
    }
    .header-content {
        text-align: center;
        color: white;
        padding: 2rem;
        max-width: 500px;
        position: relative;
        z-index: 10;
        /* Tambahin transition biar pas balik (swipe up) gak patah */
        transition: opacity 0.2s ease-out;
    }

    .subtitle {
        font-size: 9px;
        letter-spacing: 0.5em;
        color: #d4af37; /* Pake aksen emas biar mewah */
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

    /* Ornament pemanis biar gak kosong banget */
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

    .description {
        color: rgba(255, 255, 255, 0.5);
        font-size: 14px;
        line-height: 1.8;
        font-style: italic;
        font-family: "Cormorant Garamond", serif;
    }

    /* Scroll Instruction yang lebih modern */
    .scroll-instruction {
        position: absolute;
        bottom: -15vh;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
    }

    .scroll-instruction span {
        font-size: 8px;
        letter-spacing: 0.3em;
        opacity: 0.4;
    }

    .mouse {
        width: 20px;
        height: 32px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        position: relative;
    }

    .wheel {
        width: 2px;
        height: 6px;
        background: #d4af37;
        border-radius: 2px;
        position: absolute;
        top: 6px;
        left: 50%;
        transform: translateX(-50%);
        animation: scroll-anim 2s infinite;
    }

    @keyframes scroll-anim {
        0% {
            opacity: 0;
            transform: translate(-50%, 0);
        }
        30% {
            opacity: 1;
        }
        100% {
            opacity: 0;
            transform: translate(-50%, 15px);
        }
    }
</style>
