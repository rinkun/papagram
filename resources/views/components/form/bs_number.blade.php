<div class="form-group">
    {!! Form::label($name, __($lang), ['class' => 'col-sm-2']) !!}
    <div class="col-sm-10">
        {!! Form::number($name, $value, array_merge(['class' => 'form-control'], $attributes)) !!}
    </div>
</div>
