@extends('admin.layouts.sidebar')

@section('title', 'Quản lý ngân hàng')

@section('main-content')
    <div class="category-container">
        <div class="content-card">
            <div class="card-top">
                <h2 class="page-title">Danh sách ngân hàng</h2>
                <button type="button" class="action-button" data-bs-toggle="modal" data-bs-target="#addBankModal">
                    <i class="fas fa-plus"></i> Thêm ngân hàng
                </button>
            </div>
            <div class="card-content">
                @if ($banks->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-university"></i>
                        </div>
                        <h4>Chưa có ngân hàng nào</h4>
                        <p>Thêm ngân hàng để hiển thị trên website của bạn</p>
                        <button type="button" class="action-button" data-bs-toggle="modal" data-bs-target="#addBankModal">
                            <i class="fas fa-plus"></i> Thêm ngân hàng
                        </button>
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr class="text-center">
                                    <th class="column-stt text-center">STT</th>
                                    <th class="column-small text-center">Thao tác</th>
                                    <th class="column-medium">Tên ngân hàng</th>
                                    <th class="column-small">Mã</th>
                                    <th class="column-medium">Số tài khoản</th>
                                    <th class="column-medium">Chủ tài khoản</th>
                                    <th class="column-small text-center">Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                @foreach ($banks as $key => $bank)
                                    <tr class="text-center">
                                        <td>{{ $key + 1 }}</td>
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                <button type="button" class="action-icon edit-icon" data-bs-toggle="modal"
                                                    data-bs-target="#editBankModal{{ $bank->id }}" title="Chỉnh sửa"
                                                    style="border: none;">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </button>
                                                @include('components.delete-form', [
                                                    'id' => $bank->id,
                                                    'route' => route('admin.banks.destroy', $bank->id),
                                                    'message' => "Bạn có chắc chắn muốn xóa ngân hàng '{$bank->name}'?",
                                                ])
                                            </div>
                                        </td>
                                        <td>
                                            <span class="item-name">{{ $bank->name }}</span>
                                        </td>
                                        <td>
                                            <span class="item-name">{{ $bank->code }}</span>
                                        </td>
                                        <td>
                                            <span class="item-name">{{ $bank->account_number }}</span>
                                        </td>
                                        <td>
                                            <span class="item-name">{{ $bank->account_name }}</span>
                                        </td>
                                        <td>
                                            <span class="status-badge {{ $bank->status ? 'active' : 'inactive' }}">
                                                {{ $bank->status ? 'Hoạt động' : 'Vô hiệu' }}
                                            </span>
                                        </td>
                                    </tr>

                                    <div class="modal fade" id="editBankModal{{ $bank->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content modal-content-custom">
                                                <div class="modal-header">
                                                    <h5 class="modal-title color-primary-6">Chỉnh sửa ngân hàng</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('admin.banks.update', $bank->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body">
                                                        <div class="form-group mb-3">
                                                            <label for="name{{ $bank->id }}" class="form-label-custom">Tên ngân hàng <span class="required-mark">*</span></label>
                                                            <input type="text" id="name{{ $bank->id }}" name="name" class="custom-input form-control" value="{{ old('name', $bank->name) }}" required>
                                                        </div>

                                                        <div class="form-group mb-3">
                                                            <label for="code{{ $bank->id }}" class="form-label-custom">Mã ngân hàng <span class="required-mark">*</span></label>
                                                            <input type="text" id="code{{ $bank->id }}" name="code" class="custom-input form-control" value="{{ old('code', $bank->code) }}" required>
                                                            <div class="form-hint mt-1">
                                                                <i class="fas fa-info-circle"></i> Ví dụ: BIDV, VCB, TPB, MB, ...
                                                                <a class="ms-2" href="https://api.vietqr.io/v2/banks" target="_blank">Lấy code ngân hàng</a>
                                                            </div>
                                                        </div>

                                                        <div class="form-group mb-3">
                                                            <label for="account_number{{ $bank->id }}" class="form-label-custom">Số tài khoản <span class="required-mark">*</span></label>
                                                            <input type="text" id="account_number{{ $bank->id }}" name="account_number" class="custom-input form-control" value="{{ old('account_number', $bank->account_number) }}" required>
                                                        </div>

                                                        <div class="form-group mb-3">
                                                            <label for="account_name{{ $bank->id }}" class="form-label-custom">Chủ tài khoản <span class="required-mark">*</span></label>
                                                            <input type="text" id="account_name{{ $bank->id }}" name="account_name" class="custom-input form-control" value="{{ old('account_name', $bank->account_name) }}" required>
                                                        </div>

                                                        <div class="form-check mb-3 custom-switch-wrapper">
                                                            <input type="checkbox" id="status{{ $bank->id }}" name="status" class="custom-switch" value="1" {{ old('status', $bank->status) ? 'checked' : '' }}>
                                                            <label for="status{{ $bank->id }}" class="custom-switch-label form-check-label">Hoạt động</label>
                                                            <div class="form-hint mt-1">
                                                                <i class="fas fa-info-circle"></i> Ngân hàng không hoạt động sẽ không hiển thị cho người dùng
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn back-button" data-bs-dismiss="modal">Hủy</button>
                                                        <button type="submit" class="btn action-button">Cập nhật</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="px-4 pt-4">
                            <x-paginate :paginator="$banks" />
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="addBankModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content modal-content-custom">
                <div class="modal-header">
                    <h5 class="modal-title color-primary-6">Thêm ngân hàng mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.banks.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="name" class="form-label-custom">Tên ngân hàng <span class="required-mark">*</span></label>
                            <input type="text" id="name" name="name" class="custom-input form-control" value="{{ old('name') }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="code" class="form-label-custom">Mã ngân hàng <span class="required-mark">*</span></label>
                            <input type="text" id="code" name="code" class="custom-input form-control" value="{{ old('code') }}" required>
                            <div class="form-hint mt-1">
                                <i class="fas fa-info-circle"></i> Ví dụ: BIDV, VCB, TPB, MB, ...
                                <a class="ms-2" href="https://api.vietqr.io/v2/banks" target="_blank">Lấy code ngân hàng</a>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="account_number" class="form-label-custom">Số tài khoản <span class="required-mark">*</span></label>
                            <input type="text" id="account_number" name="account_number" class="custom-input form-control" value="{{ old('account_number') }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="account_name" class="form-label-custom">Chủ tài khoản <span class="required-mark">*</span></label>
                            <input type="text" id="account_name" name="account_name" class="custom-input form-control" value="{{ old('account_name') }}" required>
                        </div>

                        <div class="form-check mb-3 custom-switch-wrapper">
                            <input type="checkbox" id="status" name="status" class="custom-switch" value="1" checked>
                            <label for="status" class="custom-switch-label form-check-label">Hoạt động</label>
                            <div class="form-hint mt-1">
                                <i class="fas fa-info-circle"></i> Ngân hàng không hoạt động sẽ không hiển thị cho người dùng
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn back-button" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn action-button">Thêm mới</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
