<div class="constructor-table__panel border bg-light">
	@if ($langs)
		<div class="constructor-table__panel-langs d-flex">
			@foreach ($langs as $lang)
				<a v-bind:class="[currentLang == '{{ $lang->key }}' ? 'btn-secondary' : '', 'btn btn-default']" v-on:click="currentLang = '{{ $lang->key }}'"><i class="icon--language icon--language-{{ $lang->key }}"></i></a>
			@endforeach
			<span class="border-left btn">@[[ settings.title_ru ]]@</span>
		</div>
	@endif

	<ul class="constructor-table__panel-btns d-flex">
		<li class="border-left pl-2 border-right">
			<span class="text-center">Строки</span>

			<div class="d-flex ml-2">
				<a href="#" class="btn btn-danger" v-on:click="delRow" title="Удалить строку"><i class="fa fa-minus"></i></a>
				<a href="#" class="btn btn-light" v-on:click="addRow" title="Добавить строку"><i class="fa fa-plus"></i></a>
			</div>
		</li>

		<li class="border-right mr-5">
			<span class="text-center">Столбцы</span>

			<div class="d-flex ml-2">
				<a href="#" class="btn btn-danger" v-on:click="delCol" title="Удалить последний столбец из таблицы"><i class="fa fa-minus"></i></a>
				<a href="#" class="btn btn-light" v-on:click="addCol" title="Добавить столбец в конец таблицы"><i class="fa fa-plus"></i></a>
			</div>
		</li>

		<li>
			<a href="#" v-bind:class="['btn btn-warning']" title="График"><i class="fa fa-bar-chart-o"></i></a>

			<a href="#" v-if="showBlock == 'table'" v-bind:class="[setHeaders ? 'active' : '', 'btn btn-primary']" v-on:click="setHeaders = !setHeaders" title="Указать заголовки"><i class="fa fa-tags"></i></a>
			<a href="#" v-else v-on:click="showBlock = 'table'" class="btn btn-primary" title="Показать таблицу"><i class="fa fa-table"></i></a>

			<a href="#" v-bind:class="['btn btn-dark']" v-on:click="showBlock = 'settings'" title="Настройки таблицы"><i class="fa fa-cogs"></i></a>
		</li>
	</ul>
</div>
