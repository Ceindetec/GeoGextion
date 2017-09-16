<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Adminox - Responsive Web App Kit</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
    <meta content="Coderthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{asset('images/favicon.ico')}}">

    <!-- C3 charts css -->

    <!-- App css -->
    {{Html::style('css/bootstrap.min.css')}}
    {{Html::style('css/core.css')}}
    {{Html::style('css/components.css')}}
    {{Html::style('css/icons.css')}}
    {{Html::style('css/menu.css')}}
    {{Html::style('css/responsive.css')}}
    {{Html::style('css/pages.css')}}
    {{Html::script('js/modernizr.min.js')}}

    @yield('estyles')

</head>


<body>

<!-- Begin page -->
<div id="wrapper">

    <!-- Top Bar Start -->
    <div class="topbar">

        <!-- LOGO -->
        <div class="topbar-left">
            <!--<a href="index.html" class="logo"><span>Code<span>Fox</span></span><i class="mdi mdi-layers"></i></a>-->
            <!-- Image logo -->
            <a href="index.html" class="logo">
                        <span>
                            <img src="{{url('images/logo.png')}}" alt="" height="25">
                        </span>
                <i>
                    <img src="{{url('images/logo_sm.png')}}" alt="" height="28">
                </i>
            </a>
        </div>

        <!-- Button mobile view to collapse sidebar menu -->
        <div class="navbar navbar-default" role="navigation">
            <div class="container">

                <!-- Navbar-left -->
                <ul class="nav navbar-nav navbar-left nav-menu-left">
                    <li>
                        <button type="button" class="button-menu-mobile open-left waves-effect">
                            <i class="dripicons-menu"></i>
                        </button>
                    </li>

                </ul>

                <!-- Right(Notification) -->
                <ul class="nav navbar-nav navbar-right">

                    <li>
                        <a href="#" class="right-menu-item dropdown-toggle" data-toggle="dropdown">
                            <i class="dripicons-bell"></i>
                            <span class="badge badge-pink">4</span>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-right dropdown-lg user-list notify-list">
                            <li class="list-group notification-list m-b-0">
                                <div class="slimscroll">
                                    <!-- list item-->
                                    <a href="javascript:void(0);" class="list-group-item">
                                        <div class="media">
                                            <div class="media-left p-r-10">
                                                <em class="fa fa-diamond bg-primary"></em>
                                            </div>
                                            <div class="media-body">
                                                <h5 class="media-heading text-primary">A new order has been placed A new order has been placed</h5>
                                                <p class="m-0">
                                                    There are new settings available
                                                </p>
                                            </div>
                                        </div>
                                    </a>

                                    <!-- list item-->
                                    <a href="javascript:void(0);" class="list-group-item">
                                        <div class="media">
                                            <div class="media-left p-r-10">
                                                <em class="fa fa-cog bg-warning"></em>
                                            </div>
                                            <div class="media-body">
                                                <h5 class="media-heading text-warning">New settings</h5>
                                                <p class="m-0">
                                                    There are new settings available
                                                </p>
                                            </div>
                                        </div>
                                    </a>

                                    <!-- list item-->
                                    <a href="javascript:void(0);" class="list-group-item">
                                        <div class="media">
                                            <div class="media-left p-r-10">
                                                <em class="fa fa-bell-o bg-custom"></em>
                                            </div>
                                            <div class="media-body">
                                                <h5 class="media-heading text-custom">Updates</h5>
                                                <p class="m-0">
                                                    There are <span class="text-primary font-600">2</span> new updates available
                                                </p>
                                            </div>
                                        </div>
                                    </a>

                                    <!-- list item-->
                                    <a href="javascript:void(0);" class="list-group-item">
                                        <div class="media">
                                            <div class="media-left p-r-10">
                                                <em class="fa fa-user-plus bg-danger"></em>
                                            </div>
                                            <div class="media-body">
                                                <h5 class="media-heading text-danger">New user registered</h5>
                                                <p class="m-0">
                                                    You have 10 unread messages
                                                </p>
                                            </div>
                                        </div>
                                    </a>

                                    <!-- list item-->
                                    <a href="javascript:void(0);" class="list-group-item">
                                        <div class="media">
                                            <div class="media-left p-r-10">
                                                <em class="fa fa-diamond bg-primary"></em>
                                            </div>
                                            <div class="media-body">
                                                <h5 class="media-heading text-primary">A new order has been placed A new order has been placed</h5>
                                                <p class="m-0">
                                                    There are new settings available
                                                </p>
                                            </div>
                                        </div>
                                    </a>

                                    <!-- list item-->
                                    <a href="javascript:void(0);" class="list-group-item">
                                        <div class="media">
                                            <div class="media-left p-r-10">
                                                <em class="fa fa-cog bg-warning"></em>
                                            </div>
                                            <div class="media-body">
                                                <h5 class="media-heading text-warning">New settings</h5>
                                                <p class="m-0">
                                                    There are new settings available
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </li>
                            <!-- end notification list -->
                        </ul>
                    </li>

                    <li class="dropdown user-box">
                        <a href="" class="dropdown-toggle waves-effect user-link" data-toggle="dropdown" aria-expanded="true">
                            <img src="{{url('images/users/avatar-1.jpg')}}" alt="user-img" class="img-circle user-img">
                        </a>

                        <ul class="dropdown-menu dropdown-menu-right arrow-dropdown-menu arrow-menu-right user-list notify-list">
                            <li><a href="javascript:void(0)">Profile</a></li>
                            <li><a href="javascript:void(0)"><span class="badge badge-info pull-right">4</span>Settings</a></li>
                            <li><a href="javascript:void(0)">Lock screen</a></li>
                            <li class="divider"></li>
                            <li><a href="javascript:void(0)">Logout</a></li>
                        </ul>
                    </li>

                </ul> <!-- end navbar-right -->

            </div><!-- end container -->
        </div><!-- end navbar -->
    </div>
    <!-- Top Bar End -->


    <!-- ========== Left Sidebar Start ========== -->
    <div class="left side-menu">
        <div class="slimscroll-menu" id="remove-scroll">

            <!--- Sidemenu -->
            <div id="sidebar-menu">
                <!-- Left Menu Start -->
                <ul class="metisMenu nav" id="side-menu">
                    <li class="menu-title">Navigation</li>
                    <li>
                        <a href="javascript: void(0);" aria-expanded="true"><i class="fi-air-play"></i><span class="badge badge-success pull-right">2</span> <span> Dashboard </span></a>
                        <ul class="nav-second-level nav" aria-expanded="true">
                            <li><a href="index.html">Dashboard 1</a></li>
                            <li><a href="dashboard-2.html">Dashboard 2</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="javascript: void(0);" aria-expanded="true"><i class="fi-target"></i> <span> Admin UI </span> <span class="menu-arrow"></span></a>
                        <ul class="nav-second-level nav" aria-expanded="true">
                            <li><a href="admin-grid.html">Grid</a></li>
                            <li><a href="admin-sweet-alert.html">Sweet Alert</a></li>
                            <li><a href="admin-tiles.html">Tiles Box</a></li>
                            <li><a href="admin-nestable.html">Nestable List</a></li>
                            <li><a href="admin-rangeslider.html">Range Slider</a></li>
                            <li><a href="admin-ratings.html">Ratings</a></li>
                            <li><a href="admin-filemanager.html">File Manager</a></li>
                            <li><a href="admin-lightbox.html">Lightbox</a></li>
                            <li><a href="admin-scrollbar.html">Scroll bar</a></li>
                            <li><a href="admin-slider.html">Slider</a></li>
                            <li><a href="admin-treeview.html">Treeview</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="javascript: void(0);" aria-expanded="true"><i class="fi-briefcase"></i> <span> UI Kit </span> <span class="menu-arrow"></span></a>
                        <ul class="nav-second-level nav" aria-expanded="true">
                            <li><a href="ui-typography.html">Typography</a></li>
                            <li><a href="ui-panels.html">Panels</a></li>
                            <li><a href="ui-buttons.html">Buttons</a></li>
                            <li><a href="ui-modals.html">Modals</a></li>
                            <li><a href="ui-checkbox-radio.html">Checkboxs-Radios</a></li>
                            <li><a href="ui-spinners.html">Spinners</a></li>
                            <li><a href="ui-ribbons.html">Ribbons</a></li>
                            <li><a href="ui-portlets.html">Portlets</a></li>
                            <li><a href="ui-tabs.html">Tabs</a></li>
                            <li><a href="ui-progressbars.html">Progress Bars</a></li>
                            <li><a href="ui-notifications.html">Notification</a></li>
                            <li><a href="ui-carousel.html">Carousel</a>
                            <li><a href="ui-video.html">Video</a>
                            <li><a href="ui-tooltips-popovers.html">Tooltips & Popovers</a></li>
                            <li><a href="ui-images.html">Images</a></li>
                            <li><a href="ui-bootstrap.html">Bootstrap UI</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="tickets.html"><i class="fi-help"></i><span class="badge badge-danger pull-right">New</span> <span> Tickets </span></a>
                    </li>

                    <li>
                        <a href="javascript: void(0);" aria-expanded="true"><i class="fi-box"></i><span> Icons </span> <span class="menu-arrow"></span></a>
                        <ul class="nav-second-level nav" aria-expanded="true">
                            <li><a href="icons-colored.html">Colored Icons</a></li>
                            <li><a href="icons-materialdesign.html">Material Design</a></li>
                            <li><a href="icons-dripicons.html">Dripicons</a></li>
                            <li><a href="icons-fontawesome.html">Font awesome</a></li>
                            <li><a href="icons-feather.html">Feather Icons</a></li>
                            <li><a href="icons-simple-line.html">Simple line Icons</a></li>
                            <li><a href="icons-flags.html">Flag Icons</a></li>
                            <li><a href="icons-file.html">File Icons</a></li>
                            <li><a href="icons-pe7.html">PE7 Icons</a></li>
                            <li><a href="icons-typicons.html">Typicons</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="javascript: void(0);" aria-expanded="true"><i class="fi-bar-graph-2"></i><span> Graphs </span> <span class="menu-arrow"></span></a>
                        <ul class="nav-second-level nav" aria-expanded="true">
                            <li><a href="chart-flot.html">Flot Chart</a></li>
                            <li><a href="chart-morris.html">Morris Chart</a></li>
                            <li><a href="chart-google.html">Google Chart</a></li>
                            <li><a href="chart-echart.html">Echarts</a></li>
                            <li><a href="chart-chartist.html">Chartist Charts</a></li>
                            <li><a href="chart-chartjs.html">Chartjs Chart</a></li>
                            <li><a href="chart-c3.html">C3 Chart</a></li>
                            <li><a href="chart-justgage.html">Justgage Charts</a></li>
                            <li><a href="chart-sparkline.html">Sparkline Chart</a></li>
                            <li><a href="chart-knob.html">Jquery Knob</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="javascript: void(0);" aria-expanded="true"><i class="fi-mail"></i><span> Email </span> <span class="menu-arrow"></span></a>
                        <ul class="nav-second-level nav" aria-expanded="true">
                            <li><a href="email-inbox.html">Inbox</a></li>
                            <li><a href="email-read.html">Read Email</a></li>
                            <li><a href="email-compose.html">Compose Email</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="taskboard.html"><i class="fi-paper"></i> <span> Task Board </span></a>
                    </li>

                    <li>
                        <a href="javascript: void(0);" aria-expanded="true"><i class="fi-disc"></i><span class="badge badge-warning pull-right">12</span> <span> Forms </span></a>
                        <ul class="nav-second-level nav" aria-expanded="true">
                            <li><a href="form-elements.html">Form Elements</a></li>
                            <li><a href="form-advanced.html">Form Advanced</a></li>
                            <li><a href="form-layouts.html">Form Layouts</a></li>
                            <li><a href="form-validation.html">Form Validation</a></li>
                            <li><a href="form-pickers.html">Form Pickers</a></li>
                            <li><a href="form-wizard.html">Form Wizard</a></li>
                            <li><a href="form-mask.html">Form Masks</a></li>
                            <li><a href="form-summernote.html">Summernote</a></li>
                            <li><a href="form-wysiwig.html">Wysiwig Editors</a></li>
                            <li><a href="form-typeahead.html">Typeahead</a></li>
                            <li><a href="form-x-editable.html">X Editable</a></li>
                            <li><a href="form-uploads.html">Multiple File Upload</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="javascript: void(0);" aria-expanded="true"><i class="fi-layout"></i> <span> Tables </span> <span class="menu-arrow"></span></a>
                        <ul class="nav-second-level nav" aria-expanded="true">
                            <li><a href="tables-basic.html">Basic Tables</a></li>
                            <li><a href="tables-layouts.html">Tables Layouts</a></li>
                            <li><a href="tables-datatable.html">Data Tables</a></li>
                            <li><a href="tables-foo-tables.html">Foo Tables</a></li>
                            <li><a href="tables-responsive.html">Responsive Table</a></li>
                            <li><a href="tables-tablesaw.html">Tablesaw Tables</a></li>
                            <li><a href="tables-editable.html">Editable Tables</a></li>
                        </ul>
                    </li>

                    <li class="menu-title">More</li>

                    <li>
                        <a href="javascript: void(0);" aria-expanded="true"><i class="fi-map"></i> <span> Maps </span> <span class="menu-arrow"></span></a>
                        <ul class="nav-second-level nav" aria-expanded="true">
                            <li><a href="maps-google.html">Google Maps</a></li>
                            <li><a href="maps-google-full.html">Full Google Map</a></li>
                            <li><a href="maps-vector.html">Vector Maps</a></li>
                            <li><a href="maps-mapael.html">Mapael Maps</a></li>
                        </ul>
                    </li>

                    <li><a href="calendar.html"><i class="fi-clock"></i> <span>Calendar</span> </a></li>

                    <li>
                        <a href="javascript: void(0);" aria-expanded="true"><i class="fi-paper-stack"></i><span> Pages </span> <span class="menu-arrow"></span></a>
                        <ul class="nav-second-level nav" aria-expanded="true">
                            <li><a href="page-starter.html">Starter Page</a></li>
                            <li><a href="page-login.html">Login</a></li>
                            <li><a href="page-register.html">Register</a></li>
                            <li><a href="page-logout.html">Logout</a></li>
                            <li><a href="page-recoverpw.html">Recover Password</a></li>
                            <li><a href="page-lock-screen.html">Lock Screen</a></li>
                            <li><a href="page-confirm-mail.html">Confirm Mail</a></li>
                            <li><a href="page-404.html">Error 404</a></li>
                            <li><a href="page-404-alt.html">Error 404-alt</a></li>
                            <li><a href="page-500.html">Error 500</a></li>
                        </ul>
                    </li>

                    <li class="has_sub">
                        <a href="javascript:void(0);"><i class="fi-marquee-plus"></i><span> Extra Pages </span> <span class="menu-arrow"></span></a>
                        <ul class="nav-second-level nav" aria-expanded="true">
                            <li><a href="extras-about.html">About Us</a></li>
                            <li><a href="extras-contact.html">Contact</a></li>
                            <li><a href="extras-companies.html">Companies</a></li>
                            <li><a href="extras-members.html">Members</a></li>
                            <li><a href="extras-members-2.html">Membars 2</a></li>
                            <li><a href="extras-timeline.html">Timeline</a></li>
                            <li><a href="extras-invoice.html">Invoice</a></li>
                            <li><a href="extras-maintenance.html">Maintenance</a></li>
                            <li><a href="extras-coming-soon.html">Coming Soon</a></li>
                            <li><a href="extras-faq.html">FAQ</a></li>
                            <li><a href="extras-pricing.html">Pricing</a></li>
                            <li><a href="extras-profile.html">Profile</a></li>
                            <li><a href="extras-email-template.html">Email Templates</a></li>
                            <li><a href="extras-search-result.html">Search Results</a></li>
                            <li><a href="extras-sitemap.html">Site Map</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="todo.html"><i class="fi-layers"></i> <span> Todo </span></a>
                    </li>

                    <li>
                        <a href="javascript: void(0);" aria-expanded="true"><i class="fi-share"></i> <span>Multi Level</span> <span class="menu-arrow"></span></a>
                        <ul class="nav-second-level nav" aria-expanded="true">
                            <li><a href="javascript: void(0);">Level 1.1</a></li>
                            <li><a href="javascript: void(0);" aria-expanded="true">Level 1.2 <span class="menu-arrow"></span></a>
                                <ul class="nav-third-level nav" aria-expanded="true">
                                    <li><a href="javascript: void(0);">Level 2.1</a></li>
                                    <li><a href="javascript: void(0);">Level 2.2</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>

                </ul>

            </div>
            <!-- Sidebar -->
            <div class="clearfix"></div>

        </div>
        <!-- Sidebar -left -->

    </div>
    <!-- Left Sidebar End -->



    <!-- ============================================================== -->
    <!-- Start right Content here -->
    <!-- ============================================================== -->
    <div class="content-page">
        <!-- Start content -->
        <div class="content">
            <div class="container">
                @yield('contenido')

            </div> <!-- container -->

        </div> <!-- content -->

        <footer class="footer text-right">
            2017 © Adminox. - Coderthemes.com
        </footer>

    </div>


    <!-- ============================================================== -->
    <!-- End Right content here -->
    <!-- ============================================================== -->


</div>
<!-- END wrapper -->



<!-- jQuery  -->
{{Html::script('js/jquery.min.js')}}
{{Html::script('js/bootstrap.min.js')}}
{{Html::script('js/metisMenu.min.js')}}
{{Html::script('js/waves.js')}}
{{Html::script('js/jquery.slimscroll.js')}}

<!-- Counter js  -->
{{Html::script('plugins/waypoints/jquery.waypoints.min.js')}}
{{Html::script('plugins/counterup/jquery.counterup.min.js')}}


<!-- App js -->
{{Html::script('js/jquery.core.js')}}
{{Html::script('js/jquery.app.js')}}

@yield('scripts')

</body>
</html>