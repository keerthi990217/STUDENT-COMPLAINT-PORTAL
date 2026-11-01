// ============================================================
// js/script.js
// Student Complaint Portal - Main JavaScript
// ============================================================

// ── Helper: Show Alert ──────────────────────────────────────
function showAlert(containerId, type, message) {
    const container = document.getElementById(containerId);
    if (!container) return;
    const icons = { success: '✅', danger: '❌', warning: '⚠️', info: 'ℹ️' };
    container.innerHTML = `
        <div class="alert alert-${type}">
            <span>${icons[type] || ''}</span>
            <span>${message}</span>
        </div>`;
    container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

// ── Helper: Set Button Loading ──────────────────────────────
function setLoading(btn, loading, originalText) {
    if (loading) {
        btn.disabled = true;
        btn.innerHTML = `<span class="loader"></span> Please wait...`;
    } else {
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
}

// ── Helper: Format Date ─────────────────────────────────────
function formatDate(dateStr) {
    if (!dateStr) return '—';
    const d = new Date(dateStr);
    return d.toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' }) +
           ' ' + d.toLocaleTimeString('en-IN', { hour: '2-digit', minute: '2-digit' });
}

// ── Helper: Status Badge ────────────────────────────────────
function statusBadge(status) {
    const labels = {
        pending:     '🕐 Pending',
        in_progress: '🔄 In Progress',
        resolved:    '✅ Resolved',
        rejected:    '❌ Rejected'
    };
    return `<span class="badge badge-${status}">${labels[status] || status}</span>`;
}

// ── Helper: Category Label ──────────────────────────────────
function categoryLabel(cat) {
    const map = {
        academics:  '📚 Academics',
        faculty:    '👨‍🏫 Faculty',
        facilities: '🏫 Facilities',
        hostel:     '🏠 Hostel',
        other:      '📋 Other'
    };
    return map[cat] || cat;
}


// ============================================================
// STUDENT LOGIN
// ============================================================
function initStudentLogin() {
    const form = document.getElementById('studentLoginForm');
    if (!form) return;

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        const btn = form.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        setLoading(btn, true);

        const data = new FormData(form);

        try {
            const res  = await fetch('php/student_login.php', { method: 'POST', body: data });
            const json = await res.json();

            if (json.success) {
                showAlert('loginAlert', 'success', `Welcome back, ${json.name}! Redirecting...`);
                setTimeout(() => { window.location.href = 'dashboard.html'; }, 1200);
            } else {
                showAlert('loginAlert', 'danger', json.message);
                setLoading(btn, false, originalText);
            }
        } catch (err) {
            showAlert('loginAlert', 'danger', 'Server error. Please try again.');
            setLoading(btn, false, originalText);
        }
    });
}


// ============================================================
// ADMIN LOGIN
// ============================================================
function initAdminLogin() {
    const form = document.getElementById('adminLoginForm');
    if (!form) return;

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        const btn = form.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        setLoading(btn, true);

        const data = new FormData(form);

        try {
            const res  = await fetch('php/admin_login.php', { method: 'POST', body: data });
            const json = await res.json();

            if (json.success) {
                showAlert('loginAlert', 'success', `Welcome, ${json.name}! Redirecting...`);
                setTimeout(() => { window.location.href = 'admin/dashboard.html'; }, 1200);
            } else {
                showAlert('loginAlert', 'danger', json.message);
                setLoading(btn, false, originalText);
            }
        } catch (err) {
            showAlert('loginAlert', 'danger', 'Server error. Please try again.');
            setLoading(btn, false, originalText);
        }
    });
}


// ============================================================
// LOAD DEPARTMENTS (dropdown)
// ============================================================
async function loadDepartments(selectId) {
    const select = document.getElementById(selectId);
    if (!select) return;

    try {
        const res  = await fetch('php/get_departments.php');
        const json = await res.json();

        if (json.success) {
            json.departments.forEach(dept => {
                const opt = document.createElement('option');
                opt.value = dept.id;
                opt.textContent = dept.name;
                select.appendChild(opt);
            });
        }
    } catch (err) {
        console.error('Failed to load departments:', err);
    }
}


// ============================================================
// SUBMIT COMPLAINT
// ============================================================
function initComplaintForm() {
    const form = document.getElementById('complaintForm');
    if (!form) return;

    loadDepartments('deptSelect');

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        // Client-side validation
        const subject = form.querySelector('[name="subject"]').value.trim();
        const desc    = form.querySelector('[name="description"]').value.trim();
        const dept    = form.querySelector('[name="department_id"]').value;
        const cat     = form.querySelector('[name="category"]').value;

        if (!dept)              return showAlert('complaintAlert', 'warning', 'Please select a department.');
        if (!cat)               return showAlert('complaintAlert', 'warning', 'Please select a complaint category.');
        if (!subject)           return showAlert('complaintAlert', 'warning', 'Please enter a complaint subject.');
        if (desc.length < 20)   return showAlert('complaintAlert', 'warning', 'Description must be at least 20 characters.');

        const btn = form.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        setLoading(btn, true);

        const data = new FormData(form);

        try {
            const res  = await fetch('php/submit_complaint.php', { method: 'POST', body: data });
            const json = await res.json();

            if (json.success) {
                document.getElementById('complaintForm').reset();
                document.getElementById('codeDisplay').textContent = json.complaint_code;
                document.getElementById('successSection').classList.remove('hidden');
                document.getElementById('formSection').classList.add('hidden');
            } else {
                showAlert('complaintAlert', 'danger', json.message);
            }
        } catch (err) {
            showAlert('complaintAlert', 'danger', 'Server error. Please try again.');
        } finally {
            setLoading(btn, false, originalText);
        }
    });
}


// ============================================================
// STUDENT DASHBOARD - View My Complaints
// ============================================================
async function loadStudentComplaints() {
    const container = document.getElementById('myComplaintsContainer');
    if (!container) return;

    container.innerHTML = '<p style="color:var(--gray-500);padding:20px">Loading...</p>';

    try {
        const res  = await fetch('php/get_student_complaints.php');
        const json = await res.json();

        if (!json.success) {
            container.innerHTML = `<div class="alert alert-danger">${json.message}</div>`;
            return;
        }

        if (json.complaints.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <div class="empty-icon">📭</div>
                    <h4>No Complaints Yet</h4>
                    <p>You haven't submitted any complaints yet.</p>
                </div>`;
            return;
        }

        const rows = json.complaints.map(c => `
            <tr>
                <td><strong style="color:var(--amber);font-family:monospace">${c.complaint_code}</strong></td>
                <td>${categoryLabel(c.category)}</td>
                <td>${c.department}</td>
                <td title="${c.subject}">${c.subject.length > 40 ? c.subject.substring(0,40)+'…' : c.subject}</td>
                <td>${statusBadge(c.status)}</td>
                <td>${formatDate(c.submitted_at)}</td>
                <td>${c.admin_remarks ? `<span title="${c.admin_remarks}" style="font-size:0.82rem;color:var(--gray-500)">${c.admin_remarks.substring(0,30)}…</span>` : '—'}</td>
            </tr>
        `).join('');

        container.innerHTML = `
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Category</th>
                            <th>Department</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>${rows}</tbody>
                </table>
            </div>`;
    } catch (err) {
        container.innerHTML = '<div class="alert alert-danger">Failed to load complaints.</div>';
    }
}


// ============================================================
// CHECK COMPLAINT STATUS (Public - no login needed)
// ============================================================
function initStatusCheck() {
    const form = document.getElementById('statusCheckForm');
    if (!form) return;

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        const code = form.querySelector('#complaintCode').value.trim().toUpperCase();
        const btn  = form.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;

        if (!code) return showAlert('statusAlert', 'warning', 'Please enter your complaint code.');

        setLoading(btn, true);
        document.getElementById('statusResult').classList.add('hidden');

        try {
            const res  = await fetch(`php/check_status.php?code=${encodeURIComponent(code)}`);
            const json = await res.json();

            if (json.success) {
                const c = json.complaint;
                document.getElementById('statusResult').classList.remove('hidden');
                document.getElementById('statusResult').innerHTML = `
                    <div class="card mt-3">
                        <div class="card-header">
                            <h3>Complaint: <span style="color:var(--amber)">${c.complaint_code}</span></h3>
                            ${statusBadge(c.status)}
                        </div>
                        <div class="card-body">
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                                <div>
                                    <div class="form-label">Category</div>
                                    <p>${categoryLabel(c.category)}</p>
                                </div>
                                <div>
                                    <div class="form-label">Department</div>
                                    <p>${c.department}</p>
                                </div>
                                <div style="grid-column:1/-1">
                                    <div class="form-label">Subject</div>
                                    <p>${c.subject}</p>
                                </div>
                                <div>
                                    <div class="form-label">Submitted On</div>
                                    <p>${formatDate(c.submitted_at)}</p>
                                </div>
                                <div>
                                    <div class="form-label">Last Updated</div>
                                    <p>${formatDate(c.updated_at)}</p>
                                </div>
                                ${c.admin_remarks ? `
                                <div style="grid-column:1/-1">
                                    <div class="form-label">Admin Remarks</div>
                                    <p style="background:var(--off-white);padding:10px;border-radius:6px">${c.admin_remarks}</p>
                                </div>` : ''}
                            </div>
                        </div>
                    </div>`;
            } else {
                showAlert('statusAlert', 'danger', json.message);
            }
        } catch (err) {
            showAlert('statusAlert', 'danger', 'Server error. Please try again.');
        } finally {
            setLoading(btn, false, originalText);
        }
    });
}


// ============================================================
// ADMIN DASHBOARD
// ============================================================
async function loadAdminComplaints(status = '', deptId = '') {
    const container = document.getElementById('complaintsTableContainer');
    if (!container) return;

    container.innerHTML = '<p style="color:var(--gray-500);padding:20px">Loading complaints...</p>';

    try {
        const params = new URLSearchParams({ status, department_id: deptId });
        const res    = await fetch(`php/get_complaints.php?${params}`);
        const json   = await res.json();

        if (!json.success) {
            container.innerHTML = `<div class="alert alert-danger">${json.message}</div>`;
            return;
        }

        // Update stats
        updateStats(json.stats);

        if (json.complaints.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <div class="empty-icon">📭</div>
                    <h4>No Complaints Found</h4>
                    <p>No complaints match your filter criteria.</p>
                </div>`;
            return;
        }

        const rows = json.complaints.map(c => `
            <tr>
                <td><strong style="color:var(--amber);font-family:monospace;font-size:0.82rem">${c.complaint_code}</strong></td>
                <td>${c.is_anonymous == 1 ? '<span class="badge badge-anonymous">🕵️ Anonymous</span>' : `<span style="font-size:0.88rem">${c.student_name}<br><small style="color:var(--gray-500)">${c.student_roll}</small></span>`}</td>
                <td>${categoryLabel(c.category)}</td>
                <td style="max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="${c.subject}">${c.subject}</td>
                <td>${c.department}</td>
                <td>${statusBadge(c.status)}</td>
                <td style="font-size:0.82rem;color:var(--gray-500)">${formatDate(c.submitted_at)}</td>
                <td>
                    <button class="btn btn-sm btn-outline" onclick='openUpdateModal(${JSON.stringify(c)})'>
                        ✏️ Update
                    </button>
                </td>
            </tr>
        `).join('');

        container.innerHTML = `
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Student</th>
                            <th>Category</th>
                            <th>Subject</th>
                            <th>Department</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>${rows}</tbody>
                </table>
            </div>`;
    } catch (err) {
        container.innerHTML = '<div class="alert alert-danger">Failed to load complaints.</div>';
        console.error(err);
    }
}

function updateStats(stats) {
    const el = (id) => document.getElementById(id);
    if (el('statTotal'))      el('statTotal').textContent      = stats.total || 0;
    if (el('statPending'))    el('statPending').textContent    = stats.pending || 0;
    if (el('statProgress'))   el('statProgress').textContent   = stats.in_progress || 0;
    if (el('statResolved'))   el('statResolved').textContent   = stats.resolved || 0;
}

// Open update modal
function openUpdateModal(complaint) {
    document.getElementById('modalComplaintCode').textContent = complaint.complaint_code;
    document.getElementById('modalSubject').textContent       = complaint.subject;
    document.getElementById('modalDescription').textContent   = complaint.description;
    document.getElementById('modalDept').textContent          = complaint.department;
    document.getElementById('updateComplaintId').value        = complaint.id;
    document.getElementById('updateStatus').value             = complaint.status;
    document.getElementById('updateRemarks').value            = complaint.admin_remarks || '';
    document.getElementById('updateModal').classList.add('open');
}

function closeModal() {
    document.getElementById('updateModal').classList.remove('open');
    document.getElementById('modalAlert').innerHTML = '';
}

// Update complaint status
async function submitStatusUpdate() {
    const btn = document.getElementById('updateBtn');
    const originalText = btn.innerHTML;
    setLoading(btn, true);

    const data = new FormData();
    data.append('complaint_id', document.getElementById('updateComplaintId').value);
    data.append('status',       document.getElementById('updateStatus').value);
    data.append('remarks',      document.getElementById('updateRemarks').value);

    try {
        const res  = await fetch('php/update_status.php', { method: 'POST', body: data });
        const json = await res.json();

        if (json.success) {
            showAlert('modalAlert', 'success', json.message);
            setTimeout(() => {
                closeModal();
                loadAdminComplaints(); // refresh
            }, 1200);
        } else {
            showAlert('modalAlert', 'danger', json.message);
        }
    } catch (err) {
        showAlert('modalAlert', 'danger', 'Server error. Please try again.');
    } finally {
        setLoading(btn, false, originalText);
    }
}

function initAdminDashboard() {
    if (!document.getElementById('complaintsTableContainer')) return;

    loadAdminComplaints();
    loadDepartments('filterDept');

    // Filter form
    document.getElementById('filterForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const status = document.getElementById('filterStatus').value;
        const dept   = document.getElementById('filterDept').value;
        loadAdminComplaints(status, dept);
    });

    document.getElementById('filterForm')?.addEventListener('reset', function() {
        setTimeout(() => loadAdminComplaints(), 50);
    });
}


// ============================================================
// STUDENT REGISTRATION
// ============================================================
function initRegisterForm() {
    const form = document.getElementById('registerForm');
    if (!form) return;

    // Load departments into the select dropdown
    loadDepartments('department_id');

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        // Client-side password match check before sending to server
        const password        = form.querySelector('#password').value;
        const confirmPassword = form.querySelector('#confirm_password').value;

        if (password !== confirmPassword) {
            return showAlert('registerAlert', 'warning', 'Passwords do not match. Please re-enter.');
        }

        if (password.length < 6) {
            return showAlert('registerAlert', 'warning', 'Password must be at least 6 characters.');
        }

        const btn = form.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        setLoading(btn, true);

        const data = new FormData(form);

        try {
            const res  = await fetch('php/register.php', { method: 'POST', body: data });
            const json = await res.json();

            if (json.success) {
                showAlert('registerAlert', 'success', json.message + ' Redirecting to login...');
                form.reset();
                setTimeout(() => { window.location.href = 'login.html'; }, 2000);
            } else {
                showAlert('registerAlert', 'danger', json.message);
                setLoading(btn, false, originalText);
            }
        } catch (err) {
            showAlert('registerAlert', 'danger', 'Server error. Please try again.');
            setLoading(btn, false, originalText);
        }
    });
}


// ============================================================
// PAGE INIT
// ============================================================
document.addEventListener('DOMContentLoaded', function () {
    // Detect which page we're on and init accordingly
    const page = document.body.dataset.page;

    if (page === 'student-login')     initStudentLogin();
    if (page === 'student-register')  initRegisterForm();
    if (page === 'admin-login')       initAdminLogin();
    if (page === 'submit-complaint')  initComplaintForm();
    if (page === 'student-dashboard') loadStudentComplaints();
    if (page === 'check-status')      initStatusCheck();
    if (page === 'admin-dashboard')   initAdminDashboard();
});
