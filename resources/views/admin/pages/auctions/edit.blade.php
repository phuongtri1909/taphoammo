@extends('admin.layouts.sidebar')

@section('title', 'Chỉnh sửa phiên đấu giá')

@section('main-content')
    <div class="category-container">
        <div class="mb-4">
            <a href="{{ route('admin.auctions.index') }}" class="btn back-button">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
        </div>

        <div class="content-card">
            <div class="card-top">
                <h2 class="page-title">Chỉnh sửa phiên đấu giá</h2>
            </div>
            <div class="card-content">
                <form action="{{ route('admin.auctions.update', $auction->slug) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-lg-8">
                            <div class="form-group mb-4">
                                <label for="title" class="form-label-custom">Tiêu đề <span class="required-mark">*</span></label>
                                <input type="text" id="title" name="title" class="custom-input" value="{{ old('title', $auction->title) }}" required>
                                @error('title')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label for="description" class="form-label-custom">Mô tả</label>
                                <textarea id="description" name="description" class="custom-textarea custom-input" rows="4">{{ old('description', $auction->description) }}</textarea>
                                @error('description')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="form-group mb-4">
                                <label for="banner_position" class="form-label-custom">Vị trí banner <span class="required-mark">*</span></label>
                                <select id="banner_position" name="banner_position" class="custom-select" required>
                                    <option value="left" {{ old('banner_position', $auction->banner_position) === 'left' ? 'selected' : '' }}>Bên trái</option>
                                    <option value="right" {{ old('banner_position', $auction->banner_position) === 'right' ? 'selected' : '' }}>Bên phải</option>
                                </select>
                                @error('banner_position')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label for="starting_price" class="form-label-custom">Giá khởi điểm (VNĐ) <span class="required-mark">*</span></label>
                                <input type="number" id="starting_price" name="starting_price" class="custom-input" value="{{ old('starting_price', $auction->starting_price) }}" min="0" step="1000" required>
                                @error('starting_price')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label for="banner_duration_days" class="form-label-custom">Thời gian hạ banner (ngày) <span class="required-mark">*</span></label>
                                <input type="number" id="banner_duration_days" name="banner_duration_days" class="custom-input" value="{{ old('banner_duration_days', $auction->banner_duration_days) }}" min="1" max="365" required>
                                @error('banner_duration_days')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-4">
                                <label for="start_time" class="form-label-custom">Thời gian bắt đầu <span class="required-mark">*</span></label>
                                <input type="datetime-local" id="start_time" name="start_time" class="custom-input" value="{{ old('start_time', $auction->start_time->format('Y-m-d\TH:i')) }}" required>
                                @error('start_time')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-4">
                                <label for="end_time" class="form-label-custom">Thời gian kết thúc <span class="required-mark">*</span></label>
                                <input type="datetime-local" id="end_time" name="end_time" class="custom-input" value="{{ old('end_time', $auction->end_time->format('Y-m-d\TH:i')) }}" required>
                                @error('end_time')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <button type="submit" class="btn action-button">
                            <i class="fas fa-save"></i> Cập nhật
                        </button>
                        <a href="{{ route('admin.auctions.index') }}" class="btn back-button">
                            <i class="fas fa-times"></i> Hủy
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
