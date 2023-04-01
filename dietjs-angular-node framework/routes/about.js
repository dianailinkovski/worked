app.get('/company/contact', function(request, response, mysql){

response.data.page = 'contactus';
	response.data.title = 'VBOX - Contact Us';

	response.finish();
})



app.get('/company/about', function(request, response, mysql){

response.data.page = 'aboutus';
        response.data.title = 'VBOX -About Us';

        response.finish();
})
app.get('/chat/', function(request, response, mysql){

response.data.page = 'chat';
        response.data.title = 'calling';

        response.finish();
})



app.get('/company/faq', function(request, response, mysql){

response.data.page = 'faq';
        response.data.title = 'VBOX - FAQ';

        response.finish();
})

app.get('/company/howitworks', function(request, response, mysql){

response.data.page = 'howitworks';
        response.data.title = 'VBOX - How It Works';

        response.finish();
})

app.get('/company/createstore', function(request, response, mysql){

response.data.page = 'createstore';
        response.data.title = 'VBOX - Create Store';

        response.finish();
})

app.get('/company/createstore', function(request, response, mysql){

response.data.page = 'createstore';
        response.data.title = 'VBOX - Create Store';

        response.finish();
})

app.get('/company/invitation', function(request, response, mysql){

response.data.page = 'invitation';
        response.data.title = 'VBOX - Invitation';

        response.finish();
})

app.get('/company/placeadsbusiness', function(request, response, mysql){

response.data.page = 'placeadsbusiness';
        response.data.title = 'VBOX - Place an ad for business';

        response.finish();
})

