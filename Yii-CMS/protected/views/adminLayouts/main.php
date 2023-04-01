<?php $this->beginContent('//adminLayouts/mainEmpty'); ?>

<div class="container body">

    <div class="main_container">

        <div class="col-md-3 left_col">
            <div class="left_col scroll-view">

                <div class="navbar nav_title" style="border: 0;">
                    <a href="<?php echo $this->createUrl('/admin'); ?>" class="site_title">
                    <?php if(isset(Yii::app()->params->adminLogo)):?>
                        <img src="<?php echo CHtml::encode(Yii::app()->params->adminLogo) ?>" alt="<?php echo CHtml::encode(Yii::app()->name) ?>" class="img-responsive" />
                    <?php else: ?>
                        <span><?php echo CHtml::encode(Yii::app()->name); ?></span>
                    <?php endif ?>
                    </a>
                </div>
                <div class="clearfix"></div>

                <!-- sidebar menu -->
                <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">

                    <div class="menu_section">
                        <h3>Général</h3>
                        <ul class="nav side-menu">
                            <li><a href="<?php echo $this->createUrl('/category/admin'); ?>" title="Professions"><i class="fa fa-graduation-cap"></i> Professions</a></li>
                            <li><a href="<?php echo $this->createUrl('/usager/admin'); ?>" title="Usagers"><i class="fa fa-male"></i> Usagers</a></li>
                            <li><a href="<?php echo $this->createUrl('/document/admin'); ?>" title="Documents"><i class="fa fa-file"></i> Documents</a></li>
                            
                            <?php foreach ($this->mainMenu as $mainMenuItem): ?>

                                <?php if (isset($mainMenuItem['subMenu'])): ?>
                                <li>
                                    <a><i class="fa<?php echo (isset($mainMenuItem['icon']) ? ' fa-'.$mainMenuItem['icon'] : ''); ?>"></i> Forms <span class="fa fa-chevron-down"></span></a>
                                    <ul class="nav child_menu" style="display: none">
                                        <?php foreach ($mainMenuItem['subMenu'] as $subMenuItem): ?>
                                            <li><?php echo CHtml::link($subMenuItem['label'], $subMenuItem['url']); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </li>
                                <?php else: ?>
                                    <li><a href="<?php echo CHtml::normalizeUrl($mainMenuItem['url']); ?>" title="<?php echo CHtml::encode($mainMenuItem['label']); ?>"><i class="fa<?php echo (isset($mainMenuItem['icon']) ? ' fa-'.$mainMenuItem['icon'] : ''); ?>"></i> <?php echo CHtml::encode($mainMenuItem['label']); ?></a></li>
                                <?php endif; ?>
                            
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="menu_section">
                        <h3>Autre</h3>
                        <ul class="nav side-menu">
                           <li><a href="<?php echo $this->createUrl('/user/admin'); ?>" title="Administrateurs"><i class="fa fa-user"></i> Administrateurs</a></li>
                           <li><a href="<?php echo $this->createUrl('/settings/index'); ?>" title="Paramêtres"><i class="fa fa-gear"></i> Paramêtres</a></li>
                           <li><a href="<?php echo $this->createUrl('/admin/page/view/about'); ?>" title="Aide"><i class="fa fa-question"></i> Aide</a></li>
                        </ul>
                    </div>
                    <?php if (!($this->id == 'admin' && $this->action->id == 'login')): ?>
                    <div class="menu_section">
                        <h3>Spécial</h3>
                        <ul class="nav side-menu">
                            <li><a href="<?php echo $this->createUrl('/sections/admin'); ?>" title="Sections"><i class="fa fa-edit"></i> Sections</a></li>
                            <li><a href="<?php echo $this->createUrl('/alias/admin'); ?>" title="Alias"><i class="fa fa-edit"></i> Alias</a></li>
                            <?php //echo CHtml::link('Permissions', array('/rights')); ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                </div>
                <!-- /sidebar menu -->

                <!-- /menu footer buttons -->
                <div class="sidebar-footer hidden-small">
                    <a data-toggle="tooltip" data-placement="top" title="Settings" href="<?php echo $this->createUrl('settings/index'); ?>">
                        <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
                    </a>
                    <a data-toggle="tooltip" data-placement="top" title="FullScreen" onclick="screenfull.toggle()">
                        <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
                    </a>
                    <a data-toggle="tooltip" data-placement="top" title="Lock" onclick="$.blockUI({ message: '' })">
                        <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>
                    </a>
                    <a data-toggle="tooltip" data-placement="top" title="Logout" href="<?php echo $this->createUrl('admin/logout'); ?>">
                        <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
                    </a>
                </div>
                <!-- /menu footer buttons -->
            </div>
        </div>

        <!-- top navigation -->
        <div class="top_nav">

            <div class="nav_menu">
                <nav class="" role="navigation">
                    <div class="nav toggle">
                        <a id="menu_toggle"><i class="fa fa-bars"></i></a>
                    </div>

                    <ul class="nav navbar-nav navbar-right">
                        <li class="">
                            <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                <span class="glyphicon glyphicon glyphicon-user"></span> <?php echo Yii::app()->user->name; ?>
                                <span class=" fa fa-angle-down"></span>
                            </a>
                            <ul class="dropdown-menu dropdown-usermenu animated fadeInDown pull-right">
                                <li><a href="javascript:;">Profile</a></li>
                                <li><a href="<?php echo $this->createUrl('admin/logout'); ?>"><i class="fa fa-sign-out pull-right"></i> Log Out</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </nav>
            </div>

        </div>
        <!-- /top navigation -->

        <!-- page content -->
        <div class="right_col" role="main">
            <div class="right_col_top">
                <?php echo $content; ?>
            </div>
            <!-- /page content -->

            <!-- footer content -->
            <footer>
                <div class="">
                    <p class="pull-right">Copyright &copy; 1999–<?php echo date('Y'); ?> Société G-NeTiX Inc. All Rights Reserved.</p>
                </div>
                <div class="clearfix"></div>
            </footer>
            <!-- /footer content -->

        </div>
    </div>
</div>

<?php $this->endContent(); ?>