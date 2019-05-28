@extends('avl.default')

@section('js')
	<script src="/avl/js/vue.min.js"></script>
	<script src="{{ asset('vendor/adminbuilder/js/builder.js') }}" charset="utf-8"></script>
@endsection

@section('main')
	<div class="card" id="builder-table">
		<div class="card-header">
			<i class="fa fa-align-justify"></i> Конструктор

			<div class="card-actions">
				<button type="submit" form="submit" name="button" value="save" class="btn btn-success pl-3 pr-3" style="width: 70px;" title="Сохранить и перейти к списку"><i class="fa fa-floppy-o"></i></button>
			</div>
		</div>

		<div class="card-body table-responsive">

			{{ Form::hidden(null, $sectionId, ['id' => 'section--id']) }}
			{{ Form::hidden(null, $existTable, ['id' => 'exist-table']) }}

			<div class="card bg-light" v-if="existTable == false">
				<div class="card-body p-3">
					<div class="row">

						<div class="col-12 col-sm-9">
							<div class="input-group">
								<select class="form-control" v-model="selectedTemplate" v-on:change="getTemplateData">
									<option value="0">Выбрать шаблон</option>
									<option v-for="template in templates" v-bind:value="template.id">@[[ template.title ]]@</option>
								</select>
							</div>
						</div>

						<div class="col-12 col-sm-1 text-center">
							<div class="d-table h-100 w-100">
								<div class="d-table-row">
									<div class="d-table-cell align-middle">или</div>
								</div>
							</div>
						</div>

						<div class="col-12 col-sm-2">
							<a href="#" class="btn btn-success btn-block" v-on:click="createNewTable">Создать новый</a>
						</div>

					</div>
				</div>
			</div>

			<div class="constructor-table">
				<form v-on:submit="saveTable" method="post" id="submit">

					@include('adminbuilder::constructor.control-panel')

					<div class="border p-3" style="margin: -1px 0">

						<div class="constructor-table__table">
							<div class="table-responsive" id="constructor-table-table">
								<table class="table table-bordered mb-0">
									<tr v-for="(row, indexRow) in names">
										<td v-for="(col, indexCol) in row" class="p-1">
											<span v-for="(text, langKey) in col">
												<input v-bind:class="[currentLang == langKey ? 'd-block' : 'd-none', 'form-control']" type="text" v-model="row[indexCol][langKey]" >
											</span>
										</td>
									</tr>
								</table>
							</div>

							{{-- <a class="constructor-table__btn constructor-table__btn--column" href="#" v-on:click="addCol" title="Добавить столбец"><i class="fa fa-plus"></i></a>
							<a class="constructor-table__btn constructor-table__btn--row" href="#" v-on:click="addRow" title="Добавить строку"><i class="fa fa-plus"></i></a> --}}
						</div>
					</div>

					@include('adminbuilder::constructor.control-panel')

				</form>
			</div>

			{{-- <div class="row">
				<div class="col-12">
					<pre>@[[ names ]]@</pre>
					<pre>@[[ templates ]]@</pre>
				</div>
			</div> --}}

		</div>
	</div>
@endsection
