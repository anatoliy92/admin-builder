@extends('avl.default')

@section('js')
	<script src="/avl/js/vue.min.js"></script>
	<script src="{{ asset('vendor/adminbuilder/js/constructor.js') }}" charset="utf-8"></script>
@endsection

@section('main')
	<div class="card" id="constructor-table">
		<div class="card-header">
			<i class="fa fa-align-justify"></i> Конструктор таблиц - @[[ tableName ]]@

			<div class="card-actions">
				<a href="{{ route('adminbuilder::constructor.index') }}" class="btn btn-default pl-3 pr-3" style="width: 70px;" title="Назад"><i class="fa fa-arrow-left"></i></a>
				<button type="submit" form="submit" name="button" value="save" class="btn btn-success pl-3 pr-3" style="width: 70px;" title="Сохранить и перейти к списку"><i class="fa fa-floppy-o"></i></button>
			</div>
		</div>

		<div class="card-body">
			{{ Form::hidden(null, $id, ['id' => 'tableId']) }}

			<div class="constructor-table">
				<form v-on:submit="updateTable" action="" method="post" id="submit">

					@include('adminbuilder::constructor.control-panel')

					<div class="border p-3" style="margin: -1px 0">

						<div class="form-group">
							{{ Form::text(null, null, ['class' => 'form-control', 'placeholder' => 'Название шаблона', 'v-model' => 'tableName']) }}
						</div>

						<div class="constructor-table__table" v-if="names.length > 0">
							<div class="table-responsive" id="constructor-table-table">
								<table class="table table-bordered mb-0">
									<tr v-for="(row, indexRow) in names">
										<td v-for="(col, indexCol) in row" v-bind:class="[row[indexCol].head ? 'bg-light' : '', 'p-1']">
											<div class="input-group">
												<input type="text"
													v-for="(text, langKey) in col.translates"
													v-bind:class="[currentLang == langKey ? 'd-block' : 'd-none', 'form-control']"
													v-model="row[indexCol]['translates'][langKey]" >

												<div v-bind:class="[setHeaders ? 'd-flex' : 'd-none', 'input-group-append']">
													<span class="input-group-text">
														<input type="checkbox" v-model="row[indexCol].head" >
													</span>
												</div>
											</div>
										</td>
									</tr>
								</table>

							</div>

							{{-- <a class="constructor-table__btn constructor-table__btn--column" href="#" v-on:click="addCol" title="Добавить столбец"><i class="fa fa-plus"></i></a>
							<a class="constructor-table__btn constructor-table__btn--row" href="#" v-on:click="addRow" title="Добавить строку"><i class="fa fa-plus"></i></a> --}}
						</div>

						<div v-else class="alert alert-danger" role="alert">Таблица пуста</div>
					</div>

					@include('adminbuilder::constructor.control-panel')

				</form>
			</div>
		</div>

	</div>
@endsection
