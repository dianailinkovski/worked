<!DOCTYPE html>
<html lang="en-us">
<head>

<meta charset="utf-8">

<title><?php echo $this->page_title; ?></title>
    
<meta name="description" content="">
<meta name="author" content="">

<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

<link rel="stylesheet" href="//fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,300,400,700">
<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css" />
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css" />
<link rel="stylesheet" type="text/css" media="screen" href="/css/smartadmin-production.css?ver=20015-07-13" />
<link rel="stylesheet" type="text/css" media="screen" href="/css/smartadmin-skins.css?ver=20015-07-13" />
<link rel="stylesheet" type="text/css" media="screen" href="/css/smartadmin-rtl.css?ver=20015-07-13" />
<link rel="stylesheet" type="text/css" media="screen" href="/css/fonts/fonts.css?ver=20014-12-08" />
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.7/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" media="screen" href="/css/sticky.css?ver=2016-01-04" />
<link rel="stylesheet" type="text/css" href="/js/jqwidgets/styles/jqx.base.css" />

<link rel="shortcut icon" href="/images/favicon.ico" type="image/x-icon">
<link rel="icon" href="/images/favicon.ico" type="image/x-icon">
<link rel="apple-touch-icon-precomposed" sizes="114x114" href="/images/favicon.png"/>

<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<script src="/js/plugin/easy-pie-chart/jquery.easy-pie-chart.min.js"></script>
<script type="text/javascript" src="/js/jqwidgets/jqx-all.js"></script>
<script type="text/javascript" src="/js/jqwidgets/jqxtooltip.js"></script>
<script type="text/javascript" src="/js/sticky.js?ver=2015-09-18"></script>

<script src="//cdn.datatables.net/1.10.7/js/jquery.dataTables.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script src="/js/dataTables.colReorder.js"></script>
<script src="/js/dataTables.fixedHeader.js"></script>
<script src="/js/bootstrap/popover.js"></script>

<?php if ($this->config->item('environment') == 'production'): ?>
    <!-- start Mixpanel -->
    <script type="text/javascript">(function(e,b){if(!b.__SV){var a,f,i,g;window.mixpanel=b;b._i=[];b.init=function(a,e,d){function f(b,h){var a=h.split(".");2==a.length&&(b=b[a[0]],h=a[1]);b[h]=function(){b.push([h].concat(Array.prototype.slice.call(arguments,0)))}}var c=b;"undefined"!==typeof d?c=b[d]=[]:d="mixpanel";c.people=c.people||[];c.toString=function(b){var a="mixpanel";"mixpanel"!==d&&(a+="."+d);b||(a+=" (stub)");return a};c.people.toString=function(){return c.toString(1)+".people (stub)"};i="disable time_event track track_pageview track_links track_forms register register_once alias unregister identify name_tag set_config people.set people.set_once people.increment people.append people.union people.track_charge people.clear_charges people.delete_user".split(" ");
    for(g=0;g<i.length;g++)f(c,i[g]);b._i.push([a,e,d])};b.__SV=1.2;a=e.createElement("script");a.type="text/javascript";a.async=!0;a.src="undefined"!==typeof MIXPANEL_CUSTOM_LIB_URL?MIXPANEL_CUSTOM_LIB_URL:"file:"===e.location.protocol&&"//cdn.mxpnl.com/libs/mixpanel-2-latest.min.js".match(/^\/\//)?"https://cdn.mxpnl.com/libs/mixpanel-2-latest.min.js":"//cdn.mxpnl.com/libs/mixpanel-2-latest.min.js";f=e.getElementsByTagName("script")[0];f.parentNode.insertBefore(a,f)}})(document,window.mixpanel||[]);
    mixpanel.init("029a777def5aafc26bfa02bb336205f8");</script>
    <!-- end Mixpanel -->
<?php endif; ?>    

</head>
<body>

<div id="header" class="navbar navbar-fixed-top">

    <div id="logo-group">
        <span id="logo"><a href="/">Dashboard</a></span>
    </div>

    <?php if (isset($this->logged_in)): ?>	
        					
        <div class="pull-right">
    					
            <div id="hide-menu" class="btn-header pull-right hidden-lg">
    				    <span> <a id="mobile-toggle-menu" href="javascript:void(0);" title="Collapse Menu" data-action="toggleMenu"><i class="fa fa-reorder"></i></a> </span>
    				</div>
    
    				<ul id="mobile-profile-img" class="header-dropdown-list hidden-xs">
    				    <li id="user-profile-menu" class="">
                    <div id="header-account-menu-launch">
                        <?php if (isset($this->logged_in)): ?>
                            <a href="#" id="toggle-user-menu-button" class="dropdown-toggle userdropdown" data-toggle="dropdown">            							      
        							          <?php if ($this->user['profile_img_thumb'] != ''): ?>
        								            <img class="sml-profile-img" src="http://images.juststicky.com/stickyvision/profile_photos/<?php echo $this->user['profile_img_thumb']; ?>" />
        								        <?php else: ?>
        								            <span id="user-menu-icon">
        								                <i class="fa fa-user"></i>
        								            </span>    
        								        <?php endif; ?>
            								    <span id="account-menu-user-name" class="text text-muted">
            					              <?php echo $this->user['first_name'] . ' ' . $this->user['last_name']; ?>
            								    </span> 
            								    <span id="account-menu-caret" class="caret">
            								    
            								    </span>
        								    </a>
    					          <?php endif; ?>
    								</div>
    								<ul class="dropdown-menu pull-right" id="top-menu-user-dropdown">
                        <li>
                        	<a href="/account/profile" class="padding-10 padding-top-5 padding-bottom-5"> <i class="fa fa-user"></i> My Profile</a>
                        </li>
                        <li class="divider"></li>
                        <li>
                        	<a href="/logout" class="padding-10 padding-top-5 padding-bottom-5" data-action="userLogout"><i class="fa fa-sign-out fa-lg"></i> <strong>Logout</strong></a>
                        </li>
                    </ul>
                </li>
            </ul>
            
            <?php if (count($brands) > 1): ?>
                <div id="brand-switcher">  
                    Brand:                   
                    <form action="<?= site_url('account/switch_brand') ?>" method="post" id="switchBrandForm">
                        <select id="switchBrand" name="switchBrand">
                            <?php for ($i = 0; $i < count($brands); $i++): ?>
                                <option label="<?= $brands[$i]['store_name'] ?>" value="<?= $brands[$i]['store_id'] ?>" <?= $brands[$i]['store_id'] == $store_id ? ' selected="selected"' : '' ?>><?= $brands[$i]['store_name'] ?></option>
                            <?php endfor; ?>
                        </select>
                    </form>                          
                </div>
            <?php endif; ?>        
    			
    		<!-- end .pull-right -->		
        </div>
        
    <?php endif; ?>
					
</div>

<?php if (isset($this->logged_in)): ?>
				
    <?php $this->load->view('layouts/components/left_nav'); ?>
		    
		<!-- use class="container-fluid"? -->    
    <div id="main" role="main">
        <?php echo $content; ?>
    </div>
        
<?php else: ?> 
    
    <?php echo $content; ?>
        	
<?php endif; ?>

<script src="/js/app.config.js"></script>
<script src="/js/app.min.js"></script>
<script src="/js/plugin/jquery-touch/jquery.ui.touch-punch.min.js"></script> 
<script src="/js/smartwidgets/jarvis.widget.min.js"></script>
<script src="/js/plugin/sparkline/jquery.sparkline.min.js"></script>
<script src="/js/plugin/jquery-validate/jquery.validate.min.js"></script>
<script src="/js/plugin/masked-input/jquery.maskedinput.min.js"></script>
<script src="/js/plugin/select2/select2.min.js"></script>
<script src="/js/plugin/bootstrap-slider/bootstrap-slider.min.js"></script>
<script src="/js/plugin/msie-fix/jquery.mb.browser.min.js"></script>
<script src="/js/plugin/fastclick/fastclick.min.js"></script>

<script src="/js/flot/jquery.flot.js"></script>
<script src="/js/flot/jquery.flot.spline.min.js"></script>
<script src="/js/flot/jquery.flot.tooltip.min.js"></script>
<script src="/js/flot/jquery.flot.resize.js"></script>
<script src="/js/flot/jquery.flot.categories.js"></script>
<script src="/js/flot/jquery.flot.pie.js"></script>

<?php $this->load->view('layouts/components/javascript'); ?>

<script type="text/javascript">

$(document).ready(function() {

    pageSetUp();

		$('#left-panel').affix({
		    offset: {
		        top: 56
		    }
		});

		$('#mobile-toggle-menu').click(function(){
			window.scrollTo(0, 0);
		});
				
});

</script>


<!-- Interact auto-login -->
<script type="text/javascript">

(function(email, firstName, lastName) {
    
    var callback = function () {
        if (typeof window.stickyAutoLogin !== "undefined") {
            window.stickyAutoLogin(email, firstName, lastName);
        }
    };

    if (document.readyState === "interactive" || document.readyState === "complete") {
        callback();
    } else {
        document.addEventListener("DOMContentLoaded", callback);
    } 
    
})('<?php echo $this->user['email']; ?>', '<?php echo $this->user['first_name']; ?>', '<?php echo $this->user['last_name']; ?>');

</script>


<?php if ($this->config->item('environment') == 'production'): ?>
    <!-- <script id="interact_55c3ddaf23072" data-unique="55c3ddaf23072" data-text="Discuss this with Sticky Interact" src="//interact.juststicky.com/js/embed/client.js/2028/interact_55c3ddaf23072"></script>  -->
    <!-- <script src="//interact.juststicky.com/js/embed/client.js/2028/interact_55ce78da2ff29" id="interact_55ce78da2ff29" data-text="Discuss this with Sticky Interact" data-unique="55ce78da2ff29"></script> -->

    <script type="text/javascript">
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
    
      ga('create', 'UA-64399208-1', 'auto');
      ga('send', 'pageview');
    
    </script>
<?php endif; ?>    

</body>
</html>
