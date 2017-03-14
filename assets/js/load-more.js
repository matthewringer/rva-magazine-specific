jQuery(function($){

	$('.post-listing').append( '<div class="load-more"><i></i><span class="sr-only">Loading...</span></div>' );
	var button = $('.post-listing .load-more'); //marker for the bottom of the page.
	var page = 2;
	var max_pages = -1;
	var loading = false;
	var scrollHandling = {
	    allow: true,
	    reallow: function() {
	        scrollHandling.allow = true;
	    },
	    delay: 700 //(milliseconds) adjust to the highest acceptable value
	};

	//todo: need to check for a corner case if the scrolling is already at the bottom when the ajax reply comes. 

	handleScroll();

	$(window).scroll( handleScroll);

	function handleScroll() {
		if( ! loading && scrollHandling.allow ) {
			scrollHandling.allow = false;
			setTimeout(scrollHandling.reallow, scrollHandling.delay);
			var offset = $(button).offset().top - $(window).scrollTop();

			if( 2000 > offset && max_pages >= page || max_pages == -1 ){
				loading = true;
				$('.load-more').toggle(loading);
				var data = {
					action: 'rva_ajax_load_more',
					nonce: rvaloadmore.nonce,
					page: page,
					query: rvaloadmore.query,
				};
				$.post(rvaloadmore.url, data, function(res) {
					if( res.success) {
						$('.post-listing').append( res.data.thumbs );
						$('.post-listing').append( button ); // move the load target to the bottom of the page.
						page = page + 1;
						max_pages = res.data.max_pages;
						loading = false;
						$('.load-more').toggle(loading);
					} else {
						 console.log(res);
					}
				}).fail(function(xhr, textStatus, e) {
					console.log(xhr.responseText);
				});

			}
		}
	}
});