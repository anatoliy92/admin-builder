<?php namespace Avl\AdminBuilder\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ModelTrait;
use LaravelLocalization;

class Table extends Model
{
		use ModelTrait;

		protected $table = 'builder-table';

		protected $modelName = __CLASS__;

		protected $lang = null;

		protected $guarded = [];

		public function __construct (array $attributes = array())
		{
			parent::__construct($attributes);

			$this->lang = LaravelLocalization::getCurrentLocale();
		}

		public function section ()
		{
			return $this->belongsTo('App\Models\Sections', 'section_id', 'id');
		}

		public function data ()
		{
			return $this->hasMany('Avl\AdminBuilder\Models\TableData', 'table_id');
		}

		public function scopeDefault($query)
		{
			return $query->where('is_default', true);
		}

		public function getEditUrl ()
		{
			return route('adminbuilder::sections.builder.index', ['id' => $this->section_id]);
		}
}
