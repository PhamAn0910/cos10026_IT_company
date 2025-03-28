document.addEventListener('DOMContentLoaded', function() {
    // Get the toggle button, navigation menu, and create overlay element
    const menuToggle = document.querySelector('.menu-toggle');
    const navLinks = document.querySelector('.nav-links');
    
    // Create overlay element
    const overlay = document.createElement('div');
    overlay.className = 'nav-overlay';
    document.body.appendChild(overlay);
    
    // Toggle function for menu
    function toggleMenu() {
        navLinks.classList.toggle('active');
        overlay.classList.toggle('active');
        
        // Update aria-expanded attribute for accessibility
        const isExpanded = navLinks.classList.contains('active');
        menuToggle.setAttribute('aria-expanded', isExpanded);
    }
    
    // Add click event to toggle button
    menuToggle.addEventListener('click', toggleMenu);
    
    // Close menu when clicking on the overlay
    overlay.addEventListener('click', toggleMenu);
    
    // Close menu when clicking on a nav link
    const links = navLinks.querySelectorAll('a');
    links.forEach(link => {
        link.addEventListener('click', function() {
            if (navLinks.classList.contains('active')) {
                toggleMenu();
            }
        });
    });
    
    // Close menu when pressing Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && navLinks.classList.contains('active')) {
            toggleMenu();
        }
    });
}); 