document.addEventListener("DOMContentLoaded", function () {
    class Slider {
        constructor(owlElement, owlOptions) {
            this.owlElement = owlElement;
            this.owlOptions = owlOptions;
        }

        addSlider(owlElement, owlOptions) {
            $(owlElement).owlCarousel(owlOptions);
        }
    }

    const slider = new Slider();

    slider.addSlider(".slider", {
        margin: 30,
        responsive: {
            0: {
                items: 1
            },
            1100: {
                items: 2
            },
            1300: {
                items: 2
            },
            1500: {
                items: 2,
            }
        }
    });
});