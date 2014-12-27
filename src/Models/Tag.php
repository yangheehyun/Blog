<?php namespace Dzung\Blog\Models;

use Platform\Attributes\Models\Entity;

class Tag extends Entity {

	/**
	 * {@inheritDoc}
	 */
	protected $table = 'blog_tags';

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
	protected $eavNamespace = 'dzung/blog.tag';

    public function posts(){
        return $this->belongsToMany('\Dzung\Blog\Models\Post');
    }
}
