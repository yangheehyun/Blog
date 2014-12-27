<?php
/**
 * Created by PhpStorm.
 * User: dungdc40
 * Date: 12/12/2014
 * Time: 14:49
 */
?>
<html>
<head>
<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.4/css/jquery.dataTables.css">

<!-- jQuery -->
<script type="text/javascript" charset="utf8" src="//code.jquery.com/jquery-1.10.2.min.js"></script>

<!-- DataTables -->
<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.4/js/jquery.dataTables.js"></script>

    <script type="text/javascript">

    </script>
</head>
<body>
<h2>Index page contain from category</h2>
Category:<br/>
@foreach($categories as $category)
    Category name: {{$category->name}}<br/>
    Number of post: {{$category->num_post}} <br/>
@endforeach
<hr/>
Tag:<br/>
@foreach($tags as $tag)
    Tag name: {{$tag->name}}<br/>

@endforeach
<hr/>

@foreach($posts as $post)
    Post title: {{link_to('blog/post/'.$post->slug,$post->title, $attributes = array(), $secure = null)}}<br/>
    Post slug: {{$post->slug}}<br/>
    Created at: {{$post->created_at}} <br/>
    Post intro: {{$post->intro}}<br/>
    Post thumbnail: {{$post->thumbnail}}<br/>
    Post tags: <?php var_dump( $post->tags );?><br/>
    Post content: {{$post->content}}<br/>
    <hr>
@endforeach

<?php echo $posts->links(); ?>
</body>
</html>