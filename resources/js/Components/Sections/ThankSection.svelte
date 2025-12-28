<script>
    import { fade } from "svelte/transition";

    let copied = $state(false);

    function copyToClipboard(text) {
        // Cek apakah navigator.clipboard tersedia (hanya di HTTPS)
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard
                .writeText(text)
                .then(() => {
                    triggerSuccess();
                })
                .catch((err) => {
                    console.error("Gagal copy: ", err);
                    fallbackCopy(text);
                });
        } else {
            // Cara gerilya buat HTTP / Localhost
            fallbackCopy(text);
        }
    }

    function fallbackCopy(text) {
        const textArea = document.createElement("textarea");
        textArea.value = text;

        // Pastiin textarea-nya nggak kelihatan tapi ada di DOM
        textArea.style.position = "fixed";
        textArea.style.left = "-9999px";
        textArea.style.top = "0";
        document.body.appendChild(textArea);

        textArea.focus();
        textArea.select();

        try {
            const successful = document.execCommand("copy");
            if (successful) triggerSuccess();
        } catch (err) {
            console.error("Fallback gagal: ", err);
            alert("Gagal menyalin, silakan salin manual Bre.");
        }

        document.body.removeChild(textArea);
    }

    function triggerSuccess() {
        copied = true;
        setTimeout(() => (copied = false), 2000);
    }
</script>

<div class="thanks-viewport">
    <section class="snap-section">
        <div class="thanks-card">
            <p class="subtitle">CLOSING</p>
            <h1 class="title-serif">Thank You</h1>
            <div class="ornament"><div class="diamond"></div></div>

            <p class="closing-text">
                Merupakan suatu kehormatan dan kebahagiaan bagi kami apabila
                Bapak/Ibu/Saudara/i berkenan hadir untuk memberikan doa restu
                kepada kami.
            </p>

            <p class="final-wish">Sampai jumpa di hari bahagia kami!</p>
            <p class="signature">Maul & Kahfi</p>

            <div class="scroll-hint">
                <p>GIFT INFORMATION</p>
                <div class="arrow-down"></div>
            </div>
        </div>
    </section>

    <section class="snap-section">
        <div class="thanks-card">
            <p class="subtitle">WEDDING GIFT</p>
            <div class="ornament"><div class="diamond"></div></div>

            <p class="gift-desc">
                Bagi keluarga dan kerabat yang ingin memberikan tanda kasih,
                dapat melalui saluran di bawah ini:
            </p>

            <div class="gift-grid">
                <div class="gift-item">
                    <p class="bank-label">BANK MANDIRI</p>
                    <p class="account-number">1340030819584</p>
                    <p class="account-name">A/N M KAHFI KRESNOTUTUKO</p>
                    <button
                        type="button"
                        class="copy-btn"
                        class:copied
                        onclick={(e) => {
                            e.preventDefault(); // Mencegah reload/scroll ga sengaja
                            copyToClipboard("1234567890");
                        }}
                    >
                        {copied ? "BERHASIL DISALIN ✓" : "SALIN NOMOR REKENING"}
                    </button>
                </div>

                <div class="divider-gold"></div>

                <div class="gift-item">
                    <p class="bank-label">QRIS / SAWERIA</p>
                    <div class="qr-frame">
                        <img src="/saweria.png" alt="QR Code" />
                    </div>
                    <p class="account-name">MAUL & KAHFI</p>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
    .thanks-viewport {
        height: 100vh;
        overflow-y: auto;
        scroll-snap-type: y mandatory;
        scrollbar-width: none;
        background: #0a0a0a;
    }
    .thanks-viewport::-webkit-scrollbar {
        display: none;
    }

    .snap-section {
        height: 100vh;
        width: 100%;
        scroll-snap-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        box-sizing: border-box;
    }

    .thanks-card {
        width: 100%;
        max-width: 350px;
        text-align: center;
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(212, 175, 55, 0.15);
        border-radius: 24px;
        padding: 2.5rem 1.5rem;
        backdrop-filter: blur(10px);
    }

    /* TYPOGRAPHY */
    .subtitle {
        font-size: 9px;
        letter-spacing: 0.5em;
        color: #d4af37;
        margin-bottom: 1rem;
        font-family: "Inter", sans-serif;
    }
    .title-serif {
        font-family: "Cormorant Garamond", serif;
        font-size: 3.2rem;
        font-weight: 300;
        color: white;
        margin: 0;
    }
    .closing-text {
        font-size: 13px;
        color: rgba(255, 255, 255, 0.6);
        line-height: 1.8;
        margin: 1.5rem 0;
        font-family: "Inter", sans-serif;
    }
    .final-wish {
        font-family: "Cormorant Garamond", serif;
        font-style: italic;
        font-size: 1.1rem;
        margin-top: 2rem;
        opacity: 0.7;
    }
    .signature {
        font-family: "Cormorant Garamond", serif;
        font-size: 2.2rem;
        color: #d4af37;
        margin-top: 0.5rem;
    }

    /* GIFT STYLES */
    .gift-desc {
        font-size: 11px;
        color: rgba(255, 255, 255, 0.5);
        margin-bottom: 2rem;
        font-family: "Inter", sans-serif;
    }
    .gift-grid {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }
    .bank-label {
        font-size: 10px;
        opacity: 0.5;
        letter-spacing: 0.2em;
        margin-bottom: 0.4rem;
        font-family: "Inter", sans-serif;
    }
    .account-number {
        font-family: "Cormorant Garamond", serif;
        font-size: 1.8rem;
        color: white;
        margin: 0;
    }
    .account-name {
        font-size: 11px;
        color: #d4af37;
        letter-spacing: 0.1em;
        margin-top: 0.2rem;
    }

    .copy-btn {
        margin-top: 1rem;
        background: transparent;
        border: 1px solid #d4af37;
        color: #d4af37;
        padding: 8px 16px;
        font-size: 9px;
        border-radius: 50px;
        cursor: pointer;
        transition: 0.3s;
        font-family: "Inter", sans-serif;
    }
    .copy-btn.copied {
        background: #d4af37;
        color: black;
    }

    .qr-frame {
        background: white;
        padding: 8px;
        width: 130px;
        margin: 0.8rem auto;
        border-radius: 10px;
    }
    .qr-frame img {
        width: 100%;
        display: block;
    }

    .divider-gold {
        width: 40px;
        height: 1px;
        background: #d4af37;
        margin: 0.5rem auto;
        opacity: 0.2;
    }

    /* ORNAMENT */
    .ornament {
        display: flex;
        justify-content: center;
        margin: 1rem 0;
        opacity: 0.4;
    }
    .diamond {
        width: 5px;
        height: 5px;
        background: #d4af37;
        transform: rotate(45deg);
    }

    /* SCROLL HINT */
    .scroll-hint {
        margin-top: 2.5rem;
        opacity: 0.4;
        animation: bounce 2s infinite;
    }
    .scroll-hint p {
        font-size: 8px;
        letter-spacing: 0.3em;
        margin-bottom: 5px;
        color: #d4af37;
    }
    .arrow-down {
        width: 1px;
        height: 30px;
        background: #d4af37;
        margin: 0 auto;
    }

    @keyframes bounce {
        0%,
        20%,
        50%,
        80%,
        100% {
            transform: translateY(0);
        }
        40% {
            transform: translateY(-5px);
        }
        60% {
            transform: translateY(-3px);
        }
    }
</style>
