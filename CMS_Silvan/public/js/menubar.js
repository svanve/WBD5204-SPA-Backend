const menubar = document.querySelector('#menubar');
const menuToggle = menubar.querySelector('#menu-toggle');
const menuLables = menubar.querySelectorAll('ul span');
// const menuIcons = menubar.querySelectorAll('ul i');
// const menuUl = menubar.querySelector('ul');


menuToggle.addEventListener('click', (e) => slide(e));

function slide(e) {
    menubar.classList.toggle('slide-right');
    menuLables.forEach(
        (el) => el.classList.toggle('d-none')
    );
}