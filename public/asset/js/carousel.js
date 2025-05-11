$(document).ready(function() {
    if ($('.carousel').length === 0) return;
    
    const carousel = {
        container: $('.carousel-inner'),
        items: $('.carousel-item'),
        totalItems: $('.carousel-item').length,
        currentIndex: 0,

        init: function() {
            // Show first slide
            this.items.first().addClass('active');
            this.bindEvents();
            this.updateButtons();
        },

        bindEvents: function() {
            $('.carousel-control-next').on('click', () => this.slideNext());
            $('.carousel-control-prev').on('click', () => this.slidePrev());
        },

        slideNext: function() {
            if (this.currentIndex < this.totalItems - 1) {
                this.items.eq(this.currentIndex).removeClass('active');
                this.currentIndex++;
                this.items.eq(this.currentIndex).addClass('active');
                this.updateButtons();
            }
        },

        slidePrev: function() {
            if (this.currentIndex > 0) {
                this.items.eq(this.currentIndex).removeClass('active');
                this.currentIndex--;
                this.items.eq(this.currentIndex).addClass('active');
                this.updateButtons();
            }
        },

        updateButtons: function() {
            // Update button states
            if (this.currentIndex === 0) {
                $('.carousel-control-prev').addClass('disabled');
            } else {
                $('.carousel-control-prev').removeClass('disabled');
            }

            if (this.currentIndex >= this.totalItems - 1) {
                $('.carousel-control-next').addClass('disabled');
            } else {
                $('.carousel-control-next').removeClass('disabled');
            }
        }
    };

    carousel.init();
});