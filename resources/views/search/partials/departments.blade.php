{{-- resources/views/search/partials/departments.blade.php --}}
<div class="list-group">
    @foreach ($departments as $dept)
        <a href="{{ route('departments.show', $dept) }}" class="list-group-item list-group-item-action">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="bi bi-building fs-2 text-warning"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">{{ $dept->name }}</h6>
                            <p class="mb-1 text-muted">Code: {{ $dept->code }}</p>
                        </div>
                        <span class="badge bg-{{ $dept->status === 'active' ? 'success' : 'secondary' }}">
                            {{ ucfirst($dept->status) }}
                        </span>
                    </div>
                    @if ($dept->description)
                        <p class="mb-1 small text-muted">{{ $dept->description }}</p>
                    @endif
                    <div class="d-flex flex-wrap gap-3 mt-2">
                        <small class="text-muted">
                            <i class="bi bi-people"></i> {{ $dept->users_count ?? $dept->users()->count() }} Users
                        </small>
                        <small class="text-muted">
                            <i class="bi bi-files"></i> {{ $dept->files_count ?? $dept->files()->count() }} Files
                        </small>
                        <small class="text-muted">
                            <i class="bi bi-calendar"></i> Created {{ $dept->created_at->format('M Y') }}
                        </small>
                    </div>
                </div>
            </div>
        </a>
    @endforeach
</div>
