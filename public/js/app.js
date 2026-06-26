/**
 * E-Learning SMK — App JavaScript
 * Sidebar toggle, dropdown, modals, chart, flash auto-dismiss
 */

document.addEventListener('DOMContentLoaded', function () {

    // ── Sidebar Toggle (Mobile) ──────────────────────────────
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('open');
            sidebarOverlay.classList.toggle('active');
        });
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function () {
            sidebar.classList.remove('open');
            sidebarOverlay.classList.remove('active');
        });
    }

    // ── User Dropdown ────────────────────────────────────────
    const dropdownToggle = document.getElementById('userDropdownToggle');
    const dropdownMenu = document.getElementById('userDropdownMenu');

    if (dropdownToggle && dropdownMenu) {
        dropdownToggle.addEventListener('click', function (e) {
            e.stopPropagation();
            dropdownMenu.classList.toggle('show');
        });

        document.addEventListener('click', function (e) {
            if (!dropdownMenu.contains(e.target) && !dropdownToggle.contains(e.target)) {
                dropdownMenu.classList.remove('show');
            }
        });
    }

    // ── Flash Messages Auto-Dismiss ──────────────────────────
    const flashAlerts = document.querySelectorAll('#flashAlert');
    flashAlerts.forEach(function (alert) {
        setTimeout(function () {
            alert.style.transition = 'opacity .4s ease, transform .4s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(function () {
                alert.remove();
            }, 400);
        }, 5000);
    });

    // ── Modal ────────────────────────────────────────────────
    window.openModal = function (modalId) {
        const overlay = document.getElementById(modalId);
        if (overlay) {
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    };

    window.closeModal = function (modalId) {
        const overlay = document.getElementById(modalId);
        if (overlay) {
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }
    };

    // Close modal on overlay click
    document.querySelectorAll('.modal-overlay').forEach(function (overlay) {
        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) {
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    });

    // Close modal on Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-overlay.active').forEach(function (overlay) {
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            });
        }
    });

    // ── Landing Page Nav Scroll ──────────────────────────────
    const landingNav = document.querySelector('.landing-nav');
    if (landingNav) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > 60) {
                landingNav.classList.add('scrolled');
            } else {
                landingNav.classList.remove('scrolled');
            }
        });
    }

    // ── Simple Table Search ──────────────────────────────────
    const searchInputs = document.querySelectorAll('[data-table-search]');
    searchInputs.forEach(function (input) {
        const tableId = input.getAttribute('data-table-search');
        const table = document.getElementById(tableId);
        if (!table) return;

        input.addEventListener('input', function () {
            const query = this.value.toLowerCase();
            const rows = table.querySelectorAll('tbody tr');
            rows.forEach(function (row) {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        });
    });

    // ── Confirm Delete ───────────────────────────────────────
    document.querySelectorAll('[data-confirm]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            const message = this.getAttribute('data-confirm') || 'Apakah Anda yakin?';
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });

    // ── Revenue Chart (Dashboard) ────────────────────────────
    const chartCanvas = document.getElementById('revenueChart');
    if (chartCanvas) {
        renderRevenueChart(chartCanvas);
    }
});

/**
 * Render revenue bar chart using Canvas 2D
 */
function renderRevenueChart(canvas) {
    const ctx = canvas.getContext('2d');
    const dataAttr = canvas.getAttribute('data-values');
    const labelsAttr = canvas.getAttribute('data-labels');

    if (!dataAttr || !labelsAttr) return;

    const values = JSON.parse(dataAttr);
    const labels = JSON.parse(labelsAttr);
    const maxValue = Math.max(...values, 1);

    function draw() {
        const dpr = window.devicePixelRatio || 1;
        const rect = canvas.getBoundingClientRect();
        canvas.width = rect.width * dpr;
        canvas.height = rect.height * dpr;
        ctx.scale(dpr, dpr);

        const width = rect.width;
        const height = rect.height;
        const padding = { top: 20, right: 20, bottom: 50, left: 80 };
        const chartWidth = width - padding.left - padding.right;
        const chartHeight = height - padding.top - padding.bottom;

        ctx.clearRect(0, 0, width, height);

        // Grid lines
        const gridLines = 5;
        ctx.strokeStyle = '#e2e8f0';
        ctx.lineWidth = 1;
        ctx.font = '11px Inter, sans-serif';
        ctx.fillStyle = '#94a3b8';
        ctx.textAlign = 'right';

        for (let i = 0; i <= gridLines; i++) {
            const y = padding.top + (chartHeight / gridLines) * i;
            const value = maxValue - (maxValue / gridLines) * i;

            ctx.beginPath();
            ctx.setLineDash([4, 4]);
            ctx.moveTo(padding.left, y);
            ctx.lineTo(width - padding.right, y);
            ctx.stroke();
            ctx.setLineDash([]);

            ctx.fillText(formatRupiah(value), padding.left - 10, y + 4);
        }

        // Bars
        const barWidth = Math.min(36, (chartWidth / values.length) - 12);
        const barGap = (chartWidth - barWidth * values.length) / (values.length + 1);

        values.forEach(function (val, i) {
            const x = padding.left + barGap + (barWidth + barGap) * i;
            const barHeight = (val / maxValue) * chartHeight;
            const y = padding.top + chartHeight - barHeight;

            // Bar gradient
            const gradient = ctx.createLinearGradient(0, y, 0, y + barHeight);
            gradient.addColorStop(0, '#dc2626');
            gradient.addColorStop(1, '#991b1b');

            // Rounded top bar
            const radius = Math.min(6, barWidth / 2);
            ctx.beginPath();
            ctx.moveTo(x, y + barHeight);
            ctx.lineTo(x, y + radius);
            ctx.quadraticCurveTo(x, y, x + radius, y);
            ctx.lineTo(x + barWidth - radius, y);
            ctx.quadraticCurveTo(x + barWidth, y, x + barWidth, y + radius);
            ctx.lineTo(x + barWidth, y + barHeight);
            ctx.closePath();
            ctx.fillStyle = gradient;
            ctx.fill();

            // Label
            ctx.fillStyle = '#64748b';
            ctx.textAlign = 'center';
            ctx.font = '11px Inter, sans-serif';
            ctx.fillText(labels[i], x + barWidth / 2, height - padding.bottom + 20);
        });
    }

    draw();
    window.addEventListener('resize', draw);
}

function formatRupiah(value) {
    if (value >= 1000000) {
        return 'Rp ' + (value / 1000000).toFixed(1) + ' jt';
    } else if (value >= 1000) {
        return 'Rp ' + (value / 1000).toFixed(0) + ' rb';
    }
    return 'Rp ' + value;
}
