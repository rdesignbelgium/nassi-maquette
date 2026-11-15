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

  /* Tracklists : état visuel « en lecture » + bouton voir tout */
  /* Lecteur d'extraits (30 s, Deezer) */
  (() => { const audio = new Audio(); audio.preload = 'none'; let cur = null; const cache = {};
    const PLAY = '<svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>';
    $$('.track[data-dz]').forEach(t => { t.insertAdjacentHTML('beforeend', '<span class="pb"></span>'); t.insertAdjacentHTML('afterbegin', '<span class="play">' + PLAY + '</span>'); });
    function preview(id) { return cache[id] || (cache[id] = new Promise((res, rej) => { const cb = 'dz' + id + '_' + Date.now(); window[cb] = d => { delete window[cb]; sc.remove(); d && d.preview ? res(d.preview) : rej(); }; const sc = document.createElement('script'); sc.src = 'https://api.deezer.com/track/' + id + '?output=jsonp&callback=' + cb; sc.onerror = () => { delete window[cb]; sc.remove(); rej(); }; document.head.appendChild(sc); }).catch(e => { delete cache[id]; throw e; })); }
    function stop() { audio.pause(); if (cur) { cur.classList.remove('playing', 'loading'); cur.querySelector('.pb').style.width = 0; } cur = null; }
    async function play(t) { if (cur === t) { if (audio.paused) audio.play(); else { audio.pause(); t.classList.remove('playing'); } return; }
      stop(); cur = t; t.classList.add('loading');
      try { const src = await preview(t.dataset.dz); if (cur !== t) return; audio.src = src; await audio.play(); t.classList.remove('loading'); t.classList.add('playing'); }
      catch (e) { if (cur === t) { t.classList.remove('loading'); const a = t.closest('.ep') && t.closest('.ep').querySelector('a[href]'); if (a) window.open(a.href, '_blank'); cur = null; } } }
    audio.addEventListener('timeupdate', () => { if (cur && audio.duration) cur.querySelector('.pb').style.width = (audio.currentTime / audio.duration * 100) + '%'; });
    audio.addEventListener('play', () => { if (cur) cur.classList.add('playing'); });
    audio.addEventListener('ended', () => { const t = cur; stop(); const next = t && t.nextElementSibling; if (next && next.matches('.track[data-dz]') && (!next.classList.contains('more') || t.closest('.ep').classList.contains('open'))) play(next); });
    $$('.tracklist').forEach(tl => { tl.addEventListener('click', e => { const t = e.target.closest('.track[data-dz]'); if (t) play(t); });
      tl.addEventListener('keydown', e => { const t = e.target.closest('.track[data-dz]'); if (t && (e.key === 'Enter' || e.key === ' ')) { e.preventDefault(); play(t); } }); });
  })();
  $$('.ep .seeall').forEach(b => b.addEventListener('click', () => { const ep = b.closest('.ep'); const o = ep.classList.toggle('open'); b.textContent = o ? b.dataset.less : b.dataset.more; }));

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
