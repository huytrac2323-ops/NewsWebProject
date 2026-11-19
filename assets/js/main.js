/**
 * V-NEWS CMS - Main Frontend & Admin JavaScript
 */

document.addEventListener('DOMContentLoaded', function () {
    // 1. Dark Mode Toggle
    initTheme();

    // 2. Mobile Menu Toggle
    initMobileMenu();

    // 3. Reading Progress Bar (Nếu ở trang bài viết)
    initReadingProgress();

    // 4. Auto Slug Generator (Dành cho trang biên tập bài viết / chuyên mục)
    initAutoSlug();
});

// Quản lý Dark Mode với LocalStorage
function initTheme() {
    const themeToggleBtn = document.querySelectorAll('.theme-toggle-btn');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const savedTheme = localStorage.getItem('vnews_theme');

    if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }

    themeToggleBtn.forEach(btn => {
        btn.addEventListener('click', function () {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('vnews_theme', isDark ? 'dark' : 'light');
            showToast(isDark ? 'Đã bật chế độ Tối' : 'Đã bật chế độ Sáng', 'info');
        });
    });
}

// Menu điều hướng trên điện thoại (Mobile Drawer)
function initMobileMenu() {
    const menuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    const menuCloseBtn = document.getElementById('mobile-menu-close');

    if (menuBtn && mobileMenu) {
        menuBtn.addEventListener('click', () => {
            mobileMenu.classList.remove('hidden');
        });
    }

    if (menuCloseBtn && mobileMenu) {
        menuCloseBtn.addEventListener('click', () => {
            mobileMenu.classList.add('hidden');
        });
    }
}

// Thanh tiến trình đọc bài viết (Reading Progress Bar)
function initReadingProgress() {
    const progressBar = document.getElementById('reading-progress');
    const articleBody = document.querySelector('.article-prose');

    if (progressBar && articleBody) {
        window.addEventListener('scroll', () => {
            const totalHeight = articleBody.clientHeight - window.innerHeight;
            const currentScroll = window.scrollY - articleBody.offsetTop;
            if (currentScroll > 0) {
                const percent = Math.min(100, Math.max(0, (currentScroll / totalHeight) * 100));
                progressBar.style.width = percent + '%';
            } else {
                progressBar.style.width = '0%';
            }
        });
    }
}

// Sao chép liên kết chia sẻ
function copyCurrentUrl() {
    const dummy = document.createElement('input');
    document.body.appendChild(dummy);
    dummy.value = window.location.href;
    dummy.select();
    document.execCommand('copy');
    document.body.removeChild(dummy);
    showToast('Đã sao chép liên kết vào bộ nhớ tạm!', 'success');
}

// Hiển thị thông báo Toast đẹp mắt
function showToast(message, type = 'info') {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    const bgColors = {
        success: 'bg-emerald-600 text-white',
        error: 'bg-rose-600 text-white',
        info: 'bg-slate-800 text-white dark:bg-white dark:text-slate-900',
        warning: 'bg-amber-600 text-white'
    };

    toast.className = `toast px-4 py-3 rounded-xl shadow-lg text-sm font-medium flex items-center space-x-2 ${bgColors[type] || bgColors.info}`;
    toast.innerHTML = `<span>${message}</span>`;

    container.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Tự động tạo Slug SEO từ Tiêu đề
function initAutoSlug() {
    const titleInput = document.getElementById('title-input');
    const slugInput = document.getElementById('slug-input');

    if (titleInput && slugInput) {
        titleInput.addEventListener('input', function () {
            // Chỉ tự tạo nếu người dùng chưa tự sửa slug thủ công
            if (!slugInput.dataset.manual) {
                slugInput.value = generateSlug(titleInput.value);
            }
        });

        slugInput.addEventListener('input', function () {
            slugInput.dataset.manual = 'true';
        });
    }
}

function generateSlug(text) {
    let str = text.toLowerCase().trim();
    // Chuyển ký tự có dấu thành không dấu
    const unicode = {
        'a': 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
        'd': 'đ',
        'e': 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
        'i': 'í|ì|ỉ|ĩ|ị',
        'o': 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ở|ỡ|ợ',
        'u': 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
        'y': 'ý|ỳ|ỷ|ỹ|ỵ'
    };
    for (let nonAccent in unicode) {
        let pattern = new RegExp(unicode[nonAccent], 'g');
        str = str.replace(pattern, nonAccent);
    }
    // Xóa ký tự đặc biệt
    str = str.replace(/[^a-z0-9\s-]/g, '')
             .replace(/[\s-]+/g, '-')
             .replace(/^-+|-+$/g, '');
    return str;
}
