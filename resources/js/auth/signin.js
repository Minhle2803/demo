function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
}

function getAuthToken() {
    return localStorage.getItem('token');
}

const form = document.querySelector('#loginForm');

form.addEventListener('submit', async (e) => {
  e.preventDefault();

  const payload = {
    login: form.login.value,
    password: form.password.value,
    remember:form.remember.checked ? 1 : 0,
  };

  try {
    const res = await fetch(form.action, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': getCsrfToken(),
      },
      body: JSON.stringify(payload),
    });

    const data = await res.json();

    if (!res.ok) {
      throw new Error(data.message || 'Login failed');
    }
    // 💾 save token
    localStorage.setItem('token', data.data.token);

    // 🚀 redirect
    window.location.href = '/tradding';

  } catch (err) {
    console.error(err.message);
    alert(err.message);
  }
});