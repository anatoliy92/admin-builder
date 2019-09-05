<?php namespace Avl\AdminBuilder\Controllers\Site;

use App\Http\Controllers\Site\Sections\SectionsController;
use Avl\AdminBuilder\Traits\BuilderTrait;
use Avl\AdminBuilder\Models\Table;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Cache;
use View;

class BuilderController extends SectionsController
{
	use BuilderTrait;

	public function __construct (Request $request)
	{
		parent::__construct($request);

		// dd($this->section);
	}

	public function index (Request $request)
	{
		$template = 'site.templates.builder.short.' . $this->getTemplateFileName($this->section->current_template->file_short);

		$template = (View::exists($template)) ? $template : 'site.templates.builder.short.default';

		$tables = $this->section->tables()->isPublished()->orderBy('published_at', 'DESC')->paginate($this->section->current_template->records);

		return view($template, [
				'tables' => $tables,
				'pagination' => $tables->appends($_GET)->links()
		]);
	}

	public function show (Request $request)
	{
		$template = 'site.templates.builder.full.' . $this->getTemplateFileName($this->section->current_template->file_full);

		$template = (View::exists($template)) ? $template : 'site.templates.builder.full.default';

		$table = $this->section->tables()->isPublished()->whereId($request->id)->firstOrFail();

		$tableData = Cache::remember('table-'.$this->lang .'-' . $table->id, 60*20, function () use ($table) {
			return $this->getBodyTable($table);
		});

		return view($template, [
				'table' => $table,
				'tableData' => $tableData
		]);
	}

	public function getQuery ($result, $request)
	{

		$result = $result->where('good_' . $this->lang, 1);

		if ($request->input('date')) {
			$result = $result->whereDate('published_at', $request->input('date'));
		}

		return $result->where('published_at', '<=', Carbon::now());
	}

}
