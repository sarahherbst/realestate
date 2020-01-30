$('.resizing-frame').iFrameResize({
	log: true, 
	checkOrigin: false, 
	enablePublicMethods: true,
	heightCalculationMethod: 'lowestElement'
});

window.onload = function(){
	var frame = document.getElementById('immoframe');
	if (frame){
		var url = 'https://76360033.flowfact-webparts.net/index.php/estates';
		frame.src = url + window.location.search;
	}
}