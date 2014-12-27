@extends('layouts/default')

{{-- Page title --}}
@section('title')
{{ trans('dzung/blog::categories/general.title') }} ::
@parent
@stop

{{-- Queue Assets --}}
{{ Asset::queue('underscore', 'underscore/js/underscore.js', 'jquery') }}
{{ Asset::queue('data-grid', 'cartalyst/js/data-grid.js', 'underscore') }}
{{ Asset::queue('moment', 'moment/js/moment.js') }}

{{ Asset::queue('dzung-blog', 'dzung/blog::css/style.css', 'bootstrap') }}
{{ Asset::queue('dzung-blog', 'dzung/blog::js/script.js', 'jquery') }}

{{-- Partial Assets --}}
@section('assets')
@parent
@stop

{{-- Inline Styles --}}
@section('styles')
@parent
<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.4/css/jquery.dataTables.css">
@stop

{{-- Inline Scripts --}}
@section('scripts')
@parent

<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.4/js/jquery.dataTables.js"></script>
<script>
// my own script

$(document).ready( function () {
    $('#table_id').DataTable({
        "dom": '<"toolbar">frtlp'
    });
    var create_link = '<?php echo Request::url().'/create'; ?>';
     $("div.toolbar").html('<a href="'+create_link+'">Create</a>');



});
</script>
@stop

{{-- Page content --}}
@section('content')

{{-- Page header --}}
<div class="page-header">

	<h1>{{{ trans('dzung/blog::categories/general.title') }}}</h1>

</div>

<table id="table_id" class="display">
	<thead>
		<tr>
			{{--<th><input type="checkbox" name="checkAll" id="checkAll"></th>--}}
			<th>{{{ trans('dzung/blog::categories/table.name') }}}</th>
			<th>{{{ trans('dzung/blog::posts/table.num_post') }}}</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
	<?php

	    foreach($categories as $category){
	        $edit_link =  Request::url().'/'.$category->id.'/edit';
            $delete_link =  Request::url().'/'.$category->id.'/delete';
	        echo "<tr>";
	        echo "<td>".$category->name."</td>";
	        echo "<td>".$category->num_post."</td>";
	        echo "<td><a href='".$edit_link."'>Edit</a> | <a href='".$delete_link."'>Delete</a></td>";
	        echo "</tr>";
	    }


	?>
	</tbody>
</table>

@include('dzung/blog::grids/category/results')
@include('dzung/blog::grids/category/filters')
@include('dzung/blog::grids/category/pagination')
@include('dzung/blog::grids/category/no_results')
@include('dzung/blog::grids/category/no_filters')

@stop
