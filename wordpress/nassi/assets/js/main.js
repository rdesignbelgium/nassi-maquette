/* Thème Nassi — interactions (navigation, compte à rebours, galerie, pastilles couleur, Cercle). */
(function () {
  'use strict';
  const $ = (s, c) => (c || document).querySelector(s);
  const $$ = (s, c) => [...(c || document).querySelectorAll(s)];
  const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* Toast */
  const toastEl = $('#toast');
  function toast(msg) { if (!toastEl) return; toastEl.textContent = msg; toastEl.classList.add('on'); clearTimeout(toastEl._t); toastEl._t = setTimeout(() => toastEl.classList.remove('on'), 2200); }

  /* Navigation */
  const nav = $('#nav'), sticky = $('#stickyCta'), burger = $('#burger'), links = $('#navLinks');
  addEventListener('scroll', () => { nav && nav.classList.toggle('scrolled', scrollY > 20); sticky && sticky.classList.toggle('show', scrollY > 500); }, { passive: true });
  if (burger && links) {
    burger.addEventListener('click', () => { const o = links.classList.toggle('open'); burger.setAttribute('aria-expanded', o); });
    links.addEventListener('click', () => links.classList.remove('open'));
  }

  /* Bandeau défilant */
  const mq = $('#marquee'); if (mq) mq.innerHTML += mq.innerHTML;

  /* Compte à rebours */
  const cd = { d: $('#cdD'), h: $('#cdH'), m: $('#cdM'), s: $('#cdS') };
  if (cd.d && window.NASSI && NASSI.concertAt) {
    const target = new Date(NASSI.concertAt).getTime();
    const tick = () => {
      let diff = Math.max(0, target - Date.now());
      const d = Math.floor(diff / 864e5); diff -= d * 864e5;
      const h = Math.floor(diff / 36e5); diff -= h * 36e5;
      const m = Math.floor(diff / 6e4); const s = Math.floor((diff - m * 6e4) / 1e3);
      cd.d.textContent = String(d).padStart(2, '0'); cd.h.textContent = String(h).padStart(2, '0');
      cd.m.textContent = String(m).padStart(2, '0'); cd.s.textContent = String(s).padStart(2, '0');
    };
    tick(); setInterval(tick, 1000);
  }

  /* Tracklist : état visuel « en lecture » */
  const tl = $('#tracklist');
  if (tl) tl.addEventListener('click', e => { const t = e.target.closest('.track'); if (!t) return; $$('.track', tl).forEach(x => x.classList.toggle('playing', x === t && !x.classList.contains('playing'))); });

  /* Grain du hero */
  const g = $('#grain');
  if (g && g.getContext) { const ctx = g.getContext('2d'); g.width = 320; g.height = 320; const img = ctx.createImageData(320, 320); for (let i = 0; i < img.data.length; i += 4) { const v = Math.random() * 255; img.data[i] = img.data[i + 1] = img.data[i + 2] = v; img.data[i + 3] = 40; } ctx.putImageData(img, 0, 0); g.style.width = '100%'; g.style.height = '100%'; g.style.imageRendering = 'pixelated'; }

  /* Apparitions au scroll (seuls les blocs hors écran au chargement sont animés) */
  const els = $$('.reveal');
  els.forEach(el => { if (el.getBoundingClientRect().top < innerHeight) el.classList.add('in'); });
  if (reduce || !('IntersectionObserver' in window)) els.forEach(el => el.classList.add('in'));
  else { const io = new IntersectionObserver(en => en.forEach(x => { if (x.isIntersecting) { x.target.classList.add('in'); io.unobserve(x.target); } }), { threshold: .12 }); els.forEach(el => io.observe(el)); }

  /* Pastilles couleur sur les cartes produit */
  document.addEventListener('click', e => {
    const sw = e.target.closest('.sw'); if (!sw) return;
    e.preventDefault();
    const card = sw.closest('.product');
    $$('.sw', card).forEach(x => x.classList.toggle('on', x === sw));
    const img = $('.ph img', card); if (img && sw.dataset.img) img.src = sw.dataset.img;
    const lbl = $('.swl', card); if (lbl) lbl.textContent = sw.dataset.c;
    $$('a.ph, h3 a, a.add', card).forEach(a => { if (sw.dataset.href) a.href = sw.dataset.href; });
  });

  /* Le Cercle */
  const form = $('#joinForm');
  if (form) form.addEventListener('submit', async e => {
    e.preventDefault();
    const email = $('#jEmail', form), city = $('#jCity', form), hp = $('[name=hp]', form);
    if (!email.value || !/.+@.+\..+/.test(email.value)) { email.focus(); toast('Entrez une adresse e-mail valide'); return; }
    const btn = $('button[type=submit]', form); btn.disabled = true;
    try {
      const r = await fetch(NASSI.rest + 'cercle', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': NASSI.nonce }, body: JSON.stringify({ email: email.value, city: city ? city.value : '', hp: hp ? hp.value : '' }) });
      if (!r.ok) throw new Error();
      form.classList.add('sent');
    } catch (err) { toast('Une erreur est survenue, réessayez dans un instant'); btn.disabled = false; }
  });

  /* Panier : message après ajout AJAX de WooCommerce */
  if (window.jQuery) jQuery(document.body).on('added_to_cart', () => toast('Ajouté au panier'));
})();
