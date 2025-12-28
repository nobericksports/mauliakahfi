<script>
    import { onMount } from "svelte";
    import { fade, fly, scale } from "svelte/transition";
    import { quintOut, expoOut } from "svelte/easing";

    let mounted = $state(false);
    let showLines = $state(false);
    let showNames = $state(false);
    let showDetails = $state(false);
    let showButton = $state(false);
    let musicPlaying = $state(false);

    onMount(() => {
        mounted = true;
        setTimeout(() => (showLines = true), 400);
        setTimeout(() => (showNames = true), 900);
        setTimeout(() => (showDetails = true), 1600);
        setTimeout(() => (showButton = true), 2200);
    });

    function toggleMusic() {
        musicPlaying = !musicPlaying;
    }

    function openInvitation() {
        console.log("Opening invitation...");
    }
</script>

{#if mounted}
    <main
        class="relative min-h-screen bg-gradient-to-br from-black via-[#0a0a0a] to-[#1a1a1a] overflow-hidden"
    >
        <!-- Hero Photo Background -->
        <div class="absolute inset-0 z-0">
            <div class="absolute inset-0 bg-black/60"></div>
            <!-- Placeholder untuk foto - ganti dengan foto lo -->
            <div
                class="w-full h-full bg-cover bg-center opacity-40"
                style="background-image: url('https://images.unsplash.com/photo-1519741497674-611481863552?w=1200'); filter: grayscale(100%);"
            ></div>
        </div>

        <!-- Animated Overlay Pattern -->
        <div class="absolute inset-0 z-[1]">
            {#each Array(30) as _, i}
                <div
                    class="absolute w-px h-px bg-white rounded-full"
                    style="
                        left: {Math.random() * 100}%;
                        top: {Math.random() * 100}%;
                        animation: twinkle {2 +
                        Math.random() * 4}s ease-in-out infinite;
                        animation-delay: {Math.random() * 3}s;
                        opacity: {0.1 + Math.random() * 0.3};
                    "
                ></div>
            {/each}
        </div>

        <!-- Music Toggle -->
        <button
            on:click={toggleMusic}
            class="absolute top-6 right-6 z-50 group"
            in:fade={{ delay: 500, duration: 400 }}
        >
            <div
                class="w-10 h-10 flex items-center justify-center border border-white/20 backdrop-blur-sm
                        hover:border-white/40 hover:bg-white/5 transition-all duration-300"
            >
                <svg
                    class="w-4 h-4 text-white/70 group-hover:text-white transition-colors {musicPlaying
                        ? 'animate-pulse'
                        : ''}"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                >
                    <path
                        d="M18 3a1 1 0 00-1.196-.98l-10 2A1 1 0 006 5v9.114A4.369 4.369 0 005 14c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V7.82l8-1.6v5.894A4.37 4.37 0 0015 12c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V3z"
                    />
                </svg>
            </div>
        </button>

        <!-- Main Content -->
        <div
            class="relative z-10 min-h-screen flex flex-col items-center justify-center px-4"
        >
            <!-- Expanding Lines -->
            {#if showLines}
                <div
                    class="absolute inset-0 flex items-center justify-center pointer-events-none"
                >
                    <!-- Horizontal line -->
                    <div
                        class="absolute w-0 h-px bg-white/30"
                        in:fly={{ x: 0, duration: 800, easing: expoOut }}
                        style="animation: expandHorizontal 0.8s ease-out forwards;"
                    ></div>
                    <!-- Vertical line -->
                    <div
                        class="absolute w-px h-0 bg-white/30"
                        in:fly={{ y: 0, duration: 800, easing: expoOut }}
                        style="animation: expandVertical 0.8s ease-out forwards; animation-delay: 0.3s;"
                    ></div>
                </div>
            {/if}

            <!-- Content Container -->
            <div class="text-center max-w-4xl">
                <!-- Pre-title -->
                {#if showNames}
                    <p
                        class="text-white/50 tracking-[0.4em] text-[10px] uppercase mb-8 font-light"
                        in:fade={{ duration: 600 }}
                    >
                        Wedding Celebration
                    </p>
                {/if}

                <!-- Names dengan Split & Merge Animation -->
                {#if showNames}
                    <div class="mb-12 overflow-hidden">
                        <div
                            class="flex items-center justify-center gap-6 md:gap-8"
                        >
                            <!-- Maul dari kiri -->
                            <h1
                                class="text-3xl md:text-4xl font-serif text-white tracking-wide"
                                in:fly={{
                                    x: -100,
                                    duration: 700,
                                    delay: 100,
                                    easing: quintOut,
                                }}
                            >
                                Maul
                            </h1>

                            <!-- Ampersand -->
                            <span
                                class="text-xl md:text-2xl text-white/40 font-light"
                                in:scale={{
                                    duration: 500,
                                    delay: 400,
                                    start: 0.3,
                                    easing: quintOut,
                                }}
                            >
                                &
                            </span>

                            <!-- Kahfi dari kanan -->
                            <h1
                                class="text-3xl md:text-4xl font-serif text-white tracking-wide"
                                in:fly={{
                                    x: 100,
                                    duration: 700,
                                    delay: 100,
                                    easing: quintOut,
                                }}
                            >
                                Kahfi
                            </h1>
                        </div>
                    </div>
                {/if}

                <!-- Details -->
                {#if showDetails}
                    <div class="space-y-6 mb-12" in:fade={{ duration: 600 }}>
                        <!-- Date -->
                        <div class="flex items-center justify-center gap-3">
                            <div class="w-8 h-px bg-white/20"></div>
                            <p
                                class="text-white/80 tracking-[0.3em] text-xs font-light"
                            >
                                24.12.2025
                            </p>
                            <div class="w-8 h-px bg-white/20"></div>
                        </div>

                        <!-- Location -->
                        <p
                            class="text-white/40 text-xs tracking-[0.2em] uppercase"
                        >
                            Jakarta
                        </p>
                    </div>
                {/if}

                <!-- CTA Button -->
                {#if showButton}
                    <button
                        on:click={openInvitation}
                        class="group relative overflow-hidden border border-white/30 hover:border-white/60 transition-all duration-500"
                        in:scale={{
                            duration: 500,
                            start: 0.8,
                            easing: quintOut,
                        }}
                    >
                        <!-- Hover shimmer -->
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent
                                    -translate-x-full group-hover:translate-x-full transition-transform duration-1000"
                        ></div>

                        <!-- Button background -->
                        <div
                            class="absolute inset-0 bg-white/0 group-hover:bg-white/5 transition-colors duration-300"
                        ></div>

                        <span
                            class="relative block px-10 py-3 text-white/90 group-hover:text-white
                                     font-light tracking-[0.3em] text-[11px] uppercase transition-colors duration-300"
                        >
                            Open Invitation
                        </span>
                    </button>
                {/if}
            </div>

            <!-- Corner Ornaments -->
            {#if showLines}
                <div
                    class="absolute top-0 left-0 w-24 h-24 pointer-events-none"
                    in:fade={{ delay: 700, duration: 800 }}
                >
                    <div
                        class="absolute top-6 left-6 w-full h-px bg-white/10"
                    ></div>
                    <div
                        class="absolute top-6 left-6 w-px h-full bg-white/10"
                    ></div>
                </div>
                <div
                    class="absolute bottom-0 right-0 w-24 h-24 pointer-events-none"
                    in:fade={{ delay: 700, duration: 800 }}
                >
                    <div
                        class="absolute bottom-6 right-6 w-full h-px bg-white/10"
                    ></div>
                    <div
                        class="absolute bottom-6 right-6 w-px h-full bg-white/10"
                    ></div>
                </div>
            {/if}
        </div>
    </main>
{/if}

<style>
    @keyframes expandHorizontal {
        from {
            width: 0;
        }
        to {
            width: 100vw;
        }
    }

    @keyframes expandVertical {
        from {
            height: 0;
        }
        to {
            height: 100vh;
        }
    }

    @keyframes twinkle {
        0%,
        100% {
            opacity: 0.1;
            transform: scale(1);
        }
        50% {
            opacity: 0.6;
            transform: scale(1.5);
        }
    }

    @import url("https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500&family=Inter:wght@300;400&display=swap");

    :global(body) {
        margin: 0;
        font-family: "Inter", sans-serif;
        background-color: #000;
    }

    h1 {
        font-family: "Cormorant Garamond", serif;
        font-weight: 300;
    }
</style>
