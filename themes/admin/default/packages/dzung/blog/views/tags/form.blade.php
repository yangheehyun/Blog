@extends('layouts/default')

{{-- Page title --}}
@section('title')
	@parent
	: {{{ trans("dzung/blog::tags/general.{$mode}") }}} {{{ $tag->exists ? '- ' . $tag->name : null }}}
@stop

{{-- Queue assets --}}
{{ Asset::queue('bootstrap.tabs', 'bootstrap/js/tab.js', 'jquery') }}
{{ Asset::queue('blog', 'dzung/blog::js/script.js', 'jquery') }}

{{-- Inline scripts --}}
@section('scripts')
@parent
@stop

{{-- Inline styles --}}
@section('styles')
@parent
@stop

{{-- Page content --}}
@section('content')

{{-- Page header --}}
<div class="page-header">

	<h1>{{{ trans("dzung/blog::tags/general.{$mode}") }}} <small>{{{ $tag->name }}}</small></h1>

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

					<label for="name" class="control-label">{{{ trans('dzung/blog::tags/form.name') }}} <i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('dzung/blog::tags/form.name_help') }}}"></i></label>

					<input type="text" class="form-control" name="name" id="name" placeholder="{{{ trans('dzung/blog::tags/form.name') }}}" value="{{{ Input::old('name', $tag->name) }}}">

					<span class="help-block">{{{ $errors->first('name', ':message') }}}</span>

				</div>


			</div>

		</div>

		{{-- Attributes tab --}}
		<div class="tab-pane clearfix" id="attributes">

			@widget('platform/attributes::entity.form', [$tag])

		</div>

	</div>

	{{-- Form actions --}}
	<div class="row">

		<div class="col-lg-12 text-right">

			{{-- Form actions --}}
			<div class="form-group">

				<button class="btn btn-success" type="submit">{{{ trans('button.save') }}}</button>

				<a class="btn btn-default" href="{{{ URL::toAdmin('blog/tags') }}}">{{{ trans('button.cancel') }}}</a>

				<a class="btn btn-danger" data-toggle="modal" data-target="modal-confirm" href="{{ URL::toAdmin("blog/tags/{$tag->id}/delete") }}">{{{ trans('button.delete') }}}</a>

			</div>

		</div>

	</div>

</form>

@stop
