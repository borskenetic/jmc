(function () {
  const container = document.getElementById('sf2-students-container');
  const template = document.getElementById('sf2-student-row-template');
  const countInput = document.getElementById('sf2-student-count');
  const generateBtn = document.getElementById('sf2-generate-rows');

  if (!container || !template) {
    return;
  }

  let rowIndex = container.querySelectorAll('.sf2-student-row').length;

  function mountCalendar(row) {
    if (window.Sf2AttendanceCalendar) {
      window.Sf2AttendanceCalendar.mount(row);
    }
  }

  function addRow(data) {
    const clone = template.content.cloneNode(true);
    const row = clone.querySelector('.sf2-student-row');
    row.dataset.index = String(rowIndex);

    const prefix = `students[${rowIndex}]`;
    row.querySelectorAll('[data-field]').forEach((el) => {
      const field = el.getAttribute('data-field');
      el.name = `${prefix}[${field}]`;
      if (data && data[field] !== undefined && data[field] !== null) {
        el.value = data[field];
      }
    });

    const cal = row.querySelector('.sf2-attendance-cal');
    if (cal && data) {
      if (data.absent_dates) {
        const absent = Array.isArray(data.absent_dates)
          ? data.absent_dates
          : String(data.absent_dates).split(/[\s,;]+/).filter(Boolean);
        cal.dataset.absentInitial = JSON.stringify(absent);
      }
      if (data.tardy_dates) {
        const tardy = Array.isArray(data.tardy_dates)
          ? data.tardy_dates
          : String(data.tardy_dates).split(/[\s,;]+/).filter(Boolean);
        cal.dataset.tardyInitial = JSON.stringify(tardy);
      }
    }

    row.querySelector('.sf2-row-number').textContent = String(rowIndex + 1);
    container.appendChild(clone);
    mountCalendar(row);
    rowIndex++;
  }

  if (generateBtn && countInput) {
    generateBtn.addEventListener('click', function () {
      const n = parseInt(countInput.value, 10);
      if (!n || n < 1 || n > 80) {
        alert('Enter number of students (1–80).');
        return;
      }
      const existing = container.querySelectorAll('.sf2-student-row').length;
      const toAdd = Math.max(0, n - existing);
      for (let i = 0; i < toAdd; i++) {
        addRow({ sex: 'male' });
      }
    });
  }

  document.getElementById('sf2-add-student')?.addEventListener('click', function () {
    addRow({ sex: 'male' });
  });

  container.addEventListener('click', function (e) {
    const btn = e.target.closest('.sf2-remove-row');
    if (!btn) {
      return;
    }
    const row = btn.closest('.sf2-student-row');
    if (row && container.querySelectorAll('.sf2-student-row').length > 1) {
      row.remove();
    }
  });
})();
