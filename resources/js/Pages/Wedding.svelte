<script>
    import { onMount } from "svelte";
    import { fly, fade } from "svelte/transition";
    import Curtain from "../Components/Curtain.svelte";
    import LoadingScreen from "../Components/LoadingScreen.svelte";
    import EventSection from "../Components/Sections/EventSection.svelte";
    import StorySection from "../Components/Sections/StorySection.svelte";
    import GallerySection from "../Components/Sections/GallerySection.svelte";
    import RSVPSection from "../Components/Sections/RSVPSection.svelte";
    import MusicPlayer from "../Components/MusicPlayer.svelte";
    import ThanksSection from "../Components/Sections/ThankSection.svelte";

    // 1. TANGKAP DATA DARI CONTROLLER (INERTIA PROPS)
    let { guestData, galleryData } = $props();

    let appState = $state("curtain");
    let direction = $state("forward");
    let curtainOpen = $state(false);
    let isAnimating = $state(false);
    let sectionScrollProgress = $state(0);
    let isInnerScrolling = $state(false);

    let touchStartX = $state(0);
    let touchCurrentX = $state(0);
    let isDragging = $state(false);
    let swipeOffset = $state(0);
    let musicRef = $state(null);

    const sections = ["welcome", "story", "event", "gallery", "rsvp", "thanks"];
    let currentSectionIndex = $state(-1);
    let currentSection = $derived(sections[currentSectionIndex]);
    let showArrows = $state(false);

    // Reset Lock tiap ganti halaman
    $effect(() => {
        currentSectionIndex;
        isInnerScrolling = false;
        isAnimating = false;
        sectionScrollProgress = 0;
    });

    function onCurtainComplete() {
        if (appState === "curtain") {
            appState = "loading";
            if (musicRef) musicRef.playMusic();
        }
    }

    onMount(() => {
        // Kita gak butuh fetch manual lagi Bre,
        // karena data udah ada di props {guestData}
        curtainOpen = true;
    });

    function onLoadingComplete() {
        appState = "sections";
        currentSectionIndex = 0;
    }

    // Navigasi Logic
    function nextSection() {
        if (isAnimating || isInnerScrolling) return;
        if (currentSectionIndex < sections.length - 1) {
            isAnimating = true;
            direction = "forward";
            currentSectionIndex++;
            setTimeout(() => (isAnimating = false), 600);
        }
    }

    function prevSection() {
        if (isAnimating || currentSectionIndex <= 0) return;
        isAnimating = true;
        direction = "backward";
        currentSectionIndex--;
        setTimeout(() => (isAnimating = false), 600);
    }

    function jumpToSection(index) {
        if (isAnimating || index === currentSectionIndex) return;
        isAnimating = true;
        direction = index > currentSectionIndex ? "forward" : "backward";
        currentSectionIndex = index;
        setTimeout(() => (isAnimating = false), 600);
    }

    // Touch Handlers
    function handleTouchStart(e) {
        if (!isAnimating) {
            touchStartX = e.touches[0].clientX;
            isDragging = true;
        }
    }
    function handleTouchMove(e) {
        if (!isDragging || isAnimating || isInnerScrolling) return;
        touchCurrentX = e.touches[0].clientX;
        const diff = touchCurrentX - touchStartX;
        swipeOffset = Math.max(-100, Math.min(100, diff * 0.3));
    }
    function handleTouchEnd() {
        if (!isDragging) return;
        const diff = touchCurrentX - touchStartX;
        if (Math.abs(diff) > 50) {
            if (diff > 0) prevSection();
            else nextSection();
        }
        isDragging = false;
        swipeOffset = 0;
    }
</script>

<Curtain bind:isOpen={curtainOpen} onComplete={onCurtainComplete} />

{#if appState === "loading"}
    <LoadingScreen
        guestName={guestData?.name}
        logoUrl="/mk.png"
        onComplete={onLoadingComplete}
    />
{/if}

{#if appState === "sections"}
    <main
        class="wedding-container"
        ontouchstart={handleTouchStart}
        ontouchmove={handleTouchMove}
        ontouchend={handleTouchEnd}
        onmouseenter={() => (showArrows = true)}
        onmouseleave={() => (showArrows = false)}
        in:fly={{ y: 50, duration: 500, opacity: 0 }}
    >
        <div class="progress-bars">
            {#each sections as _, i}
                <div
                    class="progress-bar"
                    class:completed={i < currentSectionIndex}
                    class:active={i === currentSectionIndex}
                    onclick={() => jumpToSection(i)}
                >
                    <div class="progress-fill"></div>
                </div>
            {/each}
        </div>

        <MusicPlayer bind:this={musicRef} url="/music/background.mp3" />

        {#if showArrows && !isAnimating}
            {#if currentSectionIndex > 0}
                <button class="nav-arrow left" onclick={prevSection}>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        ><path d="M15 19l-7-7 7-7" stroke-width="2" /></svg
                    >
                </button>
            {/if}
            {#if currentSectionIndex < sections.length - 1}
                <button class="nav-arrow right" onclick={nextSection}>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        ><path d="M9 5l7 7-7 7" stroke-width="2" /></svg
                    >
                </button>
            {/if}
        {/if}

        <div
            class="section-wrapper"
            style="transform: translateX({swipeOffset}px);"
        >
            {#if currentSection === "welcome"}
                <div class="section" in:fade={{ duration: 1000 }}>
                    <div class="welcome-hero">
                        <p class="subtitle">THE WEDDING CELEBRATION</p>
                        <div class="names-box">
                            <h1 class="name-main">Maul</h1>
                            <div class="ampersand">
                                <div class="line"></div>
                                <span>&</span>
                                <div class="line"></div>
                            </div>
                            <h1 class="name-main">Kahfi</h1>
                        </div>
                        <div class="date-loc">
                            <p class="date">SABTU, 24 MEI 2026</p>
                            <div class="ornament">
                                <div class="diamond"></div>
                            </div>
                            <p class="loc">CIREBON, INDONESIA</p>
                        </div>
                    </div>
                </div>
            {:else if currentSection === "story"}
                <div class="section" in:fade>
                    <StorySection
                        bind:scrollProgress={sectionScrollProgress}
                        bind:isLocked={isInnerScrolling}
                    />
                </div>
            {:else if currentSection === "event"}
                <div class="section" in:fade>
                    <EventSection
                        bind:scrollProgress={sectionScrollProgress}
                        bind:isLocked={isInnerScrolling}
                    />
                </div>
            {:else if currentSection === "gallery"}
                <div class="section" in:fade>
                    <GallerySection
                        {galleryData}
                        bind:scrollProgress={sectionScrollProgress}
                        bind:isLocked={isInnerScrolling}
                    />
                </div>
            {:else if currentSection === "rsvp"}
                <div class="section" in:fade>
                    <RSVPSection {guestData} />
                </div>
            {:else if currentSection === "thanks"}
                <div class="section" in:fade>
                    <ThanksSection {guestData} />
                </div>
            {/if}
        </div>
    </main>
{/if}

<style>
    /* ... Style lo tetep sama ... */
    @import url("https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500&family=Inter:wght@300;400&display=swap");

    :global(body) {
        margin: 0;
        font-family: "Inter", sans-serif;
        background: #0a0a0a;
        color: white;
        overflow: hidden;
    }

    .wedding-container {
        position: relative;
        height: 100vh;
        width: 100%;
        overflow: hidden;
    }

    .progress-bars {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        display: flex;
        gap: 6px;
        padding: 12px;
        z-index: 100;
    }
    .progress-bar {
        flex: 1;
        height: 2px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 2px;
        cursor: pointer;
    }
    .progress-fill {
        height: 100%;
        background: #d4af37;
        width: 0%;
        transition: width 0.4s;
    }
    .progress-bar.active .progress-fill,
    .progress-bar.completed .progress-fill {
        width: 100%;
    }

    .welcome-hero {
        text-align: center;
    }
    .subtitle {
        font-size: 10px;
        letter-spacing: 0.5em;
        color: #d4af37;
        margin-bottom: 2rem;
    }
    .name-main {
        font-family: "Cormorant Garamond", serif;
        font-size: 4rem;
        font-weight: 300;
        margin: 0;
        line-height: 1;
    }

    .ampersand {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1.5rem;
        margin: 1.5rem 0;
    }
    .ampersand .line {
        width: 30px;
        height: 1px;
        background: rgba(212, 175, 55, 0.3);
    }
    .ampersand span {
        font-family: "Cormorant Garamond", serif;
        font-style: italic;
        font-size: 2rem;
        opacity: 0.6;
    }

    .date-loc {
        margin-top: 3rem;
    }
    .date {
        font-size: 12px;
        letter-spacing: 0.3em;
        opacity: 0.8;
    }
    .loc {
        font-size: 10px;
        letter-spacing: 0.2em;
        opacity: 0.5;
    }
    .ornament {
        display: flex;
        justify-content: center;
        margin: 1.5rem 0;
    }
    .diamond {
        width: 6px;
        height: 6px;
        background: #d4af37;
        transform: rotate(45deg);
    }

    .nav-arrow {
        position: fixed;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(212, 175, 55, 0.2);
        color: white;
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 90;
        cursor: pointer;
    }
    .nav-arrow.left {
        left: 20px;
    }
    .nav-arrow.right {
        right: 20px;
    }

    .section-wrapper {
        position: relative;
        height: 100vh;
        width: 100%;
        transition: transform 0.2s ease-out;
    }
    .section {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
    }

    @media (max-width: 768px) {
        .name-main {
            font-size: 3rem;
        }
        .nav-arrow {
            display: none;
        }
    }
</style>
