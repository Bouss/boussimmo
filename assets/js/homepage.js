import '../css/homepage.css';
import { renderSigninButton, handleAuthClick } from './gmail_client';

function homepageCallback(html) {
    document.getElementsByClassName('container')[0].innerHTML = html;
    document.getElementById('btn-google-authorize').onclick = handleAuthClick;
    renderSigninButton();
}

export { homepageCallback };
