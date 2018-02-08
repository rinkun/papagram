@extends('adminlte::page')

@push('css')
    <link rel="stylesheet"
          href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.min.css">
@endpush

@section('content_header')
    <h1>@lang('estimate.model_name')</h1>
@stop

@section('content')
    <div id="estimateForm" v-pre>
        {!! Form::model($estimate, ['route' => ['admin.estimates.store'], 'class' => 'form-horizontal h-adr']) !!}
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">見積情報</h3>
                </div>
                <div class="box-body">
                    @include('admin.estimates.info_fields')
                </div>
            </div>

            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">見積項目</h3>
                </div>
                <div class="box-body">
                    @include('admin.estimates.item_fields')
                </div>
                <div class="box-footer">
                    <button v-on:click.prevent="onAdd" class="btn btn-success">
                        <i class="fa fa-plus"></i>
                    </button>
                </div>
            </div>

            <div class="box box-primary">
                <div class="box-footer">
                    {!! Form::submit('保存', ['class' => 'btn btn-primary']) !!}
                </div>
            </div>
        {!! Form::close() !!}
    </div>
@stop

@push('js')
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/locales/bootstrap-datepicker.ja.min.js"></script>
    <script src="{{ asset('/js/datepicker.js') }}"></script>
    <script src="{{ asset('/js/disable_enter_key.js') }}"></script>

    <script src="{{ asset('/js/app.js') }}"></script>
    <script>
        const vm = new Vue({
            el: '#estimateForm',
            data: {
                items: {!! $items->toJson() !!},
                itemCount: {!! $items->count() !!},
            },
            methods: {
                onAdd: function () {
                    this.items.push(
                        {
                            'name': '',
                            'number': 1,
                            'unit_price': 0,
                            'line_price': 0
                        }
                    );

                    this.itemCount += 1;
                },
                onDelete: function (key) {
                    this.items.splice(key, 1);
                    this.itemCount -= 1;
                },
                onCalculateLinePrice: function (key) {
                    let item = this.items[key];
                    item.line_price = item.number * item.unit_price;
                }
            }
        });
    </script>
@endpush
