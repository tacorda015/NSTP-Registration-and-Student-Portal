// For Topbar
let profile = document.querySelector('.pro-file');
let menu = document.querySelector('.menu');

profile.onclick = function () {
  menu.classList.toggle('active');
};

document.addEventListener('click', function (event) {
  if (!event.target.closest('.menu') && !event.target.closest('.pro-file')) {
    menu.classList.remove('active');
  }
});
// End Topbar
