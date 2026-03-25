{{-- resources/views/settings/appearance.blade.php --}}
@extends('layouts.app')

@section('title', 'Appearance Settings')

@section('content')
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Appearance Settings</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('settings.appearance') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <h6 class="fw-bold">Theme</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <div
                                        class="card theme-option {{ ($settings['theme'] ?? 'light') == 'light' ? 'border-primary' : '' }}">
                                        <div class="card-body text-center">
                                            <input type="radio" name="theme" value="light" id="themeLight"
                                                class="d-none"
                                                {{ ($settings['theme'] ?? 'light') == 'light' ? 'checked' : '' }}>
                                            <label for="themeLight" class="d-block cursor-pointer">
                                                <i class="bi bi-sun display-4"></i>
                                                <h6 class="mt-2">Light Mode</h6>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div
                                        class="card theme-option {{ ($settings['theme'] ?? '') == 'dark' ? 'border-primary' : '' }}">
                                        <div class="card-body text-center">
                                            <input type="radio" name="theme" value="dark" id="themeDark"
                                                class="d-none" {{ ($settings['theme'] ?? '') == 'dark' ? 'checked' : '' }}>
                                            <label for="themeDark" class="d-block cursor-pointer">
                                                <i class="bi bi-moon display-4"></i>
                                                <h6 class="mt-2">Dark Mode</h6>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div
                                        class="card theme-option {{ ($settings['theme'] ?? '') == 'auto' ? 'border-primary' : '' }}">
                                        <div class="card-body text-center">
                                            <input type="radio" name="theme" value="auto" id="themeAuto"
                                                class="d-none" {{ ($settings['theme'] ?? '') == 'auto' ? 'checked' : '' }}>
                                            <label for="themeAuto" class="d-block cursor-pointer">
                                                <i class="bi bi-circle-half display-4"></i>
                                                <h6 class="mt-2">Auto (System)</h6>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-bold">Layout</h6>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="sidebar_collapsed"
                                    name="sidebar_collapsed" value="1"
                                    {{ $settings['sidebar_collapsed'] ?? false ? 'checked' : '' }}>
                                <label class="form-check-label" for="sidebar_collapsed">
                                    Collapsed Sidebar
                                </label>
                            </div>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="dense_mode" name="dense_mode"
                                    value="1" {{ $settings['dense_mode'] ?? false ? 'checked' : '' }}>
                                <label class="form-check-label" for="dense_mode">
                                    Dense Mode (Compact layout)
                                </label>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-bold">Font Size</h6>
                            <select class="form-select" name="font_size">
                                <option value="small" {{ ($settings['font_size'] ?? '') == 'small' ? 'selected' : '' }}>
                                    Small
                                </option>
                                <option value="medium"
                                    {{ ($settings['font_size'] ?? 'medium') == 'medium' ? 'selected' : '' }}>
                                    Medium
                                </option>
                                <option value="large" {{ ($settings['font_size'] ?? '') == 'large' ? 'selected' : '' }}>
                                    Large
                                </option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-bold">Color Scheme</h6>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="color-option text-center">
                                        <input type="radio" name="color_scheme" value="blue" id="colorBlue"
                                            class="d-none"
                                            {{ ($settings['color_scheme'] ?? 'blue') == 'blue' ? 'checked' : '' }}>
                                        <label for="colorBlue">
                                            <div class="color-preview bg-primary"
                                                style="width: 40px; height: 40px; border-radius: 50%;"></div>
                                            <small>Blue</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="color-option text-center">
                                        <input type="radio" name="color_scheme" value="green" id="colorGreen"
                                            class="d-none"
                                            {{ ($settings['color_scheme'] ?? '') == 'green' ? 'checked' : '' }}>
                                        <label for="colorGreen">
                                            <div class="color-preview bg-success"
                                                style="width: 40px; height: 40px; border-radius: 50%;"></div>
                                            <small>Green</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="color-option text-center">
                                        <input type="radio" name="color_scheme" value="purple" id="colorPurple"
                                            class="d-none"
                                            {{ ($settings['color_scheme'] ?? '') == 'purple' ? 'checked' : '' }}>
                                        <label for="colorPurple">
                                            <div class="color-preview"
                                                style="width: 40px; height: 40px; border-radius: 50%; background: #6f42c1;">
                                            </div>
                                            <small>Purple</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="color-option text-center">
                                        <input type="radio" name="color_scheme" value="orange" id="colorOrange"
                                            class="d-none"
                                            {{ ($settings['color_scheme'] ?? '') == 'orange' ? 'checked' : '' }}>
                                        <label for="colorOrange">
                                            <div class="color-preview"
                                                style="width: 40px; height: 40px; border-radius: 50%; background: #fd7e14;">
                                            </div>
                                            <small>Orange</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Save Settings
                        </button>
                    </form>
                </div>
            </div>

            <!-- Preview Card -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Preview</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Your appearance settings will be applied immediately after
                        saving.
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h6>Sample Card</h6>
                                    <p>This is how your content will look with the selected settings.</p>
                                    <button class="btn btn-primary">Sample Button</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="list-group">
                                <a href="#" class="list-group-item list-group-item-action active">
                                    Active Item
                                </a>
                                <a href="#" class="list-group-item list-group-item-action">
                                    Regular Item
                                </a>
                                <a href="#" class="list-group-item list-group-item-action">
                                    Another Item
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .cursor-pointer {
            cursor: pointer;
        }

        .theme-option {
            cursor: pointer;
            transition: all 0.3s;
        }

        .theme-option:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .theme-option.border-primary {
            border-width: 2px;
        }

        .color-option label {
            cursor: pointer;
        }

        .color-option input:checked+label .color-preview {
            border: 3px solid #333;
            transform: scale(1.1);
        }
    </style>
@endpush
