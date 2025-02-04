document.querySelector('.login-button').addEventListener('click', function() {
    let options = document.querySelector('.login-options');
    options.style.display = (options.style.display === 'block') ? 'none' : 'block';
});
document.querySelectorAll('.explore-btn').forEach(button => {
    button.addEventListener('click', function() {
        let year = this.getAttribute('data-year');
        window.location.href = year + '-syllabus.html';
    });
});

