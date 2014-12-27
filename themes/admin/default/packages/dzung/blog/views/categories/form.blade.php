@extends('layouts/default')

{{-- Page title --}}
@section('title')
	@parent
	: {{{ trans("dzung/blog::categories/general.{$mode}") }}} {{{ $category->exists ? '- ' . $category->name : null }}}
@stop

{{-- Queue assets --}}
{{ Asset::queue('bootstrap.tabs', 'bootstrap/js/tab.js', 'jquery') }}
{{ Asset::queue('blog', 'dzung/blog::js/script.js', 'jquery') }}

{{-- Inline scripts --}}
@section('scripts')
@parent
<script>

$("#name").focusout(function(e){
 if($('#slug').val().length == 0){
   var cat_name = $('#name').val();

   var url_request = "{{ URL::toAdmin('blog/categories/getslug')}}/";
   //url_request = url_request.concat(cat_name);

   $.ajax({
       url: url_request,
       type: 'GET',
       data: {name: cat_name},
       success: function(response)
       {

           $('#slug').val(response);
       }
   });
 }

});
</script>
@stop

{{-- Inline styles --}}
@section('styles')
@parent
@stop

{{-- Page content --}}
@section('content')

{{-- Page header --}}
<div class="page-header">

	<h1>{{{ trans("dzung/blog::categories/general.{$mode}") }}} <small>{{{ $category->name }}}</small></h1>

</div>

{{-- Content form --}}
<form id="blog-form" action="{{ Request::fullUrl() }}" method="post" accept-char="UTF-8" autocomplete="off">

	{{-- CSRF Token --}}
	<input type="hidden" name="_token" value="{{ csrf_token() }}">

	{{-- Tabs --}}
	<ul class="nav nav-tabs">
		<li class="active"><a href="#general" data-toggle="tab">{{{ trans('dzung/blog::general.tabs.general') }}}</a></li>
		<li><a href="#attributes" data-toggle="tab">{{{ trans('dzung/blog::general.tabs.attributes') }}}</a></li>
	</ul>

	{{-- Tabs content --}}
	<div class="tab-content tab-bordered">

		{{-- General tab --}}
		<div class="tab-pane active" id="general">

			<div class="row">

				<div class="form-group{{ $errors->first('name', ' has-error') }}">

					<label for="name" class="control-label">{{{ trans('dzung/blog::categories/form.name') }}} <i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('dzung/blog::categories/form.name_help') }}}"></i></label>

					<input type="text" class="form-control" name="name" id="name" placeholder="{{{ trans('dzung/blog::categories/form.name') }}}" value="{{{ Input::old('name', $category->name) }}}">

					<span class="help-block">{{{ $errors->first('name', ':message') }}}</span>

				</div>
                <div class="form-group{{ $errors->first('slug', ' has-error') }}">

                	<label for="name" class="control-label">Slug <i class="fa fa-info-circle" data-toggle="popover" data-content="Category slug"></i></label>

                	<input type="text" class="form-control" name="slug" id="slug" placeholder="Slug" value="{{{ Input::old('slug', $category->slug) }}}">

                	<span class="help-block">{{{ $errors->first('slug', ':message') }}}</span>

                </div>



			</div>

		</div>

		{{-- Attributes tab --}}
		<div class="tab-pane clearfix" id="attributes">

			@widget('platform/attributes::entity.form', [$category])

		</div>

	</div>

	{{-- Form actions --}}
	<div class="row">

		<div class="col-lg-12 text-right">

			{{-- Form actions --}}
			<div class="form-group">

				<button class="btn btn-success" type="submit">{{{ trans('button.save') }}}</button>

				<a class="btn btn-default" href="{{{ URL::toAdmin('blog/categories') }}}">{{{ trans('button.cancel') }}}</a>

				<a class="btn btn-danger" data-toggle="modal" data-target="modal-confirm" href="{{ URL::toAdmin("blog/categories/{$category->id}/delete") }}">{{{ trans('button.delete') }}}</a>

			</div>

		</div>

	</div>

</form>

@stop
