document.addEventListener('DOMContentLoaded', function() {
    let lastScrollTop = 0;
    const footer = document.getElementById('footer');
  
    window.addEventListener('scroll', function() {
      let scrollTop = window.scrollY || document.documentElement.scrollTop;
      if (scrollTop > lastScrollTop) {
        footer.classList.add('hidden');
      } else {
        footer.classList.remove('hidden');
      }
      lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
    }, false);
  });
  
