<?php namespace Dzung\Blog\Models;

use Platform\Attributes\Models\Entity;

class Post extends Entity {

	/**
	 * {@inheritDoc}
	 */
	protected $table = 'blog_posts';

	/**
	 * {@inheritDoc}
	 */
	protected $guarded = [
		'id',
	];

	/**
	 * {@inheritDoc}
	 */
	protected $with = [
		'values.attribute',
	];

	/**
	 * {@inheritDoc}
	 */
	protected $eavNamespace = 'dzung/blog.post';
    public function category(){
        return $this->belongsTo('\Dzung\Blog\Models\Category');
    }

    public function tags(){
        return $this->belongsToMany('\Dzung\Blog\Models\Tag');
    }
}
