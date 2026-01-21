@extends('client.layouts.app')

@section('title', 'Chỉnh sửa bài viết - ' . config('app.name'))

@section('content')
    <div class="w-full bg-gradient-to-br from-gray-50 via-white to-gray-50 min-h-screen py-4 md:py-6">
        <div class="w-full max-w-5xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden transform transition-all duration-300 hover:shadow-xl animate-fadeIn">
                <div class="p-4 md:p-5 lg:p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <a href="{{ route('shares.manage.index') }}"
                            class="w-9 h-9 flex items-center justify-center text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-all duration-200">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <div>
                            <h1 class="text-xl md:text-2xl font-bold text-gray-900">Chỉnh sửa bài viết</h1>
                            <p class="text-sm text-gray-500 mt-0.5">Cập nhật nội dung bài viết của bạn</p>
                        </div>
                    </div>

                    @if ($share->isRejected() && $share->rejection_reason)
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-exclamation-circle text-white text-sm"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-red-800 mb-1">Bài viết bị từ chối</h4>
                                    <p class="text-sm text-red-600">{{ $share->rejection_reason }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('shares.manage.update', $share) }}" method="POST" enctype="multipart/form-data" id="shareForm">
                        @csrf
                        @method('PUT')

                        <div class="space-y-5">
                            <div>
                                <label for="title" class="flex items-center gap-2 mb-2">
                                    <div class="w-7 h-7 bg-primary/10 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-heading text-primary text-xs"></i>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-700">
                                        Tiêu đề <span class="text-red-500">*</span>
                                    </span>
                                </label>
                                <input type="text" id="title" name="title" value="{{ old('title', $share->title) }}" required
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all text-sm"
                                    placeholder="Nhập tiêu đề bài viết">
                                @error('title')
                                    <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label for="share_category_id" class="flex items-center gap-2 mb-2">
                                    <div class="w-7 h-7 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-folder-open text-blue-600 text-xs"></i>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-700">
                                        Danh mục <span class="text-red-500">*</span>
                                    </span>
                                </label>
                                <select id="share_category_id" name="share_category_id" required
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all text-sm">
                                    <option value="">Chọn danh mục</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('share_category_id', $share->share_category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('share_category_id')
                                    <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label for="excerpt" class="flex items-center gap-2 mb-2">
                                    <div class="w-7 h-7 bg-purple-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-align-left text-purple-600 text-xs"></i>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-700">Mô tả ngắn</span>
                                </label>
                                <textarea id="excerpt" name="excerpt" rows="2"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all text-sm resize-none"
                                    placeholder="Mô tả ngắn gọn về bài viết (tùy chọn)">{{ old('excerpt', $share->excerpt) }}</textarea>
                                @error('excerpt')
                                    <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label class="flex items-center gap-2 mb-2">
                                    <div class="w-7 h-7 bg-green-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-image text-green-600 text-xs"></i>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-700">Ảnh đại diện</span>
                                </label>
                                <div class="flex items-center gap-4">
                                    <div id="imagePreview"
                                        class="w-32 h-32 bg-gradient-to-br from-gray-100 to-gray-200 rounded-lg flex items-center justify-center overflow-hidden border-2 border-dashed border-gray-300">
                                        @if ($share->image)
                                            <img src="{{ $share->image_url }}" class="w-full h-full object-cover">
                                        @else
                                            <i class="fas fa-image text-gray-400 text-2xl"></i>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <input type="file" id="image" name="image" accept="image/*" class="hidden"
                                            onchange="previewImage(this)">
                                        <button type="button" onclick="document.getElementById('image').click()"
                                            class="px-4 py-2 bg-gradient-to-r from-gray-100 to-gray-200 hover:from-gray-200 hover:to-gray-300 text-gray-700 font-semibold text-sm rounded-lg shadow-sm hover:shadow-md transition-all duration-200">
                                            <i class="fas fa-upload mr-1.5"></i> Đổi ảnh
                                        </button>
                                        <p class="text-xs text-gray-500 mt-2">PNG, JPG, GIF, WebP. Tối đa 5MB.</p>
                                    </div>
                                </div>
                                @error('image')
                                    <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label for="content" class="flex items-center gap-2 mb-2">
                                    <div class="w-7 h-7 bg-orange-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-align-justify text-orange-600 text-xs"></i>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-700">
                                        Nội dung <span class="text-red-500">*</span>
                                    </span>
                                </label>
                                <textarea id="content" name="content">{{ old('content', $share->content) }}</textarea>
                                @error('content')
                                    <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div class="flex flex-col sm:flex-row gap-3 pt-5 border-t border-gray-200">
                                <input type="hidden" name="action" id="formAction" value="draft">
                                <button type="submit" onclick="document.getElementById('formAction').value='draft'"
                                    class="flex-1 px-4 py-2.5 bg-gradient-to-r from-gray-100 to-gray-200 hover:from-gray-200 hover:to-gray-300 text-gray-700 font-semibold text-sm rounded-lg shadow-sm hover:shadow-md transition-all duration-300 transform hover:scale-[1.01] active:scale-[0.99]">
                                    <i class="fas fa-save mr-2"></i> Lưu nháp
                                </button>
                                <button type="submit" onclick="document.getElementById('formAction').value='submit'"
                                    class="flex-1 px-4 py-2.5 bg-gradient-to-r from-primary to-primary-6 hover:from-primary-6 hover:to-primary text-white font-semibold text-sm rounded-lg shadow-md hover:shadow-lg transition-all duration-300 transform hover:scale-[1.01] active:scale-[0.99]">
                                    <i class="fas fa-paper-plane mr-2"></i> Gửi duyệt lại
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
    <script>
        CKEDITOR.replace('content', {
            language: 'vi',
            height: 400,
            filebrowserUploadUrl: '{{ route('shares.manage.upload-image') }}?_token={{ csrf_token() }}',
            filebrowserUploadMethod: 'form',
            extraPlugins: 'image2,uploadimage,justify,colorbutton,font',
            removePlugins: 'image',
            image2_alignClasses: ['image-left', 'image-center', 'image-right'],
            image2_disableResizer: false,
            toolbarGroups: [
                { name: 'document', groups: ['mode', 'document', 'doctools'] },
                { name: 'clipboard', groups: ['clipboard', 'undo'] },
                { name: 'editing', groups: ['find', 'selection', 'spellchecker'] },
                { name: 'forms' },
                '/',
                { name: 'basicstyles', groups: ['basicstyles', 'cleanup'] },
                { name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi'] },
                { name: 'links' },
                { name: 'insert' },
                '/',
                { name: 'styles' },
                { name: 'colors' },
                { name: 'tools' },
                { name: 'others' }
            ],
            contentsCss: [
                'body { font-family: Arial, sans-serif; font-size: 14px; line-height: 1.6; }',
                '.image-left { float: left; margin-right: 15px; margin-bottom: 10px; }',
                '.image-right { float: right; margin-left: 15px; margin-bottom: 10px; }',
                '.image-center { display: block; margin: 15px auto; }'
            ]
        });

        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endpush

@push('styles')
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeIn {
            animation: fadeIn 0.6s ease-out forwards;
            opacity: 0;
        }

        .cke_editable {
            padding: 15px !important;
        }
    </style>
@endpush
