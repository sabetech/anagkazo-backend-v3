<html lang="en">
	<link type="text/css" id="dark-mode" rel="stylesheet" href="">
	<style type="text/css" id="dark-mode-custom-style">

	</style>
	<head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="stylesheet" href="{{ url('css/v2/main.css') }}">
		<link rel="stylesheet" type="text/css" href="{{ url('css/v2/loxo.css') }}">
        <link rel="stylesheet" href="css/v2/main.css">
        <link rel="stylesheet" type="text/css" href="{{ url('css/daterangepicker.css') }}">
        <title>{{ isset($pageTitle) ? $pageTitle : 'Log' }}</title>
    </head>
    <body data-gr-c-s-loaded="true">
    	@if (isset($notification_alert))
			<div class="alert alert-success alert-dismissible fade show" role="alert">
			      {!! $notification_msg !!}
			      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
			          <span aria-hidden="true"><i class="icon mdi mdi-close" aria-hidden="true"></i></span>
			      </button>
			  </div>
		@endif

		@yield('content')

		<script src="{{ url('js/v2/jquery.min.js') }}"></script>
        <script src="{{ url('js/jquery-ui.min.js') }}"></script>
        <script src="{{ url('js/select2.full.min.js') }}"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <script src="{{ url('js/daterangepicker.js') }}"></script>


		@yield('scripts')

	</body>
</html>
