<?php namespace Avl\AdminBuilder\Models;

use Illuminate\Database\Eloquent\Model;
use Avl\AdminBuilder\Models\Table;
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

		public function __construct ()
		{
			$this->lang = LaravelLocalization::getCurrentLocale();
		}

		public function section ()
		{
			return $this->belongsTo(Table::class, 'table_id', 'id');
		}

		public function scopeHead($query)
		{
			return $query->whereHead(true);
		}
}
