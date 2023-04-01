app.all(function (request, response, mysql, next) {
    console.log('------------------------------------' + response.head.account.id)
    if (response.head.account.id) {
        
        app.accounts.user(response.head.account);
        app.accounts.extend(request, response, mysql, next);
    } else {
        next();
        //	response.data.page = 'notFound';
        //	response.finish();
    }
});
