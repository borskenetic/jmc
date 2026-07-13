/**
 * Gate terminal designation — per-kiosk gate selection with server-side occupancy.
 */
(function () {
  const STORAGE_TOKEN = 'attendance_terminal_token';
  const STORAGE_GATE = 'attendance_gate_terminal';

  function cfg() {
    return window.GATE_TERMINAL_CONFIG || {};
  }

  function headers() {
    const c = cfg();
    return {
      'Content-Type': 'application/json',
      Accept: 'application/json',
      'X-CSRF-TOKEN': c.csrf || '',
    };
  }

  function terminalToken() {
    let token = localStorage.getItem(STORAGE_TOKEN);
    if (!token) {
      token = (window.crypto && crypto.randomUUID)
        ? crypto.randomUUID()
        : 't-' + Date.now() + '-' + Math.random().toString(36).slice(2);
      localStorage.setItem(STORAGE_TOKEN, token);
    }
    return token;
  }

  function getGate() {
    return localStorage.getItem(STORAGE_GATE) || '';
  }

  function setGate(name) {
    if (!name) {
      localStorage.removeItem(STORAGE_GATE);
    } else {
      localStorage.setItem(STORAGE_GATE, name);
    }
    updateBadge();
  }

  function payload() {
    const gate = getGate();
    return gate ? { gate } : {};
  }

  function updateBadge() {
    const badge = document.getElementById('gateTerminalBadge');
    const label = document.getElementById('gateTerminalLabel');
    if (!badge || !label) return;

    const gate = getGate();
    if (!gate) {
      badge.hidden = true;
      return;
    }

    label.textContent = gate;
    badge.hidden = false;
  }

  function openModal() {
    const modal = document.getElementById('gateTerminalModal');
    if (!modal) return;
    modal.style.display = 'flex';
    modal.setAttribute('aria-hidden', 'false');
    refreshButtons();
  }

  function closeModal() {
    const modal = document.getElementById('gateTerminalModal');
    if (!modal) return;
    modal.style.display = 'none';
    modal.setAttribute('aria-hidden', 'true');
  }

  async function fetchAvailable() {
    const c = cfg();
    const url = new URL(c.availableUrl, window.location.origin);
    url.searchParams.set('terminal_token', terminalToken());

    const res = await fetch(url.toString(), { headers: { Accept: 'application/json' } });
    if (!res.ok) throw new Error('Failed to load gates');
    return res.json();
  }

  async function claimGate(gate) {
    const c = cfg();
    const res = await fetch(c.claimUrl, {
      method: 'POST',
      headers: headers(),
      body: JSON.stringify({ terminal_token: terminalToken(), gate }),
    });
    const data = await res.json();
    if (!res.ok) {
      throw new Error(data.message || 'Could not claim gate');
    }
    setGate(data.gate || gate);
    closeModal();
  }

  function renderButtons(gates) {
    const container = document.getElementById('gateTerminalButtons');
    const empty = document.getElementById('gateTerminalEmpty');
    if (!container) return;

    container.innerHTML = '';
    const list = Array.isArray(gates) ? gates : [];

    if (list.length === 0) {
      if (empty) empty.hidden = false;
      return;
    }

    if (empty) empty.hidden = true;

    list.forEach((gate) => {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.dataset.gate = gate;
      btn.textContent = gate;
      btn.addEventListener('click', async () => {
        try {
          await claimGate(gate);
        } catch (e) {
          alert(e.message || 'Gate unavailable. Try another.');
          refreshButtons();
        }
      });
      container.appendChild(btn);
    });
  }

  async function refreshButtons() {
    try {
      const data = await fetchAvailable();
      if (data.current_gate && !getGate()) {
        setGate(data.current_gate);
      }
      renderButtons(data.gates || []);
    } catch (e) {
      renderButtons([]);
    }
  }

  async function requireSelection() {
    updateBadge();
    if (getGate()) {
      try {
        await claimGate(getGate());
      } catch (e) {
        setGate('');
        openModal();
      }
      return;
    }
    openModal();
  }

  async function ping() {
    const c = cfg();
    if (!getGate() || !c.pingUrl) return;
    try {
      await fetch(c.pingUrl, {
        method: 'POST',
        headers: headers(),
        body: JSON.stringify({ terminal_token: terminalToken() }),
      });
    } catch (e) { /* ignore */ }
  }

  function bindChange() {
    document.getElementById('gateTerminalChange')?.addEventListener('click', (e) => {
      e.preventDefault();
      openModal();
    });
  }

  window.GateTerminalKiosk = {
    getGate,
    payload,
    requireSelection,
    refreshButtons,
    openModal,
    updateBadge,
  };

  function init() {
    bindChange();
    requireSelection();
    setInterval(ping, 60000);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
