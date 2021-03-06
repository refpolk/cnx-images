$(function() {

	function getQuery() {
		
		newQuery = '?';
					
		if (window.location.href.split('?').length > 1) {
		
			var query = window.location.href.split('?')[1];
			var newQueryArray = [];
			var queryArray = query.split('&');
		
			for (var i = 0; i < queryArray.length; i++) {
		
				var paramName = queryArray[i].split('=')[0];
		
				if (paramName != 'p' && paramName != 's') {
					newQueryArray.push(queryArray[i]);
				}
			}
		
			if (newQueryArray.length > 0) {
				newQuery += newQueryArray.join('&') + '&';
			}
		}
		
		return newQuery;
	}
	
	$('.page-size').on('click', function() { 
		
		window.location.replace(window.location.pathname + getQuery() + "p=1&s=" + $(this).text());
	});
	
});