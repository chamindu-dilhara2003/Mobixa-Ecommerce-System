document.addEventListener('DOMContentLoaded',()=>{
  setTimeout(()=>document.querySelector('.page-loading')?.classList.add('hide'),120);

  // Keep the selected theme across the storefront and admin area.
  const themeToggle=document.querySelector('.theme-toggle');
  const syncThemeToggle=()=>{
    const dark=document.documentElement.dataset.theme==='dark';
    if(themeToggle){
      themeToggle.textContent=dark?'☀':'☾';
      themeToggle.setAttribute('aria-label',dark?'Switch to light mode':'Switch to dark mode');
      themeToggle.setAttribute('title',dark?'Switch to light mode':'Switch to dark mode');
    }
  };
  syncThemeToggle();
  themeToggle?.addEventListener('click',()=>{
    const nextTheme=document.documentElement.dataset.theme==='dark'?'light':'dark';
    document.documentElement.dataset.theme=nextTheme;
    try{localStorage.setItem('mobixa-theme',nextTheme)}catch(error){}
    syncThemeToggle();
  });

  // Keep pages already open in another tab in sync with the shared setting.
  window.addEventListener('storage',event=>{
    if(event.key==='mobixa-theme' && event.newValue){
      document.documentElement.dataset.theme=event.newValue;
      syncThemeToggle();
    }
  });

  // Delete confirmation
  document.querySelectorAll('.confirm-delete').forEach(el=>el.addEventListener('click',e=>{
    if(!confirm('Are you sure you want to delete this item?')) e.preventDefault();
  }));

  // Animated delete (e.g. admin "Delete" buttons on table rows): confirm,
  // then fade/slide the row out before actually following the delete link.
  document.querySelectorAll('.remove-btn[data-confirm]').forEach(link=>{
    link.addEventListener('click', e=>{
      e.preventDefault();
      if(!confirm(link.dataset.confirm)) return;

      const row=link.closest('tr');
      const href=link.href;

      if(row){
        row.classList.add('removing');
        row.addEventListener('transitionend', ()=>{ window.location.href=href; }, {once:true});
        // Fallback in case the transition event never fires.
        setTimeout(()=>{ window.location.href=href; }, 400);
      }else{
        window.location.href=href;
      }
    });
  });

  // Sticky header shadow
  const header=document.querySelector('.site-header,.admin-top');
  const syncHeader=()=>header&&header.classList.toggle('scrolled',window.scrollY>8);
  syncHeader(); window.addEventListener('scroll',syncHeader,{passive:true});

  // Scroll reveal for sections/cards that are dynamically added
  const revealEls=document.querySelectorAll('.section,.detail-grid,.form-card,.table-wrap,.stats,.admin-actions');
  revealEls.forEach(el=>el.classList.add('reveal'));
  if('IntersectionObserver' in window){
    const observer=new IntersectionObserver(entries=>entries.forEach(entry=>{
      if(entry.isIntersecting){entry.target.classList.add('visible');observer.unobserve(entry.target)}
    }),{threshold:.08});
    document.querySelectorAll('.reveal').forEach(el=>observer.observe(el));
  }else document.querySelectorAll('.reveal').forEach(el=>el.classList.add('visible'));

  // Ripple effect on buttons
  document.querySelectorAll('.btn').forEach(btn=>btn.addEventListener('click',e=>{
    const r=document.createElement('span'); r.className='ripple';
    const rect=btn.getBoundingClientRect(); r.style.left=(e.clientX-rect.left-5)+'px'; r.style.top=(e.clientY-rect.top-5)+'px';
    btn.appendChild(r); setTimeout(()=>r.remove(),600);
  }));

  // Smooth mobile menu close after navigation
  document.querySelectorAll('.nav-links a').forEach(a=>a.addEventListener('click',()=>{
    document.querySelector('.nav-links')?.classList.remove('show');
  }));

  // Add a lightweight image loading polish
  document.querySelectorAll('img').forEach(img=>{
    img.addEventListener('load',()=>img.classList.add('loaded'),{once:true});
  });
});
