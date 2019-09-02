@extends('avl.default')

@section('js')
	<script src="{{ asset('vendor/adminbuilder/js/constructor.js') }}" charset="utf-8"></script>
@endsection

@section('main')
	<div class="card">
		<div class="card-header">
			<i class="fa fa-align-justify"></i> Конструктор таблиц - список шаблонов таблиц

			@can('create', $accessModel)
				<div class="card-actions">
					<a href="{{ route('adminbuilder::constructor.create') }}" class="w-100 pl-4 pr-4 bg-primary text-white" title="Добавить"><i class="fa fa-plus"></i></a>
				</div>
			@endcan
		</div>

		<div class="card-body table-responsive">
			@if ($tables->count() > 0)
				<table class="table table-bordered">
					<thead>
						<tr>
							<th width="50" class="text-center">#</th>
							<th>Таблица</th>
							<th class="text-center" style="width: 160px">Дата публикации</th>
							<th class="text-center" style="width: 100px;">Действие</th>
						</tr>
					</thead>
					<tbody>
						@php $iteration = 30 * ($tables->currentPage() - 1); @endphp
						@foreach ($tables as $table)
							<tr id="table--item-{{ $table->id }}" class="position-relative">
								<th scope="row">{{ ++$iteration }}</th>
								<td>{{ $table->title_ru }}</td>
								<td>{{ date('Y-m-d H:i', strtotime($table->created_at)) }}</td>
								<td class="text-right">
									<div class="btn-group" role="group">
										@can('view', $accessModel) <a href="{{ route('adminbuilder::constructor.show', ['builder' => $table->id]) }}" class="btn btn btn-outline-primary" title="Просмотр"><i class="fa fa-eye"></i></a> @endcan
										@can('update', $accessModel) <a href="{{ route('adminbuilder::constructor.edit', ['builder' => $table->id]) }}" class="btn btn btn-outline-success" title="Изменить"><i class="fa fa-edit"></i></a> @endcan
										@can('delete', $accessModel) <a href="#" class="btn btn btn-outline-danger remove--record" title="Удалить"><i class="fa fa-trash"></i></a> @endcan
									</div>
									@can('delete', $accessModel)
										<div class="remove-message">
												<span>Вы действительно желаете удалить запись?</span>
												<span class="remove--actions btn-group btn-group-sm">
														<button class="btn btn-outline-primary cancel"><i class="fa fa-times-circle"></i> Нет</button>
														<button class="btn btn-outline-danger remove--table" data-id="{{ $table->id }}"><i class="fa fa-trash"></i> Да</button>
												</span>
										</div>
									 @endcan
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			@else
				Нет готовых шаблонов таблиц
			@endif

			<div class="d-flex justify-content-end">
				{{ $tables->appends($_GET)->links('vendor.pagination.bootstrap-4') }}
			</div>

		</div>
	</div>
@endsection
