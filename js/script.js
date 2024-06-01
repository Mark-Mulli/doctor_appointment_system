// navbar toggling
const navbarShowBtn = document.querySelector('.navbar-show-btn');
const navbarCollapseDiv = document.querySelector('.navbar-collapse');
const navbarHideBtn = document.querySelector('.navbar-hide-btn');
const navLinks = document.querySelectorAll('.nav-link');
const bookAppointmentLink = document.querySelector('.btn-appointment');

navbarShowBtn.addEventListener('click', function(){
    navbarCollapseDiv.classList.add('navbar-show');
});
navbarHideBtn.addEventListener('click', function(){
    navbarCollapseDiv.classList.remove('navbar-show');
});

// Add event listener to each navigation link
navLinks.forEach(link => {
    link.addEventListener('click', function() {
        navbarCollapseDiv.classList.remove('navbar-show');
    });
});

// Add event listener to the "Book Appointment" link
bookAppointmentLink.addEventListener('click', function() {
    navbarCollapseDiv.classList.remove('navbar-show');
});

//increment superscript icon for each service icon.
document.addEventListener("DOMContentLoaded", function() {
    var serviceItems = document.querySelectorAll('.service-item');

    serviceItems.forEach(function(item, index) {
        var icon = item.querySelector('.icon');

        // Set the unique count for each item
        icon.dataset.count = index + 1;
    });
});

// // changing search icon image on window resize
// window.addEventListener('resize', changeSearchIcon);
// function changeSearchIcon(){
//     let winSize = window.matchMedia("(min-width: 1200px)");
//     if(winSize.matches){
//         document.querySelector('.search-icon img').src = "images/search-icon.png";
//     } else {
//         document.querySelector('.search-icon img').src = "images/search-icon-dark.png";
//     }
// }
// changeSearchIcon();

// stopping all animation and transition
let resizeTimer;
window.addEventListener('resize', () =>{
    document.body.classList.add('resize-animation-stopper');
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(() => {
        document.body.classList.remove('resize-animation-stopper');
    }, 400);
});

// faq toggling
const faqs = document.querySelectorAll(".faq");
faqs.forEach(faq => {
    faq.addEventListener("click", () => {
        faq.classList.toggle("active");
    })
})