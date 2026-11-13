const menu = document.getElementById('sidebar');
const abrir = document.getElementById('openSidebar');
const fechar = document.getElementById('closeSidebar');

if (abrir && menu) {
    abrir.addEventListener('click', function () {
        menu.classList.add('open');
    });
}

if (fechar && menu) {
    fechar.addEventListener('click', function () {
        menu.classList.remove('open');
    });
}