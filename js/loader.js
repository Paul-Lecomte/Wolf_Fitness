window.onload = function() {
    var svg = document.getElementById('svg');
    var loader = document.getElementById('loader');
    
    // Start the drawing animation
    svg.style.animation = 'draw 4s ease';
    
    // Hide the loader after animation completes
    setTimeout(function() {
        loader.style.opacity = '0'; // Fading out the loader
        setTimeout(function() {
            loader.style.display = 'none'; // Hide loader after fade out
        }, 1000); // Adjust as per your animation duration
    }, 5000); // Adjust the timeout duration as needed
};
