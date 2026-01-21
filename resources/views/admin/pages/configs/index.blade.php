@extends('admin.layouts.sidebar')

@section('title', 'Quản lý Cấu hình')

@section('main-content')
    <div class="category-container">
        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <h2 class="page-title">Quản lý Cấu hình</h2>
                </div>
            </div>

            <div class="card-content">
                @if ($configs->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-cog"></i>
                        </div>
                        <h4>Chưa có cấu hình nào</h4>
                        <p>Chạy seeder để tạo dữ liệu cấu hình mẫu.</p>
                    </div>
                @else
                    <form action="{{ route('admin.configs.bulk-update') }}" method="POST" id="configForm">
                        @csrf
                        @method('PUT')

                        <div class="data-table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th class="column-stt">STT</th>
                                        <th class="column-medium">Key</th>
                                        <th class="column-large">Value</th>
                                        <th class="column-large">Description</th>
                                        <th class="column-small">Cập nhật</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($configs as $index => $config)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>
                                                <code class="config-key">{{ $config->key }}</code>
                                            </td>
                                            <td>
                                                @if (strlen($config->value) > 100 || strpos($config->value, "\n") !== false)
                                                    <textarea 
                                                        name="configs[{{ $config->id }}][value]" 
                                                        class="form-control config-value-textarea" 
                                                        rows="3"
                                                        placeholder="Nhập giá trị...">{{ old("configs.{$config->id}.value", $config->value) }}</textarea>
                                                @else
                                                    <input 
                                                        type="text" 
                                                        name="configs[{{ $config->id }}][value]" 
                                                        class="form-control config-value-input" 
                                                        value="{{ old("configs.{$config->id}.value", $config->value) }}"
                                                        placeholder="Nhập giá trị...">
                                                @endif
                                                @error("configs.{$config->id}.value")
                                                    <div class="text-danger small">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <textarea 
                                                    name="configs[{{ $config->id }}][description]" 
                                                    class="form-control config-description-textarea" 
                                                    rows="2"
                                                    placeholder="Nhập mô tả...">{{ old("configs.{$config->id}.description", $config->description) }}</textarea>
                                                @error("configs.{$config->id}.description")
                                                    <div class="text-danger small">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td class="text-center">
                                                <div class="action-buttons-wrapper">
                                                    <button 
                                                        type="button" 
                                                        class="action-icon edit-icon" 
                                                        title="Lưu"
                                                        onclick="updateSingleConfig({{ $config->id }}, '{{ $config->key }}')">
                                                        <i class="fas fa-save"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3 d-flex justify-content-end gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Lưu tất cả
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function updateSingleConfig(configId, configKey) {
            const row = event.target.closest('tr');
            const valueInput = row.querySelector('[name*="[value]"]');
            const descriptionInput = row.querySelector('[name*="[description]"]');
            
            const value = valueInput.value;
            const description = descriptionInput.value;

            fetch(`{{ route('admin.configs.update', ':id') }}`.replace(':id', configId), {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    value: value,
                    description: description
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Có lỗi xảy ra');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công',
                        text: 'Cấu hình đã được cập nhật.',
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: data.message || 'Có lỗi xảy ra khi cập nhật.'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: error.message || 'Có lỗi xảy ra khi cập nhật.'
                });
            });
        }

        document.getElementById('configForm')?.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang lưu...';
        });
    </script>
@endpush

@push('styles')
    <style>
        .config-key {
            background: #f8f9fa;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 13px;
            color: #495057;
            border: 1px solid #dee2e6;
        }

        .config-value-input,
        .config-value-textarea,
        .config-description-textarea {
            font-size: 13px;
            padding: 6px 10px;
        }

        .config-value-textarea {
            font-family: 'Courier New', monospace;
            resize: vertical;
        }

        .data-table td {
            vertical-align: middle;
        }
    </style>
@endpush
