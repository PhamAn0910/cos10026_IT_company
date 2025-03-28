document.addEventListener('DOMContentLoaded', function() {
    // Check if we're not on the homepage
    if (!document.body.classList.contains('homepage')) {
        // Get the footer logo element
        const footerLogo = document.querySelector('.footer-logo');
        
        if (footerLogo) {
            // Split the text into individual spans for each letter
            const text = footerLogo.textContent;
            footerLogo.textContent = '';
            
            // Create spans for each letter
            for (let i = 0; i < text.length; i++) {
                const span = document.createElement('span');
                span.textContent = text[i];
                footerLogo.appendChild(span);
            }
            
            // Create an Intersection Observer to watch when the footer comes into view
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    // If the footer is now visible and not already animated
                    if (entry.isIntersecting && !footerLogo.classList.contains('animate')) {
                        // Add the animate class to trigger the animation
                        footerLogo.classList.add('animate');
                        
                        // Once animation has triggered, we can stop observing
                        observer.unobserve(footerLogo);
                    }
                });
            }, {
                // Set the threshold to 0.1 (10% visibility is enough to trigger)
                threshold: 0.1
            });
            
            // Start observing the footer logo
            observer.observe(footerLogo);
        }
    }
}); 