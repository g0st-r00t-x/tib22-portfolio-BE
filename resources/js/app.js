import './bootstrap';
// Dark mode toggle
document.addEventListener('alpine:init', () => {
    Alpine.store('darkMode', {
        on: localStorage.getItem('darkMode') === 'true',

        toggle() {
            this.on = !this.on
            localStorage.setItem('darkMode', this.on)
        }
    })
})

// Scroll animations
document.addEventListener('DOMContentLoaded', () => {
    AOS.init({
        duration: 800,
        once: true,
        easing: 'ease-out-quad',
    })

    // Smooth scroll
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault()
            const target = document.querySelector(this.getAttribute('href'))
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                })
            }
        })
    })
})