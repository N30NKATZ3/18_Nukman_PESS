$(window).on('scroll', function(event) {
    var scrollValue = $(window).scrollTop();
    if (scrollValue > 150) {
         $('header').addClass('affix');
		$('.topbar').addClass('hide');
		
    } 
	else {
		$('header').removeClass('affix');
		$('.topbar').removeClass('hide');
	}
});