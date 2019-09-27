@extends('avl.default')

@section('css')
	<link rel="stylesheet" href="/avl/js/jquery-ui/jquery-ui.min.css">
	<link rel="stylesheet" href="/avl/js/jquery-ui/timepicker/jquery.ui.timepicker.css">
	<link rel="stylesheet" href="/avl/js/Chart.js/Chart.min.css">
@endsection

@section('js')
	<script src="/avl/js/tinymce/tinymce.min.js" charset="utf-8"></script>
	<script src="/avl/js/jquery-ui/jquery-ui.min.js"></script>
	<script src="/avl/js/jquery-ui/timepicker/jquery.ui.timepicker.js"></script>

	<script src="/avl/js/vue.min.js"></script>
	<script src="/avl/js/Chart.js/Chart.min.js"></script>
	<script src="{{ asset('vendor/adminbuilder/js/graph.js') }}"></script>
	<script src="{{ asset('vendor/adminbuilder/js/builder.js') }}"></script>
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

		<div class="card-body table-responsive ">
			<div class="card-body-content d-none">
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
								<div class="table-responsive" id="constructor-table-table tableWrap">
									<table class="table table-bordered mb-0">
										<thead>
											<tr style="background: #F0F0F0;">
												<th class="v-num-cells" style="min-width: 60px;">
													<div class="dropdown">
														<button type="button" class="btn border" @click="showAll = !showAll">
															<i :class="['fa', showAll ? 'fa-eye' : 'fa-eye-slash']"></i>
														</button>
													</div>
												</th>
												<th class="p-0" v-for="(col1, indexCol) in names[0]" v-show="(isHide(hidenCols, indexCol) < 0) || showAll">
													<div class="dropdown">
														<button type="button" class="btn w-100 bg-transparent" data-toggle="dropdown">@[[ getCharByCode(indexCol)  ]]@</button>
														<div class="dropdown-menu dropdown-menu-right">
															<span class="dropdown-item bg-light p-1 disabled font-weight-bold text-center">Столбец</span>
															<a class="dropdown-item p-1" href="#" @click="selectHideRowOrCol(false, indexCol, event)">
																<span v-if="isHide(hidenCols, indexCol) >= 0"><i class="fa fa-eye ml-1 mr-1"></i> показать</span>
																<span v-else><i class="fa fa-eye-slash ml-1 mr-1"></i> скрыть</span>
															</a>
															<a class="dropdown-item p-1" href="#" @click="addCol(indexCol, event)"><i class="fa fa-arrow-left ml-1 mr-1"></i>слева</a>
															<a class="dropdown-item p-1" href="#" @click="addCol(indexCol + 1, event)"><i class="fa fa-arrow-right ml-1 mr-1"></i>справа</a>
															<a class="dropdown-item p-1" href="#" @click="delCol(indexCol, event)"><i class="fa fa-trash-o ml-1 mr-1"></i>удалить</a>
														</div>
													</div>
												</th>
											</tr>
										</thead>
										<tbody>
											<tr v-for="(row, indexRow) in names" v-show="(isHide(hidenRows, indexRow) < 0) || showAll">
												<td class="v-num-cells" width="60">
													<div class="dropdown dropright border">
														<button type="button" class="btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">@[[ indexRow + 1 ]]@</button>
														<div class="dropdown-menu dropdown-menu-right">
															<span class="dropdown-item bg-light p-1 disabled font-weight-bold text-center">Строка</span>
															<a class="dropdown-item p-1" href="#" @click="selectHideRowOrCol(indexRow, false, event)">
																<span v-if="isHide(hidenRows, indexRow) >= 0"> <i class="fa fa-eye ml-1 mr-1"></i> показать</span>
																<span v-else><i class="fa fa-eye-slash ml-1 mr-1"></i> скрыть</span>
															</a>
															<a class="dropdown-item p-1" href="#" @click="addRow(indexRow, event)"><i class="fa fa-arrow-up ml-1 mr-1"></i>сверху</a>
															<a class="dropdown-item p-1" href="#" @click="addRow(indexRow + 1, event)"><i class="fa fa-arrow-down ml-1 mr-1"></i>снизу</a>
															<a class="dropdown-item p-1" href="#" @click="delRow(indexRow, event)"><i class="fa fa-trash-o ml-1 mr-1"></i>удалить</a>
														</div>
													</div>
												</td>
												<td v-for="(col, indexCol) in row"
													v-bind:class="[
														checkExistCoordinate(selectedCells, indexRow, indexCol) >= 0 ? 'bg-secondary' : row[indexCol].head ? 'cell-head' : '',
														'p-0', 'r' + indexRow, 'c' + indexCol,
														isActiveCell(indexRow, indexCol) ? 'active-cell' : '',
														isCellMerged(indexRow, indexCol) ? 'merge-cells' : '',
														col.hide == true ? 'd-none' : '',
														((isHide(hidenCols, indexCol) >= 0) || isHide(hidenRows, indexRow) >= 0) ? 'hedden-cell' : ''
													]"
													:colspan="col.colspan"
													:rowspan="col.rowspan"
													width="160"
													v-show="(isHide(hidenCols, indexCol) < 0) || showAll"
												>
													<div class="input-group">
														<div contenteditable="true" class="form-control border-0 h-100"
															 v-for="(text, langKey) in col.translates"
															 v-if="currentLang == langKey"
															 v-html="row[indexCol]['translates'][langKey]"
															 @focus="setActiveCell(indexRow, indexCol)"
															 @focusout="changeInputValue(indexRow, indexCol, langKey, event)"
															 >
														</div>

														<div v-bind:class="[setHeaders ? 'd-flex' : '', 'input-group-append border-left']">
															<span class="input-group-text border-0">
																<input type="checkbox" v-model="row[indexCol].head" >
															</span>
														</div>
													</div>

												</td>
											</tr>
										</tbody>
									</table>

								</div>

								<div class="mt-2">
									<span class="btn btn-block btn-square btn-light" v-on:click="showPanelMass = !showPanelMass">
										<span>
											<i class="fa fa-long-arrow-up" v-if="showPanelMass == true"></i>
											<i class="fa fa-long-arrow-down" v-else></i>
											 Показать / cкрыть панель быстрой вставки
										 </span>
									</span>
									<div v-if="showPanelMass">
										<textarea v-model="massFilling" class="form-control" rows="6"></textarea>
										<button type="button" class="btn btn-block btn-primary btn-square" v-on:click="readMass">Заполнить</button>

										<small class="form-text text-muted">* Для того чтоб заполнение началось с нужной строки/ячейки, то после вставки значений в поле выше, нужно установить курсор в нужную ячейку и нажать кнопку "Заполнить"</small>
										<small class="form-text text-danger">** Заполнение таким способом НЕ дает стопроцентной гарантии на то что все данные встанут по своим местам</small>
									</div>
								</div>

							</div>

							<div class="block--settings" v-show="showBlock === 'settings'">
								<div class="row">
									{{-- <div class="col-1">
											<div class="form-group">
												<label>Вкл / Выкл</label><br/>
												<label class="switch switch-3d switch-primary">
													<input type="checkbox" class="switch-input" v-model="settings.good" value="1">
													<span class="switch-label"></span>
													<span class="switch-handle"></span>
												</label>
											</div>
									</div> --}}

									<div class="col-8">
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

								<div class="row mt-3">
									<div class="col-12">
										<ul class="nav nav-tabs" role="tablist">
											<li class="nav-item"><a class="nav-link active show" href="#before" data-toggle="tab">Перед таблицей</a></li>
											<li class="nav-item"><a class="nav-link" href="#after" data-toggle="tab">После таблицы</a></li>
										</ul>
										<div class="tab-content">
											<div class="tab-pane active show" id="before" role="tabpanel">
												@foreach ($langs as $lang)
													<div :class="[('{{ $lang->key }}' == currentLang) ? 'd-block' : 'd-none']">
														<textarea class="tmc-table-before-{{ $lang->key }}" v-model="settings.descriptions.before.{{ $lang->key  }}"></textarea>
													</div>
												@endforeach
											</div>
											<div class="tab-pane" id="after" role="tabpanel">
												@foreach ($langs as $lang)
													<div :class="[('{{ $lang->key }}' == currentLang) ? 'd-block' : 'd-none']">
														<textarea class="tmc-table-after-{{ $lang->key }}" v-model="settings.descriptions.after.{{ $lang->key  }}"></textarea>
													</div>
												@endforeach
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="block-graph" v-if="showBlock === 'graph'">

								<div class="btn-group w-100 mb-3" role="group" aria-label="Basic example">
									<button v-bind:class="[graph.type == 'line' ? 'active' : '', 'btn btn-outline-primary w-50']" @click="graph.type  = 'line'" type="button">Линейный график</button>
									<button v-bind:class="[graph.type == 'bar' ? 'active' : '', 'btn btn-outline-success w-50']" @click="graph.type  = 'bar'" type="button">Гистограмма</button>
									<button v-bind:class="[graph.type == 'pie' ? 'active' : '', 'btn btn-outline-success w-50']" @click="graph.type  = 'pie'" type="button">Doughnut and Pie</button>
									<button v-bind:class="[graph.type == 'polarArea' ? 'active' : '', 'btn btn-outline-success w-50']" @click="graph.type  = 'polarArea'" type="button">Polar Area</button>
								</div>

								<table class="table table-bordered mb-0">
									<tr v-for="(row, indexRow) in heads">
										<td v-for="(col, indexCol) in row" class="p-1 bg-light">
											<div class="input-group">
												<span class="input-group-prepend">
													<button type="button"
															v-bind:class="[checkExistCoordinate(graph.cols.x, indexRow, indexCol) >= 0 ? 'active' : '', 'btn btn-outline-success']"
															@click="setXCoordinate(indexRow, indexCol)">X</button>
													<button type="button"
															v-bind:class="[checkExistCoordinate(graph.cols.y, indexRow, indexCol) >= 0 ? 'active' : '', 'btn btn-outline-danger']"
															class="btn btn-outline-danger" @click="setYCoordinate(indexRow, indexCol)">Y</button>
												</span>

												<span class="form-control" v-for="(text, langKey) in col.translates" v-if="currentLang == langKey">@[[ row[indexCol]['translates'][langKey] ]]@</span>
											</div>
										</td>
									</tr>
								</table>

								<graph-component :graph="graph" :id="currentTable"></graph-component>

							</div>

						</div>
					</form>
				</div>

				<hr />

				<div class="row mt-3" v-if="tables.length > 0">
					<div class="col-12">
						<h3>Версии</h3>
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
										@[[ table.published_at ]]@
									</td>
									<td width="100" class="text-center">
										<div class="btn-group btn-group-sm" role="group">
											<button type="button" class="btn btn-primary" v-on:click="switchTable(table.id)"><i class="fa fa-table"></i></button>
											<button type="button" class="btn btn-danger" v-on:click="removeTable(table.id)"><i class="fa fa-trash-o"></i></button>
										</div>
									</td>
								</tr>
							</tbody>
						</table>

					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
