<script>
    import { onMount } from "svelte";

    let { isOpen = $bindable(false), onComplete = () => {} } = $props();

    let leftCurtain = $state(0);
    let rightCurtain = $state(0);
    let isVisible = $state(true);

    onMount(() => {
        if (isOpen) openCurtains();
    });

    function openCurtains() {
        isVisible = true;

        requestAnimationFrame(() => {
            leftCurtain = -105;
            rightCurtain = 105;
        });

        setTimeout(() => {
            isVisible = false;
            onComplete();
        }, 2600);
    }

    function closeCurtains() {
        isVisible = true;

        requestAnimationFrame(() => {
            leftCurtain = 0;
            rightCurtain = 0;
        });

        setTimeout(onComplete, 2600);
    }

    $effect(() => {
        if (isOpen && leftCurtain === 0 && isVisible) openCurtains();
        if (!isOpen && leftCurtain !== 0) closeCurtains();
    });
</script>

{#if isVisible}
    <div class="curtain-container">
        <div
            class="curtain curtain-left"
            style="transform: translate3d({leftCurtain}%,0,0);"
        >
            <div class="fabric"></div>
        </div>

        <div
            class="curtain curtain-right"
            style="transform: translate3d({rightCurtain}%,0,0);"
        >
            <div class="fabric"></div>
        </div>
    </div>
{/if}

<style>
    .curtain-container {
        position: fixed;
        inset: 0;
        z-index: 9999;
        background: #000;
        perspective: 1200px;
    }

    /* ===============================
   CURTAIN BASE
================================ */
    .curtain {
        position: absolute;
        top: 0;
        bottom: 0;
        width: 50%;
        overflow: hidden;

        transition: transform 5.6s cubic-bezier(0.22, 1, 0.36, 1);
    }

    .curtain-left {
        left: 0;
        transform-origin: left center;
    }

    .curtain-right {
        right: 0;
        transform-origin: right center;
    }

    /* ===============================
   FABRIC REALISM
================================ */
    .fabric {
        position: absolute;
        inset: -10%;

        background:
        /* lipatan kain */
            repeating-linear-gradient(
                90deg,
                rgba(255, 255, 255, 0.08) 0px,
                rgba(255, 255, 255, 0.02) 18px,
                rgba(0, 0, 0, 0.25) 36px
            ),
            /* depth */
                linear-gradient(
                    to bottom,
                    #7a0000 0%,
                    #5a0000 40%,
                    #3b0000 100%
                );

        filter: contrast(1.05) saturate(1.1);
    }

    /* ===============================
   LIGHT & SHADOW
================================ */
    .fabric::before {
        content: "";
        position: absolute;
        inset: 0;

        background: linear-gradient(
            120deg,
            rgba(255, 255, 255, 0.35),
            transparent 40%
        );

        mix-blend-mode: overlay;
    }

    .fabric::after {
        content: "";
        position: absolute;
        inset: 0;

        box-shadow:
            inset 0 0 80px rgba(0, 0, 0, 0.8),
            inset 0 0 200px rgba(0, 0, 0, 0.6);
    }

    /* ===============================
   EDGE SHADOW (CENTER)
================================ */
    .curtain-left .fabric::after {
        box-shadow:
            inset -40px 0 50px rgba(0, 0, 0, 0.9),
            inset 0 0 200px rgba(0, 0, 0, 0.6);
    }

    .curtain-right .fabric::after {
        box-shadow:
            inset 40px 0 50px rgba(0, 0, 0, 0.9),
            inset 0 0 200px rgba(0, 0, 0, 0.6);
    }
</style>
