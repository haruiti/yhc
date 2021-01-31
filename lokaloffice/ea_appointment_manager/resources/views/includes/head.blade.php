<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    {{-- <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}"> --}}

    <!-- Bootstrap -->
   <link href="{{ asset('vendors/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- checkbox -->
    {{-- <link href="{{ asset('vendors/bootstrap/dist/css/checkbox.css') }}" rel="stylesheet"> --}}

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css" integrity="sha384-5sAR7xN1Nv6T6+dT2mhtzEpVJvfS3NScPQTrOxhwjIuvcA67KV2R5Jz6kr4abQsz" crossorigin="anonymous">

    <!-- NProgress -->
    {{-- <link href="{{ asset('vendors/nprogress/nprogress.css') }}" rel="stylesheet">

    <!-- iCheck -->
    <link href="{{ asset('vendors/iCheck/skins/flat/green.css') }}" rel="stylesheet">

    <!-- bootstrap-wysiwyg -->
    <link href="{{ asset('vendors/google-code-prettify/bin/prettify.min.css') }}" rel="stylesheet">

    <!-- bootstrap-progressbar -->
    <link href="{{ asset('vendors/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css') }}" rel="stylesheet">

    <!-- bootstrap-daterangepicker -->
    <link href="{{ asset('vendors/bootstrap-daterangepicker/daterangepicker.css') }}" rel="stylesheet">

    <!-- bootstrap-datetimepicker -->
    <link href="{{ asset('vendors/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css') }}" rel="stylesheet"> --}}

    <!-- Datatables -->
    <link href="{{ asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css') }}" rel="stylesheet">
    <link href="https://cdn.datatables.net/fixedcolumns/3.2.5/css/fixedColumns.bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/datatables.min.css') }}" rel="stylesheet">
    <script src="{{ asset('js/datatables.min.js') }} " defer></script>

    <!-- Select2 -->
    {{-- <link href="{{ asset('vendors/select2/dist/css/select2.min.css') }}" rel="stylesheet"> --}}

    <!-- Sweet Alert 2 -->
    {{-- <link href="{{ asset('vendors/sweetalert2/dist/sweetalert2.css') }}" rel="stylesheet"> --}}

    <!-- LOCAL CSS FILE -->
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- Custom Theme Style -->
    {{-- <link href="{{ asset('css/custom.css') }}" rel="stylesheet"> --}}

    <!-- bootstrap-daterangepicker -->
    {{-- <script src="{{ asset('vendors/moment/min/moment.min.js') }}"></script>
    <script src="{{ asset('vendors/bootstrap-daterangepicker/daterangepicker.js') }}"></script> --}}

    <script
    src="{{ asset('js/jquery-3.4.1.min.js') }}"></script>

    <title>Portal Lokal Office</title>
</head>

