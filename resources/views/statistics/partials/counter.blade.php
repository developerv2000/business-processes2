<div class="statistics-counter">
    @foreach ($generalStatuses as $status)
        <div class="statistics-counter__item">
            <p class="statistics-counter__count general-status-{{ $status->id }}">{{ $status->year_current_processes_count }}</p>
            <p class="statistics-counter__status-name">{{ $status->name }}</p>
        </div>
    @endforeach
</div>
