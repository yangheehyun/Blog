<?php namespace Dzung\Blog\Controllers\Frontend;

use Dzung\Blog\Models\Category;
use Platform\Foundation\Controllers\BaseController;
use View;
use Dzung\Blog\Models\Post;
use Dzung\Blog\Models\Tag;
use Dzung\Blog\Controllers\Frontend\CategoriesController;
use Dzung\Blog\Controllers\Frontend\TagsController;
class PostsController extends BaseController {

	/**
	 * Return the main view.
	 *
	 * @return \Illuminate\View\View
	 */
	public function index()
	{
        $posts = Post::paginate(5);
        foreach($posts as $post){
            $post->intro = $this->intro_content($post->content);
            $post->thumbnail = $this->get_thumbnail($post->content);
            $post->tags = $this->get_tags($post->id);
        }

        $categories = CategoriesController::category_sidebar();
        $tags = TagsController::tag_sidebar();
		return View::make('dzung/blog::index')->with('posts', $posts)->with('categories', $categories)
            ->with('tags', $tags);

	}

    // get the post to display in frontend
    public function get_post($slug){
        $post = Post::where('slug', '=', $slug)->get()->first();
        $post->intro = $this->intro_content($post->content);
        $post->thumbnail = $this->get_thumbnail($post->content);
        $post->tags = $this->get_tags($post->id);
        $categories = CategoriesController::category_sidebar();
        $tags = TagsController::tag_sidebar();
        return View::make('dzung/blog::single')->with('post', $post)->with('categories', $categories)
            ->with('tags', $tags);
    }

    // posts from catgory
    public function get_post_from_cat($slug){
        $cat_id = Category::where('slug', '=', $slug)->first()->id;
        $posts = Post::where('category_id', '=', $cat_id)->paginate(5);

        foreach($posts as $post){
            $post->intro = $this->intro_content($post->content);
            $post->thumbnail = $this->get_thumbnail($post->content);
            $post->tags = $this->get_tags($post->id);
        }

        $categories = CategoriesController::category_sidebar();
        $tags = TagsController::tag_sidebar();
        return View::make('dzung/blog::category')->with('posts', $posts)->with('categories', $categories)
            ->with('tags', $tags);
    }

    // posts from tag
    public function get_post_from_tag($tag_name){
        $posts = Tag::where('name', '=', $tag_name)->first()->posts;
        foreach($posts as $post){
            $post->intro = $this->intro_content($post->content);
            $post->thumbnail = $this->get_thumbnail($post->content);
            $post->tags = $this->get_tags($post->id);
        }

        $categories = CategoriesController::category_sidebar();
        $tags = TagsController::tag_sidebar();
        return View::make('dzung/blog::tag')->with('posts', $posts)->with('categories', $categories)
            ->with('tags', $tags);
    }

    // get post thumbnail from post id
    public function get_thumbnail($content){

        // get img
        preg_match('/<img[^>]+>/i',$content, $img);
        if(!empty($img)) {
            // get src of this img
            preg_match('/(src)=("[^"]*")/i', $img[0], $thumbnail);
            $thumbnail_src = str_replace("\"", "", $thumbnail[2]);
            return $thumbnail_src;
        }
        return "";
    }

    // get tag of a posts
    public function get_tags($id){
        $tags = Post::find($id)->tags;
        $post_tags = "";
        foreach($tags as $tag)
            $post_tags[] = $tag->name;
        return $post_tags;
    }
    //get content before readmore
    public function intro_content($content){
        $content =$this->strbefore($content, '<div style="page-break-after: always"><span style="display:none">&nbsp;</span></div>');
        return strip_tags($content);
    }

    //
    public function strbefore($string, $substring) {
        $pos = strpos($string, $substring);
        if ($pos === false)
            return $string;
        else
            return(substr($string, 0, $pos));
    }

    public function table(){
        return
            '{"data": [
                alibaba,
                uahuah,
                jaiajsia,
            ]
            }';
    }
}
