@extends('avl.default')

@section('css')
	<link rel="stylesheet" href="/avl/js/jquery-ui/jquery-ui.min.css">
	<link rel="stylesheet" href="/avl/js/jquery-ui/timepicker/jquery.ui.timepicker.css">
@endsection

@section('js')
	<script src="/avl/js/jquery-ui/jquery-ui.min.js" charset="utf-8"></script>
	<script src="/avl/js/vue.min.js"></script>
	<script src="/avl/js/jquery-ui/timepicker/jquery.ui.timepicker.js" charset="utf-8"></script>
	<script src="{{ asset('vendor/adminbuilder/js/builder.js') }}" charset="utf-8"></script>
@endsection

@section('main')
	<div class="card" id="builder-table">
		<div class="card-header">
			<i class="fa fa-align-justify"></i> Конструктор

			<div class="card-actions">
				<button type="button" class="btn btn-primary pl-3 pr-3" style="width: 70px;" title="Добавить таблицу" v-on:click="createClearTable"><i class="fa fa-plus"></i></button>
				<button type="submit" form="submit" name="button" value="save" class="btn btn-success pl-3 pr-3" style="width: 70px;" title="Сохранить"><i class="fa fa-floppy-o"></i></button>
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

					@include('adminbuilder::builder.control-panel')

					<div class="border p-3" style="margin: -1px 0">
						<div class="constructor-table__table" v-if="showBlock === 'table'">
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
						</div>

						<div class="block--settings" v-if="showBlock === 'settings'">
							<div class="row">
								<div class="col-1">
										<div class="form-group">
											<label>Вкл / Выкл</label><br/>
											<label class="switch switch-3d switch-primary">
												<input type="checkbox" class="switch-input" v-model="settings.good" value="1">
												<span class="switch-label"></span>
												<span class="switch-handle"></span>
											</label>
										</div>
								</div>
								<div class="col-7">
									@if ($langs)
										<div class="form-group">
											{{ Form::label(null, 'Название таблицы') }}
											@foreach ($langs as $lang)
												<input type="text" class="form-control" v-model="settings['title_{{ $lang->key }}']" v-if="currentLang == '{{ $lang->key }}'" placeholder="{{ $lang->key }}">
											@endforeach
										</div>
									@endif
								</div>
								<div class="col-2">
									<div class="form-group">
										{{ Form::label(null, 'Дата публикации') }}
										<input type="text" v-model="settings.date" class="form-control" id="datepicker-tab-settings">
									</div>
								</div>
								<div class="col-2">
									<div class="form-group">
										{{ Form::label(null, 'Время публикации') }}
										<input type="text" v-model="settings.time" class="form-control" id="timepicker-tab-settings">
									</div>
								</div>
							</div>
						</div>
					</div>

					@include('adminbuilder::builder.control-panel')

				</form>
			</div>

			{{-- <textarea v-model="massFilling" class="w-100"></textarea>
			<button type="button" v-on:click="readMass">asdasd</button>

			<div id="excel_table"></div> --}}

			<hr class="">

			<div class="row mt-3" v-if="tables.length > 0">
				<div class="col-12">
					<table class="table table-bordered">
						<tbody>
							<tr :class="[table.id == currentTable ? 'bg-light' : '']" v-for="(table, index) in tables">
								@if (($authUser->role->name == 'admin') || ($authUser->role->group === 1))
									<td width="50" class="text-center">
										<a href="#"><i class="fa fa-eye"></i></a>
									</td>
								@endif
								<td>@[[ table.title_ru ]]@</td>
								<td width="160" class="text-center">
									@[[ settings.date ]]@ @[[ settings.time ]]@
								</td>
								<td width="100" class="text-center">
									<div class="btn-group btn-group-sm" role="group">
										<button type="button" class="btn btn-primary" v-on:click="switchTable(table.id)"><i class="fa fa-table"></i></button>
										<button type="button" class="btn btn-danger"><i class="fa fa-trash-o"></i></button>
									</div>
								</td>
							</tr>
						</tbody>
					</table>

				</div>
			</div>

			{{-- <div class="">
				<pre>@[[ names ]]@</pre>
			</div> --}}

		</div>
	</div>
@endsection
