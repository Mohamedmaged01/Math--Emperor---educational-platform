/** JSON fetch with CSRF + session cookies; resolves { ok, status, data } (never throws on HTTP errors). */
export async function api(url, { method = 'POST', body, form } = {}) {
    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    const headers = { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' };
    if (token) headers['X-CSRF-TOKEN'] = token;

    let payload;
    if (form) {
        payload = form; // multipart (FormData)
    } else if (body !== undefined) {
        headers['Content-Type'] = 'application/json';
        payload = JSON.stringify(body);
    }

    try {
        const res = await fetch(url, { method, headers, body: payload, credentials: 'same-origin' });
        let data = {};
        try {
            data = await res.json();
        } catch {
            /* empty body */
        }
        if (res.status === 419) {
            data.error = data.error || 'انتهت صلاحية الجلسة — أعد تحميل الصفحة ثم حاول مجددًا.';
        }
        if (res.status === 422 && !data.error && data.errors) {
            data.error = Object.values(data.errors)[0]?.[0];
        }
        return { ok: res.ok, status: res.status, data };
    } catch {
        return { ok: false, status: 0, data: { error: 'تعذّر الاتصال بالخادم — تحقق من اتصالك ثم حاول مجددًا.' } };
    }
}

export const wait = (ms) => new Promise((r) => setTimeout(r, ms));
