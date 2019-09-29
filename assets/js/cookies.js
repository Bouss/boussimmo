window.setCookie = (name, value, days = 7, path = '/') => {
    const expires = new Date(Date.now() + days * 864e5).toUTCString();
    document.cookie = name + '=' + JSON.stringify(value) + '; expires=' + expires + '; path=' + path
};

window.getCookie = (name) => {
    return document.cookie.split('; ').reduce((r, v) => {
        const parts = v.split('=');
        return parts[0] === name ? JSON.parse(parts[1]) : r
    }, '')
};
