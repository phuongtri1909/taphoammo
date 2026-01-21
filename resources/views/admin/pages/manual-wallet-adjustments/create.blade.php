@extends('admin.layouts.sidebar')

@section('title', 'Tạo điều chỉnh ví thủ công')

@section('main-content')
    <div class="category-container">
        <div class="content-card">
            <div class="card-top">
                <h2 class="page-title">Tạo điều chỉnh ví thủ công</h2>
                <a href="{{ route('admin.manual-wallet-adjustments.index') }}" class="btn back-button">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
            <div class="card-content">
                <form id="adjustmentForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label-custom">Tìm người dùng <span class="required-mark">*</span></label>
                            <div class="user-search-wrapper" style="position: relative;">
                                <input type="text" id="userSearchInput" name="user_search_display" class="custom-input" required 
                                    placeholder="Nhập email hoặc tên người dùng" autocomplete="off">
                                <input type="hidden" id="userEmail" name="user_email" required>
                                <div id="userSuggestions" class="user-suggestions" style="display: none;"></div>
                            </div>
                            <small class="text-muted">Nhập email hoặc tên để tìm kiếm người dùng</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Loại điều chỉnh <span class="required-mark">*</span></label>
                            <select name="type" class="custom-select" required>
                                <option value="">-- Chọn loại --</option>
                                <option value="add">Cộng tiền</option>
                                <option value="subtract">Trừ tiền</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Số tiền <span class="required-mark">*</span></label>
                            <input type="number" name="amount" class="custom-input" required 
                                min="0.01" step="0.01" placeholder="Nhập số tiền">
                            <small class="text-muted">Số tiền phải lớn hơn 0</small>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label-custom">Lý do <span class="required-mark">*</span></label>
                            <textarea name="reason" class="custom-input" required rows="3" 
                                placeholder="Nhập lý do điều chỉnh (tối đa 500 ký tự)" maxlength="500"></textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label-custom">Ghi chú Admin (tùy chọn)</label>
                            <textarea name="admin_note" class="custom-input" rows="3" 
                                placeholder="Ghi chú nội bộ (tối đa 1000 ký tự)" maxlength="1000"></textarea>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn action-button">
                                <i class="fas fa-save"></i> Tạo điều chỉnh
                            </button>
                            <a href="{{ route('admin.manual-wallet-adjustments.index') }}" class="btn back-button">
                                <i class="fas fa-times"></i> Hủy
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    @vite('resources/assets/admin/css/product-common.css')
    <style>
        .user-search-wrapper {
            position: relative;
        }

        .user-suggestions {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            z-index: 1000;
            max-height: 300px;
            overflow-y: auto;
            margin-top: 0.25rem;
        }

        .user-suggestion-item {
            padding: 0.5rem 0.75rem;
            cursor: pointer;
            border-bottom: 1px solid #f0f0f0;
            transition: background-color 0.2s;
            font-size: 0.875rem;
        }

        .user-suggestion-item:hover {
            background-color: #f8f9fa;
        }

        .user-suggestion-item:last-child {
            border-bottom: none;
        }

        .user-suggestion-item.active {
            background-color: #e7f3ff;
        }

        .user-suggestion-item strong {
            display: block;
            color: #212529;
            font-weight: 600;
            font-size: 0.875rem;
            margin-bottom: 0.125rem;
        }

        .user-suggestion-item small {
            color: #6c757d;
            font-size: 0.75rem;
        }
    </style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let searchTimeout;
    let selectedUser = null;
    let currentSuggestions = [];

    const userSearchInput = document.getElementById('userSearchInput');
    const userEmailInput = document.getElementById('userEmail');
    const suggestionsDiv = document.getElementById('userSuggestions');

    // Debounce search
    userSearchInput.addEventListener('input', function(e) {
        const query = e.target.value.trim();
        
        clearTimeout(searchTimeout);
        
        if (query.length < 2) {
            suggestionsDiv.style.display = 'none';
            userEmailInput.value = '';
            selectedUser = null;
            return;
        }

        searchTimeout = setTimeout(() => {
            searchUsers(query);
        }, 300);
    });

    // Handle keyboard navigation
    userSearchInput.addEventListener('keydown', function(e) {
        const items = suggestionsDiv.querySelectorAll('.user-suggestion-item');
        const activeItem = suggestionsDiv.querySelector('.user-suggestion-item.active');
        let activeIndex = activeItem ? Array.from(items).indexOf(activeItem) : -1;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            activeIndex = (activeIndex + 1) % items.length;
            items.forEach(item => item.classList.remove('active'));
            if (items[activeIndex]) {
                items[activeIndex].classList.add('active');
                items[activeIndex].scrollIntoView({ block: 'nearest' });
            }
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            activeIndex = activeIndex <= 0 ? items.length - 1 : activeIndex - 1;
            items.forEach(item => item.classList.remove('active'));
            if (items[activeIndex]) {
                items[activeIndex].classList.add('active');
                items[activeIndex].scrollIntoView({ block: 'nearest' });
            }
        } else if (e.key === 'Enter' && activeItem) {
            e.preventDefault();
            const user = {
                id: parseInt(activeItem.dataset.userId),
                email: activeItem.dataset.userEmail,
                full_name: activeItem.dataset.userName,
                display: `${activeItem.dataset.userName} (${activeItem.dataset.userEmail})`
            };
            selectUser(user);
        } else if (e.key === 'Escape') {
            suggestionsDiv.style.display = 'none';
        }
    });

    // Hide suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!userSearchInput.contains(e.target) && !suggestionsDiv.contains(e.target)) {
            suggestionsDiv.style.display = 'none';
        }
    });

    function searchUsers(query) {
        fetch('{{ route("admin.manual-wallet-adjustments.search-users") }}?q=' + encodeURIComponent(query), {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(users => {
            currentSuggestions = users;
            displaySuggestions(users);
        })
        .catch(error => {
            console.error('Search error:', error);
        });
    }

    function displaySuggestions(users) {
        if (users.length === 0) {
            suggestionsDiv.innerHTML = '<div class="user-suggestion-item"><small class="text-muted">Không tìm thấy người dùng nào</small></div>';
            suggestionsDiv.style.display = 'block';
            return;
        }

        suggestionsDiv.innerHTML = users.map((user, index) => {
            const userData = escapeHtml(JSON.stringify(user));
            return `
                <div class="user-suggestion-item" data-index="${index}" data-user-id="${user.id}" data-user-email="${escapeHtml(user.email)}" data-user-name="${escapeHtml(user.full_name)}">
                    <strong>${escapeHtml(user.full_name)}</strong>
                    <small>${escapeHtml(user.email)}</small>
                </div>
            `;
        }).join('');

        // Add click event listeners
        suggestionsDiv.querySelectorAll('.user-suggestion-item').forEach(item => {
            item.addEventListener('click', function() {
                const user = {
                    id: parseInt(this.dataset.userId),
                    email: this.dataset.userEmail,
                    full_name: this.dataset.userName,
                    display: `${this.dataset.userName} (${this.dataset.userEmail})`
                };
                selectUser(user);
            });
        });

        suggestionsDiv.style.display = 'block';
    }

    function selectUser(user) {
        selectedUser = user;
        userSearchInput.value = user.display;
        userEmailInput.value = user.email;
        suggestionsDiv.style.display = 'none';
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    document.getElementById('adjustmentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const data = Object.fromEntries(formData);
        
        // Validation
        if (!data.user_email || !selectedUser) {
            Swal.fire('Lỗi', 'Vui lòng chọn người dùng từ danh sách gợi ý', 'warning');
            return;
        }

        if (!data.type || !data.amount || !data.reason) {
            Swal.fire('Lỗi', 'Vui lòng điền đầy đủ thông tin bắt buộc', 'warning');
            return;
        }
        
        if (parseFloat(data.amount) <= 0) {
            Swal.fire('Lỗi', 'Số tiền phải lớn hơn 0', 'warning');
            return;
        }

        Swal.fire({
            title: 'Xác nhận tạo điều chỉnh?',
            html: `
                <div class="text-start">
                    <p><strong>Người dùng:</strong> ${selectedUser ? selectedUser.display : data.user_email}</p>
                    <p><strong>Loại:</strong> ${data.type === 'add' ? 'Cộng tiền' : 'Trừ tiền'}</p>
                    <p><strong>Số tiền:</strong> ${parseFloat(data.amount).toLocaleString('vi-VN')}₫</p>
                    <p><strong>Lý do:</strong> ${data.reason}</p>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3b82f6',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Xác nhận',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('{{ route("admin.manual-wallet-adjustments.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Thành công!', data.message, 'success')
                            .then(() => {
                                if (data.redirect) {
                                    window.location.href = data.redirect;
                                } else {
                                    window.location.href = '{{ route("admin.manual-wallet-adjustments.index") }}';
                                }
                            });
                    } else {
                        Swal.fire('Lỗi', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Lỗi', 'Đã có lỗi xảy ra. Vui lòng thử lại sau.', 'error');
                });
            }
        });
    });
</script>
@endpush
