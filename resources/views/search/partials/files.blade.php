{{-- resources/views/search/partials/files.blade.php --}}
<div class="list-group">
    @foreach ($files as $file)
        <a href="{{ route('files.show', $file) }}" class="list-group-item list-group-item-action">
            <div class="row">
                <div class="col-auto">
                    <i class="bi {{ $file->icon }} fs-2 text-primary"></i>
                </div>
                <div class="col">
                    <h6 class="mb-1">{{ $file->name }}</h6>
                    <p class="mb-1 text-muted small">{{ $file->description ?? 'No description' }}</p>
                    <div class="d-flex flex-wrap gap-2">
                        <small class="text-muted">
                            <i class="bi bi-file-earmark"></i> {{ strtoupper($file->extension) }}
                        </small>
                        <small class="text-muted">
                            <i class="bi bi-hdd"></i> {{ $file->size_for_humans }}
                        </small>
                        <small class="text-muted">
                            <i class="bi bi-person"></i> {{ $file->owner->name }}
                        </small>
                        <small class="text-muted">
                            <i class="bi bi-calendar"></i> {{ $file->created_at->format('Y-m-d') }}
                        </small>
                        <small class="text-muted">
                            <i class="bi bi-download"></i> {{ $file->download_count }} downloads
                        </small>
                    </div>
                </div>
                <div class="col-auto">
                    @if ($file->is_encrypted)
                        <span class="badge bg-warning">
                            <i class="bi bi-lock"></i> Encrypted
                        </span>
                    @endif
                </div>
            </div>
        </a>
    @endforeach
</div>
