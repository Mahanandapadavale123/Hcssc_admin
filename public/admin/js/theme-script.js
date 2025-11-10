document.querySelector("html").setAttribute("data-theme", localStorage.getItem('theme') || 'light');
document.querySelector("html").setAttribute('data-sidebar', localStorage.getItem('sidebarTheme') || 'light');
document.querySelector("html").setAttribute('data-color', localStorage.getItem('color') || 'primary');
document.querySelector("html").setAttribute('data-topbar', localStorage.getItem('topbar') || '#16191f');
document.querySelector("html").setAttribute('data-layout', localStorage.getItem('layout') || 'default');
document.querySelector("html").setAttribute('data-topbarcolor', localStorage.getItem('topbarcolor') || 'white');
document.querySelector("html").setAttribute('data-card', localStorage.getItem('card') || 'bordered');
document.querySelector("html").setAttribute('data-size', localStorage.getItem('size') || 'default');
