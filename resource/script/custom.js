document.addEventListener('DOMContentLoaded', function() {
    var elements = document.querySelectorAll('.hoverable');
    elements.forEach(function(el, index) {
        setTimeout(function() {
            el.classList.add('bounce');
        }, index * 500);
    });
});