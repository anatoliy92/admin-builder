<div class="constructor-table__panel border bg-light">
	@if ($langs)
		<div class="constructor-table__panel-langs d-flex">
			@foreach ($langs as $lang)
				<a v-bind:class="[currentLang == '{{ $lang->key }}' ? 'btn-secondary' : '', 'btn btn-default']" v-on:click="currentLang = '{{ $lang->key }}'"><i class="icon--language icon--language-{{ $lang->key }}"></i></a>
			@endforeach
		</div>
	@endif

	<ul class="constructor-table__panel-btns d-flex">
		<li class="border-right">
			<span class="text-center">Строки</span>

			<div class="d-flex ml-2">
				<a href="#" class="btn btn-danger" v-on:click="delRow" title="Удалить строку"><i class="fa fa-minus"></i></a>
				<a href="#" class="btn btn-light" v-on:click="addRow" title="Добавить строку"><i class="fa fa-plus"></i></a>
			</div>
		</li>

		<li>
			<span class="text-center">Столбцы</span>

			<div class="d-flex ml-2">
				<a href="#" class="btn btn-danger" v-on:click="delCol" title="Удалить последний столбец из таблицы"><i class="fa fa-minus"></i></a>
				<a href="#" class="btn btn-light" v-on:click="addCol" title="Добавить столбец в конец таблицы"><i class="fa fa-plus"></i></a>
			</div>
		</li>
	</ul>
</div>
