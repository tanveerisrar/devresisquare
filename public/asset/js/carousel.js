$(document).ready(function () {
    let currentIndex = 0;
    const items = $('.carousel-item');
    const totalItems = items.length;
    const itemsPerSlide = 4; // Number of items visible at a time

    function showSlide(index) {
        const offset = -index * (100 / itemsPerSlide); // Calculate the offset for sliding
        $('.carousel-inner').css('transform', `translateX(${offset}%)`);
    }

    $('.carousel-control-next').click(function () {
        if (currentIndex < totalItems - itemsPerSlide) {
            currentIndex++;
        } else {
            currentIndex = 0; // Loop back to the start
        }
        showSlide(currentIndex);
    });

    $('.carousel-control-prev').click(function () {
        if (currentIndex > 0) {
            currentIndex--;
        } else {
            currentIndex = totalItems - itemsPerSlide; // Loop back to the end
        }
        showSlide(currentIndex);
    });
});