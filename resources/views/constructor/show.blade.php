@extends('avl.default')

@section('main')
	<div class="card h-100">
		<div class="card-header">
			<i class="fa fa-align-justify"></i> Конструктор таблиц - {{ $table->title }}

			<div class="card-actions">
				<a href="{{ route('adminbuilder::constructor.index') }}" class="btn btn-default pl-3 pr-3" style="width: 70px;" title="Назад"><i class="fa fa-arrow-left"></i></a>
			</div>
		</div>

		<div class="card-body">
			<div class="form-group border-bottom pb-3">
				{{ Form::text(null, $table->title, ['class' => 'form-control', 'disabled' => true]) }}
			</div>

			@if (count($table->data))
				<table class="table table-bordered mb-0">
					@php $tableData = $table->data()->head()->get()->toArray(); @endphp

					@for ($row = 0; $row <= getMaxRow($tableData); $row++)
						<tr>
							@for ($col = 0; $col <= getMaxCol($tableData); $col++)
								<td>{{ getValue($tableData, $row, $col, 'ru') }}</td>
							@endfor
						</tr>
					@endfor
				</table>
			@endif
		</div>
	</div>
@endsection
