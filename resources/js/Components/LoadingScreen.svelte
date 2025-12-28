<script>
    import { onMount } from "svelte";
    import { fade, fly } from "svelte/transition";

    let {
        guestName = null,
        logoUrl = "/mk.png",
        onComplete = () => {},
    } = $props();

    let progress = $state(0);
    let currentTip = $state(0);
    let showContent = $state(false);

    const tips = [
        "Love is patient, love is kind...",
        "Two souls, one heart...",
        "Forever starts here...",
        "A moment to remember...",
        "Preparing your invitation...",
    ];

    onMount(() => {
        showContent = true;
        const interval = setInterval(() => {
            progress += Math.random() * 8 + 2;
            if (progress >= 100) {
                progress = 100;
                clearInterval(interval);
                setTimeout(() => {
                    onComplete();
                }, 1000);
            }
        }, 300);

        const tipInterval = setInterval(() => {
            currentTip = (currentTip + 1) % tips.length;
        }, 4000);

        return () => {
            clearInterval(interval);
            clearInterval(tipInterval);
        };
    });
</script>

{#if showContent}
    <div class="loading-screen" transition:fade={{ duration: 800 }}>
        <div class="bg-image"><div class="bg-overlay"></div></div>

        <div class="particles">
            {#each Array(20) as _, i}
                <div
                    class="particle"
                    style="
                        left: {Math.random() * 100}%;
                        animation-delay: {Math.random() * 5}s;
                        animation-duration: {4 + Math.random() * 6}s;
                        background: rgba(212, 175, 55, {0.2 +
                        Math.random() * 0.4});
                    "
                ></div>
            {/each}
        </div>

        <div class="content">
            <div class="credits-title" in:fade={{ delay: 200, duration: 800 }}>
                <div class="line"></div>
                <div class="presents-wrapper">
                    <p class="presents">P R E S E N T S</p>
                </div>
                <div class="line"></div>
            </div>

            <div
                class="logo-container"
                in:fade={{ delay: 600, duration: 1200 }}
            >
                <div class="logo-wrapper">
                    <img
                        src={logoUrl}
                        alt="Wedding Logo"
                        class="wedding-logo"
                        onerror={(e) => {
                            e.target.style.display = "none";
                            e.target.parentElement.innerHTML =
                                '<div class="logo-fallback"><p class="names-fallback"><span>Maul</span> <span class="amp">&</span> <span>Kahfi</span></p></div>';
                        }}
                    />
                </div>
            </div>

            <div
                class="subtitle-container"
                in:fade={{ delay: 1000, duration: 800 }}
            >
                <p class="subtitle">The Wedding Celebration</p>
                <p class="date">24 MEI 2026</p>
            </div>

            {#if guestName}
                <div
                    class="guest-welcome"
                    in:fly={{ y: 20, delay: 1400, duration: 800 }}
                >
                    <p class="welcome-text">SPECIAL INVITATION FOR</p>
                    <p class="guest-name">{guestName}</p>
                    <div class="ornament-small">
                        <div class="diamond"></div>
                    </div>
                </div>
            {/if}

            <div
                class="progress-container"
                in:fade={{ delay: 1200, duration: 600 }}
            >
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {progress}%"></div>
                </div>
                <p class="progress-text">
                    Mempersiapkan Kebahagiaan... {Math.floor(progress)}%
                </p>
            </div>

            <div
                class="tips-container"
                in:fade={{ delay: 1600, duration: 600 }}
            >
                {#key currentTip}
                    <p class="tip" in:fade={{ duration: 600 }}>
                        {tips[currentTip]}
                    </p>
                {/key}
            </div>
        </div>
    </div>
{/if}

<style>
    .loading-screen {
        position: fixed;
        inset: 0;
        z-index: 9999;
        background: #0a0a0a;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .bg-image {
        position: absolute;
        inset: 0;
        background-image: radial-gradient(
            circle at 2px 2px,
            rgba(212, 175, 55, 0.05) 1px,
            transparent 0
        );
        background-size: 40px 40px;
    }
    .bg-overlay {
        position: absolute;
        inset: 0;
        background: radial-gradient(
            circle at center,
            transparent 0%,
            #0a0a0a 100%
        );
    }

    .particles {
        position: absolute;
        inset: 0;
    }
    .particle {
        position: absolute;
        width: 3px;
        height: 3px;
        border-radius: 50%;
        filter: blur(1px);
        animation: floatUp 10s linear infinite;
        opacity: 0;
    }

    @keyframes floatUp {
        0% {
            transform: translateY(100vh) scale(0);
            opacity: 0;
        }
        50% {
            opacity: 0.8;
        }
        100% {
            transform: translateY(-10vh) scale(1);
            opacity: 0;
        }
    }

    .content {
        position: relative;
        z-index: 10;
        text-align: center;
        width: 100%;
        max-width: 450px;
        padding: 2rem;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    /* FIX: PRESENTS */
    .credits-title {
        width: 100%;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1rem;
    }

    .presents-wrapper {
        flex-shrink: 0;
    }

    .presents {
        color: #d4af37;
        font-size: 9px;
        font-weight: 300;
        font-family: "Inter", sans-serif; /* Balikin ke Sans */
        letter-spacing: 0.6em; /* Dikecilin dikit biar gak meluber */
        white-space: nowrap; /* KUNCI: Biar gak turun ke bawah */
        margin: 0;
        text-align: center;
    }

    .line {
        flex-grow: 1;
        max-width: 60px;
        height: 1px;
        background: linear-gradient(to var(--dir, right), transparent, #d4af37);
    }
    .credits-title .line:last-child {
        --dir: left;
    }

    /* FIX: LOGO CENTER */
    .logo-container {
        width: 100%;
        display: flex;
        justify-content: center;
        margin-bottom: 2.5rem;
        min-height: 100px;
    }

    .logo-wrapper {
        width: 100%;
        display: flex;
        justify-content: center;
    }

    .wedding-logo {
        display: block;
        margin: 0 auto; /* KUNCI CENTER */
        max-width: 180px;
        height: auto;
        filter: brightness(1.2);
    }

    .logo-fallback {
        width: 100%;
        display: flex;
        justify-content: center;
    }
    .names-fallback {
        font-family: "Cormorant Garamond", serif;
        font-size: 2.5rem;
        color: #fff;
        font-weight: 300;
        margin: 0;
    }
    .names-fallback span {
        color: #d4af37;
    }
    .names-fallback .amp {
        color: #fff;
        opacity: 0.3;
        font-size: 1.5rem;
        margin: 0 10px;
    }

    .subtitle-container {
        width: 100%;
    }
    .subtitle {
        color: rgba(255, 255, 255, 0.5);
        font-size: 10px;
        letter-spacing: 0.4em;
        text-transform: uppercase;
        margin-bottom: 0.5rem;
    }
    .date {
        color: #d4af37;
        font-size: 12px;
        letter-spacing: 0.3em;
        font-weight: 400;
    }

    .guest-welcome {
        width: 100%;
        margin: 2.5rem 0;
        padding: 1.5rem 0;
        border-top: 1px solid rgba(212, 175, 55, 0.15);
        border-bottom: 1px solid rgba(212, 175, 55, 0.15);
    }
    .welcome-text {
        color: rgba(255, 255, 255, 0.4);
        font-size: 8px;
        letter-spacing: 0.3em;
        margin-bottom: 0.8rem;
        font-family: "Inter", sans-serif; /* Biar gak Serif semua */
        font-style: normal;
    }
    .guest-name {
        color: #fff;
        font-size: 1.4rem;
        font-family: "Cormorant Garamond", serif;
        font-style: italic;
    }
    .ornament-small {
        display: flex;
        justify-content: center;
        margin-top: 1rem;
        opacity: 0.5;
    }
    .diamond {
        width: 4px;
        height: 4px;
        background: #d4af37;
        transform: rotate(45deg);
    }

    .progress-container {
        width: 100%;
        margin: 2rem 0;
    }
    .progress-bar {
        width: 100%;
        height: 2px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 2px;
        overflow: hidden;
        margin-bottom: 1rem;
    }
    .progress-fill {
        height: 100%;
        background: linear-gradient(to right, #9a7b2c, #d4af37, #9a7b2c);
        transition: width 0.4s ease-out;
        box-shadow: 0 0 15px rgba(212, 175, 55, 0.4);
    }
    .progress-text {
        color: rgba(212, 175, 55, 0.6);
        font-size: 8px;
        letter-spacing: 0.2em;
        text-transform: uppercase;
        font-family: "Inter", sans-serif; /* Biar gak Serif semua */
        font-style: normal;
    }

    .tips-container {
        width: 100%;
        min-height: 20px;
    }
    .tip {
        color: rgba(255, 255, 255, 0.3);
        font-size: 11px;
        font-style: italic;
        font-family: "Cormorant Garamond", serif;
    }

    @media (max-width: 768px) {
        .content {
            max-width: 320px;
        }
        .names-fallback {
            font-size: 2.2rem;
        }
        .wedding-logo {
            max-width: 160px;
        }
    }
</style>
