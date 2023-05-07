

<!-- jQuery 3 -->
<script src="{{asset('admin-assets/vendors/jquery/dist/jquery.js')}}"></script>
	
	<!-- popper -->
	<script src="{{asset('admin-assets/vendors/popper/dist/popper.min.js')}}"></script>
	
	<!-- Bootstrap 4.0-->
	<script src="{{asset('admin-assets/vendors/bootstrap/dist/js/bootstrap.js')}}"></script>
	
	<!-- Morris.js charts -->
  <script src="{{asset('admin-assets/vendors/raphael/raphael.min.js')}}"></script>
  
	<script src="{{asset('admin-assets/vendors/morris.js/morris.min.js')}}"></script>	
	
	<!-- weather for demo purposes -->
	<script src="{{asset('admin-assets/vendors/weather-icons/WeatherIcon.js')}}"></script>
	
	<!-- Sparkline -->
	<script src="{{asset('admin-assets/vendors/jquery-sparkline/dist/jquery.sparkline.js')}}"></script>
	
	<!-- daterangepicker -->
  <script src="{{asset('admin-assets/vendors/moment/min/moment.min.js')}}"></script>
  
	<script src="{{asset('admin-assets/vendors/bootstrap-daterangepicker/daterangepicker.js')}}"></script>
	
	<!-- datepicker -->
	<script src="{{asset('admin-assets/vendors/bootstrap-datepicker/dist/js/bootstrap-datepicker.js')}}"></script>
	
	<!-- Bootstrap WYSIHTML5 -->
	<script src="{{asset('admin-assets/vendors/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.js')}}"></script>
	
	<!-- Slimscroll -->
	<script src="{{asset('admin-assets/vendors/jquery-slimscroll/jquery.slimscroll.js')}}"></script>
	
	<!-- FastClick -->
	<script src="{{asset('admin-assets/vendors/fastclick/lib/fastclick.js')}}"></script>
	
	<!-- peity -->
	<script src="{{asset('admin-assets/vendors/jquery.peity/jquery.peity.js')}}"></script>
	
	<!-- Unique_Admin App -->
	<script src="{{asset('admin-assets/js/template.js')}}"></script>
	
	<!-- Unique_Admin dashboard demo (This is only for demo purposes) -->
	<script src="{{asset('admin-assets/js/dashboard.js')}}"></script>
	
	<!-- Unique_Admin for demo purposes -->
	<script src="{{asset('admin-assets/js/demo.js')}}"></script>

  <script src="https://cdn.ckeditor.com/ckeditor5/21.0.0/classic/ckeditor.js"></script>

  <script src="{{asset('admin-assets/vendors/datatables.net/js/jquery.dataTables.min.js')}}"></script>

  <script src="{{asset('js/select2.full.min.js')}}" type="text/javascript"></script>
    
  <script src="{{asset('js/form-select2.min.js')}}" type="text/javascript"></script>

  <script src="{{asset('admin-assets/js/bootstrap-datetimepicker.min.js')}}"></script>


  <script>
	  $(document).ready(function () {

		var table = $('#dataTable').DataTable({
			"searching": true,
			"paging":   true,
			"info":     true,
			"iDisplayLength": "{{Setting::get('admin_take_count', 10)}}"
		});

		var check_box_table = $('#checkBoxData').DataTable({
			"searching": true,
			"paging":   true,
			"info":     true,
			"iDisplayLength": "{{Setting::get('admin_take_count', 10)}}",	
			"order": [[1, 'asc']],
			"columnDefs": [ {
				"targets": 0,
				"orderable": false
				} ]
		});

	  });


	  $(document).ready(function(){
		  setTimeout(function(){
             $('.dataTables_length').hide();
			 $('.dataTables_paginate').hide();
			 $('.dataTables_filter').hide();
		  },500);
	  })

	  $('.datetimepicker').datetimepicker({
            // minDate: new Date(),
            format: 'YYYY-MM-DD HH:mm:ss',
            icons: {
                  time: "fa fa-clock-o",
                  date: "fa fa-calendar",
                  up: "fa fa-arrow-up",
                  down: "fa fa-arrow-down"
              },
              sideBySide: true
        });

  </script>

<script>
(function () {
    // hold onto the drop down menu                                             
    var dropdownMenu;

    // and when you show it, move it to the body                                     
    $(window).on('show.bs.dropdown', function (e) {

    // grab the menu        
    dropdownMenu = $(e.target).find('.action-dropdown-menu');

    // detach it and append it to the body
    $('body').append(dropdownMenu.detach());

    // grab the new offset position
    var eOffset = $(e.target).offset();

    // make sure to place it where it would normally go (this could be improved)
    dropdownMenu.css({
        'display': 'block',
            'top': eOffset.top + $(e.target).outerHeight(),
            'left': eOffset.left
       });
    });

    // and when you hide it, reattach the drop down, and hide it normally                                                   
    $(window).on('hide.bs.dropdown', function (e) {
        $(e.target).append(dropdownMenu.detach());
        dropdownMenu.hide();
    });
})();
</script>