<html>
   <head>
      <meta charset="utf-8"/>
      <link rel="stylesheet" href="/css/bootstrap.min.css">
      <!-- Font Awesome  -->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
        <!-- Ionicons  -->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
        <!-- Theme style -->
      <link rel="stylesheet" href="/css/AdminLTE.min.css">
        <!-- AdminLTE Skins. Choose a skin from the css/skins
           folder instead of downloading all of them to reduce the load. -->
      <link rel="stylesheet" href="/css/skins/_all-skins.min.css">

       <link rel="stylesheet" href="/plugins/daterangepicker/daterangepicker.css">

       <!-- DataTables -->
      <link rel="stylesheet" href="/plugins/datatables/dataTables.bootstrap.css">

      <style type="text/css">
         .font-big{
            font-size: 90px;
         }
      </style>
         
    </head>
    <body class="hold-transition skin-blue sidebar-collapse sidebar-mini">
         <div class="wrapper">
            <!-- =============================================== -->

            <!-- Left side column. contains the sidebar -->
            <aside class="main-sidebar">
               <!-- sidebar: style can be found in sidebar.less -->
               <section class="sidebar">
                  <!-- sidebar menu: : style can be found in sidebar.less -->
                  <ul class="sidebar-menu">
                     <li class="header">MAIN NAVIGATION</li>
                     <!-- <li class="treeview active">
                        <a href="#">
                           <i class="fa fa-home"></i> <span>Home</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                           <i class="fa fa-bomb"></i> <span>Attacks</span>
                        </a>
                     </li>
                     <li class="treeview">
                        <a href="#">
                           <i class="glyphicon glyphicon-pushpin"></i> <span>Parameters</span>
                        </a>
                     </li> -->                   
                  </ul>
               </section>
               <!-- /.sidebar -->
            </aside>

            <!-- =============================================== -->

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper" id="main-content">   
              <section class="content row">
              <div class="row">
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <h3>Loading content .... </h3>
                <div class="col-md-offset-1 col-md-10">
                <div class="progress progress-sm active" data-toggle="tooltip" data-placement="bottom"  data-widget="chat-pane-toggle" data-original-title="Transformation data. Please wait for some moment.Dont reload or close this page.">
                  <div class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                    <span class="sr-only">   </span>
                  </div>
                </div> 
                </div> 
              </div>
              </section>          
               <!-- Main Content -->
            </div>
            <!-- /.content-wrapper -->
            <footer class="main-footer">
               <div class="pull-right hidden-xs">
                  <b>Version</b> 1.0
               </div>
               <strong>Copyright &copy; <?php echo date("Y")=="2017"?"2017": "2017 - ".date("Y"); ?> GlasMon
            </footer>

            <!-- /.control-sidebar -->
            <!-- Add the sidebar's background. This div must be placed
                immediately after the control sidebar -->
            <div class="control-sidebar-bg"></div>
         </div>

         <!-- jQuery 2.2.3 -->
         <script src="/plugins/jQuery/jquery-2.2.3.min.js"></script>
         <!-- Bootstrap 3.3.6 -->
         <script src="/plugins/bootstrap/js/bootstrap.min.js"></script>
         <!-- SlimScroll -->
         <script src="/plugins/slimScroll/jquery.slimscroll.min.js"></script>
         <!-- FastClick -->
         <!-- AdminLTE App -->
         <script src="/js/app.min.js"></script>

         <!-- ChartJS 1.0.1 -->
        <!-- <script src="/plugins/chartjs/Chart.min.js"></script> -->
        <script src="/plugins/chartjs/Chart.bundle.min.js"></script>

        <!-- DataTables -->
        <script src="/plugins/datatables/jquery.dataTables.min.js"></script>
        <script src="/plugins/datatables/dataTables.bootstrap.min.js"></script>
        
         <!-- date time picker -->
         <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
         <script src="/plugins/daterangepicker/daterangepicker.js"></script>

         <script type="text/javascript" src="/js/react.js"></script>
         <script type="text/javascript" src="/js/react-dom.js"></script>
         <script src="https://unpkg.com/babel-standalone@6.15.0/babel.min.js"></script>
         <script type="text/babel" src="/js/glasmon.js"></script>
         <script type="text/babel">            
         ReactDOM.render(
            <MainApp   />,
               document.getElementById('main-content')
         );
            
         </script>

    </body>
</html>