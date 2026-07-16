// Intersection Observer for scroll animations
const observerOptions = {
  threshold: 0.1,
  rootMargin: "0px 0px -50px 0px",
};

const observer = new IntersectionObserver((entries) => {
  entries.forEach((entry) => {
    if (entry.isIntersecting) {
      entry.target.classList.add("in-view");
    }
  });
}, observerOptions);

// Observe elements for animation
document.addEventListener("DOMContentLoaded", () => {
  const animateElements = document.querySelectorAll(".animate-on-scroll");
  animateElements.forEach((el) => observer.observe(el));
});



// Additional interactive features
document.addEventListener("DOMContentLoaded", () => {
  // Mouse movement parallax effect
  document.addEventListener("mousemove", (e) => {
    const mouseX = e.clientX / window.innerWidth;
    const mouseY = e.clientY / window.innerHeight;

    const particles = document.querySelectorAll(".particle");
    particles.forEach((particle, index) => {
      const speed = (index + 1) * 0.5;
      const x = (mouseX - 0.5) * speed * 20;
      const y = (mouseY - 0.5) * speed * 20;
      particle.style.transform = `translate(${x}px, ${y}px)`;
    });

    // Subtle parallax for diagonal light
  });
});

// Dynamic color shifting based on scroll position
window.addEventListener("scroll", () => {
  const scrolled = window.pageYOffset;
  const maxScroll = document.body.scrollHeight - window.innerHeight;
  const scrollPercent = scrolled / maxScroll;

  const hue = scrollPercent * 60; // Shift through blues and purples
  const lightingOverlay = document.querySelector(".lighting-overlay");
  if (lightingOverlay) {
    lightingOverlay.style.filter = `hue-rotate(${hue}deg)`;
  }
});

// Inside your script.js file, likely within a DOMContentLoaded listener

const contactForm = document.getElementById('contactForm');
const messageBox = document.getElementById('messageBox');

if (contactForm) {
    contactForm.addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent default form submission

        const formData = new FormData(contactForm);
        const messageBox = document.getElementById('messageBox');

        fetch('process_form.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                messageBox.innerHTML = `<p style="color: green;">${data.message}</p>`;
                contactForm.reset(); // Clear the form
            } else {
                messageBox.innerHTML = `<p style="color: red;">${data.message}</p>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            messageBox.innerHTML = '<p style="color: red;">An unexpected error occurred. Please try again later.</p>';
        });
    });
}


document.addEventListener('DOMContentLoaded', function() {
    const hamburgerMenu = document.getElementById('hamburgerMenu');
    const navCenter = document.querySelector('.header-nav-center');
    const navRight = document.querySelector('.header-utility-right');
  
    hamburgerMenu.addEventListener('click', function() {
      navCenter.classList.toggle('active');
      navRight.classList.toggle('active');
    });
  });
  
  document.addEventListener('DOMContentLoaded', () => {
            const navLinks = document.querySelectorAll('nav a[href^="#"]');

            navLinks.forEach(link => {
                link.addEventListener('click', (e) => {
                    // Prevent the default anchor link behavior
                    e.preventDefault();

                    // Get the target element's ID from the href attribute
                    const targetId = e.currentTarget.getAttribute('href');
                    const targetElement = document.querySelector(targetId);

                    if (targetElement) {
                        // Use scrollIntoView with smooth behavior for a pleasant transition
                        targetElement.scrollIntoView({
                            behavior: 'smooth'
                        });
                    }
                });
            });
        });
        

window.addEventListener('scroll', function() {
    const header = document.querySelector('.header');
    if (window.scrollY > 50) { // Adjust this value to your liking
        header.classList.add('scrolled');
    } else {
        header.classList.remove('scrolled');
    }
});