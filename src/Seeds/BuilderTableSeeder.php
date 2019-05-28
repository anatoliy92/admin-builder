<?php namespace Avl\AdminBuilder\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Menu;
use DB;

class BuilderTableSeeder extends Seeder
{
		/**
		 * Run the database seeds.
		 *
		 * @return void
		 */
		public function run()
		{
				$menu = Menu::whereRoute('adminbuilder::constructor.index')->orderBy('order', 'DESC')->first();
				$lastOrder = Menu::whereNull('parent_id')->orderBy('order', 'DESC')->first();

				if (is_null($menu)) {
					DB::table('menu')->insert([
							'title' => 'Конструктор таблиц',
							'url' => '',
							'target' => '_self',
							'route' => 'adminbuilder::constructor.index',
							'model' => 'Avl\AdminBuilder\Models\Table',
							'icon_class' => 'fa fa-table',
							'order' => $lastOrder->order++,
							'created_at' => date("Y-m-d H:i:s"),
							'updated_at' => date("Y-m-d H:i:s")
					]);
				}

		}
}
