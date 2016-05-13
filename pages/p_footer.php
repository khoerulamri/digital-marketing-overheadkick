	<footer class="main-footer">
        <div class="pull-right hidden-xs">
          <b>Version</b> 1.0.1
        </div>
        <strong>Copyright © 2016 <a href="#">Overheadkick</a>.</strong> All rights reserved.
     </footer>
	
	<!--Control Sidebar-->
	<aside class="control-sidebar control-sidebar-dark">
      </aside>
      
    <!-- Add the sidebar's background. This div must be placed
           immediately after the control sidebar -->
      
    </div><!-- ./wrapper -->

	<!-- GLOBAL -->
    <!-- jQuery 2.1.4 -->
    <script src="../assets/plugins/jQuery/jQuery-2.1.4.min.js" type="text/javascript"></script>
    <!-- Bootstrap 3.3.2 JS -->
    <script src="../assets/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    
	<!-- DATA TABLES SCRIPT -->
    <script src="../assets/plugins/datatables/jquery.dataTables.min.js" type="text/javascript"></script>
    <script src="../assets/plugins/datatables/dataTables.bootstrap.min.js" type="text/javascript"></script>
    
	<!-- FORMS SCRIPT -->
	 <!-- Select2 -->
    <script src="../assets/plugins/select2/select2.full.min.js" type="text/javascript"></script>
    <!-- date-range-picker -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js" type="text/javascript"></script>
    <script src="../assets/plugins/daterangepicker/daterangepicker.js" type="text/javascript"></script>
    
	
	<!-- AdminLTE App -->
    <script src="../assets/dist/js/app.min.js" type="text/javascript"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="../assets/dist/js/demo.js" type="text/javascript"></script>
    
	<!-- DATA TABLES-->
	<!-- page script -->
    <script type="text/javascript">
      $(function () {
		  //Initialize Select2 Elements
        $(".select2").select2();

        $("#bestcustomer").DataTable({
		  "paging": true,
          "lengthChange": false,
          "searching": true,
          "ordering": true,
          "info": true,
          "autoWidth": false
		});
		$("#TotalAreaPenjualanBasedPenagihan").DataTable({
		  "paging": true,
          "lengthChange": false,
          "searching": true,
          "ordering": true,
          "info": true,
          "autoWidth": false
		});
		$("#TotalAreaPenjualanBasedPengiriman").DataTable({
		  "paging": true,
          "lengthChange": false,
          "searching": true,
          "ordering": true,
          "info": true,
          "autoWidth": false
		});
		$("#DataPelanggan").DataTable({
		  "paging": true,
          "lengthChange": false,
          "searching": true,
          "ordering": true,
          "info": true,
          "autoWidth": false
		});
		
      });
    </script>
	
	<!-- FORMS -->
	<script type="text/javascript">
      $(function () {
        //Initialize Select2 Elements
        $(".select2").select2();

        
        //Date range picker
        $('#reservation').daterangepicker();
        //Date range picker with time picker
        $('#reservationtime').daterangepicker({timePicker: true, timePickerIncrement: 30, format: 'YYYY-MM-DD HH:mm:ss', showMeridian: false });
        //Date range as a button
        $('#daterange-btn').daterangepicker(
                {
                  ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                  },
                  startDate: moment().subtract(29, 'days'),
                  endDate: moment()
                },
        function (start, end) {
          $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }
        );

       

        
      });
    </script>
	<script type="text/javascript">
		$(document).ready(function(){
			var filter = $("#filter").val();
			$("#export2").attr("href", "export2.php?k1=<?php echo $cabang;?>&k2=<?php echo $periode1;?>&k3=<?php echo $periode2;?>&filter="+filter);
			
			$("#filter").keyup(function() {
				var filter = $("#filter").val();
				$("#export2").attr("href", "export2.php?k1=<?php echo $cabang;?>&k2=<?php echo $periode1;?>&k3=<?php echo $periode2;?>&filter="+filter);
			});
			
		});
	</script>
	
	<script type="text/javascript" src="../assets/plugins/treegrid/js/jquery.treegrid.js"></script>
	<script type="text/javascript" src="../assets/plugins/treegrid/js/jquery.treegrid.bootstrap3.js"></script>
	<script type="text/javascript">
    $(document).ready(function() {
        $('.tree').treegrid({
                    expanderExpandedClass: 'glyphicon glyphicon-minus',
                    expanderCollapsedClass: 'glyphicon glyphicon-plus',
					initialState : 'collapsed',
					treeColumn: 1,
                });
    });
	</script>
	
  </body>
</html>