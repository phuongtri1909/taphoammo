<div id="termsPopup" class="fixed bottom-0 left-0 right-0 z-50 transform translate-y-full transition-transform duration-300 ease-in-out">
    <div class="bg-white border-t border-gray-200 shadow-lg max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                    <i class="fas fa-file-contract text-primary"></i>
                    <h4 class="font-semibold text-gray-900 text-sm sm:text-base" id="termsPopupTitle">Điều khoản sử dụng</h4>
                </div>
                <p class="text-xs sm:text-sm text-gray-600" id="termsPopupSummary">
                    Bằng việc sử dụng website này, bạn đồng ý với các điều khoản sử dụng của chúng tôi.
                </p>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <a href="{{ route('terms-of-service.index') }}" id="termsPopupLink" class="px-3 py-1.5 text-xs sm:text-sm font-medium text-primary hover:text-primary-6 transition-colors">
                    Xem chi tiết
                </a>
                <button type="button" id="termsAcceptBtn" class="px-4 py-1.5 text-xs sm:text-sm font-semibold bg-primary hover:bg-primary-6 text-white rounded-lg transition-colors shadow-sm">
                    Đồng ý
                </button>
                <button type="button" id="termsCloseBtn" class="p-1.5 text-gray-400 hover:text-gray-600 transition-colors" title="Đóng">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const termsPopup = document.getElementById('termsPopup');
        const termsAcceptBtn = document.getElementById('termsAcceptBtn');
        const termsCloseBtn = document.getElementById('termsCloseBtn');
        const termsPopupLink = document.getElementById('termsPopupLink');
        const termsPopupTitle = document.getElementById('termsPopupTitle');
        const termsPopupSummary = document.getElementById('termsPopupSummary');

        const hasAcceptedTerms = localStorage.getItem('terms_accepted');

        if (!hasAcceptedTerms) {
            fetch('{{ route('terms-of-service.get-summary') }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        termsPopupTitle.textContent = data.title;
                        termsPopupSummary.textContent = data.summary;
                        termsPopupLink.href = data.link;
                    }
                    
                    setTimeout(() => {
                        termsPopup.classList.remove('translate-y-full');
                        termsPopup.classList.add('translate-y-0');
                    }, 1000);
                })
                .catch(() => {
                    setTimeout(() => {
                        termsPopup.classList.remove('translate-y-full');
                        termsPopup.classList.add('translate-y-0');
                    }, 1000);
                });
        }

        termsAcceptBtn.addEventListener('click', function() {
            localStorage.setItem('terms_accepted', 'true');
            
            termsPopup.classList.remove('translate-y-0');
            termsPopup.classList.add('translate-y-full');
        });

        termsCloseBtn.addEventListener('click', function() {
            localStorage.setItem('terms_accepted', 'true');
            
            termsPopup.classList.remove('translate-y-0');
            termsPopup.classList.add('translate-y-full');
        });
    });
</script>
@endpush
