/**
 * Auth modal: switch between Login and Register without page reload.
 * Uses history.replaceState so /user/login and /user/register stay in sync.
 */
(function () {
  'use strict';

  var loginUrl = typeof window.authLoginUrl !== 'undefined' ? window.authLoginUrl : '/user/login';
  var registerUrl = typeof window.authRegisterUrl !== 'undefined' ? window.authRegisterUrl : '/user/register';

  function showPanel(panelId) {
    var panels = document.querySelectorAll('.auth-panel');
    if (panels.length === 0) return;
    panels.forEach(function (el) {
      el.classList.remove('auth-panel-active');
    });
    var panel = document.getElementById(panelId);
    if (panel) {
      panel.classList.add('auth-panel-active');
    }
  }

  function setUrl(url) {
    try {
      history.replaceState({}, '', url);
    } catch (e) {}
    if (window.self !== window.top) {
      try {
        var path = window.location.pathname + window.location.search;
        window.parent.postMessage({ type: 'st-auth-url', url: path }, '*');
      } catch (err) {}
    }
  }

  function init() {
    document.querySelectorAll('a.switch-auth[href*="/user/register"]').forEach(function (link) {
      link.addEventListener('click', function (e) {
        e.preventDefault();
        showPanel('auth-panel-register');
        setUrl(registerUrl);
        return false;
      });
    });
    document.querySelectorAll('a.switch-auth[href*="/user/login"]').forEach(function (link) {
      link.addEventListener('click', function (e) {
        e.preventDefault();
        showPanel('auth-panel-login');
        setUrl(loginUrl);
        return false;
      });
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();

function togglePassword(id) {
  var input = document.getElementById(id);
  if (!input) return;
  if (input.type === 'password') {
    input.type = 'text';
  } else {
    input.type = 'password';
  }
}

// Realtime username check for register modal (lowercase, unique, 6-30 chars)
(function () {
  'use strict';
  var form = document.getElementById('modalRegisterForm');
  if (!form) return;
  var input = form.querySelector('input[name="username"]');
  var tick = form.querySelector('.username-available-tick');
  var errorEl = form.querySelector('.usernameExist');
  var checkUrl = window.checkUserUrl || '';
  var csrf = window.csrfToken || '';
  var debounceTimer = null;

  function hideTick() {
    if (tick) tick.classList.add('d-none');
  }

  function setError(msg) {
    if (errorEl) errorEl.textContent = msg || '';
    if (msg) hideTick();
  }

  function runCheck(valueAtRequest) {
    if (!checkUrl || !csrf) return;
    var body = 'username=' + encodeURIComponent(valueAtRequest) + '&_token=' + encodeURIComponent(csrf);
    fetch(checkUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
        'X-CSRF-TOKEN': csrf,
        'Accept': 'application/json'
      },
      body: body
    })
      .then(function (res) { return res.ok ? res.json() : null; })
      .then(function (data) {
        if (!data || data.type !== 'username') {
          hideTick();
          return;
        }
        if (data.valid === false) {
          setError('Username must be 6-30 characters, letters, numbers or underscore.');
        } else if (data.data !== false) {
          setError('This username is already taken.');
        } else {
          setError('');
          if (tick) tick.classList.remove('d-none');
        }
      })
      .catch(function () {
        hideTick();
      });
  }

  function handleInput() {
    if (!input) return;
    var v = (input.value || '').trim().toLowerCase();
    input.value = v;
    if (v.length === 0) {
      setError('');
      hideTick();
      return;
    }
    if (v.length < 6 || v.length > 30 || /[^a-z0-9_]/.test(v)) {
      setError('Username must be 6-30 characters, letters, numbers or underscore.');
      return;
    }
    setError('');
    hideTick();
    if (debounceTimer) clearTimeout(debounceTimer);
    debounceTimer = setTimeout(function () {
      var current = (input.value || '').trim().toLowerCase();
      if (current === v) {
        runCheck(current);
      }
    }, 450);
  }

  if (input) {
    input.addEventListener('input', handleInput);
    input.addEventListener('blur', handleInput);
  }
})();

// Require Remember Me for login and I agree for register before enabling buttons
(function () {
  'use strict';
  function wireCheckboxGate(formId, checkboxSelector, buttonSelector) {
    var form = document.getElementById(formId);
    if (!form) return;
    var checkbox = form.querySelector(checkboxSelector);
    var btn = form.querySelector(buttonSelector);
    if (!checkbox || !btn) return;
    function updateState() {
      btn.disabled = !checkbox.checked;
    }
    checkbox.addEventListener('change', updateState);
    // initial
    updateState();
  }
  wireCheckboxGate('pageLoginForm', '#modal_remember', 'button[type=\"submit\"]');
  wireCheckboxGate('modalRegisterForm', '#modal_agree', 'button[type=\"submit\"]');
})();
