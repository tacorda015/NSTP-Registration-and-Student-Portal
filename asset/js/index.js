// Start Side Bar
let arrow = document.querySelectorAll('.arrow');
for (var i = 0; i < arrow.length; i++) {
  arrow[i].addEventListener('click', (e) => {
    let arrowParent = e.target.parentElement.parentElement; //selecting main parent of arrow
    arrowParent.classList.toggle('showMenu');
  });
}
let sidebar = document.querySelector('.sidebar');
let sidebarBtn = document.querySelector('.bx-menu');
// console.log(sidebarBtn);
sidebarBtn.addEventListener('click', () => {
  sidebar.classList.toggle('close');
});
// End Side Bar

// Start Form
const form = document.querySelector('form'),
  nextBtn = form.querySelector('.nextBtn'),
  backBtn = form.querySelector('.backBtn'),
  allInput = form.querySelectorAll('.first input');

nextBtn.addEventListener('click', () => {
  allInput.forEach((input) => {
    if (input.value != '') {
      form.classList.add('secActive');
    } else {
      form.classList.remove('secActive');
    }
  });
});

backBtn.addEventListener('click', () => form.classList.remove('secActive'));

// End Form
