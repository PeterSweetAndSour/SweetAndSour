$(document).ready(function(){
	const carouselWrapper = document.querySelector("#carouselWrapper");
	const upperCarouselImage = document.querySelector("#upperImage");
	const lowerCarouselImage = document.querySelector("#lowerImage");
	let colorClasses = ["green", "blue", "purple", "red", "orange"];
	let imageClasses = ["image1", "image2", "image3", "image4", "image5"];
	let currentSlideIndex = null;
	let newSlideIndex = null;


  $(".carousel").slick({
				dots: true,
				arrows: false,
        autoplay: false,
        autoplaySpeed: 10000,
        infinite: true,
        speed: 500,
        fade: true,
        slidesToShow: 1,
        adaptiveHeight: true,
        customPaging: function (slick, index) {
             return '<a class="dot">0' + (index + 1) + '</a>'
        }
	}).on('init', function(event, slick, currentSlide, nextSlide){

	}).on('beforeChange', function(event, slick, currentSlide, nextSlide){
		currentSlideIndex = $(".carousel").slick('slickCurrentSlide');
	}).on('afterChange', function(event, slick, currentSlide, nextSlide){
		newSlideIndex = $(".carousel").slick('slickCurrentSlide');

		carouselWrapper.classList.remove(colorClasses[currentSlideIndex]);

		//Place new photo in lower image placeholder
		lowerCarouselImage.classList.add(imageClasses[newSlideIndex]);

		//For transition, switch active from upper to lower
		upperCarouselImage.classList.remove("active");
		lowerCarouselImage.classList.add("active");

		//After transition is complete, reset so lower image placeholder is empty
		window.setTimeout(function() {
			upperCarouselImage.classList.remove(imageClasses[currentSlideIndex]);
			upperCarouselImage.classList.add(imageClasses[newSlideIndex]);
			upperCarouselImage.classList.add("active");

			lowerCarouselImage.classList.remove(imageClasses[newSlideIndex]);
			lowerCarouselImage.classList.remove("active");
		}, 500)
	});
});
