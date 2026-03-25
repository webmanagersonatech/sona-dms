{{-- resources/views/layouts/partials/search-bar.blade.php --}}
<form class="d-flex align-items-center" id="searchForm" action="{{ route('search.advanced') }}" method="GET">
    <div class="input-group" style="width: 300px;">
        <input type="text" class="form-control" name="q" placeholder="Search..." value="{{ request('q') }}"
            id="searchInput" autocomplete="off">
        <button class="btn btn-outline-secondary" type="submit">
            <i class="bi bi-search"></i>
        </button>
    </div>

    <!-- Quick Search Results Dropdown -->
    <div class="search-dropdown" id="searchResults" style="display: none;">
        <div class="search-dropdown-header">
            <span class="fw-bold">Quick Results</span>
            <span class="badge bg-primary" id="resultCount">0</span>
        </div>
        <div class="search-dropdown-body" id="resultList">
            <!-- Results will be loaded here -->
        </div>
        <div class="search-dropdown-footer">
            <a href="#" id="viewAllResults" class="text-decoration-none">
                View all results <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
</form>

<style>
    .search-dropdown {
        position: absolute;
        top: 40px;
        left: 0;
        width: 100%;
        max-width: 500px;
        background: white;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        margin-top: 5px;
    }

    .search-dropdown-header {
        padding: 10px 15px;
        border-bottom: 1px solid #eee;
        background: #f8f9fa;
        border-radius: 8px 8px 0 0;
    }

    .search-dropdown-body {
        max-height: 400px;
        overflow-y: auto;
        padding: 10px;
    }

    .search-dropdown-footer {
        padding: 10px 15px;
        border-top: 1px solid #eee;
        background: #f8f9fa;
        border-radius: 0 0 8px 8px;
        text-align: center;
    }

    .search-result-item {
        display: flex;
        align-items: center;
        padding: 10px;
        border-radius: 5px;
        transition: background 0.3s;
        text-decoration: none;
        color: inherit;
    }

    .search-result-item:hover {
        background: #f0f0f0;
    }

    .result-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        margin-right: 12px;
        font-size: 1.2rem;
    }

    .result-content {
        flex: 1;
    }

    .result-title {
        font-weight: 600;
        margin-bottom: 2px;
        color: #333;
    }

    .result-subtitle {
        font-size: 0.85rem;
        color: #666;
    }

    .result-badge {
        font-size: 0.75rem;
        padding: 2px 8px;
        border-radius: 12px;
        margin-left: 10px;
    }

    [data-theme="dark"] .search-dropdown {
        background: #2d2d2d;
        border-color: #404040;
    }

    [data-theme="dark"] .search-dropdown-header,
    [data-theme="dark"] .search-dropdown-footer {
        background: #363636;
        border-color: #404040;
        color: #f0f0f0;
    }

    [data-theme="dark"] .search-result-item:hover {
        background: #404040;
    }

    [data-theme="dark"] .result-title {
        color: #f0f0f0;
    }

    [data-theme="dark"] .result-subtitle {
        color: #aaa;
    }
</style>

<script>
    let searchTimeout;

    document.getElementById('searchInput').addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        const query = e.target.value;

        if (query.length < 2) {
            document.getElementById('searchResults').style.display = 'none';
            return;
        }

        searchTimeout = setTimeout(() => {
            fetch(`/search?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    displaySearchResults(data);
                });
        }, 300);
    });

    function displaySearchResults(data) {
        const resultsDiv = document.getElementById('searchResults');
        const resultList = document.getElementById('resultList');
        const resultCount = document.getElementById('resultCount');
        const viewAllLink = document.getElementById('viewAllResults');

        if (data.results.length === 0) {
            resultsDiv.style.display = 'none';
            return;
        }

        resultCount.textContent = data.total;
        viewAllLink.href = `/search/advanced?q=${encodeURIComponent(document.getElementById('searchInput').value)}`;

        resultList.innerHTML = '';
        data.results.forEach(result => {
            const colors = {
                file: 'primary',
                transfer: 'success',
                user: 'info',
                department: 'warning'
            };

            const item = document.createElement('a');
            item.href = result.url;
            item.className = 'search-result-item';
            item.innerHTML = `
            <div class="result-icon bg-${colors[result.type]} bg-opacity-10">
                <i class="bi ${result.icon} text-${colors[result.type]}"></i>
            </div>
            <div class="result-content">
                <div class="result-title">${escapeHtml(result.title)}</div>
                <div class="result-subtitle">${escapeHtml(result.subtitle)}</div>
            </div>
            ${result.badge ? `<span class="result-badge bg-${colors[result.type]} text-white">${escapeHtml(result.badge)}</span>` : ''}
        `;
            resultList.appendChild(item);
        });

        resultsDiv.style.display = 'block';
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#searchForm')) {
            document.getElementById('searchResults').style.display = 'none';
        }
    });

    // Update view all link when form submits
    document.getElementById('searchForm').addEventListener('submit', function(e) {
        const query = document.getElementById('searchInput').value;
        document.getElementById('viewAllResults').href = `/search/advanced?q=${encodeURIComponent(query)}`;
    });
</script>
