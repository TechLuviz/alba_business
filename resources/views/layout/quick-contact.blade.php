@php($qc = \App\Models\QuickContact::current())
@php($qcShow = $qc->exists && $qc->is_active && ($qc->email || $qc->phone || $qc->whatsapp))
<aside class="quick-contact" aria-label="Quick contact">
@if($qcShow)
        @if($qc->whatsapp)
            <a class="qc-btn qc-wa" href="https://wa.me/{{ ltrim(\App\Models\QuickContact::digits($qc->whatsapp), '+') }}" target="_blank" rel="noopener noreferrer" aria-label="Chat on WhatsApp" style="--i:0">
                <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2zm0 18.2c-1.5 0-3-.4-4.2-1.2l-.3-.2-3 .8.8-2.9-.2-.3A8.2 8.2 0 1 1 12 20.2zm4.5-6.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1-.7-.3-1.5-.8-2.1-1.4-.6-.6-1-1.3-1.4-2-.1-.2 0-.4.1-.5l.4-.5.2-.4c.1-.1 0-.3 0-.4l-.8-1.8c-.2-.4-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.6.6-.9 1.3-.9 2.1.1 1 .5 1.9 1.1 2.7 1.3 1.8 2.9 3.1 4.9 3.9.6.2 1.2.4 1.9.4.8 0 1.6-.6 1.9-1.3.1-.3.1-.6.1-.8l-.2-.2z"/></svg>
                <span>WhatsApp</span>
            </a>
        @endif
        @if($qc->phone)
            <a class="qc-btn qc-call" href="tel:{{ \App\Models\QuickContact::digits($qc->phone) }}" aria-label="Call us" style="--i:1">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2z"/></svg>
                <span>Call us</span>
            </a>
        @endif
        @if($qc->email)
            <a class="qc-btn qc-mail" href="mailto:{{ $qc->email }}" aria-label="Email us" style="--i:2">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg>
                <span>Email us</span>
            </a>
        @endif
@endif
    <button type="button" class="qc-btn qc-top" id="backToTop" aria-label="Back to top" hidden>
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5M5 12l7-7 7 7"/></svg>
        <span>Back to top</span>
    </button>
</aside>
<script>
(function () {
    var b = document.getElementById('backToTop');
    if (!b) return;
    var shown = false, ticking = false;
    function update() {
        ticking = false;
        var show = (window.pageYOffset || document.documentElement.scrollTop) > 300;
        if (show === shown) return;
        shown = show;
        if (show) { b.hidden = false; requestAnimationFrame(function () { b.classList.add('is-visible'); }); }
        else { b.classList.remove('is-visible'); setTimeout(function () { if (!shown) b.hidden = true; }, 300); }
    }
    window.addEventListener('scroll', function () { if (!ticking) { ticking = true; requestAnimationFrame(update); } }, { passive: true });
    b.addEventListener('click', function () {
        var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        window.scrollTo({ top: 0, behavior: reduce ? 'auto' : 'smooth' });
    });
    update();
})();
</script>
