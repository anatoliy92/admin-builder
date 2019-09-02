<?php namespace Avl\AdminBuilder\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ModelTrait;
use LaravelLocalization;

class TableData extends Model
{
		use ModelTrait;

		protected $table = 'builder-table-data';

		protected $primaryKey = null;

		public $incrementing = false;

		protected $guarded = [];

		protected $modelName = __CLASS__;

		protected $lang = null;

		protected $casts = [
			'head' => 'boolean',
		];

		protected $touches = ['table'];

		public function __construct (array $attributes = array())
		{
			parent::__construct($attributes);

			$this->lang = LaravelLocalization::getCurrentLocale();
		}

		// public function section ()
		// {
		// 	return $this->belongsTo('Avl\AdminBuilder\Models\Table', 'table_id', 'id');
		// }

		public function scopeHead($query)
		{
			return $query->whereHead(true);
		}

		public function table ()
		{
			return $this->belongsTo('Avl\AdminBuilder\Models\Table', 'table_id', 'id');
		}
}
