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
		<li>
			<a href="#" v-bind:class="['btn btn-light border-left']" v-on:click="showBlock = 'graph'" title="График"><i class="fa fa-bar-chart-o"></i></a>
			<a @click="mergeCell" v-if="!checkCellIsMerged()" class="btn btn-light border-left" title="Объеденить ячейки">
				<img src="/avl/img/icons/merge-cells.svg" width="16" height="16" style="margin-bottom: 1px">
			</a>
			<a @click="clearMergeCell" v-if="checkCellIsMerged()" class="btn btn-light border-left" title="Объеденить ячейки">
				<img src="/avl/img/icons/merge-cells.svg" width="16" height="16" style="margin-bottom: 1px">
			</a>

			<a href="#" v-if="showBlock == 'table'" v-bind:class="[setHeaders ? 'active' : '', 'btn btn-light border-left']" v-on:click="setHeaders = !setHeaders" title="Указать заголовки"><i class="fa fa-tags"></i></a>
			<a href="#" v-else v-on:click="showBlock = 'table'" class="btn btn-light border-left" title="Показать таблицу"><i class="fa fa-table"></i></a>

			<a href="#" v-bind:class="['btn btn-light border-left']" v-on:click="showBlock = 'settings'" title="Настройки таблицы"><i class="fa fa-cogs"></i></a>
		</li>
	</ul>
</div>
