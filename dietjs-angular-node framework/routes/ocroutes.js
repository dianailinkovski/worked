var cred = {
    username  : 'babarkhan7311-facilitator_api1.gmail.com',
    password  : 'ZN9KB3YVL52RBCBZ',
    signature : 'An5ns1Kso7MWUdW4ErQKJJJ4qi4-AfCKnPe.b93Tep4KeS7PTfMKMymQ'
};

var opts = {
    sandbox : true,
    version : '78.0'
};

app.get('/company/deposit', function(request, response, mysql){
    if(response.head.account) {
        var id_user = response.head.account.id;
        response.data.page = 'deposit';
	response.data.title = 'VBOX - Deposit Funds';
        var Wallet = require(request.domainApp.options.path+'/Wallet.js');
        var wallet = new Wallet();
        wallet.getFunds(id_user, function(result){
            if(result.success) response.data.funds = result.funds;
            else  response.data.funds = 0.00;
            console.log(response.data.funds);
            response.finish();
        });
    }
    else{
        response.redirect("home");
    }
       
});
app.get('/company/deposit/confirm/', function(req, res, mysql){
    var token = req.query.token;
    var PayerId = req.query.PayerID;
    var total = amount = req.query.amount;
    var id_user = req.query.id_user;
    
    var PayPalEC = require( 'paypal-ec' );
    var ec       = new PayPalEC( cred, opts );
    
    var Wallet = require(req.domainApp.options.path+'/Wallet.js');
    var wallet = new Wallet();
    res.data.page = 'deposit';
    res.data.title = 'VBOX - Deposit Funds';
    
    var params = {
            token                           : encodeURIComponent(token),
            PAYERID                         : encodeURIComponent(PayerId),
            PAYMENTACTION                   : 'sale'
        };
    ec.get_details( params, function ( err, data ){
        if (data && (data.ACK == "Success" || data.ACK == "SUCCESSWITHWARNING") ) {
            amount = data.AMT;
            currency = data.CURRENCYCODE;
            var params = {
                token                           : encodeURIComponent(token),
                PAYERID                         : encodeURIComponent(PayerId),
                PAYMENTACTION                   : 'sale',
                AMT                             : data.AMT,
                CURRENCYCODE                    : data.CURRENCYCODE
            };
            ec.do_payment( params, function ( err, data ){
                console.log(err);
                if (data && (data.ACK == "Success" || data.ACK == "SUCCESSWITHWARNING")) {
                    wallet.addFunds(amount, id_user, function(data){
                        res.data.msg = "<div class='alert alert-success'>You have successfully deposited an amount of " + amount + currency + "</div>";
                        res.finish();
                    });
                    
                }
                else{
                    res.data.msg = "<div class='alert alert-danger'>"+err+"</div>";
                    res.finish();
                }
            });
        }
        else{
            res.data.msg = "<div class='alert alert-danger'>"+err+"</div>";
            res.finish();
        }
        
    });
        
          
});
app.post('/company/deposit', function(req, res, mysql){
    var id_user = res.head.account.id;
    if (req.body.paypal_action) {
        var url = req.url.protocol + "//" + req.url.host;
        var amount = req.body.amount;
          
          var PayPalEC = require( 'paypal-ec' );
          var ec       = new PayPalEC( cred, opts );
          
          var params = {
            returnUrl : url + '/company/deposit/confirm/?id_user='+id_user+'&amount='+amount,
            cancelUrl : url + '/company/deposit/cancel',
            SOLUTIONTYPE                   : 'sole',
            PAYMENTREQUEST_0_AMT           : amount,
            PAYMENTREQUEST_0_ITEMAMT       : amount,
            L_PAYMENTREQUEST_0_AMT0        : amount,
            AMT                            : amount,
            PAYMENTREQUEST_0_DESC          : 'Funds Deposit',
            PAYMENTREQUEST_0_CURRENCYCODE  : 'USD',
            PAYMENTREQUEST_0_PAYMENTACTION : 'Sale',
          };
          ec.set( params, function ( err, data ){
            if (data.ACK == "Success") {
                res.redirect(data[ 'PAYMENTURL' ]);
            }
            else{
                console.log(error);
                res.data.error = error;
                res.data.page = 'deposit';
                res.data.title = 'VBOX - Deposit Funds';
                res.finish();
            }
          });

    }
    else if(req.body.stripeToken){
        res.data.page = 'deposit';
        res.data.title = 'VBOX - Deposit Funds';
        var Wallet = require(req.domainApp.options.path+'/Wallet.js');
        var wallet = new Wallet();
        console.log("========================================doStripeAction=================================");
		var error= false;
		var stripe = require("stripe")("sk_test_BQokikJOvBiI2HlWgH4olfQ2");
                var amount = req.body.amount;
                var stripeToken = req.body.stripeToken;
		var amountInCents = amount * Number(100);
		var charge = stripe.charges.create({
		  amount: amountInCents, // amount in cents, again
		  currency: "usd",
		  source: stripeToken,
		  description: "Funds Deposit",
		  metadata: {id_user:id_user}
		}, function(err, charg) {
		  if (err && err.type === 'StripeCardError') {
			error = true;
                        res.data.msg = "<div class='alert alert-danger'>"+err+"</div>";
                        res.finish();
		  }
		  else{
			result = JSON.stringify(charg);
			console.log(charg);
			funds = Number(result.amount) / Number(100);
                        if (funds > 0) {
                            wallet.addFunds(funds, id_user, function(data){
                                res.data.msg = "<div class='alert alert-success'>You have successfully deposited an amount of " + amount + currency + "</div>";
                                res.finish();
                            });
                        }else{
                             res.data.msg = "<div class='alert alert-success'>No amount was charged</div>";
                             res.finish();
                        }
			
		  }
		});
                console.log("---------------------------------charge-----------------------");
                console.log(charge);
		console.log("========================================doStripeAction=================================");
    }
    else{
        res.data.page = 'deposit';
        console.log("---------------------------------------console.log(req.body)-------------------------------------");
        console.log(req.body);
        res.data.msg = "<div class='alert alert-danger'>No action taken</div>";
        res.finish();
        }
});