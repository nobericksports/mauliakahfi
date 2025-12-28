<script>
    import { onMount, onDestroy, tick } from "svelte";

    let {
        guestData = null,
        scrollProgress = $bindable(0),
        isLocked = $bindable(false),
    } = $props();

    let items = $state([]);
    let viewport = $state(null);
    let ticking = false;

    // States
    let attendance = $state(null);
    let isSubmitting = $state(false);
    let isSubmitted = $state(false);

    function blockParent(e) {
        if (e) {
            e.stopPropagation();
            e.stopImmediatePropagation();
        }
    }

    // Di dalam fungsi handleAttendance
    async function handleAttendance(e, status) {
        blockParent(e);
        if (isSubmitting || isSubmitted) return;

        attendance = status;
        isSubmitting = true;

        try {
            const url = `http://10.159.173.90:8000/api/rsvp/${guestData.code}`;
            console.log("Nembak ke:", url);

            const response = await fetch(url, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                },
                body: JSON.stringify({ status: status }),
            });

            const result = await response.json();

            if (response.ok) {
                console.log("Sukses:", result);
                isSubmitting = false;
                isSubmitted = true;
                isLocked = false;

                await tick();
                if (viewport) {
                    viewport.scrollTo({
                        top: viewport.scrollHeight,
                        behavior: "smooth",
                    });
                }
            } else {
                // Laravel ngirim error (misal 422 atau 500)
                console.error("Laravel Ngambek:", result);
                alert("Server Error: " + (result.message || "Cek Console"));
                isSubmitting = false;
            }
        } catch (error) {
            // Koneksi putus atau IP berubah
            console.error("Network Error:", error);
            alert("Gagal konek ke Server. Cek IP Laptop!");
            isSubmitting = false;
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

                // Progress cuma diitung kalau ada area scroll
                scrollProgress = scrollH > 0 ? (scrollTop / scrollH) * 100 : 0;

                // LOCK HORIZONTAL: Selama belum submit, jangan kasih kabur ke section lain
                if (!isSubmitted) isLocked = true;

                items.forEach((el) => {
                    if (!el) return;
                    const rect = el.getBoundingClientRect();
                    const eCenter = rect.top + rect.height / 2;
                    const normalized = (eCenter - vCenter) / (vRect.height / 2);
                    const absNorm = Math.abs(normalized);

                    const z = Math.pow(1 - Math.min(1, absNorm), 2) * 100;
                    const scale =
                        0.85 + Math.pow(1 - Math.min(1, absNorm), 2) * 0.15;
                    const opacity = 1 - absNorm * 1.5;

                    el.style.transform = `translateZ(${z}px) scale(${scale})`;
                    el.style.opacity = opacity > 0 ? opacity : 0;
                    el.style.zIndex = Math.round((1 - absNorm) * 100);
                    el.style.pointerEvents = absNorm < 0.4 ? "auto" : "none";
                });
                ticking = false;
            });
            ticking = true;
        }
    }

    onMount(() => {
        update3D();
        viewport.addEventListener("scroll", update3D, { passive: true });
    });
</script>

<div class="rsvp-viewport" bind:this={viewport} class:no-scroll={!isSubmitted}>
    <section class="snap-section">
        <div class="item-3d" bind:this={items[0]}>
            <div class="glass-card">
                <p class="subtitle">RESERVATION</p>
                <h1 class="title">RSVP</h1>
                <div class="ornament"><div class="diamond"></div></div>

                {#if guestData}
                    <p class="welcome">Halo, <span>{guestData.name}</span></p>
                {/if}
                <p class="desc">
                    Konfirmasi kehadiran Anda sangat berarti bagi kami.
                </p>

                <div class="btn-column">
                    <button
                        class="rsvp-btn confirmed"
                        disabled={isSubmitting || isSubmitted}
                        onclick={(e) => handleAttendance(e, "confirmed")}
                        ontouchstart={blockParent}
                    >
                        {#if isSubmitting && attendance === "confirmed"}
                            MENGIRIM...
                        {:else if isSubmitted && attendance === "confirmed"}
                            SUDAH KONFIRMASI ✓
                        {:else}
                            SAYA AKAN HADIR
                        {/if}
                    </button>

                    <button
                        class="rsvp-btn declined"
                        disabled={isSubmitting || isSubmitted}
                        onclick={(e) => handleAttendance(e, "declined")}
                        ontouchstart={blockParent}
                    >
                        {#if isSubmitting && attendance === "declined"}
                            MENGIRIM...
                        {:else if isSubmitted && attendance === "declined"}
                            SUDAH KONFIRMASI ✓
                        {:else}
                            MOHON MAAF, TIDAK HADIR
                        {/if}
                    </button>
                </div>
            </div>
        </div>
    </section>

    {#if isSubmitted}
        <section class="snap-section">
            <div class="item-3d" bind:this={items[1]}>
                <div class="glass-card success-card">
                    <div class="success-icon">
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="3"
                        >
                            <path
                                d="M5 13l4 4L19 7"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>
                    </div>
                    <h2 class="title-small">Terima Kasih!</h2>
                    <p class="desc">Konfirmasi Anda telah kami terima.</p>
                    <div class="divider"></div>
                    <p class="next-hint">SWIPE KE KANAN UNTUK PENUTUP</p>
                </div>
            </div>
        </section>
    {/if}
</div>

<style>
    .rsvp-viewport {
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

    /* Tambahan: Paksa jangan bisa scroll kalau belum submitted */
    .no-scroll {
        overflow-y: hidden;
    }

    .rsvp-viewport::-webkit-scrollbar {
        display: none;
    }

    /* ... (CSS Sisanya Tetap Sama Kayak Sebelumnya) ... */
    .snap-section {
        height: 100vh;
        width: 100%;
        scroll-snap-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        pointer-events: none;
        transform-style: preserve-3d;
    }
    .item-3d {
        width: 100%;
        display: flex;
        justify-content: center;
        will-change: transform, opacity;
        transform-style: preserve-3d;
    }
    .glass-card {
        width: 82%;
        max-width: 340px;
        padding: 2.5rem 2rem;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(212, 175, 55, 0.2);
        border-radius: 20px;
        text-align: center;
        backdrop-filter: blur(15px);
        pointer-events: auto;
    }
    .subtitle {
        font-size: 10px;
        letter-spacing: 0.4em;
        color: #d4af37;
        text-transform: uppercase;
        margin-bottom: 0.8rem;
    }
    .title {
        font-family: "Cormorant Garamond", serif;
        font-size: 3rem;
        font-weight: 300;
        color: white;
        margin: 0;
        background: linear-gradient(to bottom, #fff, #a1a1aa);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .welcome {
        font-family: "Cormorant Garamond", serif;
        font-size: 1.5rem;
        color: white;
        margin-top: 1rem;
    }
    .welcome span {
        color: #d4af37;
    }
    .desc {
        font-size: 13px;
        color: rgba(255, 255, 255, 0.5);
        margin: 1.5rem 0;
        line-height: 1.6;
    }
    .btn-column {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-top: 2rem;
    }
    .rsvp-btn {
        width: 100%;
        padding: 1.1rem;
        border-radius: 50px;
        font-size: 10px;
        letter-spacing: 0.15em;
        font-weight: 600;
        cursor: pointer;
        transition: 0.3s;
        border: 1px solid #d4af37;
        touch-action: manipulation;
    }
    .confirmed {
        background: #d4af37;
        color: black;
        box-shadow: 0 10px 20px rgba(212, 175, 55, 0.15);
    }
    .declined {
        background: transparent;
        color: #d4af37;
    }
    .rsvp-btn:active {
        transform: scale(0.96);
        opacity: 0.8;
    }
    .rsvp-btn:disabled {
        opacity: 0.3;
        filter: grayscale(1);
        cursor: not-allowed;
    }
    .ornament {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }
    .diamond {
        width: 5px;
        height: 5px;
        background: #d4af37;
        transform: rotate(45deg);
    }
    .divider {
        width: 40px;
        height: 1px;
        background: #d4af37;
        margin: 1.5rem auto;
        opacity: 0.3;
    }
    .success-icon {
        width: 60px;
        height: 60px;
        border: 1px solid #d4af37;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #d4af37;
        margin: 0 auto 1.5rem;
    }
    .success-icon svg {
        width: 30px;
        height: 30px;
    }
    .title-small {
        font-family: "Cormorant Garamond", serif;
        font-size: 2.2rem;
        color: white;
        margin-bottom: 0.5rem;
    }
    .next-hint {
        font-size: 8px;
        letter-spacing: 0.3em;
        color: #d4af37;
        opacity: 0.6;
    }
</style>
