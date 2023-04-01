<!DOCTYPE html>
<html lang="en-us">
<head>

<meta charset="utf-8">

<title>TrackStreet</title>
    
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
<link rel="stylesheet" type="text/css" media="screen" href="/css/sticky.css?ver=20015-10-13a" />
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
<script type="text/javascript" src="/js/sticky.js?ver=2015-09-16"></script>

<script src="//cdn.datatables.net/1.10.7/js/jquery.dataTables.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script src="/js/dataTables.colReorder.js"></script>
<script src="/js/dataTables.fixedHeader.js"></script>
<script src="/js/bootstrap/popover.js"></script>   

</head>

<body id="modal-body">

<?php echo $content; ?>

<?php $this->load->view('layouts/components/javascript'); ?>

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
