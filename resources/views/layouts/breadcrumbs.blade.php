@if ($fullScreen)
    <span class="pre-content__fullscreen material-symbols-outlined" title="{{ __('Full screen mode') }}" data-click-action="request-fullscreen" data-target-selector="{{ $fullScreenSelector }}">fullscreen</span>
@endif

<ul class="breadcrumbs">
    @foreach ($crumbs as $crumb)
        <li class="breadcrumbs__item">{!! $crumb !!}</li>
    @endforeach
</ul>
