mysqlCountRows = function(request, response, mysql, sql, callback){
	mysql(sql, function(rows){
		//console.log('TOTAL_ROWS:', rows[0]['COUNT(*)']);
		response.data.current_page = isset(request.query.page) ? parseInt(request.query.page) : 1 ;
		response.data.total_rows = rows[0]['COUNT(*)'];
		response.data.total_pages = Math.ceil(response.data.total_rows / response.data.limit_count);
	
		var showPageCount = 3;
		response.data.from_pages = response.data.current_page-showPageCount > 0  
			? response.data.current_page-showPageCount
			: 1 ;
		response.data.to_pages = response.data.current_page+showPageCount < response.data.total_pages 
			? response.data.current_page+showPageCount
			: response.data.total_pages ;
		
		callback();
	});
}