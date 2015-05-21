$(function(){
	var $vidContainer = $('#ambientVideo');
	$vidContainer.vide("/wp-content/uploads/2015/05/ambientvideo.mp4", {
		volume: 0,
	    playbackRate: 1,
	    muted: true,
	    loop: true,
	    autoplay: true,
	    position: "50% 50%", // Similar to the CSS `background-position` property.
	    posterType: "jpg", // Poster image type. "detect" — auto-detection; "none" — no poster; "jpg", "png", "gif",... - extensions.
	    resizing: true // Auto-resizing, read: https://github.com/VodkaBears/Vide#resizing
	});
});