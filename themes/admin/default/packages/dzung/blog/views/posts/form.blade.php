@extends('layouts/default')

{{-- Page title --}}
@section('title')
	@parent
	: {{{ trans("dzung/blog::posts/general.{$mode}") }}} {{{ $post->exists ? '- ' . $post->name : null }}}
@stop

{{-- Queue assets --}}
{{ Asset::queue('bootstrap.tabs', 'bootstrap/js/tab.js', 'jquery') }}
{{ Asset::queue('blog', 'dzung/blog::js/script.js', 'jquery') }}

{{-- Inline scripts --}}
@section('scripts')
<script>

$("#title").focusout(function(e){
 if($('#slug').val().length == 0){
   var post_title = $('#title').val();
   var url_request = "{{ URL::toAdmin('blog/posts/getslug')}}/";
   url_request = url_request.concat(post_title);
   $.ajax({
       url: url_request,
       type: 'GET',
       data: { title: post_title },
       success: function(response)
       {
           $('#slug').val(response);
       }
   });
 }

});
</script>
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

	<h1>{{{ trans("dzung/blog::posts/general.{$mode}") }}} <small>{{{ $post->name }}}</small></h1>

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

				<div class="form-group{{ $errors->first('title', ' has-error') }}">

					<label for="title" class="control-label">{{{ trans('dzung/blog::posts/form.title') }}} <i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('dzung/blog::posts/form.title_help') }}}"></i></label>

					<input type="text" class="form-control" name="title" id="title" placeholder="{{{ trans('dzung/blog::posts/form.title') }}}" value="{{{ Input::old('title', $post->title) }}}">

					<span class="help-block">{{{ $errors->first('title', ':message') }}}</span>

				</div>

				<div class="form-group{{ $errors->first('slug', ' has-error') }}">

                					<label for="slug" class="control-label">{{{ trans('dzung/blog::posts/form.slug') }}} <i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('dzung/blog::posts/form.slug_help') }}}"></i></label>

                					<input type="text" class="form-control" name="slug" id="slug" placeholder="{{{ trans('dzung/blog::posts/form.slug') }}}" value="{{{ Input::old('slug', $post->slug) }}}">

                					<span class="help-block">{{{ $errors->first('slug', ':message') }}}</span>

                </div>

				<div class="form-group{{ $errors->first('content', ' has-error') }}">

					<label for="content" class="control-label">{{{ trans('dzung/blog::posts/form.content') }}} <i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('dzung/blog::posts/form.content_help') }}}"></i></label>

					<textarea class="form-control" name="content" id="content" placeholder="{{{ trans('dzung/blog::posts/form.content') }}}">{{{ Input::old('content', $post->content) }}}</textarea>
					<script src="{{ URL::asset('themes/admin/default/assets/ckeditor/ckeditor.js') }}"></script>
					<script src="{{ URL::asset('themes/admin/default/assets/ckfinder/ckfinder.js') }}"></script>
                    <script>
                            var ck_link = '<?php echo Request::root().'/themes/admin/default/assets/';?>';
                            var ck_finder = ck_link.concat('ckfinder');
                            var editor = CKEDITOR.replace( 'content');
                            CKFinder.setupCKEditor( editor, ck_finder );

                             
                    </script>
					<span class="help-block">{{{ $errors->first('content', ':message') }}}</span>

				</div>

                <div class="form-group{{ $errors->first('title', ' has-error') }}">

                	<label for="tags" class="control-label">{{{ trans('dzung/blog::tags/form.tag') }}} <i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('dzung/blog::tags/form.tag') }}}"></i></label>

                	<input type="text" class="form-control" name="tags" id="tags"  placeholder="Enter your tags" value="{{{ Input::old('tags', $tags) }}}">

                	<span class="help-block">{{{ $errors->first('tags', ':message') }}}</span>

                </div>


				<div class="form-group{{ $errors->first('category_id', ' has-error') }}">

					<label for="category_id" class="control-label">{{{ trans('dzung/blog::posts/form.category_id') }}} <i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('dzung/blog::posts/form.category_id_help') }}}"></i></label>

					{{--<input type="text" class="form-control" name="category_id" id="category_id" placeholder="{{{ trans('dzung/blog::posts/form.category_id') }}}" value="{{{ Input::old('category_id', $post->category_id) }}}">--}}

                    <?php //var_dump($post); ?>
                    <select name ="category_id">

                        <?php
                            $old_cat_id = 0;
                            foreach($categories as $category){
                                if($category->id == $post->category_id){
                                    $selected = "selected='selected'";
                                    $old_cat_id = $category->id;
                                }

                                else $selected = "";
                                echo '<option name='.$category->id.' '.$selected.'>'.$category->name.'</option>';
                            }
                        ?>

                    </select>
                    <input type="hidden" name="old_cat_id" value="{{$old_cat_id}}">
					<span class="help-block">{{{ $errors->first('category_id', ':message') }}}</span>

				</div>


			</div>

		</div>

		{{-- Attributes tab --}}
		<div class="tab-pane clearfix" id="attributes">

			@widget('platform/attributes::entity.form', [$post])

		</div>

	</div>

	{{-- Form actions --}}
	<div class="row">

		<div class="col-lg-12 text-right">

			{{-- Form actions --}}
			<div class="form-group">

				<button class="btn btn-success" type="submit">{{{ trans('button.save') }}}</button>

				<a class="btn btn-default" href="{{{ URL::toAdmin('blog/posts') }}}">{{{ trans('button.cancel') }}}</a>

				<a class="btn btn-danger" data-toggle="modal" data-target="modal-confirm" href="{{ URL::toAdmin("blog/posts/{$post->id}/delete") }}">{{{ trans('button.delete') }}}</a>

			</div>

		</div>

	</div>

</form>

@stop
