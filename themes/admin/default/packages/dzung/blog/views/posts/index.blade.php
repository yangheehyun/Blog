@extends('layouts/default')

{{-- Page title --}}
@section('title')
{{ trans('dzung/blog::posts/general.title') }} ::
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
<style type="text/css">
.toolbar {
    float: left;
}
</style>

@parent
<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.4/css/jquery.dataTables.css">
@stop

{{-- Inline Scripts --}}
@section('scripts')
@parent
{{--<!-- jQuery -->--}}
{{--<script type="text/javascript" charset="utf8" src="//code.jquery.com/jquery-1.10.2.min.js"></script>--}}
<!-- DataTables -->
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

	<h1>{{{ trans('dzung/blog::posts/general.title') }}}</h1>

</div>


<table id="table_id" class="display">
	<thead>
		<tr>
			{{--<th><input type="checkbox" name="checkAll" id="checkAll"></th>--}}
			<th>{{{ trans('dzung/blog::posts/table.id') }}}</th>
			<th>{{{ trans('dzung/blog::posts/table.title') }}}</th>
			<th>{{{ trans('dzung/blog::posts/table.slug') }}}</th>
			<th>{{{ trans('dzung/blog::posts/table.category_name') }}}</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
	<?php

	    foreach($posts as $post){
	        $edit_link =  Request::url().'/'.$post->id.'/edit';
            $delete_link =  Request::url().'/'.$post->id.'/delete';
	        echo "<tr>";
	        echo "<td>".$post->id."</td>";
	        echo "<td>".$post->title."</td>";
	        echo "<td>".$post->slug."</td>";
	        echo "<td>".$post->category->name."</td>";
	        echo "<td><a href='".$edit_link."'>Edit</a> | <a href='".$delete_link."'>Delete</a></td>";
	        echo "</tr>";
	    }


	?>
	</tbody>
</table>



@include('dzung/blog::grids/post/results')
@include('dzung/blog::grids/post/filters')
@include('dzung/blog::grids/post/pagination')
@include('dzung/blog::grids/post/no_results')
@include('dzung/blog::grids/post/no_filters')

@stop
