<div id="qaWidget">
        <div class="container">
                <div class="button clear show_widget">
                        <?php
                            switch($controller) {
                                case 'violationoverview':
                                    $controller = 'Violation Overview';
                                    break;
                            }
                        ?>  
                        <h3><?php echo ucfirst($controller).' Help'; ?></h3>
                        <div class="widget_container">
                            <link  href="http://newdesign.juststicky.com/css/widget/widget_all.css" rel="stylesheet" type="text/css" />
                            <script type="text/javascript" src="http://newdesign.juststicky.com/VOL/137/store/163/final.js"></script>
                            <script type="text/javascript" src="http://newdesign.juststicky.com/js/widget/widget_new.js?p=b2lkPTcxMSZzaWQ9MTYz&url=<?php echo $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']?>&un=&e="></script>
                            <script type="text/javascript">
                                    $jQAW(document).ready(function(){
                                      $jQAW(".show_widget").click(function(e){
                                            e.preventDefault();
                                            qaw_widget.open_widget_popup();
                                      });
                                    });
                            </script>
                            <!--[if gte IE 8]>
                                <style type="text/css">
                                .stickyGrayBar {
                                filter: none !important;
                                }

                                .stickyButton{
                                filter: none !important;
                                }
                                </style>
                            <![endif]-->
                        </div>
                        <div class="cta clear">
                                <span>Show</span>
                                <img src="images/arrow-qawidget.png" alt="" width="8" height="11">
                        </div>
                </div>
        </div>
</div>
