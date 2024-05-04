@if ($instance->{$attribute})
    {{ App\Support\Helper::formatPrice($instance->{$attribute}) }}
@endif
