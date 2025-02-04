document.querySelectorAll('.toggle-chapters').forEach(button => {
    button.addEventListener('click', () => {
        const chapters = button.nextElementSibling; // Get the corresponding chapters list
        if (chapters.style.display === "block") {
            chapters.style.display = "none"; // Hide chapters if already visible
        } else {
            chapters.style.display = "block"; // Show chapters
        }
    });
});
