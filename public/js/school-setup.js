document.addEventListener('DOMContentLoaded', function () {
    const cfg = window.SCHOOL_SETUP || {};

    function toggleLoading(button, loading) {
        if (!button) return;
        const spinner = button.querySelector('.spinner-border');
        const text = button.querySelector('.btn-text');
        button.disabled = loading;
        if (spinner) spinner.classList.toggle('d-none', !loading);
        if (text) text.classList.toggle('d-none', loading);
    }

    function showToast(message, type) {
        const container = document.getElementById('setupToastContainer');
        if (!container || !window.bootstrap) return;

        const el = document.createElement('div');
        el.className = 'toast align-items-center text-bg-' + (type === 'error' ? 'danger' : 'success') + ' border-0';
        el.setAttribute('role', 'alert');
        el.innerHTML =
            '<div class="d-flex">' +
            '<div class="toast-body">' + message + '</div>' +
            '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>' +
            '</div>';
        container.appendChild(el);
        const toast = new bootstrap.Toast(el, { delay: 2500 });
        toast.show();
        el.addEventListener('hidden.bs.toast', function () { el.remove(); });
    }

    function getModal(id) {
        const el = document.getElementById(id);
        return el ? bootstrap.Modal.getOrCreateInstance(el) : null;
    }

    function slugPart(text) {
        return String(text || '')
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
    }

    function listIdForGradeStrand(grade, strand) {
        return 'grade-sections-' + slugPart(grade + (strand ? '-' + strand : ''));
    }

    function seniorBodyId(grade) {
        return 'senior-body-' + slugPart(grade);
    }

    function openAddSectionModal(grade, strand) {
        const strandWrap = document.getElementById('addSectionStrandWrap');
        const strandSelect = document.getElementById('addSectionStrand');
        const strandHidden = document.getElementById('addSectionStrandHidden');
        const title = document.getElementById('addSectionModalTitle');
        const isSenior = (cfg.seniorHighGrades || []).includes(grade);

        document.getElementById('addSectionGrade').value = grade;
        document.getElementById('addSectionName').value = '';

        if (isSenior && strand) {
            strandWrap.hidden = true;
            strandHidden.value = strand;
            strandSelect.required = false;
            title.textContent = 'Add section — ' + grade + ' / ' + strand;
        } else if (isSenior) {
            strandWrap.hidden = false;
            strandHidden.value = '';
            strandSelect.innerHTML = '<option value="">Select strand…</option>';
            (cfg.shsStrands || []).forEach(function (s) {
                const opt = document.createElement('option');
                opt.value = s;
                opt.textContent = s;
                strandSelect.appendChild(opt);
            });
            strandSelect.required = true;
            title.textContent = 'Add section — ' + grade;
        } else {
            strandWrap.hidden = true;
            strandHidden.value = '';
            strandSelect.required = false;
            title.textContent = 'Add section — ' + grade;
        }

        getModal('addSectionModal').show();
        setTimeout(function () {
            document.getElementById('addSectionName').focus();
        }, 250);
    }

    function openAddStrandModal() {
        document.getElementById('addStrandName').value = '';
        getModal('addStrandModal').show();
        setTimeout(function () {
            document.getElementById('addStrandName').focus();
        }, 250);
    }

    document.body.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-action]');
        if (!btn) return;

        const action = btn.dataset.action;

        if (action === 'add-section') {
            e.preventDefault();
            openAddSectionModal(btn.dataset.grade || '', btn.dataset.strand || '');
            return;
        }

        if (action === 'add-strand') {
            e.preventDefault();
            openAddStrandModal();
            return;
        }

        if (action === 'remove-section') {
            e.preventDefault();
            if (!confirm('Remove this section?')) return;
            fetch(cfg.urls.gradeSection + '/' + btn.dataset.id, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': cfg.csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                    Accept: 'application/json',
                },
            }).then(function (response) {
                if (response.ok) {
                    document.getElementById('grade-section-' + btn.dataset.id)?.remove();
                    showToast('Section removed');
                } else {
                    showToast('Could not remove section', 'error');
                }
            });
            return;
        }

        if (action === 'remove-strand') {
            e.preventDefault();
            const strandName = btn.dataset.strand;
            const strandId = btn.dataset.strandId;
            if (!confirm('Remove strand "' + strandName + '" and all its sections?')) return;

            fetch(cfg.urls.strands + '/' + strandId, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': cfg.csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                    Accept: 'application/json',
                },
            }).then(function (response) {
                if (!response.ok) {
                    showToast('Could not remove strand', 'error');
                    return response.json().then(function () { throw new Error(); });
                }
                return response.json();
            }).then(function (data) {
                document.querySelectorAll('.strand-block[data-strand="' + CSS.escape(data.name) + '"]').forEach(function (el) {
                    el.remove();
                });
                cfg.shsStrands = (cfg.shsStrands || []).filter(function (s) { return s !== data.name; });
                showToast('Strand removed');
            }).catch(function () {});
            return;
        }

        if (action === 'edit-course') {
            e.preventDefault();
            const form = document.getElementById('editCourseForm');
            form.action = cfg.urls.course + '/' + btn.dataset.id;
            document.getElementById('editCourseCode').value = btn.dataset.code;
            document.getElementById('editCourseName').value = btn.dataset.name;
            getModal('editCourseModal').show();
            return;
        }

        if (action === 'delete-course') {
            e.preventDefault();
            const form = document.getElementById('deleteCourseForm');
            form.action = cfg.urls.course + '/' + btn.dataset.id;
            document.getElementById('deleteCourseMessage').textContent =
                'Remove course "' + btn.dataset.code + '"?';
            getModal('deleteCourseModal').show();
            return;
        }

        if (action === 'edit-program') {
            e.preventDefault();
            const form = document.getElementById('editProgramForm');
            form.action = cfg.urls.program + '/' + btn.dataset.id;
            document.getElementById('editProgramCode').value = btn.dataset.code;
            document.getElementById('editProgramName').value = btn.dataset.name;
            getModal('editProgramModal').show();
            return;
        }

        if (action === 'delete-program') {
            e.preventDefault();
            const form = document.getElementById('deleteProgramForm');
            form.action = cfg.urls.program + '/' + btn.dataset.id;
            document.getElementById('deleteProgramCode').textContent = btn.dataset.code;
            getModal('deleteProgramModal').show();
        }
    });

    const addSectionForm = document.getElementById('addSectionForm');
    if (addSectionForm) {
        addSectionForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const btn = document.getElementById('addSectionBtn');
            toggleLoading(btn, true);

            const formData = new FormData(this);
            const strandWrap = document.getElementById('addSectionStrandWrap');
            const strandHidden = document.getElementById('addSectionStrandHidden');
            if (!strandWrap.hidden) {
                const picked = document.getElementById('addSectionStrand').value;
                formData.set('strand', picked);
            } else if (strandHidden.value) {
                formData.set('strand', strandHidden.value);
            } else {
                formData.delete('strand');
            }

            const response = await fetch(cfg.urls.sectionsStore, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' },
            });

            toggleLoading(btn, false);

            if (!response.ok) {
                const err = await response.json().catch(function () { return {}; });
                showToast(err.message || 'Could not add section', 'error');
                return;
            }

            const data = await response.json();
            const list = document.getElementById(listIdForGradeStrand(data.grade_level, data.strand || ''));
            if (list) {
                list.querySelector('.section-empty')?.remove();
                list.insertAdjacentHTML('beforeend', data.html);
            }

            getModal('addSectionModal').hide();
            showToast('Section added');
        });
    }

    const addStrandForm = document.getElementById('addStrandForm');
    if (addStrandForm) {
        addStrandForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const btn = document.getElementById('addStrandBtn');
            toggleLoading(btn, true);

            const response = await fetch(cfg.urls.strandsStore, {
                method: 'POST',
                body: new FormData(this),
                headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' },
            });

            toggleLoading(btn, false);

            if (!response.ok) {
                const err = await response.json().catch(function () { return {}; });
                const msg = err.errors?.name?.[0] || err.message || 'Could not add strand';
                showToast(msg, 'error');
                return;
            }

            const data = await response.json();
            cfg.shsStrands = cfg.shsStrands || [];
            if (!cfg.shsStrands.includes(data.name)) {
                cfg.shsStrands.push(data.name);
                cfg.shsStrands.sort();
            }

            Object.keys(data.htmlByGrade || {}).forEach(function (grade) {
                const body = document.getElementById(seniorBodyId(grade));
                if (!body) return;
                body.querySelector('.strand-empty-msg')?.remove();
                body.insertAdjacentHTML('beforeend', data.htmlByGrade[grade]);
            });

            getModal('addStrandModal').hide();
            showToast('Strand added');
        });
    }

    const editCourseForm = document.getElementById('editCourseForm');
    if (editCourseForm) {
        editCourseForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const btn = document.getElementById('editCourseBtn');
            toggleLoading(btn, true);
            const response = await fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });
            toggleLoading(btn, false);
            if (response.ok) {
                const html = await response.text();
                const courseId = this.action.split('/').pop();
                document.getElementById('course-' + courseId).outerHTML = html;
                getModal('editCourseModal').hide();
                showToast('Course updated');
            } else {
                showToast('Error updating course', 'error');
            }
        });
    }

    const deleteCourseForm = document.getElementById('deleteCourseForm');
    if (deleteCourseForm) {
        deleteCourseForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const btn = document.getElementById('deleteCourseBtn');
            toggleLoading(btn, true);
            const response = await fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });
            toggleLoading(btn, false);
            if (response.ok) {
                const courseId = this.action.split('/').pop();
                document.getElementById('course-' + courseId)?.remove();
                getModal('deleteCourseModal').hide();
                showToast('Course deleted');
            } else {
                showToast('Error deleting course', 'error');
            }
        });
    }

    document.querySelectorAll('.add-course-form').forEach(function (form) {
        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            const btn = this.querySelector('button[type="submit"]');
            const programId = this.dataset.program;
            toggleLoading(btn, true);
            const response = await fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });
            toggleLoading(btn, false);
            if (response.ok) {
                const html = await response.text();
                const list = document.getElementById('course-list-' + programId);
                list?.querySelector('.course-empty')?.remove();
                list?.insertAdjacentHTML('beforeend', html);
                this.reset();
                showToast('Course added');
            } else {
                showToast('Error adding course', 'error');
            }
        });
    });

    const editProgramForm = document.getElementById('editProgramForm');
    if (editProgramForm) {
        editProgramForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const btn = document.getElementById('editProgramBtn');
            toggleLoading(btn, true);
            const response = await fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' },
            });
            toggleLoading(btn, false);
            if (response.ok) {
                const data = await response.json();
                const label = document.getElementById('program-name-' + data.id);
                if (label) label.textContent = data.program_code + ' — ' + data.program_name;
                getModal('editProgramModal').hide();
                showToast('Program updated');
            } else {
                showToast('Error updating program', 'error');
            }
        });
    }

    const deleteProgramForm = document.getElementById('deleteProgramForm');
    if (deleteProgramForm) {
        deleteProgramForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const btn = document.getElementById('deleteProgramBtn');
            toggleLoading(btn, true);
            const response = await fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' },
            });
            toggleLoading(btn, false);
            if (response.ok) {
                const data = await response.json();
                document.getElementById('program-card-' + data.id)?.remove();
                getModal('deleteProgramModal').hide();
                showToast('Program deleted');
            } else {
                showToast('Error deleting program', 'error');
            }
        });
    }
});
